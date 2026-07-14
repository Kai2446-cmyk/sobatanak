@extends('layouts.app')
@section('title','Produk — SobatAnak')
@section('content')

@php
    $ageCategories = ['Bayi 0–12 bln', 'Balita 1–3 thn', 'Anak 3–12 thn'];
    $allCategories = collect($categories ?? [])->values();
    $ageList = $allCategories->filter(fn($cat) => in_array($cat, $ageCategories))->values();
    $normalList = $allCategories->reject(fn($cat) => in_array($cat, $ageCategories))->values();
    $featuredProduct = $products->sortByDesc('sold')->first();
    $maxSoldProduct = $products->max('sold');
@endphp

<section class="kids-shop-page">
    <div class="kids-shop-bg-shape kids-shop-bg-shape-1">✦</div>
    <div class="kids-shop-bg-shape kids-shop-bg-shape-2">✈</div>
    <div class="kids-shop-bg-shape kids-shop-bg-shape-3">☁</div>

    <div class="kids-shop-wrap">
        <aside class="kids-shop-side">
            <div class="kids-side-card kids-cat-box">
                <div class="kids-side-title">Categories</div>

                <button data-filter-cat="Semua" class="kids-cat-link active">
                    <span>🧸</span>
                    <b>Semua Produk</b>
                </button>

                @if($ageList->count())
                    <div class="kids-cat-divider">Umur Anak</div>
                    @foreach($ageList as $cat)
                        <button data-filter-cat="{{ $cat }}" class="kids-cat-link">
                            <span>👶</span>
                            <b>{{ $cat }}</b>
                        </button>
                    @endforeach
                @endif

                @if($normalList->count())
                    <div class="kids-cat-divider">Kategori</div>
                    @foreach($normalList as $cat)
                        @php
                            $catLower = strtolower($cat);
                            $catIcon = str_contains($catLower, 'pakaian') ? '👕' :
                                (str_contains($catLower, 'nutrisi') ? '🥣' :
                                (str_contains($catLower, 'perawatan') ? '🧴' :
                                (str_contains($catLower, 'mainan') ? '🪀' : '🎁')));
                        @endphp
                        <button data-filter-cat="{{ $cat }}" class="kids-cat-link">
                            <span>{{ $catIcon }}</span>
                            <b>{{ $cat }}</b>
                        </button>
                    @endforeach
                @endif
            </div>

            <div class="kids-side-card kids-filter-box">
                <div class="kids-side-title">Cari Produk</div>
                <label class="kids-search-mini">
                    <span>🔎</span>
                    <input data-product-search placeholder="Cari produk..." autocomplete="off">
                </label>
                <select data-product-sort class="kids-sort-mini">
                    <option>Terlaris</option>
                    <option>Terbaru</option>
                    <option>Harga Terendah</option>
                    <option>Harga Tertinggi</option>
                    <option>Rating Tertinggi</option>
                </select>
            </div>
        </aside>

        <main class="kids-shop-main">
            <section class="kids-shop-hero">
                <div class="kids-hero-text">
                    <p>Koleksi SobatAnak</p>
                    <h1>Play & Smile<br><span>New Collection</span></h1>
                    <small>Produk bayi, anak, mainan edukatif, dan kebutuhan mom & baby care pilihan keluarga.</small>
                    <a href="#kids-product-list">Belanja Sekarang</a>
                </div>

                <div class="kids-hero-preview kids-hero-baby-photo">
                    <img src="{{ asset('images/produk-hero-baby-care.png') }}" alt="Produk bayi dan anak SobatAnak" onerror="this.onerror=null;this.src='{{ asset('images/article-placeholder.jpg') }}';">
                </div>

                <div class="kids-hero-badge">Baby Care</div>
            </section>

            <section id="kids-product-list" class="kids-product-section">
                <div class="kids-product-head">
                    <div>
                        <span>Sale</span>
                        <h2>Produk SobatAnak</h2>
                    </div>
                    <b><span data-product-count>{{ $products->count() }}</span> produk</b>
                </div>

                <div class="kids-product-grid">
                    @foreach($products as $p)
                        @php
                            $stock = (int) ($p->stock ?? 0);
                            $isBestSeller = ((int) ($p->sold ?? 0)) === (int) ($maxSoldProduct ?? 0) && (int) ($maxSoldProduct ?? 0) > 0;
                            $productBadge = $isBestSeller ? 'Sale' : ($p->badge ?? null);
                        @endphp

                        <a href="{{ route('product.show', $p->id) }}"
                           id="product-{{ $p->id }}"
                           class="kids-product-card {{ $stock <= 0 ? 'is-sold' : '' }}"
                           data-product-card
                           data-name="{{ $p->name }}"
                           data-category="{{ $p->category }}"
                           data-price="{{ $p->price }}"
                           data-rating="{{ $p->rating }}"
                           data-sold="{{ $p->sold }}"
                           data-id="{{ $p->id }}">
                            <div class="kids-product-imgbox">
                                <img src="{{ $p->image }}" alt="{{ $p->name }}">
                                @if($productBadge)
                                    <span class="kids-product-badge">{{ $productBadge }}</span>
                                @endif
                                <span class="kids-heart">♡</span>
                                @if($stock <= 0)
                                    <div class="kids-sold-mask">Stok Habis</div>
                                @endif
                            </div>

                            <div class="kids-product-info">
                                <p>{{ $p->category }}</p>
                                <h3>{{ $p->name }}</h3>
                                <div class="kids-stars">
                                    <span>★ ★ ★ ★ ★</span>
                                    <small>{{ number_format($p->rating,1) }} · {{ number_format($p->sold,0,',','.') }} terjual</small>
                                </div>
                                <strong>Rp {{ number_format($p->price,0,',','.') }}</strong>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div id="prod-empty" class="hidden kids-empty">
                    <div>🔍</div>
                    <h3>Produk tidak ditemukan</h3>
                    <p>Coba kata kunci atau kategori lain.</p>
                </div>
            </section>
        
    <section class="kids-voucher-offer-section" aria-label="Tawaran voucher SobatAnak">
        <div class="kids-voucher-offer-bg" aria-hidden="true">
            <span class="kids-voucher-planet">🪐</span>
            <span class="kids-voucher-cloud">☁️</span>
            <span class="kids-voucher-star">✨</span>
        </div>

        <div class="kids-voucher-offer-card">
            <div class="kids-voucher-offer-copy">
                <span class="kids-voucher-eyebrow">POINTS & REWARDS</span>
                <h2>Ada Tawaran <strong>Voucher</strong> Untuk Kamu!</h2>
                <p>
                    Tukarkan poin SobatAnak menjadi voucher belanja atau reward lucu.
                    Cek voucher yang tersedia dan pakai saat checkout.
                </p>

                <div class="kids-voucher-mini-list">
                    <div>
                        <b>🎁 Voucher Belanja</b>
                        <small>Potongan belanja produk bayi & anak.</small>
                    </div>
                    <div>
                        <b>🚚 Gratis Ongkir</b>
                        <small>Bantu hemat biaya pengiriman.</small>
                    </div>
                </div>

                @if(session('user_id'))
                    <a href="{{ route('profile.rewards') }}" class="kids-voucher-cta">Lihat Voucher →</a>
                @else
                    <a href="{{ route('login') }}" class="kids-voucher-cta">Login untuk Voucher →</a>
                @endif
            </div>

            <div class="kids-voucher-offer-visual" aria-hidden="true">
                <div class="kids-voucher-gift">🎁</div>
                <div class="kids-voucher-ticket">
                    <span>Voucher</span>
                    <b>SobatAnak</b>
                    <small>Reward Point</small>
                </div>
            </div>
        </div>
    </section>

</main>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const search = document.querySelector('[data-product-search]');
    const sort = document.querySelector('[data-product-sort]');
    const catBtns = document.querySelectorAll('[data-filter-cat]');
    const cards = document.querySelectorAll('[data-product-card]');
    const countEl = document.querySelector('[data-product-count]');
    const emptyEl = document.getElementById('prod-empty');
    let activeCat = 'Semua';

    function filterProducts() {
        const q = (search?.value || '').toLowerCase().trim();
        let visible = 0;

        cards.forEach(c => {
            const matchCat = activeCat === 'Semua' || c.dataset.category === activeCat;
            const matchQ = !q || c.dataset.name.toLowerCase().includes(q) || c.dataset.category.toLowerCase().includes(q);
            const show = matchCat && matchQ;
            c.style.display = show ? '' : 'none';
            if (show) visible++;
        });

        if (countEl) countEl.textContent = visible;
        if (emptyEl) emptyEl.classList.toggle('hidden', visible > 0);

        if (sort) {
            const vis = [...cards].filter(c => c.style.display !== 'none');
            vis.sort((a, b) => {
                const s = sort.value;
                if (s === 'Harga Terendah') return +a.dataset.price - +b.dataset.price;
                if (s === 'Harga Tertinggi') return +b.dataset.price - +a.dataset.price;
                if (s === 'Rating Tertinggi') return +b.dataset.rating - +a.dataset.rating;
                if (s === 'Terbaru') return +b.dataset.id - +a.dataset.id;
                return +b.dataset.sold - +a.dataset.sold;
            });
            vis.forEach(c => c.parentNode.appendChild(c));
        }
    }

    catBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            catBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            activeCat = btn.dataset.filterCat;
            filterProducts();
        });
    });

    search?.addEventListener('input', filterProducts);
    sort?.addEventListener('change', filterProducts);
    filterProducts();
});
</script>

<style>
.kids-shop-page{
    position:relative;
    overflow:hidden;
    min-height:100vh;
    padding:52px 0 118px;
    background:
        radial-gradient(circle at 7% 22%, rgba(255, 164, 195, .20), transparent 9rem),
        radial-gradient(circle at 92% 28%, rgba(80, 192, 215, .20), transparent 12rem),
        radial-gradient(circle at 86% 72%, rgba(167, 223, 255, .32), transparent 17rem),
        linear-gradient(90deg,#FCECF8 0%,#F7F1FB 17%,#EAF8F8 48%,#D9F4F2 100%);
    border-top:1px solid #D4EEEC;
}
.kids-shop-page:before{
    content:'';
    position:absolute;
    left:0;
    right:0;
    bottom:-2px;
    height:150px;
    pointer-events:none;
    background:
        radial-gradient(circle at 5% 78%, #fff 0 48px, transparent 49px),
        radial-gradient(circle at 13% 70%, #BFEAFF 0 65px, transparent 66px),
        radial-gradient(circle at 20% 78%, #fff 0 70px, transparent 71px),
        radial-gradient(circle at 32% 82%, #fff 0 58px, transparent 59px),
        radial-gradient(circle at 72% 78%, #fff 0 72px, transparent 73px),
        radial-gradient(circle at 82% 69%, #C9F0FF 0 62px, transparent 63px),
        radial-gradient(circle at 91% 76%, #fff 0 82px, transparent 83px),
        linear-gradient(to top, #fff 0 58%, transparent 59%);
    opacity:.98;
    z-index:0;
}
.kids-shop-page:after{
    content:'';
    position:absolute;
    inset:0;
    pointer-events:none;
    background:
        linear-gradient(115deg, transparent 0 8%, rgba(255,255,255,.22) 8% 8.5%, transparent 8.5% 100%),
        linear-gradient(245deg, transparent 0 9%, rgba(255,255,255,.24) 9% 9.5%, transparent 9.5% 100%);
    opacity:.55;
}
.kids-shop-bg-shape{
    position:absolute;
    pointer-events:none;
    z-index:1;
    opacity:.42;
    font-weight:1000;
}
.kids-shop-bg-shape-1{
    left:2.5%;
    top:18%;
    width:62px;
    height:62px;
    font-size:0;
    border-radius:12px;
    background:linear-gradient(135deg,#FC9AB9,#D9B8FF);
    clip-path:polygon(50% 0%,88% 18%,100% 60%,64% 100%,18% 88%,0% 42%);
    transform:rotate(20deg);
}
.kids-shop-bg-shape-2{
    right:4.5%;
    top:39%;
    width:88px;
    height:88px;
    font-size:0;
    border-radius:14px;
    background:linear-gradient(135deg,#88D7FF,#BDEBFF);
    clip-path:polygon(50% 0%,90% 20%,100% 62%,62% 100%,20% 88%,0% 42%);
    transform:rotate(-16deg);
}
.kids-shop-bg-shape-3{
    left:3.5%;
    bottom:7%;
    font-size:56px;
    color:#58C7E8;
    opacity:.46;
}
.kids-shop-bg-shape-3:before{
    content:'🚀';
}
.kids-shop-bg-shape-3{
    font-size:0;
}
.kids-shop-bg-shape-3:before{
    font-size:54px;
}
.kids-shop-wrap{
    width:min(1280px, calc(100% - 48px));
    margin:0 auto;
    display:grid;
    grid-template-columns:240px minmax(0,1fr);
    gap:30px;
    align-items:start;
    position:relative;
    z-index:2;
}
.kids-shop-side{
    position:sticky;
    top:96px;
    display:flex;
    flex-direction:column;
    gap:22px;
}
.kids-side-card{
    background:rgba(255,255,255,.88);
    border:1px solid rgba(212,238,236,.78);
    border-radius:10px;
    overflow:hidden;
    box-shadow:0 18px 50px rgba(42,61,60,.08);
}
.kids-side-title{
    background:#72CFC4;
    color:#fff;
    padding:15px 20px;
    font-weight:1000;
    letter-spacing:.02em;
}
.kids-cat-box{padding-bottom:12px}
.kids-cat-link{
    width:100%;
    display:flex;
    align-items:center;
    gap:10px;
    border:0;
    background:transparent;
    padding:9px 18px;
    color:#607D7A;
    text-align:left;
    font-weight:900;
    transition:.2s ease;
}
.kids-cat-link span{width:24px;text-align:center}
.kids-cat-link:hover,.kids-cat-link.active{
    color:#E86D80;
    background:#FFF3F1;
}
.kids-cat-divider{
    margin:10px 18px 6px;
    color:#9AB0AE;
    font-size:10px;
    text-transform:uppercase;
    letter-spacing:.15em;
    font-weight:1000;
}
.kids-filter-box{padding:0 20px 22px}
.kids-search-mini{
    display:flex;
    align-items:center;
    gap:8px;
    margin-top:18px;
    background:#E7FAF7;
    border:1px solid #C9EFEB;
    border-radius:8px;
    padding:0 12px;
}
.kids-search-mini input{
    width:100%;
    height:42px;
    background:transparent;
    outline:0;
    font-weight:900;
    color:#2A3D3C;
}
.kids-sort-mini{
    width:100%;
    margin-top:12px;
    height:42px;
    border:1px solid #C9EFEB;
    background:#fff;
    color:#2A3D3C;
    border-radius:8px;
    padding:0 12px;
    font-weight:900;
}
.kids-shop-main{min-width:0}
.kids-shop-hero{
    min-height:330px;
    display:grid;
    grid-template-columns:minmax(0,1fr) minmax(360px,1.08fr);
    align-items:center;
    gap:18px;
    background:rgba(255,255,255,.90);
    border:1px solid rgba(212,238,236,.82);
    border-radius:12px;
    overflow:hidden;
    position:relative;
    box-shadow:0 20px 60px rgba(42,61,60,.08);
}
.kids-shop-hero:after{
    content:'';
    position:absolute;
    right:-60px;
    top:-70px;
    width:230px;
    height:230px;
    border-radius:50%;
    background:rgba(117,207,197,.13);
}
.kids-hero-text{
    padding:42px 48px;
    position:relative;
    z-index:2;
}
.kids-hero-text p{
    color:#FF7F93;
    font-size:12px;
    font-weight:1000;
    letter-spacing:.22em;
    text-transform:uppercase;
    margin-bottom:14px;
}
.kids-hero-text h1{
    font-family:var(--font-display,inherit);
    color:#273D3B;
    font-size:clamp(2.4rem,4.3vw,4.45rem);
    line-height:.94;
    letter-spacing:-.055em;
    font-weight:1000;
}
.kids-hero-text h1 span{color:#62CBBF}
.kids-hero-text small{
    display:block;
    color:#6B8A88;
    font-size:1rem;
    line-height:1.65;
    max-width:470px;
    font-weight:800;
    margin-top:16px;
}
.kids-hero-text a{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    min-height:46px;
    padding:0 24px;
    margin-top:22px;
    background:#F26F8F;
    color:#fff;
    border-radius:6px;
    font-weight:1000;
    text-decoration:none;
    box-shadow:0 16px 32px rgba(242,111,143,.23);
}
.kids-hero-preview{
    height:100%;
    min-height:320px;
    display:flex;
    align-items:center;
    justify-content:center;
    background:
        radial-gradient(circle at center, rgba(255,255,255,.85), transparent 18rem),
        linear-gradient(135deg,#fff 0%,#F6FDFB 100%);
    overflow:hidden;
    position:relative;
    z-index:2;
}
.kids-hero-preview img{
    width:100%;
    height:100%;
    object-fit:cover;
    filter:none;
}
.kids-hero-empty{font-size:9rem;opacity:.28}
.kids-hero-badge{
    position:absolute;
    right:22px;
    top:22px;
    background:#fff;
    color:#70CFC4;
    border:1px solid #E6F2F0;
    border-radius:999px;
    padding:11px 18px;
    font-weight:1000;
    box-shadow:0 12px 26px rgba(42,61,60,.08);
    z-index:3;
}
.kids-product-section{margin-top:30px}
.kids-product-head{
    display:flex;
    align-items:end;
    justify-content:space-between;
    margin-bottom:18px;
}
.kids-product-head span{color:#839D9A;font-weight:1000}
.kids-product-head h2{
    font-family:var(--font-display,inherit);
    font-size:2rem;
    color:#263B3A;
    font-weight:1000;
    margin-top:3px;
}
.kids-product-head b{
    color:#E86D80;
    background:#fff;
    border:1px solid #FFD6DD;
    border-radius:999px;
    padding:9px 15px;
}
.kids-product-grid{
    display:grid;
    grid-template-columns:repeat(3,minmax(0,1fr));
    gap:26px 28px;
}
.kids-product-card{
    display:block;
    text-decoration:none;
    color:#2A3D3C;
    transition:.25s ease;
}
.kids-product-card:hover{transform:translateY(-5px)}
.kids-product-imgbox{
    position:relative;
    aspect-ratio:1/1;
    background:#fff;
    border:1px solid #E7F2F1;
    border-radius:8px;
    box-shadow:0 16px 38px rgba(42,61,60,.065);
    overflow:hidden;
}
.kids-product-imgbox img{
    width:100%;
    height:100%;
    object-fit:contain;
    padding:18px;
    transition:.3s ease;
}
.kids-product-card:hover img{transform:scale(1.035)}
.kids-product-badge{
    position:absolute;
    top:11px;
    left:11px;
    background:#FF8EA1;
    color:#fff;
    border-radius:4px;
    padding:5px 8px;
    font-size:11px;
    font-weight:1000;
}
.kids-heart{
    position:absolute;
    right:12px;
    top:12px;
    color:#B7C8C6;
    font-size:20px;
}
.kids-product-info{
    padding:5px 4px 0;
    text-align:center;
    min-height:auto;
}
.kids-product-info p{
    color:#7B9693;
    text-transform:uppercase;
    letter-spacing:.13em;
    font-size:9.5px;
    font-weight:1000;
    margin:0 0 2px;
    line-height:1.05;
}
.kids-product-info h3{
    color:#243B3A;
    font-weight:1000;
    font-size:.98rem;
    line-height:1.12;
    min-height:1.45em;
    margin:0;
}
.kids-stars{
    margin-top:2px;
    display:flex;
    justify-content:center;
    align-items:center;
    gap:6px;
    flex-wrap:wrap;
    line-height:1.05;
}
.kids-stars span{
    color:#FFB84E;
    font-size:12px;
    letter-spacing:.02em;
}
.kids-stars small{
    color:#8AA09E;
    font-size:10.5px;
    font-weight:800;
}
.kids-product-info strong{
    display:block;
    color:#F26F8F;
    margin-top:2px;
    font-size:1rem;
    line-height:1.05;
}
.kids-sold-mask{
    position:absolute;
    inset:0;
    display:flex;
    align-items:center;
    justify-content:center;
    background:rgba(255,255,255,.78);
    color:#E86D80;
    font-weight:1000;
}
.is-sold img{filter:grayscale(.75);opacity:.55}
.kids-empty{
    padding:70px 20px;
    text-align:center;
    background:rgba(255,255,255,.75);
    border-radius:18px;
    border:1px dashed #C9EFEB;
}
.kids-empty div{font-size:4rem}
.kids-empty h3{font-size:1.7rem;font-weight:1000;color:#263B3A}
.kids-empty p{color:#6B8A88;font-weight:800;margin-top:6px}

@media(max-width:1100px){
    .kids-shop-wrap{grid-template-columns:1fr}
    .kids-shop-side{position:relative;top:auto;display:grid;grid-template-columns:1fr 1fr}
    .kids-product-grid{grid-template-columns:repeat(2,minmax(0,1fr))}
}
@media(max-width:760px){
    .kids-shop-page{padding:30px 0 95px}
    .kids-shop-wrap{width:min(100% - 28px,1280px)}
    .kids-shop-side{grid-template-columns:1fr}
    .kids-shop-hero{grid-template-columns:1fr}
    .kids-hero-text{padding:34px 28px}
    .kids-hero-preview{min-height:220px}
    .kids-product-grid{grid-template-columns:1fr}
}

.kids-hero-baby-photo{
    background:#F7FBFA;
    padding:28px 34px 28px 0;
}
.kids-hero-baby-photo img{
    border-radius:0;
    object-position:center;
}
.kids-shop-page .kids-product-info h3{
    margin-bottom:0;
}
.kids-product-info .kids-stars{
    margin-bottom:0;
}
.kids-product-info strong{
    padding-top:0;
}
@media(max-width:760px){
    .kids-hero-baby-photo{
        padding:0;
    }
    .kids-hero-baby-photo img{
        min-height:230px;
    }
}


/* FIX FINAL BACKGROUND PRODUK:
   yang dihapus hanya strip putih/awan bawah, dekorasi roket/planet tetap muncul */
.kids-shop-page{
    padding-bottom:70px !important;
    background:
        radial-gradient(circle at 7% 22%, rgba(255, 164, 195, .20), transparent 9rem),
        radial-gradient(circle at 92% 28%, rgba(80, 192, 215, .20), transparent 12rem),
        radial-gradient(circle at 86% 72%, rgba(167, 223, 255, .32), transparent 17rem),
        linear-gradient(90deg,#FCECF8 0%,#F7F1FB 17%,#EAF8F8 48%,#D9F4F2 100%) !important;
}
.kids-shop-page:before{
    content:none !important;
    display:none !important;
}
.kids-shop-page:after{
    background:
        linear-gradient(115deg, transparent 0 8%, rgba(255,255,255,.18) 8% 8.5%, transparent 8.5% 100%),
        linear-gradient(245deg, transparent 0 9%, rgba(255,255,255,.18) 9% 9.5%, transparent 9.5% 100%) !important;
    opacity:.45 !important;
}
.kids-heart{
    display:none !important;
}

/* dekorasi samping tetap ada seperti referensi */
.kids-shop-bg-shape{
    position:absolute !important;
    pointer-events:none !important;
    z-index:1 !important;
}
.kids-shop-bg-shape-1{
    left:2.5% !important;
    top:18% !important;
    width:62px !important;
    height:62px !important;
    font-size:0 !important;
    border-radius:12px !important;
    background:linear-gradient(135deg,#FC9AB9,#D9B8FF) !important;
    clip-path:polygon(50% 0%,88% 18%,100% 60%,64% 100%,18% 88%,0% 42%) !important;
    transform:rotate(20deg) !important;
    opacity:.46 !important;
}
.kids-shop-bg-shape-2{
    right:4.5% !important;
    top:39% !important;
    width:88px !important;
    height:88px !important;
    font-size:0 !important;
    border-radius:14px !important;
    background:linear-gradient(135deg,#88D7FF,#BDEBFF) !important;
    clip-path:polygon(50% 0%,90% 20%,100% 62%,62% 100%,20% 88%,0% 42%) !important;
    transform:rotate(-16deg) !important;
    opacity:.42 !important;
}
.kids-shop-bg-shape-3{
    left:3.5% !important;
    bottom:6% !important;
    font-size:0 !important;
    color:#58C7E8 !important;
    opacity:.55 !important;
    width:160px !important;
    height:80px !important;
}
.kids-shop-bg-shape-3:before{
    content:'🪐  🚀' !important;
    font-size:54px !important;
    display:block !important;
    filter:saturate(.95) opacity(.75) !important;
}


/* FIX: hapus area putih kosong sebelum footer di halaman Produk */
.kids-shop-page{
    margin-bottom:0 !important;
    padding-bottom:0 !important;
    background:
        radial-gradient(circle at 7% 22%, rgba(255, 164, 195, .20), transparent 9rem),
        radial-gradient(circle at 92% 28%, rgba(80, 192, 215, .20), transparent 12rem),
        radial-gradient(circle at 86% 72%, rgba(167, 223, 255, .32), transparent 17rem),
        linear-gradient(90deg,#FCECF8 0%,#F7F1FB 17%,#EAF8F8 48%,#D9F4F2 100%) !important;
}
.kids-shop-page + footer,
.kids-shop-page ~ footer{
    margin-top:0 !important;
}
.kids-shop-page .kids-product-catalog,
.kids-shop-page .kids-products-wrap,
.kids-shop-page .kids-products-section,
.kids-shop-page .kids-product-list,
.kids-shop-page .kids-shop-grid{
    margin-bottom:0 !important;
    padding-bottom:34px !important;
}
body:has(.kids-shop-page){
    background:
        linear-gradient(90deg,#FCECF8 0%,#F7F1FB 17%,#EAF8F8 48%,#D9F4F2 100%) !important;
}


/* FIX: beri ruang aman sebelum footer agar produk tidak nabrak */
.kids-shop-page{
    padding-bottom:96px !important;
    margin-bottom:0 !important;
    background:
        radial-gradient(circle at 7% 22%, rgba(255, 164, 195, .20), transparent 9rem),
        radial-gradient(circle at 92% 28%, rgba(80, 192, 215, .20), transparent 12rem),
        radial-gradient(circle at 86% 72%, rgba(167, 223, 255, .32), transparent 17rem),
        linear-gradient(90deg,#FCECF8 0%,#F7F1FB 17%,#EAF8F8 48%,#D9F4F2 100%) !important;
}
.kids-shop-page .kids-product-catalog,
.kids-shop-page .kids-products-wrap,
.kids-shop-page .kids-products-section,
.kids-shop-page .kids-product-list,
.kids-shop-page .kids-shop-grid{
    padding-bottom:90px !important;
    margin-bottom:0 !important;
}
.kids-shop-page + footer,
.kids-shop-page ~ footer{
    margin-top:0 !important;
}
body:has(.kids-shop-page){
    background:
        linear-gradient(90deg,#FCECF8 0%,#F7F1FB 17%,#EAF8F8 48%,#D9F4F2 100%) !important;
}
@media(max-width:768px){
    .kids-shop-page{
        padding-bottom:76px !important;
    }
    .kids-shop-page .kids-product-catalog,
    .kids-shop-page .kids-products-wrap,
    .kids-shop-page .kids-products-section,
    .kids-shop-page .kids-product-list,
    .kids-shop-page .kids-shop-grid{
        padding-bottom:70px !important;
    }
}


/* SECTION BARU: tawaran voucher/reward di bawah produk sebelum footer */
.kids-voucher-offer-section{
    position:relative;
    overflow:hidden;
    padding:56px 0 86px;
    background:
        radial-gradient(circle at 12% 20%, rgba(255, 164, 195, .24), transparent 14rem),
        radial-gradient(circle at 88% 68%, rgba(143, 231, 216, .30), transparent 16rem),
        linear-gradient(135deg,#FCECF8 0%,#F4FFFC 48%,#FFF8EC 100%);
    border-top:1px solid rgba(75,191,176,.16);
}
.kids-voucher-offer-bg span{
    position:absolute;
    pointer-events:none;
    opacity:.50;
    z-index:1;
}
.kids-voucher-planet{
    left:7%;
    top:22%;
    font-size:54px;
    transform:rotate(-12deg);
}
.kids-voucher-cloud{
    right:9%;
    top:18%;
    font-size:58px;
    opacity:.28!important;
}
.kids-voucher-star{
    left:18%;
    bottom:18%;
    font-size:42px;
}
.kids-voucher-offer-card{
    position:relative;
    z-index:2;
    width:min(1180px, calc(100% - 48px));
    margin:0 auto;
    display:grid;
    grid-template-columns:minmax(0, 1fr) 360px;
    gap:30px;
    align-items:center;
    min-height:320px;
    padding:42px 46px;
    border-radius:38px;
    background:
        linear-gradient(135deg,rgba(255,255,255,.92) 0%,rgba(255,255,255,.76) 100%);
    border:1px solid #D4EEEC;
    box-shadow:0 28px 80px rgba(42,61,60,.10);
    backdrop-filter:blur(14px);
}
.kids-voucher-eyebrow{
    display:block;
    color:#E8756A;
    font-weight:1000;
    text-transform:uppercase;
    letter-spacing:.22em;
    font-size:12px;
    margin-bottom:12px;
}
.kids-voucher-offer-copy h2{
    font-family:var(--font-display, inherit);
    color:#243D3B;
    font-size:clamp(2.45rem, 5vw, 4.9rem);
    line-height:.96;
    font-weight:1000;
    letter-spacing:-.055em;
    max-width:760px;
}
.kids-voucher-offer-copy h2 strong{
    color:#4BBFB0;
    font-weight:1000;
}
.kids-voucher-offer-copy p{
    margin-top:18px;
    color:#6B8A88;
    font-weight:850;
    line-height:1.75;
    max-width:650px;
}
.kids-voucher-mini-list{
    display:grid;
    grid-template-columns:repeat(2,minmax(0,1fr));
    gap:14px;
    margin-top:22px;
    max-width:720px;
}
.kids-voucher-mini-list div{
    padding:15px 16px;
    border-radius:20px;
    background:#F8FFFD;
    border:1px solid #D4EEEC;
}
.kids-voucher-mini-list b{
    display:block;
    color:#243D3B;
    font-weight:1000;
}
.kids-voucher-mini-list small{
    display:block;
    color:#7C9693;
    font-weight:850;
    margin-top:4px;
}
.kids-voucher-cta{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    min-height:54px;
    margin-top:24px;
    padding:0 26px;
    border-radius:999px;
    background:linear-gradient(135deg,#FF8FA3,#FF9B55);
    color:#fff;
    text-decoration:none;
    font-weight:1000;
    box-shadow:0 18px 38px rgba(232,117,106,.22);
    transition:.22s ease;
}
.kids-voucher-cta:hover{
    transform:translateY(-3px);
    box-shadow:0 22px 48px rgba(232,117,106,.28);
}
.kids-voucher-offer-visual{
    position:relative;
    min-height:250px;
    display:flex;
    align-items:center;
    justify-content:center;
}
.kids-voucher-gift{
    position:absolute;
    top:18px;
    right:44px;
    width:112px;
    height:112px;
    border-radius:34px;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:4.5rem;
    background:#FFF8D7;
    box-shadow:0 20px 45px rgba(42,61,60,.11);
    transform:rotate(9deg);
    z-index:3;
}
.kids-voucher-ticket{
    width:270px;
    min-height:170px;
    border-radius:28px;
    background:linear-gradient(135deg,#4BBFB0,#8FE4D8);
    color:#fff;
    padding:28px;
    box-shadow:0 24px 60px rgba(75,191,176,.28);
    transform:rotate(-5deg);
    position:relative;
    overflow:hidden;
}
.kids-voucher-ticket:before,
.kids-voucher-ticket:after{
    content:'';
    position:absolute;
    top:50%;
    width:38px;
    height:38px;
    border-radius:50%;
    background:rgba(255,255,255,.92);
    transform:translateY(-50%);
}
.kids-voucher-ticket:before{left:-19px}
.kids-voucher-ticket:after{right:-19px}
.kids-voucher-ticket span{
    display:inline-flex;
    padding:7px 12px;
    border-radius:999px;
    background:rgba(255,255,255,.25);
    font-weight:1000;
}
.kids-voucher-ticket b{
    display:block;
    font-size:2rem;
    line-height:1;
    margin-top:18px;
    font-weight:1000;
}
.kids-voucher-ticket small{
    display:block;
    margin-top:8px;
    font-weight:900;
    opacity:.86;
}
@media(max-width:900px){
    .kids-voucher-offer-card{
        grid-template-columns:1fr;
        padding:32px 24px;
    }
    .kids-voucher-offer-visual{
        min-height:220px;
    }
    .kids-voucher-mini-list{
        grid-template-columns:1fr;
    }
}


/* FIX: section voucher dibuat full/lebar, tidak mengikuti lebar grid produk */
.kids-voucher-offer-section{
    width:100vw !important;
    max-width:none !important;
    margin-left:calc(50% - 50vw) !important;
    margin-right:calc(50% - 50vw) !important;
    padding:72px 0 96px !important;
    background:
        radial-gradient(circle at 10% 20%, rgba(255, 164, 195, .26), transparent 16rem),
        radial-gradient(circle at 90% 70%, rgba(143, 231, 216, .34), transparent 18rem),
        linear-gradient(135deg,#FCECF8 0%,#F4FFFC 48%,#FFF8EC 100%) !important;
}
.kids-voucher-offer-card{
    width:min(1420px, calc(100vw - 96px)) !important;
    max-width:1420px !important;
    margin:0 auto !important;
    grid-template-columns:minmax(0, 1.25fr) 430px !important;
    min-height:360px !important;
    padding:52px 64px !important;
}
.kids-voucher-offer-copy h2{
    max-width:850px !important;
}
.kids-voucher-offer-copy p{
    max-width:760px !important;
}
.kids-voucher-mini-list{
    max-width:780px !important;
}
.kids-voucher-offer-visual{
    justify-content:center !important;
}
.kids-voucher-ticket{
    width:320px !important;
    min-height:190px !important;
}
.kids-voucher-ticket b{
    font-size:2.35rem !important;
}
.kids-voucher-gift{
    right:28px !important;
    top:8px !important;
}
@media(max-width:1100px){
    .kids-voucher-offer-card{
        width:min(1000px, calc(100vw - 48px)) !important;
        grid-template-columns:1fr !important;
        padding:38px 28px !important;
    }
}
@media(max-width:640px){
    .kids-voucher-offer-section{
        padding:46px 0 68px !important;
    }
    .kids-voucher-offer-card{
        width:calc(100vw - 28px) !important;
        border-radius:28px !important;
    }
}


/* FIX: tambah space aman antara list produk dengan section voucher/footer */
.kids-shop-page{
    padding-bottom:0 !important;
}
.kids-shop-page .kids-product-catalog,
.kids-shop-page .kids-products-wrap,
.kids-shop-page .kids-products-section,
.kids-shop-page .kids-product-list,
.kids-shop-page .kids-shop-grid{
    padding-bottom:150px !important;
    margin-bottom:0 !important;
}
.kids-voucher-offer-section{
    margin-top:0 !important;
    padding-top:92px !important;
}
.kids-voucher-offer-section:before{
    content:'';
    display:block;
    position:absolute;
    top:-115px;
    left:0;
    right:0;
    height:120px;
    background:
        linear-gradient(180deg, rgba(244,255,252,0) 0%, rgba(244,255,252,.82) 58%, rgba(244,255,252,1) 100%);
    pointer-events:none;
    z-index:0;
}
.kids-voucher-offer-card{
    position:relative;
    z-index:2;
}
@media(max-width:768px){
    .kids-shop-page .kids-product-catalog,
    .kids-shop-page .kids-products-wrap,
    .kids-shop-page .kids-products-section,
    .kids-shop-page .kids-product-list,
    .kids-shop-page .kids-shop-grid{
        padding-bottom:110px !important;
    }
    .kids-voucher-offer-section{
        padding-top:70px !important;
    }
}


/* FIX: section voucher ngikut background halaman produk, tidak bikin background baru */
.kids-voucher-offer-section{
    width:100vw !important;
    max-width:none !important;
    margin-left:calc(50% - 50vw) !important;
    margin-right:calc(50% - 50vw) !important;
    background:transparent !important;
    border-top:0 !important;
    box-shadow:none !important;
    padding:80px 0 110px !important;
}
.kids-voucher-offer-section:before{
    display:none !important;
    content:none !important;
}
.kids-voucher-offer-card{
    width:min(1500px, calc(100vw - 140px)) !important;
    max-width:1500px !important;
    margin-left:auto !important;
    margin-right:auto !important;
    transform:translateX(-36px) !important;
    grid-template-columns:minmax(0, 1.28fr) 430px !important;
    padding:52px 72px !important;
    background:rgba(255,255,255,.82) !important;
    border:1px solid rgba(191,236,230,.95) !important;
    box-shadow:0 28px 80px rgba(42,61,60,.09) !important;
}
.kids-voucher-offer-bg span{
    z-index:1 !important;
}
.kids-voucher-offer-copy,
.kids-voucher-offer-visual{
    position:relative !important;
    z-index:3 !important;
}
@media(max-width:1200px){
    .kids-voucher-offer-card{
        width:calc(100vw - 70px) !important;
        transform:none !important;
        padding:42px 36px !important;
    }
}
@media(max-width:900px){
    .kids-voucher-offer-card{
        grid-template-columns:1fr !important;
        width:calc(100vw - 36px) !important;
        padding:34px 24px !important;
    }
}


/* FIX: geser card voucher lebih ke kiri agar posisinya lebih center di layar */
.kids-voucher-offer-card{
    transform:translateX(-92px) !important;
}
@media(max-width:1200px){
    .kids-voucher-offer-card{
        transform:translateX(-28px) !important;
    }
}
@media(max-width:900px){
    .kids-voucher-offer-card{
        transform:none !important;
    }
}


/* FIX FINAL: geser section/card voucher lebih jauh ke kiri */
.kids-voucher-offer-card{
    transform:translateX(-165px) !important;
}
@media(max-width:1400px){
    .kids-voucher-offer-card{
        transform:translateX(-110px) !important;
    }
}
@media(max-width:1200px){
    .kids-voucher-offer-card{
        transform:translateX(-42px) !important;
    }
}
@media(max-width:900px){
    .kids-voucher-offer-card{
        transform:none !important;
    }
}


/* FIX: balikin posisi voucher sedikit ke kanan, tidak terlalu kiri */
.kids-voucher-offer-card{
    transform:translateX(-92px) !important;
}
@media(max-width:1400px){
    .kids-voucher-offer-card{
        transform:translateX(-72px) !important;
    }
}
@media(max-width:1200px){
    .kids-voucher-offer-card{
        transform:translateX(-28px) !important;
    }
}
@media(max-width:900px){
    .kids-voucher-offer-card{
        transform:none !important;
    }
}

</style>
@endsection
