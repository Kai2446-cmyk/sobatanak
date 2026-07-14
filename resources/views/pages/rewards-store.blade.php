@extends('layouts.app')
@section('title','Tukar Poin Reward — SobatAnak')
@section('content')
<section class="bg-gradient-to-br from-[#D0F0ED] via-white to-[#FDECEA] py-14">
    <div class="max-w-7xl mx-auto px-6 md:px-12">
        <span class="text-coral font-black uppercase tracking-widest text-xs">Reward Voucher</span>
        <h1 class="font-display hero-title mt-3">Tukar <span class="text-teal">Poin Kamu</span></h1>
        <p class="text-[#6B8A88] font-bold mt-2">Gunakan poin dari mini game untuk menukar voucher dan hadiah SobatAnak.</p>
        <div class="flex flex-wrap gap-3 mt-6">
            <a href="{{ route('profile') }}" class="btn-pill bg-white border border-[#D4EEEC]">← Kembali ke Profile</a>
            <a href="{{ route('mini-games') }}" class="btn-pill btn-teal">🎮 Main & Kumpulkan Poin</a>
        </div>
    </div>
</section>

<section class="max-w-7xl mx-auto px-6 md:px-12 py-12 rewards-store-page">
    <div class="reward-summary-card">
        <div>
            <span>Poin Tersedia</span>
            <h2>⭐ {{ number_format($point->points,0,',','.') }}</h2>
            <p>Pilih reward yang sesuai dengan jumlah poin kamu.</p>
        </div>
        <div class="reward-summary-note">
            Reward yang berhasil ditukar akan otomatis masuk ke riwayat reward di profile.
        </div>
    </div>

    @if(session('success'))
        <div class="profile-alert-success mt-5">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="profile-alert-error mt-5">{{ $errors->first() }}</div>
    @endif

    <div class="reward-grid mt-7">
        @forelse($rewards as $reward)
            @php
                $limit = $reward->claim_limit ?? null;
                $claimed = (int) ($reward->used_claims_count ?? 0);
                $remainingQuota = ($limit === null || (int) $limit <= 0) ? null : max(0, (int) $limit - $claimed);
                $quotaAvailable = $remainingQuota === null || $remainingQuota > 0;
                $expired = $reward->expires_at && now()->gt($reward->expires_at);
                $expiryLabel = $reward->expires_at ? $reward->expires_at->format('d M Y') : 'Tidak ada batas waktu';
                $canRedeem = !$expired && $quotaAvailable && $point->points >= $reward->points;
                $remaining = max(0, $reward->points - $point->points);
            @endphp
            <div class="reward-voucher-card {{ $canRedeem ? 'available' : 'locked' }}">
                <div class="reward-ticket-top">
                    <div class="reward-icon">🎁</div>
                    <div>
                        <span class="reward-label">Voucher Reward</span>
                        <h3>{{ $reward->name }}</h3>
                    </div>
                </div>

                <p>{{ $reward->description }}</p>

                <div class="reward-cost">
                    <span>Butuh Poin</span>
                    <b>⭐ {{ number_format($reward->points,0,',','.') }}</b>
                </div>
                @php
                    $type = $reward->discount_type ?? 'fixed';
                    $value = (int) ($reward->discount_value ?? 0);
                    $max = (int) ($reward->max_discount ?? 0);
                    $min = (int) ($reward->min_order ?? 0);
                    $discountLabel = $type === 'percent'
                        ? $value.'% off'.($max > 0 ? ' maks. Rp '.number_format($max,0,',','.') : '')
                        : ($type === 'free_shipping'
                            ? 'Gratis ongkir'.($max > 0 ? ' maks. Rp '.number_format($max,0,',','.') : '')
                            : 'Potongan Rp '.number_format($value,0,',','.'));
                @endphp
                <div class="reward-cost mt-3">
                    <span>Manfaat Voucher</span>
                    <b>{{ $discountLabel }}</b>
                </div>
                @if($min > 0)
                    <p class="text-xs font-black text-[#6B8A88] mt-2">Minimal belanja Rp {{ number_format($min,0,',','.') }}</p>
                @endif
                <p class="text-xs font-black {{ $expired ? 'text-coral' : 'text-[#6B8A88]' }} mt-2">
                    Berlaku sampai: {{ $expiryLabel }}
                </p>
                @if($remainingQuota !== null)
                    <p class="text-xs font-black text-[#6B8A88] mt-2">Sisa kuota voucher: {{ number_format($remainingQuota,0,',','.') }}</p>
                @endif

                @if($canRedeem)
                    <form method="POST" action="{{ route('reward.redeem') }}" class="reward-redeem-form">
                        @csrf
                        <input type="hidden" name="reward_id" value="{{ $reward->id }}">
                        <button class="btn-pill btn-coral">Tukar Sekarang</button>
                    </form>
                @elseif($expired)
                    <button class="btn-pill reward-locked-btn" disabled>Voucher Kadaluarsa</button>
                @elseif(!$quotaAvailable)
                    <button class="btn-pill reward-locked-btn" disabled>Voucher Habis</button>
                @else
                    <button class="btn-pill reward-locked-btn" disabled>Kurang {{ number_format($remaining,0,',','.') }} Poin</button>
                @endif
            </div>
        @empty
            <div class="card p-6">
                <h2 class="font-display text-3xl">Belum ada reward</h2>
                <p class="text-[#6B8A88] font-bold mt-2">Admin belum menambahkan voucher reward.</p>
            </div>
        @endforelse
    </div>

    <div class="card p-6 mt-8">
        <h2 class="font-display text-3xl mb-5">Riwayat Reward Ditukar</h2>
        @forelse($claims as $claim)
            <div class="reward-claim-row">
                <div>
                    <b>{{ $claim->reward_name }}</b>
                    <p>-{{ number_format($claim->points_used,0,',','.') }} poin · {{ $claim->created_at->format('d M Y H:i') }}</p>
                    @if(!empty($claim->voucher_code))
                        <p><strong>Kode voucher:</strong> {{ $claim->voucher_code }}</p>
                    @endif
                    @if(!empty($claim->expires_at))
                        <p><strong>Berlaku sampai:</strong> {{ $claim->expires_at->format('d M Y') }}</p>
                    @endif
                </div>
                <span>{{ ($claim->status ?? 'available') === 'used' ? 'Sudah dipakai' : 'Siap dipakai' }}</span>
            </div>
        @empty
            <p class="text-[#6B8A88] font-bold">Belum ada reward yang ditukar.</p>
        @endforelse
    </div>
</section>

<script>
document.querySelectorAll('.reward-redeem-form').forEach((form) => {
    form.addEventListener('submit', function(){
        const btn = form.querySelector('button');
        if(btn){
            btn.disabled = true;
            btn.textContent = 'Menukar...';
        }
    });
});
</script>
@endsection
