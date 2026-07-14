<?php

namespace App\Http\Controllers;

use App\Models\{CartItem, Product, Reward, UserPoint, RewardClaim, GameSetting};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class UserActionController extends Controller
{
    private function userId()
    {
        return session('user_id');
    }

    private function requireLogin()
    {
        if (!$this->userId()) {
            abort(response()->json([
                'ok' => false,
                'message' => 'Silakan login dulu agar cart dan poin tersimpan per akun.',
                'redirect' => route('login'),
            ], 401));
        }
    }

    public function addCart(Request $request)
    {
        $this->requireLogin();

        $data = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
        ]);

        $product = Product::findOrFail($data['product_id']);
        $stock = (int) ($product->stock ?? 0);

        if ($stock <= 0) {
            return response()->json(['ok' => false, 'message' => 'Maaf, stok produk ini sedang habis.'], 422);
        }

        $item = CartItem::firstOrNew([
            'user_id' => $this->userId(),
            'product_id' => $data['product_id'],
        ]);

        $nextQty = ($item->quantity ?: 0) + 1;
        if ($nextQty > $stock) {
            return response()->json(['ok' => false, 'message' => 'Jumlah produk di cart sudah mencapai stok tersedia.'], 422);
        }

        $item->quantity = $nextQty;
        $item->save();

        $count = CartItem::where('user_id', $this->userId())->sum('quantity');

        return response()->json([
            'ok' => true,
            'cart_count' => $count,
            'message' => 'Produk berhasil masuk keranjang akun kamu.',
        ]);
    }

    public function playGame(Request $request)
    {
        $this->requireLogin();

        $data = $request->validate([
            'game' => 'nullable|string|max:80',
            'score' => 'nullable|integer|min:0|max:1000000',
        ]);

        $userId = (int) $this->userId();
        $game = trim((string) ($data['game'] ?? 'demo')) ?: 'demo';
        $score = max(0, (int) ($data['score'] ?? 0));

        $pointsPerPlay = 10;
        $maxPoints = 60;

        if (DB::getSchemaBuilder()->hasTable('game_settings')) {
            $setting = GameSetting::where('slug', $game)->first();

            if ($setting) {
                if (!$setting->is_active) {
                    return response()->json([
                        'ok' => false,
                        'message' => 'Game sedang dinonaktifkan admin.',
                    ], 422);
                }

                $pointsPerPlay = max(0, (int) ($setting->points_per_play ?? $pointsPerPlay));
                $maxPoints = max(0, (int) ($setting->max_points ?? $maxPoints));
            }
        }

        // Hitung poin dari setting admin.
        // TapTap Kuman berbasis skor, game lain minimal dapat points_per_play saat selesai main.
        if ($game === 'tap-tap-kuman') {
            $earned = $score > 0 ? ($score * max(1, $pointsPerPlay)) : max(1, $pointsPerPlay);
            $earned = $maxPoints > 0 ? min($earned, $maxPoints) : $earned;
        } else {
            $earned = $pointsPerPlay;
            if ($score > 0 && $pointsPerPlay > 0) {
                // Kalau game sudah mengirim skor, tetap aman dibatasi max_points.
                $earned = max($pointsPerPlay, min($score, $maxPoints ?: $score));
            }
            $earned = $maxPoints > 0 ? min($earned, $maxPoints) : $earned;
        }

        $earned = max(0, (int) $earned);

        $point = DB::transaction(function () use ($userId, $earned) {
            $point = UserPoint::where('user_id', $userId)->lockForUpdate()->first();

            if (!$point) {
                $point = UserPoint::create([
                    'user_id' => $userId,
                    // User baru mulai dari 0, lalu ditambah poin game.
                    'points' => 0,
                ]);
            }

            if ($earned > 0) {
                $point->points = (int) $point->points + $earned;
                $point->save();
            }

            return $point->fresh();
        });

        return response()->json([
            'ok' => true,
            'points' => (int) $point->points,
            'earned' => $earned,
            'game' => $game,
            'message' => $earned > 0
                ? 'Yeay! Kamu dapat '.$earned.' poin 🎉'
                : 'Game selesai. Poin game ini sedang diset 0 oleh admin.',
        ]);
    }


    private function makeVoucherCode(int $userId): string
    {
        do {
            $code = 'SA-VCR-' . $userId . '-' . strtoupper(Str::random(6));
        } while (RewardClaim::where('voucher_code', $code)->exists());

        return $code;
    }

    public function redeem(Request $request)
    {
        $this->requireLogin();

        $data = $request->validate([
            'reward_id' => 'required|integer|exists:rewards,id',
        ]);

        $userId = (int) $this->userId();
        $reward = Reward::findOrFail($data['reward_id']);

        if (Schema::hasColumn('rewards', 'is_active') && ! $reward->is_active) {
            return back()->withErrors(['reward' => 'Voucher ini sedang dinonaktifkan admin.']);
        }

        if (Schema::hasColumn('rewards', 'expires_at') && $reward->expires_at && now()->gt($reward->expires_at)) {
            return back()->withErrors(['reward' => 'Voucher ini sudah kadaluarsa. Silakan pilih voucher lain.']);
        }

        try {
            $claim = DB::transaction(function () use ($userId, $reward) {
            $reward = Reward::whereKey($reward->id)->lockForUpdate()->firstOrFail();

            if (Schema::hasColumn('rewards', 'is_active') && ! $reward->is_active) {
                throw new \RuntimeException('Voucher ini sedang dinonaktifkan admin.');
            }

            if (Schema::hasColumn('rewards', 'expires_at') && $reward->expires_at && now()->gt($reward->expires_at)) {
                throw new \RuntimeException('Voucher ini sudah kadaluarsa. Silakan pilih voucher lain.');
            }

            if (Schema::hasColumn('rewards', 'claim_limit')) {
                $limit = $reward->claim_limit;
                if ($limit !== null && (int) $limit > 0) {
                    $claimedQuery = RewardClaim::where('reward_id', $reward->id);
                    if (Schema::hasColumn('reward_claims', 'status')) {
                        $claimedQuery->whereIn('status', ['available', 'reserved', 'used']);
                    }
                    if ((int) $claimedQuery->count() >= (int) $limit) {
                        throw new \RuntimeException('Kuota voucher ini sudah habis. Silakan pilih voucher lain.');
                    }
                }
            }

            $point = UserPoint::where('user_id', $userId)->lockForUpdate()->first();

            if (! $point) {
                $point = UserPoint::create([
                    'user_id' => $userId,
                    'points' => 0,
                ]);
            }

            if ((int) $point->points < (int) $reward->points) {
                throw new \RuntimeException('Poin belum cukup untuk menukar reward ini.');
            }

            $point->points = (int) $point->points - (int) $reward->points;
            $point->save();

            $payload = [
                'user_id' => $userId,
                'reward_name' => $reward->name,
                'points_used' => (int) $reward->points,
            ];

            if (Schema::hasColumn('reward_claims', 'reward_id')) $payload['reward_id'] = $reward->id;
            if (Schema::hasColumn('reward_claims', 'voucher_code')) $payload['voucher_code'] = $this->makeVoucherCode($userId);
            if (Schema::hasColumn('reward_claims', 'discount_type')) $payload['discount_type'] = $reward->discount_type ?: 'fixed';
            if (Schema::hasColumn('reward_claims', 'discount_value')) $payload['discount_value'] = (int) ($reward->discount_value ?? 0);
            if (Schema::hasColumn('reward_claims', 'max_discount')) $payload['max_discount'] = $reward->max_discount;
            if (Schema::hasColumn('reward_claims', 'min_order')) $payload['min_order'] = (int) ($reward->min_order ?? 0);
            if (Schema::hasColumn('reward_claims', 'expires_at')) $payload['expires_at'] = $reward->expires_at;
            if (Schema::hasColumn('reward_claims', 'status')) $payload['status'] = 'available';

            return RewardClaim::create($payload);
            });
        } catch (\RuntimeException $e) {
            if ($request->expectsJson()) {
                return response()->json(['ok' => false, 'message' => $e->getMessage()], 422);
            }
            return back()->withErrors(['reward' => $e->getMessage()]);
        }

        $codeText = $claim->voucher_code ? ' Kode voucher kamu: '.$claim->voucher_code.'.' : '';

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'voucher_code' => $claim->voucher_code,
                'message' => 'Reward berhasil ditukar.'.$codeText,
            ]);
        }

        return redirect()->route('profile.rewards')->with('success', 'Reward berhasil ditukar.'.$codeText.' Gunakan kode ini di checkout.');
    }
}
