<?php
namespace App\Http\Controllers;

use App\Models\{Product, ProductReview};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index()
    {
        return view('pages.products', [
            'products' => Product::query()
                ->orderByDesc('sold')
                ->orderByDesc('rating')
                ->orderByDesc('id')
                ->get(),
            'categories' => Product::select('category')
                ->distinct()
                ->pluck('category'),
        ]);
    }

    public function show(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $galleryImages = collect();
        if (Schema::hasTable('product_images')) {
            $product->load(['galleryImages' => fn ($query) => $query->orderBy('sort_order')->orderBy('id')]);
            $galleryImages = $product->galleryImages
                ->pluck('image')
                ->filter()
                ->unique()
                ->values();
        }

        if ($galleryImages->isEmpty() && !empty($product->image)) {
            $galleryImages = collect([$product->image]);
        }

        $reviewImageColumn = Schema::hasColumn('product_reviews', 'image');
        $reviewMultiImageTable = Schema::hasTable('product_review_images');
        $reviewLikesColumn = Schema::hasColumn('product_reviews', 'likes_count');
        $activeReviewFilter = $request->query('review_filter', 'all');

        $baseReviewsQuery = ProductReview::with('user')->where('product_id', $id);
        $allReviews = (clone $baseReviewsQuery)->latest()->get();

        // Semua ulasan dikirim ke Blade supaya filter bisa jalan langsung di browser tanpa reload.
        // Query string review_filter tetap dibaca oleh JavaScript untuk menentukan filter awal.
        $reviews = (clone $baseReviewsQuery)->latest()->get();

        $reviewImagesMap = collect();
        if ($reviewMultiImageTable && $allReviews->count()) {
            $reviewImagesMap = DB::table('product_review_images')
                ->whereIn('product_review_id', $allReviews->pluck('id'))
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get()
                ->groupBy('product_review_id');
        }

        $avgRating = $allReviews->count() ? round($allReviews->avg('rating'), 1) : $product->rating;
        $reviewCount = $allReviews->count();
        $photoReviewCount = $allReviews->filter(function ($review) use ($reviewImageColumn, $reviewImagesMap) {
            $hasLegacyImage = $reviewImageColumn && !empty($review->image);
            $hasMultiImages = ($reviewImagesMap[$review->id] ?? collect())->count() > 0;
            return $hasLegacyImage || $hasMultiImages;
        })->count();
        $starCounts = [];
        for ($i = 1; $i <= 5; $i++) {
            $starCounts[$i] = $allReviews->where('rating', $i)->count();
        }

        $related = Product::where('category', $product->category)
            ->where('id', '!=', $id)
            ->orderByDesc('sold')
            ->take(4)
            ->get();
        $authUser = \App\Models\User::find(session('user_id'));
        $userAddress = $authUser ? \App\Models\UserAddress::where('user_id', $authUser->id)->first() : null;
        $userReview = $authUser ? ProductReview::where('product_id', $id)->where('user_id', $authUser->id)->first() : null;

        // User boleh kasih ulasan hanya kalau produk ini pernah dibeli dan pembayarannya berhasil.
        // Kalau user sudah punya ulasan lama, tombol edit/hapus tetap boleh muncul tanpa cek ulang status belinya.
        $canReviewProduct = false;
        if ($authUser) {
            $canReviewProduct = \App\Models\Order::where('user_id', $authUser->id)
                ->where(function ($query) {
                    $query->where('status', 'paid')
                        ->orWhereIn('payment_status', ['settlement', 'capture', 'success', 'paid']);
                })
                ->whereHas('items', function ($query) use ($id) {
                    $query->where('product_id', $id);
                })
                ->exists();
        }

        return view('pages.product-detail', compact(
            'product',
            'galleryImages',
            'reviews',
            'avgRating',
            'related',
            'authUser',
            'userAddress',
            'userReview',
            'canReviewProduct',
            'activeReviewFilter',
            'reviewCount',
            'photoReviewCount',
            'starCounts',
            'reviewImageColumn',
            'reviewLikesColumn',
            'reviewMultiImageTable',
            'reviewImagesMap'
        ));
    }
}
