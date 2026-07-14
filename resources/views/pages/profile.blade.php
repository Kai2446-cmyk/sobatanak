@extends('layouts.app')
@section('title','Profile — SobatAnak')
@section('content')
@php
    $avatarUrl = $user->avatar_url;
@endphp

<section class="bg-gradient-to-br from-[#D0F0ED] via-white to-[#FDECEA] py-14">
    <div class="max-w-7xl mx-auto px-6 md:px-12">
        <span class="text-coral font-black uppercase tracking-widest text-xs">Profile</span>
        <h1 class="font-display hero-title mt-3">Halo, <span class="text-teal">{{ $user->name }}</span></h1>
        <p class="text-[#6B8A88] font-bold mt-2">Data akun, poin, cart, reward, dan pengaturan profile ini khusus akun kamu.</p>
    </div>
</section>

<section class="max-w-7xl mx-auto px-6 md:px-12 py-12 profile-page-polish profile-page-v2">
    @if(session('success'))
        <div class="profile-alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="profile-alert-error">{{ $errors->first() }}</div>
    @endif

    <div class="profile-main-grid">
        <div class="card profile-account-card p-6">
            <div class="profile-avatar-wrap">
                @if($avatarUrl)
                    <img src="{{ $avatarUrl }}" class="profile-photo-large" alt="{{ $user->name }}">
                @else
                    <div class="profile-avatar big profile-avatar-empty" aria-label="Belum ada foto profil"></div>
                @endif
            </div>

            <h2 class="font-display text-2xl mt-4">{{ $user->name }}</h2>
            <p class="text-[#6B8A88] font-bold">{{ $user->email }}</p>

            <div class="profile-mini-info mt-4">
                <span>👤 Nama bisa diganti</span>
                <span>🖼️ Foto profile bisa diupload</span>
                <span>🔒 Email tetap aman</span>
            </div>

            <div class="flex flex-wrap gap-3 mt-5">
                <a href="{{ route('cart.index') }}" class="btn-pill btn-teal">🛒 Buka Cart</a>
                <form method="POST" action="{{ route('logout') }}">@csrf<button class="btn-pill btn-coral">Logout</button></form>
            </div>
        </div>

        <div class="card profile-edit-card p-7">
            <div class="profile-edit-head">
                <span class="text-coral font-black uppercase text-xs">Edit Profile</span>
                <h2 class="font-display text-3xl mt-2">Atur Identitas Kamu</h2>
                <p class="profile-muted mt-2">Ubah nama/nickname dan foto profile supaya akun SobatAnak lebih personal.</p>
            </div>

            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="profile-edit-form mt-6">
                @csrf
                @method('PATCH')

                <label class="profile-field">
                    <span>Nama / Nickname</span>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" maxlength="100" required>
                </label>

                <label class="profile-field">
                    <span>Email</span>
                    <input type="email" value="{{ $user->email }}" disabled>
                    <small>Email dipakai untuk login, jadi tidak bisa diganti dari halaman ini.</small>
                </label>

                <label class="profile-field">
                    <span>Ganti Foto Profile</span>
                    <div class="profile-file-wrap">
                        <input type="file" name="avatar" accept="image/png,image/jpeg,image/jpg,image/webp">
                    </div>
                    <small>Format JPG, PNG, WEBP. Maksimal 2MB.</small>
                </label>

                @if($avatarUrl)
                    <label class="profile-remove-avatar">
                        <input type="checkbox" name="remove_avatar" value="1">
                        <span>Hapus foto profile sekarang</span>
                    </label>
                @endif

                <button class="btn-pill btn-teal profile-save-btn">Simpan Profile</button>
            </form>
        </div>

        <div class="profile-side-stack">
            <a href="{{ route('profile.rewards') }}" class="card p-6 profile-point-link">
                <span class="text-coral font-black uppercase text-xs">Poin Kamu</span>
                <h2 class="font-display text-5xl mt-3">⭐ {{ number_format($point->points,0,',','.') }}</h2>
                <p class="text-[#6B8A88] font-bold mt-3">Klik kartu ini untuk menukar poin dengan reward voucher.</p>
                <div class="profile-point-cta">Tukar Poin →</div>
            </a>

            <a href="{{ route('profile.purchases') }}" class="card p-6 profile-history-link {{ (($profilePendingPaymentCount ?? 0) > 0 || ($profileFreshPaidCount ?? 0) > 0) ? 'has-order-pin' : '' }}">
                @if(($profilePendingPaymentCount ?? 0) > 0 || ($profileFreshPaidCount ?? 0) > 0)
                    <div class="profile-red-pin" title="Ada update pesanan penting">📌</div>
                @endif
                <span class="text-coral font-black uppercase text-xs">Riwayat Belanja</span>
                <h2 class="font-display text-5xl mt-3">📦</h2>
                <p class="text-[#6B8A88] font-bold mt-3">Lihat barang yang sudah kamu checkout dan status pembayaran pesananmu.</p>

                @if(($profilePendingPaymentCount ?? 0) > 0)
                    <div class="profile-order-notice pending">
                        <b>{{ $profilePendingPaymentCount }}</b> pesanan menunggu pembayaran
                    </div>
                @endif
                @if(($profileFreshPaidCount ?? 0) > 0)
                    <div class="profile-order-notice paid">
                        <b>{{ $profileFreshPaidCount }}</b> pesanan baru selesai dibayar
                    </div>
                @endif

                <div class="profile-point-cta">Lihat Pesanan →</div>
            </a>

            <div class="card p-6">
                <span class="text-coral font-black uppercase text-xs">Cart Kamu</span>
                <h2 class="font-display text-5xl mt-3">🛍️ {{ $cartItems->sum('quantity') }}</h2>
                <p class="text-[#6B8A88] font-bold mt-3">Keranjang dipisah di halaman Cart supaya checkout tidak gabung dengan profile.</p>
                <a href="{{ route('cart.index') }}" class="btn-pill btn-coral mt-5">Lihat Cart</a>
            </div>
        </div>
    </div>

    <div class="grid md:grid-cols-1 gap-5 mt-8">
        <div class="card p-6">
            <h2 class="font-display text-3xl mb-5">Reward Ditukar</h2>
            @forelse($claims as $claim)
                <div class="border-b border-[#D4EEEC] py-3">
                    <b>{{ $claim->reward_name }}</b>
                    <p class="text-sm text-[#6B8A88]">-{{ number_format($claim->points_used,0,',','.') }} poin · {{ $claim->created_at->format('d M Y H:i') }}</p>
                    @if(!empty($claim->voucher_code))
                        <p class="text-sm text-[#2f9e92] font-black">Kode voucher: {{ $claim->voucher_code }} · {{ ($claim->status ?? 'available') === 'used' ? 'Sudah dipakai' : 'Siap dipakai' }}</p>
                        @if(!empty($claim->expires_at))
                            <p class="text-sm {{ now()->gt($claim->expires_at) ? 'text-coral' : 'text-[#6B8A88]' }} font-black">Berlaku sampai: {{ $claim->expires_at->format('d M Y') }}</p>
                        @endif
                    @endif
                </div>
            @empty
                <p class="text-[#6B8A88] font-bold">Belum ada reward yang ditukar.</p>
            @endforelse
        </div>
    </div>
</section>

<style>
.profile-history-link{display:block;text-decoration:none;position:relative;overflow:hidden;transition:.22s ease}.profile-history-link:before{content:"";position:absolute;right:-42px;top:-42px;width:120px;height:120px;border-radius:999px;background:rgba(232,117,106,.13)}.profile-history-link:hover{transform:translateY(-4px);box-shadow:0 20px 48px rgba(42,61,60,.12)}.profile-history-link h2{filter:drop-shadow(0 10px 18px rgba(47,158,146,.16))}.profile-history-link.has-order-pin{border-color:#FFC9C2;box-shadow:0 18px 42px rgba(232,117,106,.13)}.profile-red-pin{position:absolute;right:1.1rem;top:1rem;width:2.6rem;height:2.6rem;border-radius:999px;background:#E53935;color:#fff;display:grid;place-items:center;font-size:1.18rem;font-weight:1000;box-shadow:0 12px 28px rgba(229,57,53,.28);z-index:2;animation:profilePinPulse 1.5s ease-in-out infinite}.profile-red-pin:after{content:"";position:absolute;inset:-5px;border-radius:999px;border:2px solid rgba(229,57,53,.23)}.profile-order-notice{position:relative;z-index:1;display:flex;align-items:center;gap:.45rem;width:max-content;max-width:100%;border-radius:999px;padding:.55rem .8rem;margin-top:.7rem;font-size:.82rem;font-weight:1000}.profile-order-notice b{min-width:1.45rem;height:1.45rem;border-radius:999px;display:grid;place-items:center;background:#fff}.profile-order-notice.pending{background:#FFF3CD;color:#9A6500;border:1px solid #FFE0A3}.profile-order-notice.paid{background:#FDECEA;color:#E53935;border:1px solid #FFC9C2}.profile-order-notice.paid:before{content:"📍"}.profile-order-notice.pending:before{content:"⏳"}@keyframes profilePinPulse{0%,100%{transform:scale(1)}50%{transform:scale(1.08)}}
</style>
@endsection
