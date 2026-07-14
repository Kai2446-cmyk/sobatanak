@extends('layouts.app')@section('title','SobatAnak — Mom & Baby Care')@section('content')

@if(session('success'))
    <div id="sobat-success-toast" class="sobat-success-toast" role="status" aria-live="polite">
        <span class="sobat-success-toast-icon">✓</span>
        <div>
            <b>{{ session('success') }}</b>
            <small>Selamat datang kembali di SobatAnak.</small>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toast = document.getElementById('sobat-success-toast');
            if (!toast) return;

            requestAnimationFrame(() => toast.classList.add('show'));

            setTimeout(() => {
                toast.classList.remove('show');
                toast.classList.add('hide');

                setTimeout(() => {
                    toast.remove();
                }, 420);
            }, 2600);
        });
    </script>
@endif

<div class="sobat-landing-soft">
<section class="hero-slider relative overflow-hidden bg-gradient-to-br from-[#D0F0ED] via-white to-[#FDECEA]">
    <div class="absolute top-10 right-20 w-64 h-64 rounded-full bg-[#4BBFB0]/10 blur-3xl"></div>
    <div class="hero-orb hero-orb-one"></div>
    <div class="hero-orb hero-orb-two"></div>
    <div class="max-w-7xl mx-auto px-6 md:px-12 py-8 md:py-10 relative">
        @php $slides=[
            ['Lovely Kids 🌟','Semua yang Si Kecil','Butuhkan Ada di Sini!','Produk bayi berkualitas tinggi — dari pakaian hingga mainan edukatif. Belanja dengan cinta, seperti kasih ibu.','Lihat Koleksi',route('products'),'https://images.unsplash.com/photo-1514090319495-53885bfc202f'],
            ['Produk Terbaik 🍼','Kualitas Premium untuk','Buah Hati Tercinta','Dipilih dengan cermat oleh para ahli parenting. Aman, nyaman, dan menyenangkan untuk tumbuh kembang si kecil.','Belanja Sekarang',route('products'),'https://img.rocket.new/generatedImages/rocket_gen_img_1598e07f6-1765125327744.png'],
            ['Main & Menang 🎮','Main Game, Kumpulkan','Poin & Tukar Hadiah!','Nikmati mini game seru, kumpulkan poin, dan tukarkan dengan voucher belanja menarik.','Main Sekarang',route('mini-games'),'https://img.rocket.new/generatedImages/rocket_gen_img_1eb162b6f-1766561509584.png']
        ]; @endphp
        <div class="hero-slide-stage min-h-[340px] md:min-h-[390px]">
            @foreach($slides as $idx=>$s)
            <div data-slide class="hero-slide {{ $idx===0 ? 'is-active' : '' }} grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
                <div class="hero-copy">
                    <span class="text-coral font-black text-xs uppercase tracking-widest">— {{ $s[0] }}</span>
                    <h1 class="font-display hero-title-sm mt-3">{{ $s[1] }}<br><span class="text-teal">{{ $s[2] }}</span></h1>
                    <p class="text-base text-[#6B8A88] font-bold max-w-md mt-3">{{ $s[3] }}</p>
                    <div class="flex flex-wrap gap-3 mt-4">
                        <a class="btn-pill btn-coral" href="{{ $s[5] }}">{{ $s[4] }} →</a>
                        @if($idx === 2)
                            <a class="btn-pill border-2 border-[#4BBFB0] text-teal" href="{{ route('mini-games') }}">🎮 Main & Poin</a>
                        @endif
                    </div>
                </div>
                <div class="relative hero-media-wrap">
                    <img class="hero-img-sm w-full rounded-[1.5rem] shadow-xl object-cover" src="{{ $s[6] }}" alt="SobatAnak slide">
                </div>
            </div>
            @endforeach
        </div>
        <div class="hero-controls" style="display:none" aria-hidden="true">
            <button data-prev class="hero-nav" aria-label="Slide sebelumnya">‹</button>
            <div class="hero-dots">@foreach($slides as $idx=>$s)<button data-dot="{{ $idx }}" class="hero-dot {{ $idx===0 ? 'is-active' : '' }}" aria-label="Pindah slide {{ $idx+1 }}"></button>@endforeach</div>
            <button data-next class="hero-nav" aria-label="Slide berikutnya">›</button>
        </div>

        {{-- AI Search tepat di bawah hero controls --}}
        <div class="hero-ai-inline mt-6">
            <div class="hero-ai-inner">
                <div class="hero-ai-left">
                    <span class="sobat-ai-kicker">✦ AI SEARCH SOBATANAK</span>
                    <h2 class="hero-ai-title">Tanya AI <mark>Mom & Baby</mark></h2>
                    <p class="hero-ai-desc">Ketik pertanyaan, AI akan rekomendasikan produk & artikel SobatAnak.</p>
                </div>
                <div class="sobat-ai-right">
                    <form class="sobat-ai-search" id="sobatAiForm" autocomplete="off">
                        <span class="sobat-ai-dot"></span>
                        <input id="sobatAiInput" type="text" placeholder="Contoh: perawatan kulit bayi" aria-label="Tanya AI SobatAnak">
                        <button type="submit">Jelajahi</button>
                        <button type="button" id="sobatAiClear" class="sobat-ai-clear" aria-label="Bersihkan">×</button>
                    </form>
                    <div id="sobatAiSuggestions" class="sobat-ai-suggestions" aria-live="polite"></div>
                    <div class="sobat-ai-chips" id="sobatAiChips">
                        <button type="button">botol susu anti kolik</button>
                        <button type="button">popok newborn</button>
                        <button type="button">mainan edukatif</button>
                        <button type="button">perawatan bayi</button>
                        <button type="button">perlengkapan mpasi</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>




<style>
.top-deals-article-section{
    position:relative;
    overflow:hidden;
    padding:78px 0 88px;
    background:
        radial-gradient(circle at 14% 22%, rgba(255, 146, 133, .12), transparent 17rem),
        radial-gradient(circle at 88% 70%, rgba(75, 191, 176, .14), transparent 18rem),
        linear-gradient(180deg,#FFF9F4 0%,#FFFFFF 48%,#F7FFFD 100%);
    border-top:1px solid #FFE0D7;
    border-bottom:1px solid #D4EEEC;
}
.top-deals-wave{
    position:absolute;
    top:-1px;
    left:0;
    right:0;
    height:26px;
    background:
        radial-gradient(circle at 14px 0, transparent 15px, #F1EFFF 16px 27px, transparent 28px) 0 0/54px 26px repeat-x;
    opacity:.95;
}
.top-deals-article-section:before{
    content:'✦';
    position:absolute;
    left:5%;
    top:32%;
    color:#FFC4B8;
    font-size:22px;
    opacity:.75;
}
.top-deals-article-section:after{
    content:'☂';
    position:absolute;
    right:5%;
    top:22%;
    color:#A9DDF9;
    font-size:34px;
    opacity:.75;
    transform:rotate(10deg);
}
.top-deals-heading{
    text-align:center;
    margin-bottom:34px;
    position:relative;
    z-index:2;
}
.top-deals-heading span{
    display:block;
    color:#F18478;
    font-weight:1000;
    text-transform:uppercase;
    letter-spacing:.22em;
    font-size:11px;
    margin-bottom:8px;
}
.top-deals-heading h2{
    font-family:var(--font-display,inherit);
    color:#243D3B;
    font-size:clamp(2.1rem,3.2vw,3.2rem);
    font-weight:1000;
    letter-spacing:-.035em;
    line-height:1;
}
.top-deals-heading i{
    display:block;
    width:80px;
    height:10px;
    margin:13px auto 0;
    background:
        radial-gradient(circle at 5px 5px,#FF8C9A 0 3px,transparent 4px) 0 0/16px 10px repeat-x;
    opacity:.9;
}
.top-deals-grid{
    display:grid;
    grid-template-columns:repeat(3,minmax(0,1fr));
    gap:26px;
    position:relative;
    z-index:2;
}
.top-deals-card{
    min-height:240px;
    position:relative;
    display:block;
    border-radius:14px;
    overflow:hidden;
    text-decoration:none;
    background:#fff;
    box-shadow:0 22px 60px rgba(42,61,60,.10);
    border:1px solid rgba(255,255,255,.72);
    transition:.25s ease;
}
.top-deals-card:hover{
    transform:translateY(-7px);
    box-shadow:0 30px 80px rgba(42,61,60,.15);
}
.top-deals-card img{
    width:100%;
    height:100%;
    min-height:240px;
    object-fit:cover;
    display:block;
    transition:.35s ease;
}
.top-deals-card:hover img{
    transform:scale(1.04);
}
.top-deals-overlay{
    position:absolute;
    inset:0;
    background:linear-gradient(90deg,rgba(37,61,59,.72) 0%,rgba(37,61,59,.36) 45%,rgba(255,255,255,.05) 100%);
}
.top-deals-card-2 .top-deals-overlay{
    background:linear-gradient(90deg,rgba(232,117,106,.70) 0%,rgba(232,117,106,.34) 50%,rgba(255,255,255,.06) 100%);
}
.top-deals-card-3 .top-deals-overlay{
    background:linear-gradient(90deg,rgba(75,191,176,.72) 0%,rgba(75,191,176,.36) 48%,rgba(255,255,255,.05) 100%);
}
.top-deals-badge{
    position:absolute;
    left:18px;
    top:16px;
    background:rgba(255,255,255,.90);
    color:#E8756A;
    border-radius:999px;
    padding:7px 12px;
    font-size:10px;
    font-weight:1000;
    text-transform:uppercase;
    letter-spacing:.12em;
}
.top-deals-content{
    position:absolute;
    inset:auto 0 0 0;
    padding:22px 22px 20px;
    color:#fff;
}
.top-deals-content p{
    font-size:10px;
    font-weight:1000;
    letter-spacing:.16em;
    text-transform:uppercase;
    margin-bottom:8px;
    color:#FFE5DD;
}
.top-deals-content h3{
    font-family:var(--font-display,inherit);
    font-size:1.55rem;
    line-height:1.12;
    font-weight:1000;
    max-width:92%;
    text-shadow:0 2px 18px rgba(0,0,0,.20);
}
.top-deals-content small{
    display:block;
    margin-top:8px;
    color:rgba(255,255,255,.88);
    font-weight:800;
    line-height:1.5;
    max-width:92%;
}
.top-deals-bottom{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:12px;
    margin-top:15px;
}
.top-deals-bottom b{
    display:inline-flex;
    align-items:center;
    color:#fff;
    font-weight:1000;
    letter-spacing:.12em;
    text-transform:uppercase;
    font-size:11px;
}
.top-deals-bottom em{
    color:#fff;
    font-style:normal;
    font-weight:900;
    font-size:12px;
    background:rgba(255,255,255,.18);
    border:1px solid rgba(255,255,255,.22);
    border-radius:999px;
    padding:6px 10px;
    backdrop-filter:blur(10px);
}
.top-deals-more{
    display:flex;
    justify-content:center;
    margin-top:26px;
    position:relative;
    z-index:2;
}
.top-deals-more a{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    min-height:46px;
    padding:0 22px;
    border-radius:999px;
    background:#fff;
    color:#243D3B;
    font-weight:1000;
    text-decoration:none;
    border:1px solid #D4EEEC;
    box-shadow:0 14px 35px rgba(42,61,60,.08);
}
@media(max-width:980px){
    .top-deals-grid{grid-template-columns:1fr}
    .top-deals-card,.top-deals-card img{min-height:270px}
}
</style>

<style>
/* Hero lebih kecil */
.hero-title-sm{font-family:'Fredoka',system-ui,sans-serif;font-size:clamp(2rem,5vw,3.6rem);line-height:1.08;font-weight:700;letter-spacing:.01em;color:#2a3d3c}
.hero-title-sm .text-teal{color:#4BBFB0}
.hero-img-sm{height:300px;object-fit:cover;transition:transform 8s ease}
.hero-slide.is-active .hero-img-sm{transform:scale(1.03)}
@media(max-width:768px){.hero-img-sm{height:220px}}

/* AI inline di dalam hero */
.hero-ai-inline{border-top:1px solid rgba(75,191,176,.2);padding-top:1.25rem}
.hero-ai-inner{display:grid;grid-template-columns:.55fr 1.45fr;gap:2rem;align-items:center;background:rgba(255,255,255,.7);border:1px solid rgba(75,191,176,.22);border-radius:1.5rem;padding:1.5rem 2rem;backdrop-filter:blur(8px)}
.hero-ai-left{position:relative;z-index:1}
.hero-ai-title{font-family:'Fredoka',system-ui,sans-serif;color:#2a3d3c;font-weight:700;font-size:clamp(1.3rem,2.8vw,2rem);line-height:1.1;letter-spacing:.01em}
.hero-ai-title mark{background:linear-gradient(180deg,transparent 55%,#ffe8a6 0);color:#2a3d3c;padding:0 .06em}
.hero-ai-desc{color:#6b8a88;font-weight:800;font-size:.9rem;line-height:1.6;margin-top:.5rem}
@media(max-width:860px){.hero-ai-inner{grid-template-columns:1fr;gap:1rem;padding:1.25rem}}
@media(max-width:560px){.hero-ai-inner{padding:1rem}.hero-ai-title{font-size:1.4rem}}

/* Komponen AI (dipindahkan dari section lama) */
.sobat-ai-kicker{display:block;color:#e8756a;font-weight:1000;letter-spacing:.18em;font-size:.68rem;margin-bottom:.75rem}
.sobat-ai-search{display:flex;align-items:center;gap:.9rem;background:#fff;border:1.5px solid rgba(47,118,214,.28);border-radius:999px;padding:.55rem .6rem .55rem 1rem;box-shadow:0 12px 30px rgba(42,61,60,.08);position:relative}
.sobat-ai-dot{width:.85rem;height:.85rem;border-radius:999px;background:#67d98c;box-shadow:0 0 0 .6rem rgba(103,217,140,.15);flex:0 0 auto}
.sobat-ai-search input{flex:1;min-width:0;border:0;outline:0;background:transparent;color:#2a3d3c;font-weight:900;font-size:1rem}
.sobat-ai-search input::placeholder{color:#99a7b5}
.sobat-ai-search button[type="submit"]{border:0;background:#2f76d6;color:#fff;font-weight:1000;border-radius:999px;padding:.75rem 1.2rem;font-size:.92rem;box-shadow:0 8px 18px rgba(47,118,214,.22);transition:.22s}
.sobat-ai-search button[type="submit"]:hover{transform:translateY(-2px)}
.sobat-ai-clear{display:none;border:0!important;background:#f4f8f8!important;color:#6b8a88!important;width:2rem;height:2rem;border-radius:999px;font-size:1.1rem;font-weight:1000;padding:0!important;box-shadow:none!important}
.sobat-ai-search.has-text .sobat-ai-clear{display:inline-grid;place-items:center}
.sobat-ai-chips{display:flex;flex-wrap:wrap;gap:.55rem;margin-top:.8rem}
.sobat-ai-chips button,.sobat-ai-suggestions button{border:1px solid rgba(75,191,176,.35);background:rgba(255,255,255,.92);color:#2da69b;border-radius:999px;padding:.55rem .85rem;font-weight:900;font-size:.85rem;transition:.2s}
.sobat-ai-chips button:hover,.sobat-ai-suggestions button:hover{background:#dff7f4;transform:translateY(-1px)}
.sobat-ai-suggestions{display:none;margin:.65rem 0 0;background:#fff;border:1px solid rgba(75,191,176,.25);border-radius:1.2rem;padding:.5rem;box-shadow:0 12px 28px rgba(42,61,60,.08)}
.sobat-ai-suggestions.is-open{display:grid;gap:.4rem}
.sobat-ai-suggestions button{width:100%;text-align:left;border-radius:.9rem;background:#fbfffe;color:#2a3d3c}

/* FIX rewards landing: kata-kata dan posisi isi card lebih rapi */
.kids-program-title{
    max-width:860px;
    margin-left:auto;
    margin-right:auto;
}
.kids-program-heading p{
    max-width:760px;
    margin-left:auto;
    margin-right:auto;
    line-height:1.75 !important;
}
.kids-program-card{
    display:grid !important;
    grid-template-columns:auto 1fr !important;
    grid-template-rows:1fr auto !important;
    align-items:start !important;
}
.kids-program-card > div:not(.kids-program-number){
    min-width:0 !important;
}
.kids-program-card h3{
    line-height:1.25 !important;
    margin-bottom:.55rem !important;
}
.kids-program-card p{
    line-height:1.65 !important;
    margin-bottom:1rem !important;
}
.kids-program-card a{
    display:inline-flex !important;
    align-items:center !important;
    width:max-content !important;
    max-width:100% !important;
    margin-top:.25rem !important;
    padding-bottom:.2rem !important;
    white-space:nowrap !important;
}
.kids-program-card strong{
    grid-column:2 !important;
    justify-self:end !important;
    align-self:end !important;
    margin-top:1rem !important;
    line-height:1 !important;
}
.kids-program-number{
    align-self:start !important;
}
@media(max-width:900px){
    .kids-program-title{
        max-width:620px;
    }
    .kids-program-card strong{
        justify-self:start !important;
    }
}


/* FIX landing rewards: link voucher pakai route GET yang aman */
.kids-program-card a{
    text-decoration:none !important;
}


/* FIX home: Produk Unggulan desain catalog seperti referensi, algoritma tetap */
.home-featured-products-catalog{
    position:relative;
    overflow:hidden;
    background:
        radial-gradient(circle at 7% 22%, rgba(255,143,122,.18), transparent 15rem),
        radial-gradient(circle at 90% 12%, rgba(75,191,176,.18), transparent 16rem),
        linear-gradient(135deg,#FFF7EC 0%,#FFFDF7 52%,#ECFFFB 100%);
}
.home-featured-products-catalog::before{
    content:"☀";
    position:absolute;
    left:4%;
    top:8%;
    color:#FF8A7A;
    font-size:3rem;
    opacity:.52;
}
.home-featured-products-catalog::after{
    content:"🧸";
    position:absolute;
    right:4%;
    bottom:8%;
    font-size:3.4rem;
    opacity:.48;
    filter:drop-shadow(0 14px 22px rgba(42,61,60,.10));
}
.home-featured-head{
    display:flex;
    align-items:end;
    justify-content:space-between;
    gap:24px;
    margin-bottom:26px;
}
.home-featured-kicker{
    color:#FF7B6E;
    font-weight:1000;
    text-transform:uppercase;
    letter-spacing:.18em;
    font-size:.78rem;
}
.home-featured-title{
    margin-top:8px;
    color:#243D3B;
    font-size:clamp(2.3rem,4.6vw,4.9rem);
    line-height:.95;
    letter-spacing:-.055em;
    font-weight:1000;
}
.home-featured-title span{
    display:block;
    color:#4BBFB0;
}
.home-featured-head p{
    margin-top:12px;
    color:#6B8A88;
    font-weight:900;
}
.home-featured-view-all{
    min-height:46px;
    padding:0 18px;
    border-radius:999px;
    display:inline-flex;
    align-items:center;
    color:#243D3B;
    background:rgba(255,255,255,.72);
    border:1px solid #D4EEEC;
    text-decoration:none;
    font-weight:1000;
    white-space:nowrap;
}
.home-featured-layout{
    display:grid;
    grid-template-columns:minmax(260px,.75fr) 1.6fr;
    gap:24px;
    align-items:start;
}
.home-featured-promo{
    min-height:390px;
    position:sticky;
    top:96px;
    overflow:hidden;
    border-radius:26px;
    padding:28px;
    display:flex;
    flex-direction:column;
    justify-content:space-between;
    text-decoration:none;
    color:#fff;
    background:linear-gradient(135deg,#FF7B54 0%,#FFB15F 100%);
    box-shadow:0 24px 60px rgba(255,123,84,.24);
}
.home-featured-promo::after{
    content:"";
    position:absolute;
    inset:auto -30px -50px auto;
    width:190px;
    height:190px;
    border-radius:50%;
    background:rgba(255,255,255,.18);
}
.home-featured-promo span{
    display:inline-flex;
    width:max-content;
    padding:8px 12px;
    border-radius:999px;
    background:#FFD319;
    color:#243D3B;
    font-weight:1000;
    font-size:.78rem;
}
.home-featured-promo h3{
    margin-top:16px;
    font-size:2.35rem;
    line-height:1.05;
    font-weight:1000;
    max-width:310px;
}
.home-featured-promo p{
    margin-top:10px;
    max-width:310px;
    font-weight:900;
    opacity:.95;
}
.home-featured-promo b{
    margin-top:18px;
    display:inline-flex;
    width:max-content;
    min-height:40px;
    align-items:center;
    padding:0 15px;
    border-radius:999px;
    background:#fff;
    color:#FF7B54;
}
.home-featured-promo img{
    position:absolute;
    right:-28px;
    bottom:-12px;
    width:58%;
    max-height:220px;
    object-fit:contain;
    filter:drop-shadow(0 18px 34px rgba(42,61,60,.20));
}
.home-featured-grid{
    display:grid;
    grid-template-columns:repeat(3,minmax(0,1fr));
    gap:18px;
}
.home-featured-card{
    position:relative;
    overflow:hidden;
    border-radius:22px;
    background:#fff;
    border:1px solid rgba(212,238,236,.95);
    box-shadow:0 18px 44px rgba(42,61,60,.08);
    transition:.22s ease;
}
.home-featured-card:hover{
    transform:translateY(-6px);
    box-shadow:0 26px 62px rgba(42,61,60,.13);
}
.home-featured-img-wrap{
    position:relative;
    height:190px;
    background:#FFFDF7;
    overflow:hidden;
}
.home-featured-img-wrap img{
    width:100%;
    height:100%;
    object-fit:cover;
    transition:.28s ease;
}
.home-featured-card:hover .home-featured-img-wrap img{
    transform:scale(1.045);
}
.home-featured-badge,
.home-featured-stock{
    position:absolute;
    top:12px;
    left:12px;
    min-height:28px;
    display:inline-flex;
    align-items:center;
    padding:0 10px;
    border-radius:999px;
    background:#FF8A7A;
    color:#fff;
    font-weight:1000;
    font-size:.72rem;
}
.home-featured-badge.is-best{
    background:#FFD319;
    color:#243D3B;
}
.home-featured-stock{
    left:auto;
    right:12px;
    background:#4BBFB0;
}
.home-featured-stock.is-empty{
    background:#E8756A;
}
.home-featured-body{
    padding:16px;
}
.home-featured-card-top{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:8px;
}
.home-featured-card-top span{
    color:#6B8A88;
    font-size:.72rem;
    font-weight:1000;
    letter-spacing:.14em;
    text-transform:uppercase;
}
.home-featured-card-top small{
    color:#FFB33E;
    font-weight:1000;
    white-space:nowrap;
}
.home-featured-body h3{
    min-height:48px;
    margin-top:8px;
    color:#243D3B;
    font-weight:1000;
    font-size:1.05rem;
    line-height:1.28;
}
.home-featured-meta{
    margin-top:6px;
    color:#6B8A88;
    font-weight:900;
    font-size:.82rem;
}
.home-featured-price-row{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:10px;
    margin-top:12px;
}
.home-featured-price-row b{
    color:#FF6F91;
    font-weight:1000;
    font-size:1.05rem;
}
.home-featured-cart-btn{
    width:36px;
    height:36px;
    border:0;
    border-radius:12px;
    background:#FFF0B8;
    color:#243D3B;
    font-weight:1000;
    font-size:1.25rem;
    cursor:pointer;
    box-shadow:0 8px 0 #D9B700;
    transition:.18s ease;
}
.home-featured-cart-btn:hover{
    transform:translateY(-2px);
    box-shadow:0 10px 0 #D9B700;
}
.home-featured-habis-chip{
    min-height:34px;
    padding:0 12px;
    border-radius:999px;
    display:inline-flex;
    align-items:center;
    background:#FDECEA;
    color:#E8756A;
    font-weight:1000;
}
@media(max-width:1100px){
    .home-featured-layout{
        grid-template-columns:1fr;
    }
    .home-featured-promo{
        position:relative;
        top:auto;
        min-height:280px;
    }
}
@media(max-width:820px){
    .home-featured-head{
        align-items:flex-start;
        flex-direction:column;
    }
    .home-featured-grid{
        grid-template-columns:repeat(2,minmax(0,1fr));
    }
}
@media(max-width:560px){
    .home-featured-grid{
        grid-template-columns:1fr;
    }
    .home-featured-img-wrap{
        height:220px;
    }
}


/* FIX home Produk Unggulan: promo card kiri dibuat rapi, tidak saling tabrak */
.home-featured-layout{
    grid-template-columns:minmax(340px,.78fr) 1.55fr !important;
    align-items:start !important;
}
.home-featured-promo{
    position:relative !important;
    top:auto !important;
    min-height:0 !important;
    height:auto !important;
    padding:28px !important;
    display:grid !important;
    grid-template-columns:1fr !important;
    grid-template-rows:auto auto !important;
    gap:22px !important;
    border-radius:30px !important;
    overflow:hidden !important;
    background:
        radial-gradient(circle at 96% 10%, rgba(255,255,255,.30), transparent 9rem),
        linear-gradient(135deg,#FF7B54 0%,#FF9B68 58%,#FFB15F 100%) !important;
}
.home-featured-promo::after{
    width:180px !important;
    height:180px !important;
    right:-52px !important;
    bottom:-62px !important;
    opacity:.55 !important;
}
.home-featured-promo > div{
    position:relative !important;
    z-index:2 !important;
    max-width:100% !important;
}
.home-featured-promo span{
    margin-bottom:12px !important;
}
.home-featured-promo h3{
    max-width:100% !important;
    margin-top:12px !important;
    font-size:clamp(1.8rem, 2.6vw, 2.55rem) !important;
    line-height:1.08 !important;
    letter-spacing:-.035em !important;
}
.home-featured-promo p{
    max-width:100% !important;
    margin-top:12px !important;
    color:rgba(255,255,255,.92) !important;
    line-height:1.65 !important;
}
.home-featured-promo b{
    margin-top:18px !important;
    min-height:46px !important;
    padding:0 18px !important;
    border-radius:999px !important;
    position:relative !important;
    z-index:3 !important;
    box-shadow:0 12px 28px rgba(42,61,60,.12) !important;
}
.home-featured-promo img{
    position:relative !important;
    right:auto !important;
    bottom:auto !important;
    z-index:1 !important;
    width:100% !important;
    height:230px !important;
    max-height:none !important;
    object-fit:cover !important;
    border-radius:24px !important;
    margin:0 !important;
    filter:drop-shadow(0 18px 32px rgba(42,61,60,.16)) !important;
    background:#fff !important;
}
.home-featured-promo:hover img{
    transform:scale(1.025) !important;
}
@media(max-width:1100px){
    .home-featured-layout{
        grid-template-columns:1fr !important;
    }
    .home-featured-promo{
        grid-template-columns:1.1fr .9fr !important;
        align-items:center !important;
    }
    .home-featured-promo img{
        height:260px !important;
    }
}
@media(max-width:720px){
    .home-featured-promo{
        grid-template-columns:1fr !important;
        padding:22px !important;
    }
    .home-featured-promo img{
        height:220px !important;
    }
}


/* FIX login success: notifikasi jadi popup toast, bukan alert di halaman */
.sobat-success-toast{
    position:fixed;
    top:96px;
    right:28px;
    z-index:9999;
    width:min(360px, calc(100vw - 32px));
    display:flex;
    align-items:center;
    gap:14px;
    padding:16px 18px;
    border-radius:22px;
    background:rgba(255,255,255,.92);
    border:1px solid #D4EEEC;
    box-shadow:0 24px 60px rgba(42,61,60,.16);
    color:#243D3B;
    opacity:0;
    transform:translateY(-16px) translateX(18px) scale(.96);
    pointer-events:none;
    backdrop-filter:blur(16px);
    transition:opacity .32s ease, transform .32s ease;
}
.sobat-success-toast.show{
    opacity:1;
    transform:translateY(0) translateX(0) scale(1);
}
.sobat-success-toast.hide{
    opacity:0;
    transform:translateY(-10px) translateX(18px) scale(.96);
}
.sobat-success-toast-icon{
    width:42px;
    height:42px;
    border-radius:15px;
    display:flex;
    align-items:center;
    justify-content:center;
    flex:0 0 auto;
    background:linear-gradient(135deg,#4BBFB0,#72D7CE);
    color:#fff;
    font-weight:1000;
    box-shadow:0 12px 28px rgba(75,191,176,.22);
}
.sobat-success-toast b{
    display:block;
    font-weight:1000;
    line-height:1.2;
}
.sobat-success-toast small{
    display:block;
    margin-top:3px;
    color:#6B8A88;
    font-weight:850;
}
@media(max-width:640px){
    .sobat-success-toast{
        top:78px;
        left:16px;
        right:16px;
        width:auto;
    }
}

</style>

<script>
(() => {
    const form = document.getElementById('sobatAiForm');
    if (!form) return;
    const input = document.getElementById('sobatAiInput');
    const clearBtn = document.getElementById('sobatAiClear');
    const suggestions = document.getElementById('sobatAiSuggestions');
    const chips = document.getElementById('sobatAiChips');
    const placeholders = ['botol susu anti kolik','popok newborn','mainan edukatif','perawatan kulit bayi','pakaian bayi nyaman','perlengkapan mpasi','anak susah makan'];
    let placeholderIndex = 0;
    let debounce = null;

    setInterval(() => {
        if (document.activeElement === input || input.value.trim()) return;
        placeholderIndex = (placeholderIndex + 1) % placeholders.length;
        input.placeholder = 'Contoh: ' + placeholders[placeholderIndex];
    }, 2400);

    function esc(text){ return String(text || '').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m])); }
    function askAI(text){
        if (!text) return;
        window.location.href = '{{ route('ai-chat.page') }}?q=' + encodeURIComponent(text);
    }
    form.addEventListener('submit', e => {
        e.preventDefault();
        const text = input.value.trim();
        if (!text) return;
        askAI(text);
        input.value = '';
        form.classList.remove('has-text');
    });
    input.addEventListener('input', () => {
        const q = input.value.trim();
        form.classList.toggle('has-text', !!q);
        clearTimeout(debounce);
        if (!q) { suggestions.classList.remove('is-open'); suggestions.innerHTML=''; return; }
        debounce = setTimeout(async () => {
            try {
                const res = await fetch('{{ route('ai-chat.suggest') }}?q=' + encodeURIComponent(q), {headers:{'Accept':'application/json'}});
                const data = await res.json();
                if (!data.suggestions || !data.suggestions.length) { suggestions.classList.remove('is-open'); suggestions.innerHTML=''; return; }
                suggestions.innerHTML = data.suggestions.map(item => `<button type="button">${esc(item)}</button>`).join('');
                suggestions.classList.add('is-open');
            } catch(e) { suggestions.classList.remove('is-open'); }
        }, 140);
    });
    suggestions.addEventListener('click', e => {
        const btn = e.target.closest('button');
        if (!btn) return;
        input.value = btn.textContent.trim();
        form.classList.add('has-text');
        suggestions.classList.remove('is-open');
        input.focus();
    });
    chips.addEventListener('click', e => {
        const btn = e.target.closest('button');
        if (!btn) return;
        input.value = btn.textContent.trim();
        form.classList.add('has-text');
        askAI(input.value.trim());
        input.value = '';
        form.classList.remove('has-text');
    });
    clearBtn.addEventListener('click', () => { input.value=''; form.classList.remove('has-text'); suggestions.classList.remove('is-open'); suggestions.innerHTML=''; input.focus(); });
})();
</script>

<section class="home-featured-products-catalog py-16">
    <div class="max-w-7xl mx-auto px-6 md:px-12">
        <div class="home-featured-head">
            <div>
                <span class="home-featured-kicker">Produk Unggulan</span>
                <h2 class="home-featured-title font-display">Collections that <span>Care Every Day</span></h2>
                <p>Produk pilihan SobatAnak yang paling sering dibeli keluarga.</p>
            </div>
            <a class="home-featured-view-all" href="{{ route('products') }}">Lihat Semua Produk →</a>
        </div>

        @php
            $maxSoldLanding = $products->max('sold');
            $featuredProduct = $products->first();
        @endphp

        <div class="home-featured-layout">
            @if($featuredProduct)
                @php
                    $heroStock = is_null($featuredProduct->stock ?? null) ? 20 : (int) $featuredProduct->stock;
                @endphp
                <a class="home-featured-promo" href="{{ route('product.show', $featuredProduct->id) }}">
                    <div>
                        <span>Enjoy Special Pick</span>
                        <h3>{{ $featuredProduct->name }}</h3>
                        <p>Produk favorit untuk kebutuhan si kecil hari ini.</p>
                        <b>Belanja Sekarang →</b>
                    </div>
                    <img src="{{ $featuredProduct->image }}" alt="{{ $featuredProduct->name }}">
                </a>
            @endif

            <div class="home-featured-grid">
                @foreach($products as $p)
                    @php
                        $stock = is_null($p->stock ?? null) ? 20 : (int) $p->stock;
                        $isBestSeller = ((int) ($p->sold ?? 0)) === (int) ($maxSoldLanding ?? 0) && (int) ($maxSoldLanding ?? 0) > 0;
                        $productBadge = $isBestSeller ? 'Terlaris' : ((strtolower((string) ($p->badge ?? '')) === 'terlaris') ? null : ($p->badge ?? null));
                    @endphp

                    <div class="home-featured-card reveal modal-card cursor-pointer {{ $stock <= 0 ? 'is-stock-empty' : '' }}"
                        data-product-link="{{ route('product.show', $p->id) }}"
                        data-id="{{ $p->id }}"
                        data-name="{{ $p->name }}"
                        data-category="{{ $p->category }}"
                        data-price="{{ $p->price }}"
                        data-rating="{{ $p->rating }}"
                        data-sold="{{ $p->sold }}"
                        data-image="{{ $p->image }}"
                        data-badge="{{ $productBadge }}"
                        data-stock="{{ $stock }}">
                        <div class="home-featured-img-wrap">
                            <img class="{{ $stock <= 0 ? 'stock-empty-img' : '' }}" src="{{ $p->image }}" alt="{{ $p->name }}">
                            @if($productBadge)
                                <span class="home-featured-badge {{ $isBestSeller ? 'is-best' : '' }}">{{ $productBadge }}</span>
                            @endif
                            @if($stock <= 0)
                                <span class="home-featured-stock is-empty">Habis</span>
                            @elseif($stock <= 5)
                                <span class="home-featured-stock">Stok sedikit</span>
                            @endif
                        </div>

                        <div class="home-featured-body">
                            <div class="home-featured-card-top">
                                <span>{{ $p->category }}</span>
                                <small>⭐ {{ number_format((float) $p->rating, 1) }}</small>
                            </div>

                            <h3>{{ $p->name }}</h3>

                            <div class="home-featured-meta">
                                <span>{{ number_format($p->sold,0,',','.') }} terjual</span>
                            </div>

                            <div class="home-featured-price-row">
                                <b>Rp {{ number_format($p->price,0,',','.') }}</b>
                                @if($stock > 0)
                                    <button data-buy data-product-id="{{ $p->id }}" class="home-featured-cart-btn" aria-label="Tambah {{ $p->name }} ke keranjang">+</button>
                                @else
                                    <span class="home-featured-habis-chip">Habis</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
<script>
(() => {
    const landing = document.querySelector('.sobat-landing-soft');
    if (!landing) return;

    landing.addEventListener('click', (event) => {
        const card = event.target.closest('[data-product-link]');
        if (!card) return;

        // Tombol keranjang dan link detail tetap menjalankan fungsi masing-masing.
        if (event.target.closest('button, a, input, select, textarea, form')) return;

        const url = card.dataset.productLink;
        if (url) window.location.href = url;
    });
})();
</script>

<section class="kids-program-reward-section">
    <div class="kids-program-zigzag kids-program-zigzag-top" aria-hidden="true"></div>
    <span class="kids-program-deco kids-program-sun" aria-hidden="true">☼</span>
    <span class="kids-program-deco kids-program-ball" aria-hidden="true">⚽</span>
    <span class="kids-program-deco kids-program-plane" aria-hidden="true">✈</span>
    <span class="kids-program-deco kids-program-umbrella" aria-hidden="true">☂</span>

    <div class="max-w-7xl mx-auto px-6 md:px-12 relative">
        <div class="kids-program-heading">
            <span class="kids-program-kicker">POINTS & REWARDS</span>
            <h2 class="kids-program-title font-display">Main Seru & Tukarkan <span>Poin</span></h2>
            <p>Kumpulkan poin dari mini game SobatAnak, lalu tukarkan dengan voucher dan hadiah lucu untuk si kecil.</p>
        </div>

        <div class="kids-program-grid">
            @foreach($rewards as $index => $r)
                <div class="kids-program-card kids-program-card-{{ ($index % 3) + 1 }}">
                    <div class="kids-program-number">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</div>
                    <div>
                        <h3>{{ $r->name }}</h3>
                        <p>{{ $r->description }}</p>
                        <a href="{{ \Illuminate\Support\Facades\Route::has('rewards') ? route('rewards') : url('/rewards') }}">Lihat Voucher →</a>
                    </div>
                    <strong>{{ $r->points }} pts</strong>
                </div>
            @endforeach
        </div>

        <div class="kids-program-action">
            <a class="btn-pill btn-coral" href="{{ route('mini-games') }}">Main Mini Game 🎮</a>
        </div>
    </div>
    <div class="kids-program-zigzag kids-program-zigzag-bottom" aria-hidden="true"></div>
</section>

<style>
.kids-program-reward-section{position:relative;overflow:hidden;padding:5.5rem 0 5.25rem;background:linear-gradient(135deg,#fffdf7 0%,#f7fffb 48%,#fff4ec 100%);border-top:1px solid rgba(75,191,176,.18);border-bottom:1px solid rgba(75,191,176,.18)}
.kids-program-zigzag{position:absolute;left:0;right:0;height:28px;opacity:.95;background-size:36px 28px;background-repeat:repeat-x;background-image:linear-gradient(135deg,transparent 25%,#fff 25%,#fff 50%,transparent 50%),linear-gradient(225deg,transparent 25%,#fff 25%,#fff 50%,transparent 50%)}
.kids-program-zigzag-top{top:0;transform:rotate(180deg)}
.kids-program-zigzag-bottom{bottom:0}
.kids-program-deco{position:absolute;z-index:0;font-weight:900;line-height:1;opacity:.72;filter:drop-shadow(0 8px 16px rgba(42,61,60,.08))}
.kids-program-sun{left:3.5rem;top:3.5rem;color:#ff8e9a;font-size:2.4rem}
.kids-program-ball{left:2.5rem;bottom:3.2rem;color:#f5c95d;font-size:2rem}
.kids-program-plane{right:4.2rem;bottom:4rem;color:#f3a986;font-size:2rem;transform:rotate(-12deg)}
.kids-program-umbrella{right:5rem;top:3.8rem;color:#86c9f4;font-size:2rem}
.kids-program-heading{text-align:center;max-width:760px;margin:0 auto 2.6rem;position:relative;z-index:1}
.kids-program-kicker{display:block;color:#ff8a7d;font-weight:1000;letter-spacing:.22em;font-size:.72rem;margin-bottom:.8rem}
.kids-program-title{font-size:clamp(2.3rem,5vw,4.2rem);line-height:1.05;color:#2a3d3c;font-weight:900;letter-spacing:.01em}
.kids-program-title span{color:#4fc8ba}
.kids-program-heading p{margin-top:1rem;color:#6f8886;font-weight:800;font-size:1.05rem}
.kids-program-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:1.6rem;position:relative;z-index:1}
.kids-program-card{position:relative;display:grid;grid-template-columns:auto 1fr;gap:1rem;min-height:185px;padding:1.55rem;border-radius:1.25rem;border:2px dashed rgba(42,61,60,.12);box-shadow:0 18px 44px rgba(42,61,60,.07);transition:.22s ease;overflow:hidden}
.kids-program-card:hover{transform:translateY(-4px);box-shadow:0 24px 58px rgba(42,61,60,.11)}
.kids-program-card-1{background:#fff0e7;border-color:rgba(255,143,120,.38);transform:rotate(-1deg)}
.kids-program-card-2{background:#eaf6ff;border-color:rgba(98,174,226,.34);transform:rotate(.8deg)}
.kids-program-card-3{background:#fff9e9;border-color:rgba(238,193,78,.38);transform:rotate(-.8deg)}
.kids-program-number{width:2.35rem;height:2.35rem;border-radius:999px;display:grid;place-items:center;background:#ff9f7d;color:#fff;font-weight:1000;box-shadow:0 8px 18px rgba(255,159,125,.23)}
.kids-program-card h3{font-family:'Fredoka',system-ui,sans-serif;color:#2a3d3c;font-weight:900;font-size:1.15rem;margin:.12rem 0 .35rem}
.kids-program-card p{color:#6f8886;font-weight:700;line-height:1.55;font-size:.95rem;max-width:15rem}
.kids-program-card a{display:inline-flex;margin-top:1rem;color:#2a3d3c;font-weight:1000;font-size:.82rem;letter-spacing:.08em;text-transform:uppercase;border-bottom:1px solid rgba(42,61,60,.35)}
.kids-program-card strong{position:absolute;right:1.25rem;bottom:1.25rem;color:#ee7068;font-size:1.2rem;font-weight:1000}
.kids-program-action{display:flex;justify-content:center;margin-top:2.4rem;position:relative;z-index:1}
@media(max-width:900px){.kids-program-grid{grid-template-columns:1fr}.kids-program-card{transform:none!important}.kids-program-deco{display:none}.kids-program-reward-section{padding:4.5rem 0}}
</style>
<section class="py-16 testimonial-web-section">
    <div class="testimonial-zigzag" aria-hidden="true"></div>
    <div class="testimonial-deco testimonial-deco-star">✦</div>
    <div class="testimonial-deco testimonial-deco-dot">●</div>
    <div class="max-w-7xl mx-auto px-6 md:px-12 testimonial-inner-wrap">
        <div class="testimonial-home-head simple-review-head">
            <div>
                <span class="simple-eyebrow">Ulasan Website</span>
                <h2 class="section-title font-display mt-2">Apa Kata <span class="text-teal">Mereka</span></h2>
            </div>
            <div class="testimonial-home-actions simple-review-actions">
                @if($authUser)
                    <button type="button" class="btn-pill btn-coral simple-write-btn" onclick="document.getElementById('testimonial-form-box')?.classList.toggle('hidden')">+ Tulis Ulasan</button>
                @else
                    <a href="{{ route('login') }}" class="btn-pill btn-coral simple-write-btn">Login untuk Ulasan</a>
                @endif
                <a href="{{ route('testimonials.index', ['sort' => 'liked']) }}" class="btn-pill simple-all-btn">Lihat Semua Ulasan</a>
            </div>
        </div>

        @if($errors->any())
            <div class="testimonial-alert mb-6 p-4 rounded-xl font-bold" style="background:#FDECEA; color:#D84315;">
                <ul style="list-style-type: disc; margin-left: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if($authUser)
            <div id="testimonial-form-box" class="testimonial-form-box hidden mb-8">
                <form method="POST" action="{{ route('testimonials.store.user') }}" class="grid gap-4">
                    @csrf
                    <div>
                        <label class="text-sm font-black text-[#2A3D3C]">Rating</label>
                        <select name="rating" class="auth-input mt-2">
                            <option value="5">★★★★★ - Sangat suka</option>
                            <option value="4">★★★★☆ - Bagus</option>
                            <option value="3">★★★☆☆ - Cukup</option>
                            <option value="2">★★☆☆☆ - Kurang</option>
                            <option value="1">★☆☆☆☆ - Perlu diperbaiki</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-black text-[#2A3D3C]">Komentar kamu</label>
                        <textarea name="message" class="auth-input min-h-[120px] mt-2" placeholder="Tulis pengalaman kamu memakai SobatAnak..." maxlength="400" required></textarea>
                    </div>
                    <button class="btn-pill btn-coral w-fit">Kirim Ulasan</button>
                </form>
            </div>
        @endif

        <div class="simple-testimonial-grid">
            @forelse($testimonials as $t)
                @php
                    $displayLikes = max((int)($t->likes_count ?? 0), (int)($t->real_likes_count ?? 0));
                    $isOwner = $authUser && (int)($t->user_id ?? 0) === (int)$authUser->id;
                    $isEdited = (bool)($t->is_edited ?? false);
                    $avatarPath = $t->user->avatar ?? null;
                    $avatarUrl = $avatarPath ? ((str_starts_with($avatarPath, 'http://') || str_starts_with($avatarPath, 'https://') || str_starts_with($avatarPath, '/')) ? $avatarPath : asset($avatarPath)) : null;
                    $isAdminReview = strtolower((string)($t->user->role ?? 'user')) === 'admin';
                @endphp
                <article class="simple-testimonial-card">
                    <div class="simple-review-top">
                        <p class="simple-stars">{{ str_repeat('★', (int)($t->rating ?? 5)) }}{{ str_repeat('☆', 5 - (int)($t->rating ?? 5)) }}</p>
                        <button type="button" class="testimonial-like-btn simple-like-btn {{ in_array($t->id, $likedTestimonialIds ?? []) ? 'liked' : '' }}" data-testimonial-like="{{ $t->id }}" aria-label="Like komentar">♥ <span>{{ $displayLikes }}</span></button>
                    </div>
                    <p class="simple-review-message">“{{ $t->message }}”</p>
                    @if($isEdited)
                        <span class="testimonial-edited-badge">Edited</span>
                    @endif
                    @if($isOwner)
                        <div class="testimonial-owner-actions">
                            <button type="button" class="testimonial-mini-action" data-toggle-edit="home-testimonial-edit-{{ $t->id }}">Edit</button>
                            <form method="POST" action="{{ route('testimonials.destroy.user', $t) }}" data-cute-confirm="Hapus ulasan website ini?" data-cute-detail="Ulasan kamu akan dihapus permanen dari SobatAnak.">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="testimonial-mini-action danger">Hapus</button>
                            </form>
                        </div>
                        <form id="home-testimonial-edit-{{ $t->id }}" method="POST" action="{{ route('testimonials.update', $t) }}" class="testimonial-inline-edit hidden">
                            @csrf
                            @method('PATCH')
                            <select name="rating" class="auth-input">
                                @for($rate = 5; $rate >= 1; $rate--)
                                    <option value="{{ $rate }}" @selected((int)($t->rating ?? 5) === $rate)>{{ str_repeat('★', $rate) }}{{ str_repeat('☆', 5 - $rate) }}</option>
                                @endfor
                            </select>
                            <textarea name="message" class="auth-input" maxlength="400" required>{{ $t->message }}</textarea>
                            <div class="flex gap-2 flex-wrap">
                                <button class="testimonial-save-btn">Simpan</button>
                                <button type="button" class="testimonial-cancel-btn" data-toggle-edit="home-testimonial-edit-{{ $t->id }}">Batal</button>
                            </div>
                        </form>
                    @endif
                    <div class="simple-review-footer">
                        <div class="reviewer-inline">
                            @if($avatarUrl)
                                <img src="{{ $avatarUrl }}" alt="{{ $t->name }}" class="review-avatar-sm">
                            @else
                                <span class="review-avatar-sm review-avatar-fallback">{{ strtoupper(mb_substr($t->name ?? 'U', 0, 1)) }}</span>
                            @endif
                            <span>
                                <b>{{ $t->name }}</b>
                                @if($isAdminReview)
                                    <em class="admin-review-badge">Admin SobatAnak</em>
                                @endif
                            </span>
                        </div>
                        <span>{{ optional($t->created_at)->format('d M Y') }}</span>
                    </div>
                </article>
            @empty
                <div class="md:col-span-3 card p-8 text-center"><h3 class="font-display text-2xl">Belum ada ulasan</h3><p class="text-[#6B8A88] mt-2">Jadilah user pertama yang memberi komentar tentang SobatAnak.</p></div>
            @endforelse
        </div>
    </div>
</section>
<section class="top-deals-article-section">
    <div class="top-deals-wave" aria-hidden="true"></div>
    <div class="max-w-7xl mx-auto px-6 md:px-12">
        <div class="top-deals-heading">
            <span>Paling sering dibaca</span>
            <h2>This week’s top reads</h2>
            <i></i>
        </div>

        <div class="top-deals-grid">
            @foreach($articles as $index => $a)
                @php
                    $cleanExcerpt = trim(preg_replace([
                        '/\[caption\].*?\[\/caption\]/is',
                        '/Baca juga:\s*.*$/im',
                    ], '', (string) ($a->excerpt ?? '')));

                    if ($cleanExcerpt === '') {
                        $plainContent = strip_tags(preg_replace('/\[caption\].*?\[\/caption\]/is', '', (string) ($a->content ?? '')));
                        $cleanExcerpt = \Illuminate\Support\Str::limit($plainContent, 85);
                    } else {
                        $cleanExcerpt = \Illuminate\Support\Str::limit($cleanExcerpt, 85);
                    }

                    $articleLabel = $index === 0 ? 'Top Read' : ($index === 1 ? 'Popular' : 'Trending');
                @endphp

                <a href="{{ route('article.show', $a->slug ?: $a->id) }}"
                   class="top-deals-card top-deals-card-{{ ($index % 3) + 1 }}"
                   aria-label="Baca artikel {{ $a->title }}">
                    <img src="{{ $a->image }}" alt="{{ $a->title }}" onerror="this.onerror=null;this.src='{{ asset('images/article-placeholder.jpg') }}';">
                    <div class="top-deals-overlay"></div>

                    <span class="top-deals-badge">{{ $articleLabel }}</span>

                    <div class="top-deals-content">
                        <p>{{ $a->category_name }}</p>
                        <h3>{{ $a->title }}</h3>
                        <small>{{ $cleanExcerpt }}</small>
                        <div class="top-deals-bottom">
                            <b>Read More</b>
                            <em>👁 {{ number_format((int)($a->counter ?? 0), 0, ',', '.') }} dibaca</em>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="top-deals-more">
            <a href="{{ route('articles') }}">Semua Artikel →</a>
        </div>
    </div>
</section>

<div class="info-modal" data-product-modal aria-hidden="true"><div class="info-modal-backdrop" data-modal-close></div><div class="info-modal-panel product-detail-panel" role="dialog" aria-modal="true"><button class="modal-close" data-modal-close aria-label="Tutup">×</button><div class="grid lg:grid-cols-[1fr_1.05fr] gap-6 items-stretch"><div class="modal-image-wrap"><img data-modal-product-image src="" alt="Detail produk"></div><div class="modal-content"><span class="modal-kicker" data-modal-product-category></span><h2 class="font-display modal-title" data-modal-product-name></h2><div class="flex flex-wrap gap-2 my-4"><span class="modal-chip" data-modal-product-rating></span><span class="modal-chip" data-modal-product-sold></span><span class="modal-chip" data-modal-product-badge></span></div><h3 class="modal-price" data-modal-product-price></h3><p class="modal-desc" data-modal-product-desc></p><div class="modal-benefits"><div><b>✨ Kelebihan</b><p>Aman, nyaman, dan cocok untuk kebutuhan harian bayi serta anak.</p></div><div><b>🧸 Cocok Untuk</b><p>Orang tua yang ingin produk praktis, berkualitas, dan mudah digunakan.</p></div><div><b>🛍️ Catatan</b><p>Tambahkan ke cart untuk lanjut ke halaman checkout khusus akun kamu.</p></div></div><div class="flex flex-wrap gap-3 mt-6"><button data-modal-product-buy class="btn-pill btn-coral">+ Masukkan Cart</button><a href="{{ route('cart.index') }}" class="btn-pill btn-teal">Lihat Cart</a></div></div></div></div></div>
<div class="info-modal" data-article-modal aria-hidden="true"><div class="info-modal-backdrop" data-modal-close></div><div class="info-modal-panel article-detail-panel" role="dialog" aria-modal="true"><button class="modal-close" data-modal-close aria-label="Tutup">×</button><div class="modal-article-hero"><img data-modal-article-image src="" alt="Detail artikel"></div><div class="modal-content article-modal-content"><span class="modal-kicker" data-modal-article-category></span><h2 class="font-display modal-title" data-modal-article-title></h2><p class="modal-meta">📅 <span data-modal-article-date></span> · ⏱️ 3 menit baca · SobatAnak Editorial</p><p class="modal-desc" data-modal-article-excerpt></p><div class="article-pop-grid"><div><b>Ringkasan</b><p data-modal-article-summary></p></div><div><b>Yang Dibahas</b><ul><li>Tips praktis untuk orang tua.</li><li>Hal penting yang perlu diperhatikan.</li><li>Rekomendasi kebiasaan baik di rumah.</li></ul></div><div><b>Catatan SobatAnak</b><p>Gunakan artikel ini sebagai panduan awal. Sesuaikan kembali dengan kebutuhan si kecil dan kondisi keluarga.</p></div></div></div></div></div>

<style>
.testimonial-web-section{position:relative;overflow:hidden;background:linear-gradient(135deg,#FFF9EF 0%,#FFF4EC 42%,#F0FFFC 100%);border-top:1px solid #FFE0D7;border-bottom:1px solid #D4EEEC;padding-top:5rem;padding-bottom:5rem}.testimonial-web-section:before{content:"";position:absolute;inset:0;background:radial-gradient(circle at 12% 18%,rgba(255,184,117,.24),transparent 26%),radial-gradient(circle at 84% 16%,rgba(75,191,176,.16),transparent 24%),radial-gradient(circle at 52% 88%,rgba(255,217,120,.17),transparent 28%);pointer-events:none}.testimonial-web-section:after{content:"";position:absolute;right:-90px;top:70px;width:330px;height:330px;border-radius:50%;background:rgba(255,210,193,.32);filter:blur(.2px);pointer-events:none}.testimonial-zigzag{position:absolute;left:0;right:0;top:0;height:24px;background:linear-gradient(135deg,#fff 25%,transparent 25%) 0 0/34px 34px,linear-gradient(225deg,#fff 25%,transparent 25%) 0 0/34px 34px;opacity:.9}.testimonial-deco{position:absolute;z-index:1;color:#FFB875;font-weight:1000;pointer-events:none}.testimonial-deco-star{left:10%;top:4.8rem;font-size:20px}.testimonial-deco-dot{right:14%;bottom:4.5rem;font-size:12px;color:#4BBFB0}.testimonial-inner-wrap{position:relative;z-index:2}.testimonial-home-head{display:grid;grid-template-columns:minmax(0,1fr) auto;gap:2rem;align-items:end;margin-bottom:1.6rem}.simple-eyebrow{display:inline-flex;color:#F38A72;font-weight:1000;text-transform:uppercase;letter-spacing:.2em;font-size:12px}.simple-review-head .section-title{color:#263D3B}.simple-review-actions{display:flex;flex-direction:column;gap:.8rem;min-width:245px}.simple-write-btn{justify-content:center;box-shadow:0 18px 36px rgba(243,138,114,.22);background:linear-gradient(135deg,#FF8B78,#FFB167);color:#fff}.simple-all-btn{justify-content:center;background:#fffdf8;border:2px solid #4BBFB0;color:#2E8F84}.simple-all-btn:hover{background:#EFFFFB;transform:translateY(-2px)}.simple-testimonial-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:1.35rem;margin-top:1.15rem}.simple-testimonial-card{position:relative;background:rgba(255,255,255,.76);border:1.5px solid rgba(255,190,176,.62);border-radius:34px;padding:1.8rem 1.95rem;min-height:220px;box-shadow:0 22px 55px rgba(136,95,72,.08);transition:.22s ease;display:flex;flex-direction:column;justify-content:space-between;backdrop-filter:blur(6px)}.simple-testimonial-card:before{content:"";position:absolute;inset:12px;border:1px dashed rgba(75,191,176,.20);border-radius:26px;pointer-events:none}.simple-testimonial-card:nth-child(2n){background:rgba(255,250,238,.84);border-color:rgba(255,170,152,.72)}.simple-testimonial-card:nth-child(3n){background:rgba(240,255,252,.78);border-color:rgba(190,236,230,.95)}.simple-testimonial-card:hover{transform:translateY(-4px);border-color:#FFB8A8;box-shadow:0 28px 70px rgba(136,95,72,.13)}.simple-review-top{display:flex;align-items:center;justify-content:space-between;gap:1rem}.simple-stars{color:#FFB23E;letter-spacing:.07em;font-weight:1000;font-size:1.08rem}.simple-like-btn{display:inline-flex;align-items:center;gap:.35rem;border:1.5px solid #FFD2CC;background:#FFF4F2;color:#E8756A;border-radius:999px;padding:.48rem .78rem;font-weight:1000;transition:.2s;line-height:1}.simple-like-btn:hover{border-color:#E8756A;background:#fff;transform:translateY(-1px)}.simple-like-btn.liked{background:#FDECEA;border-color:rgba(232,117,106,.55);color:#E8756A}.testimonial-like-btn:disabled{opacity:.65;cursor:not-allowed;transform:none}.simple-review-message{color:#637E7B;font-weight:850;line-height:1.8;font-size:1.05rem;margin:1.35rem 0;min-height:54px}.simple-review-footer{display:flex;align-items:center;justify-content:space-between;gap:1rem;margin-top:auto}.simple-review-footer b{font-family:var(--font-display,inherit);font-size:1.08rem;color:#1F3332}.simple-review-footer span{color:#6B8A88;font-weight:900;font-size:.9rem}.testimonial-form-box{background:rgba(255,255,255,.86);border:1px solid #FFE0D7;border-radius:1.5rem;padding:1.25rem;box-shadow:0 18px 42px rgba(136,95,72,.08)}.testimonial-edited-badge{display:inline-flex;width:max-content;border:1px solid #D4EEEC;background:#F0FFFC;color:#2E8F84;border-radius:999px;padding:.25rem .6rem;font-size:.72rem;font-weight:1000;text-transform:uppercase;letter-spacing:.08em;margin-top:-.35rem;margin-bottom:.65rem}.testimonial-owner-actions{display:flex;align-items:center;gap:.45rem;flex-wrap:wrap;margin:.15rem 0 .9rem}.testimonial-owner-actions form{display:inline}.testimonial-mini-action{border:1.5px solid #D4EEEC;background:#fff;color:#2E8F84;border-radius:999px;padding:.42rem .75rem;font-weight:1000;font-size:.78rem;transition:.18s}.testimonial-mini-action:hover{background:#D0F0ED;transform:translateY(-1px)}.testimonial-mini-action.danger{color:#E8756A;border-color:#FFD2CC;background:#FFF7F5}.testimonial-mini-action.danger:hover{background:#FDECEA}.testimonial-inline-edit{display:grid;gap:.65rem;background:#FFFDF8;border:1px dashed #FFD2CC;border-radius:18px;padding:.85rem;margin:.25rem 0 .9rem}.testimonial-inline-edit.hidden{display:none}.testimonial-inline-edit textarea{min-height:86px;resize:vertical}.testimonial-save-btn,.testimonial-cancel-btn{border:0;border-radius:999px;padding:.55rem .95rem;font-weight:1000}.testimonial-save-btn{background:#4BBFB0;color:white}.testimonial-cancel-btn{background:#FDECEA;color:#E8756A}
.reviewer-inline{display:flex;align-items:center;gap:.7rem;min-width:0}.reviewer-inline>span{display:grid;gap:.15rem}.review-avatar-sm{width:46px;height:46px;border-radius:16px;object-fit:cover;display:inline-flex;align-items:center;justify-content:center;box-shadow:0 10px 20px rgba(42,61,60,.10);border:3px solid #FFF7E6;flex:0 0 46px;transform:rotate(-2deg)}.review-avatar-fallback{background:linear-gradient(135deg,#4BBFB0,#E8756A);color:#fff;font-weight:1000}.admin-review-badge{display:inline-flex;width:max-content;background:#FFF5D9;border:1px solid #F5A400;color:#9B6400;border-radius:999px;padding:.18rem .55rem;font-size:.68rem;font-weight:1000;text-transform:uppercase;letter-spacing:.08em;font-style:normal}
@media(max-width:1024px){.testimonial-home-head{grid-template-columns:1fr}.simple-review-actions{flex-direction:row;flex-wrap:wrap;min-width:0}.simple-write-btn,.simple-all-btn{width:fit-content}.simple-testimonial-grid{grid-template-columns:1fr 1fr}}@media(max-width:640px){.testimonial-web-section{padding-top:4rem;padding-bottom:4rem}.simple-review-actions{flex-direction:column;align-items:stretch}.simple-write-btn,.simple-all-btn{width:100%}.simple-testimonial-grid{grid-template-columns:1fr}.simple-testimonial-card{padding:1.35rem}}
</style>
<script>

document.addEventListener('click', (event) => {
    const toggle = event.target.closest('[data-toggle-edit]');
    if (!toggle) return;
    event.preventDefault();
    const target = document.getElementById(toggle.dataset.toggleEdit);
    if (target) target.classList.toggle('hidden');
});

document.addEventListener('click', async (event) => {
    const btn = event.target.closest('[data-testimonial-like]');
    if (!btn) return;
    event.preventDefault();
    btn.disabled = true;
    const id = btn.dataset.testimonialLike;
    try {
        const response = await fetch(`/testimonials/${id}/like`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });
        const data = await response.json();
        if (!data.ok) {
            if (data.redirect) window.location.href = data.redirect;
            else alert(data.message || 'Gagal like komentar.');
            return;
        }
        btn.classList.toggle('liked', !!data.liked);
        const count = btn.querySelector('span');
        if (count) count.textContent = data.likes_count;
    } catch (error) {
        alert('Gagal like komentar. Coba lagi.');
    } finally {
        btn.disabled = false;
    }
});
</script>


<script>
(() => {
    const root = document.querySelector('.sobat-landing-soft');
    if (!root) return;

    const selectors = [
        '.hero-copy',
        '.hero-media-wrap',
        '.hero-ai-inline',
        'section > .max-w-7xl',
        '.grid > .card',
        '.simple-testimonial-card',
        '.testimonial-home-head',
        'section.bg-white a.card'
    ];

    const items = [];
    selectors.forEach(selector => {
        root.querySelectorAll(selector).forEach(el => {
            if (el.closest('.info-modal')) return;
            if (items.includes(el)) return;
            items.push(el);
        });
    });

    items.forEach((el, index) => {
        el.classList.add('sobat-scroll-reveal');

        if (el.classList.contains('hero-copy')) el.classList.add('sobat-reveal-soft-left');
        if (el.classList.contains('hero-media-wrap')) el.classList.add('sobat-reveal-soft-right');

        const parent = el.parentElement;
        const siblingItems = parent
            ? Array.from(parent.children).filter(child => items.includes(child))
            : [];
        const siblingIndex = Math.max(0, siblingItems.indexOf(el));
        const delay = Math.min(siblingIndex * 65, 195);
        el.style.setProperty('--sobat-reveal-delay', delay + 'ms');
    });

    const show = el => el.classList.add('sobat-scroll-visible');

    if (!('IntersectionObserver' in window)) {
        items.forEach(show);
        return;
    }

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (!entry.isIntersecting) return;
            show(entry.target);
            observer.unobserve(entry.target);
        });
    }, {
        threshold: 0.12,
        rootMargin: '0px 0px -5% 0px'
    });

    requestAnimationFrame(() => {
        items.forEach(el => observer.observe(el));
    });
})();
</script>

</div>
@endsection