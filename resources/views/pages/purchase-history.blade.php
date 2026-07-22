@extends('layouts.app')
@section('title','Riwayat Belanja — SobatAnak')
@section('content')
<section class="purchase-hero">
    <div class="purchase-shell">
        <a href="{{ route('profile') }}" class="purchase-back">← Kembali ke Profile</a>
        <span class="purchase-kicker">Riwayat Belanja</span>
        <h1>Pesanan <span>{{ $user->name }}</span></h1>
        <p>Pantau pembayaran dan lihat kembali produk yang pernah kamu checkout.</p>
    </div>
</section>

<section class="purchase-shell purchase-page">
    @if(session('success'))
        <div class="purchase-alert success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="purchase-alert error">{{ $errors->first() }}</div>
    @endif

    @if(($pendingPaymentCount ?? 0) > 0 || ($freshPaidCount ?? 0) > 0)
        <div class="purchase-notices">
            @if(($pendingPaymentCount ?? 0) > 0)
                <div class="purchase-notice pending">⏳ <b>{{ $pendingPaymentCount }}</b> pesanan masih menunggu pembayaran.</div>
            @endif
            @if(($freshPaidCount ?? 0) > 0)
                <div class="purchase-notice paid">✅ <b>{{ $freshPaidCount }}</b> pesanan baru berhasil dibayar.</div>
            @endif
        </div>
    @endif

    <nav class="purchase-tabs" aria-label="Filter riwayat belanja">
        @foreach($tabs as $key => $tab)
            <a href="{{ route('profile.purchases', ['tab' => $key]) }}"
               class="purchase-tab {{ $activeTab === $key ? 'active' : '' }}">
                <span>{{ $tab['label'] }}</span>
                <b>{{ $tab['count'] }}</b>
            </a>
        @endforeach
    </nav>

    <div class="purchase-list">
        @forelse($orders as $order)
            @php
                $shipping = is_array($order->shipping_snapshot) ? $order->shipping_snapshot : [];
                $isPending = in_array($order->status, ['pending', 'challenge'], true);
                $isPaid = $order->status === 'paid' || in_array($order->payment_status, ['settlement', 'capture', 'success', 'paid'], true);
            @endphp
            <article class="purchase-card">
                <header class="purchase-card-head">
                    <div>
                        <span class="purchase-order-label">Nomor Pesanan</span>
                        <h2>{{ $order->order_number }}</h2>
                        <p>{{ optional($order->created_at)->format('d M Y, H:i') }}</p>
                    </div>
                    <span class="purchase-status {{ $order->status_tone }}">{{ $order->status_label }}</span>
                </header>

                <div class="purchase-products">
                    @foreach($order->items as $item)
                        <div class="purchase-product">
                            @if($item->product_image)
                                <img src="{{ $item->product_image }}" alt="{{ $item->product_name }}">
                            @else
                                <div class="purchase-product-placeholder">🛍️</div>
                            @endif
                            <div class="purchase-product-info">
                                <h3>{{ $item->product_name }}</h3>
                                <p>{{ $item->quantity }} × Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                                @if($isPaid && $item->product_id)
                                    <a href="{{ route('product.show', $item->product_id) }}">{{ in_array((int) $item->product_id, $reviewedProductIds ?? [], true) ? 'Lihat produk' : 'Beri ulasan produk' }} →</a>
                                @endif
                            </div>
                            <strong>Rp {{ number_format($item->line_total, 0, ',', '.') }}</strong>
                        </div>
                    @endforeach
                </div>

                <div class="purchase-summary">
                    <div class="purchase-meta">
                        @if(!empty($order->selected_payment_label))
                            <span><small>Metode pembayaran</small><b>{{ $order->selected_payment_label }}</b></span>
                        @endif
                        @if(!empty($shipping['recipient_name']))
                            <span><small>Penerima</small><b>{{ $shipping['recipient_name'] }}</b></span>
                        @endif
                    </div>
                    <div class="purchase-total">
                        <small>Total Pembayaran</small>
                        <b>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</b>
                    </div>
                </div>

                <footer class="purchase-actions">
                    @if($isPending)
                        <a href="{{ route('checkout.payment', $order) }}" class="purchase-btn primary">Lanjutkan Pembayaran</a>
                    @else
                        <a href="{{ route('checkout.payment', $order) }}" class="purchase-btn secondary">Lihat Detail Pembayaran</a>
                    @endif
                    <a href="{{ route('products') }}" class="purchase-btn ghost">Belanja Lagi</a>
                </footer>
            </article>
        @empty
            <div class="purchase-empty">
                <div>📦</div>
                <h2>Belum ada pesanan di bagian ini</h2>
                <p>Pesanan yang sesuai filter akan muncul di sini.</p>
                <a href="{{ route('products') }}" class="purchase-btn primary">Mulai Belanja</a>
            </div>
        @endforelse
    </div>

    @if($orders->hasPages())
        <div class="purchase-pagination">{{ $orders->links() }}</div>
    @endif
</section>

<style>
.purchase-shell{width:min(1180px,calc(100% - 2rem));margin:0 auto}.purchase-hero{background:linear-gradient(135deg,#d8f5f1 0%,#fff 50%,#fdecea 100%);padding:3.25rem 0 3rem}.purchase-back{display:inline-flex;color:#2f9e92;font-weight:1000;text-decoration:none;margin-bottom:1.25rem}.purchase-kicker{display:block;color:#e8756a;font-size:.76rem;font-weight:1000;letter-spacing:.16em;text-transform:uppercase}.purchase-hero h1{font-family:var(--font-display,inherit);font-size:clamp(2.2rem,5vw,4.2rem);font-weight:1000;line-height:1;margin:.65rem 0;color:#263e3c}.purchase-hero h1 span{color:#4bbfb0}.purchase-hero p{color:#6b8a88;font-weight:900}.purchase-page{padding:2.4rem 0 4rem}.purchase-alert{border-radius:1rem;padding:1rem 1.1rem;margin-bottom:1rem;font-weight:900}.purchase-alert.success{background:#eafaf6;color:#237b70}.purchase-alert.error{background:#fff0ee;color:#b9473d}.purchase-notices{display:flex;gap:.75rem;flex-wrap:wrap;margin-bottom:1.25rem}.purchase-notice{border-radius:999px;padding:.7rem 1rem;font-weight:900}.purchase-notice.pending{background:#fff6d8;color:#946300;border:1px solid #ffe3a0}.purchase-notice.paid{background:#eafaf6;color:#237b70;border:1px solid #bde9df}.purchase-tabs{display:flex;gap:.7rem;overflow:auto;padding:.2rem 0 1.25rem}.purchase-tab{display:flex;align-items:center;gap:.55rem;white-space:nowrap;text-decoration:none;padding:.75rem 1rem;border-radius:999px;background:#fff;border:1.5px solid #d4eeec;color:#5d7d7a;font-weight:1000}.purchase-tab b{display:grid;place-items:center;min-width:1.7rem;height:1.7rem;padding:0 .4rem;border-radius:999px;background:#eff8f7}.purchase-tab.active{background:#4bbfb0;color:#fff;border-color:#4bbfb0;box-shadow:0 12px 30px rgba(75,191,176,.22)}.purchase-tab.active b{background:rgba(255,255,255,.22)}.purchase-list{display:grid;gap:1.1rem}.purchase-card{background:#fff;border:1.5px solid #d4eeec;border-radius:1.6rem;overflow:hidden;box-shadow:0 18px 46px rgba(38,62,60,.08)}.purchase-card-head{display:flex;justify-content:space-between;align-items:flex-start;gap:1rem;padding:1.25rem 1.35rem;border-bottom:1px solid #e8f3f2;background:#fcfffe}.purchase-order-label{font-size:.72rem;text-transform:uppercase;letter-spacing:.12em;color:#e8756a;font-weight:1000}.purchase-card-head h2{font-size:1rem;color:#263e3c;font-weight:1000;margin:.25rem 0}.purchase-card-head p{font-size:.83rem;color:#78928f;font-weight:850}.purchase-status{border-radius:999px;padding:.55rem .8rem;font-size:.76rem;font-weight:1000}.purchase-status.paid{background:#e6f8f3;color:#247e72}.purchase-status.pending{background:#fff4d3;color:#966500}.purchase-status.failed{background:#fff0ee;color:#bd493f}.purchase-status.refund{background:#f0efff;color:#6256a9}.purchase-products{padding:.2rem 1.35rem}.purchase-product{display:grid;grid-template-columns:74px minmax(0,1fr) auto;gap:1rem;align-items:center;padding:1rem 0;border-bottom:1px solid #edf5f4}.purchase-product:last-child{border-bottom:0}.purchase-product img,.purchase-product-placeholder{width:74px;height:74px;border-radius:1rem;object-fit:cover;background:#f4f9f8}.purchase-product-placeholder{display:grid;place-items:center;font-size:1.7rem}.purchase-product-info h3{font-weight:1000;color:#263e3c;line-height:1.25}.purchase-product-info p{font-size:.86rem;color:#78928f;font-weight:850;margin:.25rem 0}.purchase-product-info a{font-size:.82rem;color:#2f9e92;font-weight:1000;text-decoration:none}.purchase-product>strong{color:#263e3c;font-weight:1000;white-space:nowrap}.purchase-summary{display:flex;justify-content:space-between;gap:1rem;align-items:flex-end;padding:1rem 1.35rem;background:#f8fcfb;border-top:1px solid #e8f3f2}.purchase-meta{display:flex;gap:1.5rem;flex-wrap:wrap}.purchase-meta span,.purchase-total{display:flex;flex-direction:column;gap:.2rem}.purchase-meta small,.purchase-total small{color:#78928f;font-size:.75rem;font-weight:900}.purchase-meta b{color:#263e3c;font-size:.88rem}.purchase-total{text-align:right}.purchase-total b{color:#e8756a;font-size:1.25rem;font-weight:1000}.purchase-actions{display:flex;justify-content:flex-end;gap:.7rem;flex-wrap:wrap;padding:1rem 1.35rem}.purchase-btn{display:inline-flex;align-items:center;justify-content:center;text-decoration:none;border-radius:999px;padding:.78rem 1rem;font-weight:1000;transition:.2s}.purchase-btn:hover{transform:translateY(-2px)}.purchase-btn.primary{background:#e8756a;color:#fff}.purchase-btn.secondary{background:#4bbfb0;color:#fff}.purchase-btn.ghost{background:#fff;color:#2f9e92;border:1.5px solid #bde4df}.purchase-empty{text-align:center;background:#fff;border:1.5px dashed #bde4df;border-radius:1.6rem;padding:3rem 1rem}.purchase-empty>div{font-size:3.5rem}.purchase-empty h2{font-size:1.6rem;font-weight:1000;color:#263e3c;margin:.7rem 0}.purchase-empty p{color:#78928f;font-weight:850;margin-bottom:1.2rem}.purchase-pagination{margin-top:1.4rem}.purchase-pagination nav>div:first-child{display:none}@media(max-width:700px){.purchase-card-head,.purchase-summary{align-items:flex-start;flex-direction:column}.purchase-status{align-self:flex-start}.purchase-product{grid-template-columns:60px minmax(0,1fr)}.purchase-product img,.purchase-product-placeholder{width:60px;height:60px}.purchase-product>strong{grid-column:2}.purchase-total{text-align:left}.purchase-actions{justify-content:stretch}.purchase-actions .purchase-btn{flex:1 1 100%}}
</style>
@endsection
