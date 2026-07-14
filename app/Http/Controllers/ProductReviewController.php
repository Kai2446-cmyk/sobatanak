<?php
namespace App\Http\Controllers;

use App\Models\{ProductReview, Product, ProductReviewLike};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductReviewController extends Controller
{
    private function userId() { return session('user_id'); }

    private function syncProductRating($productId): array
    {
        $reviews = ProductReview::where('product_id', $productId)->get();
        $avgRating = $reviews->count() ? round((float) $reviews->avg('rating'), 1) : 0;

        Product::where('id', $productId)->update([
            'rating' => $avgRating,
            'updated_at' => now(),
        ]);

        return [$avgRating, $reviews->count()];
    }

    private function saveUploadedReviewImage($file): string
    {
        $dir = public_path('uploads/reviews');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $filename = 'review-' . now()->format('YmdHis') . '-' . Str::random(8) . '.' . $file->getClientOriginalExtension();
        $file->move($dir, $filename);

        return 'uploads/reviews/' . $filename;
    }

    private function storeReviewImages(Request $request, ProductReview $review): array
    {
        $files = [];
        if ($request->hasFile('images')) {
            $files = $request->file('images');
        } elseif ($request->hasFile('image')) {
            $files = [$request->file('image')];
        }

        $files = array_values(array_filter(is_array($files) ? $files : [$files]));
        if (!$files) {
            return [];
        }

        $savedPaths = [];
        $existingCount = 0;
        if (Schema::hasTable('product_review_images')) {
            $existingCount = DB::table('product_review_images')
                ->where('product_review_id', $review->id)
                ->count();
        } elseif (Schema::hasColumn('product_reviews', 'image') && !empty($review->image)) {
            $existingCount = 1;
        }

        $remainingSlots = max(0, 5 - (int) $existingCount);
        foreach (array_slice($files, 0, $remainingSlots) as $file) {
            $path = $this->saveUploadedReviewImage($file);
            $savedPaths[] = $path;

            if (Schema::hasTable('product_review_images')) {
                DB::table('product_review_images')->insert([
                    'product_review_id' => $review->id,
                    'image' => $path,
                    'sort_order' => $existingCount + count($savedPaths),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Tetap isi kolom lama product_reviews.image supaya data lama dan tampilan lama tidak rusak.
        if (Schema::hasColumn('product_reviews', 'image') && empty($review->image) && !empty($savedPaths[0])) {
            $review->update(['image' => $savedPaths[0]]);
        }

        return $savedPaths;
    }

    private function reviewImages(ProductReview $review): array
    {
        $images = [];
        if (Schema::hasTable('product_review_images')) {
            $images = DB::table('product_review_images')
                ->where('product_review_id', $review->id)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->pluck('image')
                ->filter()
                ->values()
                ->all();
        }

        if (Schema::hasColumn('product_reviews', 'image') && !empty($review->image)) {
            array_unshift($images, $review->image);
        }

        return array_values(array_unique(array_filter($images)));
    }



    private function deleteSelectedReviewImages(Request $request, ProductReview $review): int
    {
        $selected = collect($request->input('delete_review_images', []))
            ->filter(fn ($image) => is_string($image) && trim($image) !== '')
            ->map(fn ($image) => trim($image))
            ->unique()
            ->values();

        if ($selected->isEmpty()) {
            return 0;
        }

        $deleted = 0;
        foreach ($selected as $imagePath) {
            if (Schema::hasTable('product_review_images')) {
                $deleted += DB::table('product_review_images')
                    ->where('product_review_id', $review->id)
                    ->where('image', $imagePath)
                    ->delete();
            }

            if (Schema::hasColumn('product_reviews', 'image') && (string) ($review->image ?? '') === $imagePath) {
                $review->forceFill(['image' => null])->save();
                $deleted++;
            }

            // Hapus file fisik hanya kalau benar-benar file upload ulasan lokal.
            if (str_starts_with($imagePath, 'uploads/reviews/')) {
                $fullPath = public_path($imagePath);
                if (is_file($fullPath)) {
                    @unlink($fullPath);
                }
            }
        }

        // Kalau kolom lama kosong tapi masih ada foto di tabel baru, isi ulang supaya kompatibel dengan tampilan lama.
        if (Schema::hasColumn('product_reviews', 'image') && empty($review->fresh()->image) && Schema::hasTable('product_review_images')) {
            $firstImage = DB::table('product_review_images')
                ->where('product_review_id', $review->id)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->value('image');
            if ($firstImage) {
                $review->forceFill(['image' => $firstImage])->save();
            }
        }

        return $deleted;
    }

    public function store(Request $request, $productId)
    {
        if (!$this->userId()) {
            return response()->json([
                'ok' => false,
                'message' => 'Silakan login dulu untuk memberikan ulasan.',
                'redirect' => route('login')
            ], 401);
        }

        $data = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'body'   => 'required|string|max:1000',
            'image'  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'delete_review_images' => 'nullable|array|max:5',
            'delete_review_images.*' => 'string|max:255',
        ]);

        $existingReview = ProductReview::where('product_id', $productId)
            ->where('user_id', $this->userId())
            ->first();

        if ($existingReview) {
            $this->deleteSelectedReviewImages($request, $existingReview);

            $currentImages = count($this->reviewImages($existingReview->fresh()));
            $newImagesCount = $request->hasFile('images') ? count((array) $request->file('images')) : 0;
            if (($currentImages + $newImagesCount) > 5) {
                return response()->json(['ok' => false, 'message' => 'Maksimal 5 foto ulasan untuk 1 produk ya. Hapus foto lama dulu kalau mau tambah yang baru.'], 422);
            }
        }

        if ($existingReview) {
            $payload = [
                'rating' => $data['rating'],
                'body' => $data['body'],
                'updated_at' => now(),
                'is_edited' => 1,
            ];
            $existingReview->update($payload);
            $this->storeReviewImages($request, $existingReview->fresh());
            $review = $existingReview->fresh('user');
            $message = 'Ulasan berhasil diedit!';
        } else {
            $payload = [
                'product_id' => $productId,
                'user_id' => $this->userId(),
                'rating' => $data['rating'],
                'body' => $data['body'],
                'is_edited' => 0,
            ];
            $review = ProductReview::create($payload);
            $this->storeReviewImages($request, $review);
            $review = $review->fresh('user');
            $message = 'Ulasan berhasil disimpan!';
        }

        [$avgRating, $reviewCount] = $this->syncProductRating($productId);

        $isEdited = (bool) ($review->is_edited ?? false);
        $avatarUrl = null;
        if ($review->user && !empty($review->user->avatar)) {
            $avatar = trim((string) $review->user->avatar);
            $avatarUrl = (str_starts_with($avatar, 'http://') || str_starts_with($avatar, 'https://') || str_starts_with($avatar, '/'))
                ? $avatar
                : asset($avatar);
        }

        return response()->json([
            'ok' => true,
            'message' => $message,
            'mode' => $existingReview ? 'updated' : 'created',
            'review' => [
                'id' => $review->id,
                'rating' => $review->rating,
                'body' => $review->body,
                'image' => !empty($review->image) ? asset($review->image) : null,
                'images' => collect($this->reviewImages($review))->map(fn ($image) => asset($image))->values()->all(),
                'likes_count' => (int) ($review->likes_count ?? 0),
                'user_name' => $review->user?->name ?? 'Kamu',
                'user_role' => $review->user?->role ?? 'user',
                'user_avatar' => $avatarUrl,
                'created_at' => $review->created_at->format('d M Y'),
                'updated_at' => $review->updated_at->format('d M Y'),
                'is_edited' => $isEdited,
            ],
            'avg_rating' => $avgRating,
            'review_count' => $reviewCount,
        ]);
    }

    public function like(ProductReview $review)
    {
        if (!$this->userId()) {
            return response()->json([
                'ok' => false,
                'message' => 'Silakan login dulu untuk menyukai ulasan.',
                'redirect' => route('login')
            ], 401);
        }

        if (!Schema::hasTable('product_review_likes') || !Schema::hasColumn('product_reviews', 'likes_count')) {
            return response()->json(['ok' => false, 'message' => 'Fitur like ulasan belum siap. Jalankan migration dulu.'], 422);
        }

        $like = ProductReviewLike::where('product_review_id', $review->id)
            ->where('user_id', $this->userId())
            ->first();

        if ($like) {
            $like->delete();
            $review->likes_count = max(0, (int) $review->likes_count - 1);
            $liked = false;
        } else {
            ProductReviewLike::create([
                'product_review_id' => $review->id,
                'user_id' => $this->userId(),
            ]);
            $review->likes_count = (int) $review->likes_count + 1;
            $liked = true;
        }
        $review->save();

        return response()->json([
            'ok' => true,
            'liked' => $liked,
            'likes_count' => (int) $review->likes_count,
        ]);
    }

    public function destroy($productId)
    {
        if (!$this->userId()) {
            return response()->json(['ok' => false, 'message' => 'Unauthorized'], 401);
        }
        $review = ProductReview::where('product_id', $productId)->where('user_id', $this->userId())->first();
        if ($review && Schema::hasTable('product_review_images')) {
            DB::table('product_review_images')->where('product_review_id', $review->id)->delete();
        }
        if ($review) {
            $review->delete();
        }
        [$avgRating, $reviewCount] = $this->syncProductRating($productId);

        return response()->json([
            'ok' => true,
            'message' => 'Ulasan dihapus.',
            'avg_rating' => $avgRating,
            'review_count' => $reviewCount,
        ]);
    }
}
