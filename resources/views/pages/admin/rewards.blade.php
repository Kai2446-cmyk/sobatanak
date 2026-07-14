@extends('layouts.admin')
@section('title','Admin Reward — SobatAnak')
@section('page-title','Reward & Poin')
@section('admin-content')
<section class="max-w-7xl mx-auto px-6 md:px-12 py-10">
    <div class="mb-6">
        <span class="text-coral font-black uppercase tracking-widest text-xs">Admin CRUD</span>
        <h1 class="font-display section-title">Kelola <span class="text-teal">Voucher Reward</span></h1>
        <p class="text-[#6B8A88] font-bold mt-2">Voucher yang dibuat admin bisa diklaim user memakai poin game, lalu dipakai saat checkout.</p>
    </div>

    @if(session('success'))
        <div class="profile-alert-success mb-5">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="profile-alert-error mb-5">{{ $errors->first() }}</div>
    @endif

    <div class="grid lg:grid-cols-2 gap-6">
        <div class="card p-6">
            <h2 class="font-display text-3xl mb-4">Tambah Voucher</h2>
            <form method="POST" action="{{ route('admin.rewards.store') }}" class="grid gap-4">
                @csrf
                <input name="name" class="auth-input" placeholder="Nama voucher, contoh: Diskon Rp25.000" required>
                <input name="points" type="number" class="auth-input" placeholder="Poin dibutuhkan" min="1" required>
                <textarea name="description" class="auth-input min-h-[110px]" placeholder="Deskripsi voucher" required></textarea>

                <div class="grid md:grid-cols-2 gap-4">
                    <label class="grid gap-2 font-black text-[#2A3D3C]">
                        Jenis voucher
                        <select name="discount_type" class="auth-input admin-discount-type" required>
                            <option value="fixed">Potongan Rupiah</option>
                            <option value="percent">Diskon Persen</option>
                            <option value="free_shipping">Gratis Ongkir</option>
                        </select>
                    </label>
                    <label class="grid gap-2 font-black text-[#2A3D3C] admin-voucher-value-wrap">
                        Nilai diskon
                        <input
                            name="discount_value"
                            type="number"
                            class="auth-input admin-discount-value admin-discount-fixed"
                            min="0"
                            placeholder="Contoh: 25000 untuk potongan Rp25.000"
                        >

                        <select class="auth-input admin-discount-value admin-discount-percent" disabled>
                            <option value="">Pilih persen diskon</option>
                            <option value="5">5% off</option>
                            <option value="10">10% off</option>
                            <option value="15">15% off</option>
                            <option value="20">20% off</option>
                            <option value="25">25% off</option>
                            <option value="30">30% off</option>
                            <option value="40">40% off</option>
                            <option value="50">50% off</option>
                        </select>

                        <input
                            type="number"
                            class="auth-input admin-discount-value admin-discount-shipping"
                            min="0"
                            placeholder="Contoh: 20000 untuk gratis ongkir maks. Rp20.000"
                            disabled
                        >

                        <small class="admin-voucher-help text-[#6B8A88] font-bold">
                            Potongan Rupiah dan Gratis Ongkir diisi manual. Diskon Persen dipilih dari dropdown.
                        </small>
                    </label>
                    <label class="grid gap-2 font-black text-[#2A3D3C] admin-max-discount-wrap">
                        Maksimal diskon
                        <input name="max_discount" type="number" class="auth-input admin-max-discount" min="0" placeholder="Opsional, contoh 25000">
                    </label>
                    <label class="grid gap-2 font-black text-[#2A3D3C]">
                        Minimal belanja
                        <input name="min_order" type="number" class="auth-input" min="0" value="0">
                    </label>
                    <label class="grid gap-2 font-black text-[#2A3D3C]">
                        Kuota klaim voucher
                        <input name="claim_limit" type="number" class="auth-input" min="0" placeholder="0 / kosong = tanpa batas">
                        <small class="text-[#6B8A88] font-bold">Contoh isi 10: voucher hanya bisa diklaim 10 kali oleh semua user.</small>
                    </label>
                    <label class="grid gap-2 font-black text-[#2A3D3C]">
                        Tanggal kadaluarsa
                        <input name="expires_at" type="date" class="auth-input" min="{{ now()->toDateString() }}">
                        <small class="text-[#6B8A88] font-bold">Kosongkan jika voucher tidak punya masa berlaku.</small>
                    </label>
                </div>

                <label class="inline-flex items-center gap-2 font-black text-[#2A3D3C]">
                    <input type="checkbox" name="is_active" value="1" checked>
                    Voucher aktif dan bisa diklaim user
                </label>

                <button class="btn-pill btn-coral w-fit">Tambah Voucher</button>
            </form>
        </div>

        <div class="card p-6">
            <h2 class="font-display text-3xl mb-4">Voucher Aktif</h2>
            @forelse($rewards as $reward)
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
                    $limit = $reward->claim_limit ?? null;
                    $claimed = (int) ($reward->used_claims_count ?? 0);
                    $remainingQuota = ($limit === null || (int) $limit <= 0) ? null : max(0, (int) $limit - $claimed);
                    $quotaLabel = $remainingQuota === null
                        ? 'Kuota tanpa batas · Sudah diklaim '.number_format($claimed,0,',','.')
                        : 'Sisa kuota '.number_format($remainingQuota,0,',','.').' dari '.number_format((int) $limit,0,',','.').' klaim';
                    $expired = $reward->expires_at && now()->gt($reward->expires_at);
                    $expiredLabel = $reward->expires_at
                        ? 'Kadaluarsa: '.$reward->expires_at->format('d M Y')
                        : 'Kadaluarsa: Tidak ada batas waktu';
                @endphp
                <div class="admin-list">
                    <div class="flex-1">
                        <b>{{ $reward->name }}</b>
                        <p>{{ number_format($reward->points,0,',','.') }} poin · {{ $discountLabel }} · Min. belanja Rp {{ number_format($min,0,',','.') }}</p>
                        <p><b>{{ $quotaLabel }}</b></p>
                        <p><b>{{ $expiredLabel }}</b></p>
                        @if($expired)
                            <p class="text-coral font-black">Voucher sudah kadaluarsa dan tidak muncul di halaman user.</p>
                        @endif
                        @if($remainingQuota !== null && $remainingQuota <= 0)
                            <p class="text-coral font-black">Kuota habis, voucher otomatis tidak muncul di halaman user.</p>
                        @endif
                        <p>{{ $reward->description }}</p>
                    </div>
                    <form method="POST" action="{{ route('admin.rewards.destroy',$reward) }}">
                        @csrf @method('DELETE')
                        <button class="btn-pill btn-coral text-xs py-2" onclick="return confirm('Hapus voucher?')">Hapus</button>
                    </form>
                </div>
            @empty
                <p class="text-[#6B8A88] font-bold">Belum ada voucher reward.</p>
            @endforelse
        </div>
    </div>

    <div class="card p-6 mt-6">
        <h2 class="font-display text-3xl mb-4">Riwayat Penukaran Reward</h2>
        @forelse($claims as $claim)
            <div class="admin-list">
                <span class="profile-avatar">🎁</span>
                <div>
                    <b>{{ $claim->reward_name }}</b>
                    <p>{{ number_format($claim->points_used,0,',','.') }} poin · {{ $claim->created_at->format('d M Y H:i') }}</p>
                    @if(!empty($claim->voucher_code))
                        <p>Kode: <b>{{ $claim->voucher_code }}</b> · {{ ($claim->status ?? 'available') === 'used' ? 'Sudah dipakai' : 'Belum dipakai' }}</p>
                    @endif
                </div>
            </div>
        @empty
            <p class="text-[#6B8A88] font-bold">Belum ada penukaran reward.</p>
        @endforelse
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const typeSelect = document.querySelector('.admin-discount-type');
    const fixedInput = document.querySelector('.admin-discount-fixed');
    const percentSelect = document.querySelector('.admin-discount-percent');
    const shippingSelect = document.querySelector('.admin-discount-shipping');
    const maxDiscountInput = document.querySelector('.admin-max-discount');
    const helpText = document.querySelector('.admin-voucher-help');

    if (!typeSelect || !fixedInput || !percentSelect || !shippingSelect) return;

    const clearAndDisable = (el) => {
        el.disabled = true;
        el.removeAttribute('name');
        el.value = '';
        el.style.display = 'none';
    };

    const enableAsDiscountValue = (el) => {
        el.disabled = false;
        el.setAttribute('name', 'discount_value');
        el.style.display = 'block';
    };

    function syncDiscountField() {
        const type = typeSelect.value;

        clearAndDisable(fixedInput);
        clearAndDisable(percentSelect);
        clearAndDisable(shippingSelect);

        if (type === 'percent') {
            enableAsDiscountValue(percentSelect);
            if (maxDiscountInput) {
                maxDiscountInput.placeholder = 'Contoh: 25000 untuk batas diskon';
                maxDiscountInput.disabled = false;
            }
            if (helpText) helpText.textContent = 'Pilih persen diskon dari dropdown. Maksimal diskon boleh diisi agar potongan tidak terlalu besar.';
            return;
        }

        if (type === 'free_shipping') {
            enableAsDiscountValue(shippingSelect);
            if (maxDiscountInput) {
                maxDiscountInput.placeholder = 'Otomatis mengikuti nominal gratis ongkir';
                maxDiscountInput.disabled = false;
            }
            if (helpText) helpText.textContent = 'Masukkan nominal gratis ongkir manual, contoh 20000 untuk gratis ongkir maks. Rp20.000.';
            return;
        }

        enableAsDiscountValue(fixedInput);
        if (maxDiscountInput) {
            maxDiscountInput.placeholder = 'Opsional, contoh 25000';
            maxDiscountInput.disabled = false;
        }
        if (helpText) helpText.textContent = 'Masukkan nominal rupiah manual, contoh 25000 untuk potongan Rp25.000.';
    }

    typeSelect.addEventListener('change', syncDiscountField);
    syncDiscountField();
});
</script>

<style>
/* FIX admin voucher: field diskon menyesuaikan jenis voucher */
.admin-voucher-value-wrap .admin-discount-value{
    width:100%;
}
.admin-voucher-value-wrap select.admin-discount-value{
    cursor:pointer;
    appearance:auto !important;
    background:#fff !important;
}
.admin-voucher-value-wrap .admin-discount-percent,
.admin-voucher-value-wrap .admin-discount-shipping{
    display:none;
}
.admin-voucher-help{
    display:block;
    margin-top:2px;
    line-height:1.55;
}
</style>


<script>
document.addEventListener('DOMContentLoaded', () => {
    const typeSelect = document.querySelector('.admin-discount-type');
    const maxWrap = document.querySelector('.admin-max-discount-wrap');
    const maxInput = document.querySelector('.admin-max-discount');

    if (!typeSelect || !maxWrap || !maxInput) return;

    function simplifyMaxDiscount() {
        // Dibuat simple: admin cukup isi/pilih Nilai Diskon.
        // Maksimal diskon disembunyikan agar tidak membingungkan.
        maxWrap.style.display = 'none';
        maxInput.value = '';
        maxInput.disabled = true;
    }

    typeSelect.addEventListener('change', simplifyMaxDiscount);
    simplifyMaxDiscount();
});
</script>

<style>
/* FIX admin voucher: Maksimal Diskon disembunyikan supaya form lebih mudah */
.admin-max-discount-wrap{
    display:none !important;
}
</style>


<style>
/* FIX admin voucher: kotak form dirapikan dan tidak terlalu besar */
form[action*="rewards"] .auth-input,
.admin-discount-value,
.admin-discount-type{
    min-height:54px !important;
    height:54px !important;
    padding:0 18px !important;
    border-radius:18px !important;
    font-size:15px !important;
    line-height:1.2 !important;
}
form[action*="rewards"] textarea.auth-input{
    height:96px !important;
    min-height:96px !important;
    padding:16px 18px !important;
    resize:vertical;
}
form[action*="rewards"] .grid.md\:grid-cols-2{
    gap:16px !important;
    align-items:start !important;
}
form[action*="rewards"] label.grid{
    gap:8px !important;
}
form[action*="rewards"] small{
    line-height:1.45 !important;
}
.admin-voucher-value-wrap .admin-discount-shipping{
    display:none;
}
</style>

@endsection
