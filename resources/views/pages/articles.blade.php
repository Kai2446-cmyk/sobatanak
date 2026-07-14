@extends('layouts.app')
@section('title','Artikel — SobatAnak')
@section('content')
@php
    $articles = collect($articles ?? []);

    $articleCategories = collect($categories ?? [])
        ->map(function ($cat) {
            return (object) [
                'name' => $cat->name ?? $cat->category_name ?? $cat->title ?? 'Artikel',
                'slug' => $cat->slug ?? \Illuminate\Support\Str::slug($cat->name ?? $cat->category_name ?? $cat->title ?? 'artikel'),
            ];
        })
        ->filter(fn ($cat) => filled($cat->name))
        ->values();

    if ($articleCategories->isEmpty()) {
        $articleCategories = $articles
            ->map(function ($article) {
                $name = $article->category->name
                    ?? $article->category_name
                    ?? $article->category
                    ?? 'Artikel';

                return (object) [
                    'name' => $name,
                    'slug' => \Illuminate\Support\Str::slug($name),
                ];
            })
            ->filter(fn ($cat) => filled($cat->name))
            ->unique('slug')
            ->values();
    }

    $popularCategorySlugs = $articles
        ->groupBy(function ($article) {
            $name = $article->category->name
                ?? $article->category_name
                ?? $article->category
                ?? 'Artikel';

            return \Illuminate\Support\Str::slug($name);
        })
        ->map(fn ($items) => $items->sum(fn ($item) => (int) ($item->counter ?? $item->views ?? 0)) + ($items->count() * 10))
        ->sortDesc()
        ->keys()
        ->take(3)
        ->values();

    if ($popularCategorySlugs->isEmpty()) {
        $popularCategorySlugs = $articleCategories->pluck('slug')->take(3)->values();
    }
@endphp

<section class="article-hero-showcase">
    <div class="article-hero-bg article-hero-bg-one"></div>
    <div class="article-hero-bg article-hero-bg-two"></div>
    <div class="article-hero-bg article-hero-bg-three"></div>

    <div class="article-hero-inner max-w-7xl mx-auto px-6 md:px-12">
        <div class="article-hero-copy">
            <span class="article-hero-eyebrow">Artikel SobatAnak</span>
            <h1 class="font-display article-hero-title">Tips Parenting & <span>Mom Care</span></h1>
            <p class="article-hero-desc">Temukan bacaan ringan seputar perawatan bayi, tumbuh kembang anak, nutrisi, dan kebutuhan keluarga dengan bahasa yang hangat untuk Bunda dan Ayah.</p>

            <div class="article-hero-actions">
                <a href="#artikel-tersedia" class="article-hero-btn primary">Jelajahi Artikel</a>
                <a href="#artikel-tersedia" class="article-hero-btn secondary">Lihat Bacaan Terbaru</a>
            </div>

            <div class="article-hero-stats">
                <span>🌿 Parenting care</span>
                <span>🍼 Baby tips</span>
            </div>
        </div>

        <div class="article-hero-visual" aria-hidden="true">
            <div class="hero-orbit hero-orbit-a"></div>
            <div class="hero-orbit hero-orbit-b"></div>

            <div class="hero-book-card">
                <div class="book-cover">
                    <span class="book-icon">📖</span>
                </div>
                <div class="book-info">
                    <small>Topik hari ini</small>
                    <strong>Rutinitas sehat si kecil</strong>
                    <p>Bacaan singkat untuk menemani keputusan orang tua.</p>
                </div>
            </div>

            <div class="floating-note note-one">
                <span>💡</span>
                <b>Tips praktis</b>
            </div>
            <div class="floating-note note-two">
                <span>🧸</span>
                <b>Tumbuh kembang</b>
            </div>
            <div class="floating-note note-three">
                <span>❤️</span>
                <b>Mom care</b>
            </div>
        </div>
    </div>
</section>

<section id="kategori-artikel" class="article-tools-sam">
    <div class="max-w-7xl mx-auto px-6 md:px-12">
        <label class="article-search-box">
            <span>🔎</span>
            <input data-article-search placeholder="Cari artikel parenting, MPASI, perawatan..." autocomplete="off">
        </label>

        <div class="article-cat-row">
            <button type="button" data-filter-article-cat="Semua" class="article-cat active">Semua</button>
            @foreach($articleCategories as $cat)
                <button type="button" data-filter-article-cat="{{ $cat->slug }}" class="article-cat">{{ $cat->name }}</button>
            @endforeach
        </div></div>
</section>

<section id="artikel-tersedia" class="article-collection-wrap max-w-7xl mx-auto px-6 md:px-12 py-12">
    <div class="article-section-head">
        <div>
            <p class="article-eyebrow">Kumpulan Artikel</p>
            <p class="article-section-desc article-section-desc-clean">Baca tips ringan seputar parenting, perawatan bayi, tumbuh kembang anak, dan kebutuhan keluarga.</p>
        </div>
        <div class="article-count-pill">
            <span data-article-count>{{ $articles->count() }}</span>
            <small>artikel</small>
        </div>
    </div>

    <div class="article-grid-sam">
        @foreach($articles as $a)
            @php
                $catName = $a->category->name
                    ?? $a->category_name
                    ?? $a->category
                    ?? 'Artikel';

                $catSlug = $a->category->slug
                    ?? \Illuminate\Support\Str::slug($catName);

                $articleSlug = $a->slug ?? $a->id;

                $rawImage = $a->image ?? null;
                if ($rawImage && \Illuminate\Support\Str::startsWith($rawImage, ['http://', 'https://', '/'])) {
                    $imageUrl = $rawImage;
                } elseif ($rawImage) {
                    $imageUrl = asset($rawImage);
                } else {
                    $imageUrl = asset('images/article-placeholder.jpg');
                }

                $plainContent = trim(strip_tags((string) ($a->content ?? $a->body ?? $a->excerpt ?? '')));
                $wordCount = str_word_count($plainContent);
                $readTime = max(2, (int) ceil($wordCount / 180));

                $catKey = \Illuminate\Support\Str::of($catSlug)->lower()->toString();
                $categoryColorClass = 'cat-default';

                if (\Illuminate\Support\Str::contains($catKey, ['parenting', 'orang-tua', 'parent'])) {
                    $categoryColorClass = 'cat-parenting';
                } elseif (\Illuminate\Support\Str::contains($catKey, ['mom', 'ibu', 'moms'])) {
                    $categoryColorClass = 'cat-mom';
                } elseif (\Illuminate\Support\Str::contains($catKey, ['baby', 'bayi', 'care', 'perawatan'])) {
                    $categoryColorClass = 'cat-baby';
                } elseif (\Illuminate\Support\Str::contains($catKey, ['edukasi', 'pendidikan', 'mainan', 'tumbuh-kembang'])) {
                    $categoryColorClass = 'cat-edukasi';
                } elseif (\Illuminate\Support\Str::contains($catKey, ['nutrisi', 'mpasi', 'gizi', 'makanan'])) {
                    $categoryColorClass = 'cat-nutrisi';
                }

                $searchText = trim(
                    ($a->title ?? '') . ' ' .
                    ($catName ?? '') . ' ' .
                    ($a->excerpt ?? '') . ' ' .
                    $plainContent
                );
            @endphp
            <a href="{{ route('article.show', $articleSlug) }}"
               class="article-card-modern"
               data-article-card
               data-title="{{ e($a->title ?? '') }}"
               data-category="{{ $catSlug }}"
               data-category-name="{{ e($catName) }}"
               data-search="{{ e($searchText) }}">
                <div class="article-card-image">
                    <img src="{{ $imageUrl }}" alt="{{ $a->title ?? 'Artikel SobatAnak' }}" loading="lazy" onerror="this.onerror=null;this.src='{{ asset('images/article-placeholder.jpg') }}';">
                    <span class="article-card-tag {{ $categoryColorClass }}">{{ $catName }}</span>
                </div>

                <div class="article-card-body">
                    <h3>{{ $a->title }}</h3>
                    <p>{{ $a->excerpt }}</p>
                    <div class="article-card-meta">
                        <span>{{ $readTime }} menit baca</span>
                        <span>•</span>
                        <span>{{ (int) ($a->counter ?? 0) }} dilihat</span>
                    </div>
                </div>
            </a>
        @endforeach
    </div>

    <div id="article-empty" class="hidden text-center py-20">
        <div class="text-6xl mb-4">🔍</div>
        <h3 class="font-display text-2xl text-[#6B8A88]">Artikel tidak ditemukan</h3>
        <p class="text-[#6B8A88] mt-2">Coba kata kunci atau kategori lain.</p>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const search = document.querySelector('[data-article-search]');
    const catBtns = [...document.querySelectorAll('[data-filter-article-cat]')];
    const cards = [...document.querySelectorAll('[data-article-card]')];
    const countEl = document.querySelector('[data-article-count]');
    const emptyEl = document.getElementById('article-empty');

    let activeCat = 'Semua';

    function normalize(text) {
        return (text || '').toString().toLowerCase().trim();
    }

    function filterArticles() {
        const q = normalize(search ? search.value : '');
        let visible = 0;

        cards.forEach(card => {
            const cardCategory = card.dataset.category || '';
            const cardSearch = normalize(card.dataset.search);
            const matchCat = activeCat === 'Semua' || cardCategory === activeCat;
            const matchQ = !q || cardSearch.includes(q);
            const show = matchCat && matchQ;

            card.hidden = !show;
            card.style.display = show ? '' : 'none';
            if (show) visible++;
        });

        if (countEl) countEl.textContent = visible;
        if (emptyEl) emptyEl.classList.toggle('hidden', visible > 0);
    }

    catBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            catBtns.forEach(item => item.classList.remove('active'));
            btn.classList.add('active');
            activeCat = btn.dataset.filterArticleCat || 'Semua';
            filterArticles();
            document.getElementById('artikel-tersedia')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });

    if (search) search.addEventListener('input', filterArticles);
    filterArticles();
});
</script>

<style>
.article-hero-showcase{position:relative;overflow:hidden;min-height:320px;background:linear-gradient(125deg,#D0F0ED 0%,#F9FFFE 42%,#FDECEA 100%);border-bottom:1px solid #D4EEEC}.article-hero-showcase:after{content:"";position:absolute;left:-5%;right:-5%;bottom:-90px;height:180px;background:#fff;border-radius:50% 50% 0 0/100% 100% 0 0;z-index:1}.article-hero-bg{position:absolute;border-radius:999px;filter:blur(.2px);opacity:.8}.article-hero-bg-one{width:360px;height:360px;right:7%;top:65px;background:radial-gradient(circle,#8FE1D8 0%,#4BBFB0 72%);box-shadow:0 36px 80px rgba(75,191,176,.22)}.article-hero-bg-two{width:280px;height:280px;left:-80px;top:90px;background:rgba(240,111,103,.16)}.article-hero-bg-three{width:190px;height:190px;right:34%;bottom:38px;background:rgba(245,168,94,.18)}.article-hero-inner{position:relative;z-index:2;display:grid;grid-template-columns:minmax(0,1.04fr) minmax(360px,.86fr);align-items:center;gap:38px;padding-top:48px;padding-bottom:74px}.article-hero-copy{max-width:720px}.article-hero-eyebrow{display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,.55);border:1px solid rgba(75,191,176,.22);color:#F06F67;border-radius:999px;padding:9px 15px;text-transform:uppercase;letter-spacing:.16em;font-size:12px;font-weight:1000;box-shadow:0 16px 36px rgba(42,61,60,.06)}.article-hero-title{margin-top:18px;font-size:clamp(44px,5.4vw,78px);line-height:.95;color:#2A3D3C;letter-spacing:-.045em}.article-hero-title span{color:#4BBFB0}.article-hero-desc{margin-top:18px;max-width:620px;color:#5F7D7A;font-size:16px;line-height:1.7;font-weight:700}.article-hero-actions{display:flex;align-items:center;gap:14px;flex-wrap:wrap;margin-top:22px}.article-hero-btn{display:inline-flex;align-items:center;justify-content:center;min-height:52px;border-radius:18px;padding:0 22px;font-weight:1000;transition:.22s ease;text-decoration:none}.article-hero-btn.primary{background:#F9D521;color:#2A3D3C;box-shadow:0 8px 0 #D1A912,0 22px 42px rgba(249,213,33,.22)}.article-hero-btn.primary:hover{transform:translateY(-3px);box-shadow:0 11px 0 #D1A912,0 28px 56px rgba(249,213,33,.28)}.article-hero-btn.secondary{background:rgba(255,255,255,.62);border:1px solid rgba(75,191,176,.25);color:#4B7774;box-shadow:0 18px 42px rgba(42,61,60,.06)}.article-hero-btn.secondary:hover{background:#fff;transform:translateY(-3px)}.article-hero-stats{display:flex;gap:10px;flex-wrap:wrap;margin-top:20px}.article-hero-stats span{display:inline-flex;align-items:center;min-height:38px;border-radius:999px;background:rgba(255,255,255,.52);border:1px solid rgba(255,255,255,.66);padding:0 14px;color:#6B8A88;font-size:13px;font-weight:1000;box-shadow:0 14px 32px rgba(42,61,60,.06)}.article-hero-visual{position:relative;min-height:320px}.hero-orbit{position:absolute;border-radius:999px}.hero-orbit-a{width:210px;height:210px;right:84px;top:8px;background:#53C8D7;box-shadow:inset -18px -28px 0 rgba(0,0,0,.06),0 34px 80px rgba(42,61,60,.18)}.hero-orbit-a:before,.hero-orbit-a:after{content:"";position:absolute;top:-22px;width:56px;height:56px;border-radius:999px;background:#118EAD;border:14px solid rgba(255,255,255,.2)}.hero-orbit-a:before{left:36px}.hero-orbit-a:after{right:36px}.hero-orbit-b{width:120px;height:92px;right:128px;top:175px;background:linear-gradient(180deg,#FF9B66,#F06F67);box-shadow:0 26px 48px rgba(240,111,103,.26)}.hero-book-card{position:absolute;left:0;bottom:10px;width:min(470px,100%);min-height:185px;background:rgba(255,255,255,.9);border:1px solid rgba(212,238,236,.78);border-radius:32px;box-shadow:0 36px 90px rgba(42,61,60,.14);display:grid;grid-template-columns:160px 1fr;gap:20px;padding:20px;backdrop-filter:blur(16px)}.book-cover{position:relative;border-radius:28px;background:linear-gradient(145deg,#68D8E4,#4BBFB0);min-height:145px;display:flex;align-items:center;justify-content:center;box-shadow:inset -18px -20px 0 rgba(42,61,60,.06);transform:rotate(-4deg)}.book-icon{font-size:52px;filter:drop-shadow(0 12px 18px rgba(42,61,60,.18))}.book-lines{position:absolute;left:22px;right:22px;bottom:22px;display:grid;gap:8px}.book-lines i{display:block;height:8px;border-radius:999px;background:rgba(255,255,255,.55)}.book-info{align-self:center}.book-info small{display:inline-flex;border-radius:999px;background:#FFF7CF;color:#D39600;padding:7px 12px;text-transform:uppercase;font-size:11px;font-weight:1000;letter-spacing:.08em}.book-info strong{display:block;margin-top:13px;color:#2A3D3C;font-size:24px;line-height:1.1;font-weight:1000}.book-info p{margin-top:12px;color:#6B8A88;font-weight:700;line-height:1.6}.floating-note{position:absolute;display:flex;align-items:center;gap:10px;background:rgba(255,255,255,.7);border:1px solid rgba(255,255,255,.72);border-radius:999px;padding:12px 16px;color:#2A3D3C;font-weight:1000;box-shadow:0 20px 46px rgba(42,61,60,.12);backdrop-filter:blur(10px)}.floating-note span{width:38px;height:38px;border-radius:999px;display:inline-flex;align-items:center;justify-content:center;background:#F9D521}.note-one{right:0;top:86px}.note-two{left:74px;top:26px}.note-three{right:38px;bottom:52px}
@keyframes articleFloatSoft{0%,100%{transform:translateY(0)}50%{transform:translateY(-10px)}}@keyframes articlePulseSoft{0%,100%{transform:scale(1);opacity:.82}50%{transform:scale(1.035);opacity:1}}@keyframes articleNoteDrift{0%,100%{transform:translateY(0) rotate(0deg)}50%{transform:translateY(-8px) rotate(-1deg)}}.hero-orbit-a{animation:articlePulseSoft 5s ease-in-out infinite}.hero-orbit-b{animation:articleFloatSoft 4.5s ease-in-out infinite}.hero-book-card{animation:articleFloatSoft 6s ease-in-out infinite}.floating-note{animation:articleNoteDrift 4s ease-in-out infinite}.note-two{animation-delay:.7s}.note-three{animation-delay:1.2s}

.article-tools-sam{position:sticky;top:76px;z-index:40;background:rgba(255,255,255,.94);backdrop-filter:blur(16px);border-top:1px solid #D4EEEC;border-bottom:1px solid #D4EEEC;padding:14px 0}.article-search-box{display:flex;align-items:center;gap:12px;background:#F8FEFD;border:1px solid #D4EEEC;border-radius:999px;padding:0 18px;box-shadow:0 10px 34px rgba(75,191,176,.06)}.article-search-box input{width:100%;height:52px;background:transparent;outline:0;font-weight:900;color:#2A3D3C}.article-search-box input::placeholder{color:#9AA7B3}.article-cat-row{display:flex;gap:10px;flex-wrap:wrap;margin-top:12px}.article-cat{border:1px solid #D4EEEC;background:#fff;color:#6B8A88;border-radius:999px;padding:10px 16px;font-weight:1000;transition:.2s}.article-cat.active,.article-cat:hover{background:#4BBFB0;color:#fff;border-color:#4BBFB0;box-shadow:0 12px 24px rgba(75,191,176,.18)}
.article-collection-wrap{background:linear-gradient(180deg,#F9FFFE 0%,#FFFFFF 100%)}.article-section-head{display:flex;justify-content:space-between;align-items:flex-end;gap:24px;margin-bottom:28px}.article-eyebrow{color:#F06F67;text-transform:uppercase;letter-spacing:.14em;font-size:12px;font-weight:1000;margin-bottom:8px}.article-section-desc-clean{margin-top:0!important}.article-section-head h2{font-size:clamp(34px,4vw,54px);line-height:1;color:#2A3D3C}.article-section-desc{margin-top:10px;max-width:760px;color:#5F7D7A;font-size:18px;line-height:1.7;font-weight:700}.article-count-pill{min-width:86px;height:86px;border-radius:28px;background:#EEFFFB;border:1px solid #BFECE6;color:#2A3D3C;display:flex;flex-direction:column;align-items:center;justify-content:center;box-shadow:0 18px 42px rgba(75,191,176,.12)}.article-count-pill span{font-size:24px;font-weight:1000;line-height:1}.article-count-pill small{font-weight:900;color:#6B8A88;margin-top:4px}.article-grid-sam{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:30px}.article-card-modern{position:relative;display:flex;flex-direction:column;min-height:320px;border-radius:28px;overflow:hidden;background:#fff;border:1px solid #D4EEEC;box-shadow:0 22px 54px rgba(42,61,60,.08);transition:transform .22s ease,box-shadow .22s ease}.article-card-modern:hover{transform:translateY(-5px);box-shadow:0 28px 70px rgba(42,61,60,.13)}.article-card-image{position:relative;height:245px;background:#F0FBF9;overflow:hidden}.article-card-image img{width:100%;height:100%;object-fit:cover;display:block}.article-card-tag{position:absolute;top:0;right:0;min-width:148px;text-align:center;color:#fff;text-transform:uppercase;font-size:12px;font-weight:1000;letter-spacing:.04em;padding:13px 18px;border-bottom-left-radius:28px;box-shadow:0 12px 28px rgba(42,61,60,.14)}.article-card-tag.cat-parenting{background:#4BBFB0;box-shadow:0 12px 28px rgba(75,191,176,.22)}.article-card-tag.cat-mom{background:#F06F67;box-shadow:0 12px 28px rgba(240,111,103,.22)}.article-card-tag.cat-baby{background:#70CFC8;box-shadow:0 12px 28px rgba(112,207,200,.22)}.article-card-tag.cat-edukasi{background:#F5A85E;box-shadow:0 12px 28px rgba(245,168,94,.22)}.article-card-tag.cat-nutrisi{background:#F9C84A;color:#2A3D3C;box-shadow:0 12px 28px rgba(249,200,74,.22)}.article-card-tag.cat-default{background:#6B8A88;box-shadow:0 12px 28px rgba(107,138,136,.18)}.article-card-body{flex:1;background:#fff;color:#2A3D3C;padding:22px 24px 24px;display:flex;flex-direction:column}.article-card-modern:nth-child(3n+2) .article-card-body,.article-card-modern:nth-child(3n+3) .article-card-body{background:#fff}.article-card-body h3{font-family:inherit;font-size:24px;line-height:1.25;font-weight:1000;color:#4BBFB0;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}.article-card-body p{margin-top:12px;color:#7B8F8D;line-height:1.65;font-weight:500;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}.article-card-meta{margin-top:auto;padding-top:18px;display:flex;align-items:center;gap:8px;color:#6B8A88;font-weight:900;font-size:14px}.article-card-meta span:first-child{color:#4BBFB0}.article-card-meta span:nth-child(2){color:#B8D7D3}.article-card-modern:hover .article-card-body h3{color:#39AFA1}.hidden{display:none!important}@media(max-width:1000px){.article-grid-sam{grid-template-columns:repeat(2,minmax(0,1fr));gap:22px}.article-section-head{align-items:flex-start}}@media(max-width:640px){.article-tools-sam{top:70px}.article-section-head{display:block}.article-count-pill{margin-top:18px;width:86px}.article-grid-sam{grid-template-columns:1fr}.article-card-image{height:230px}}
@media(max-width:1040px){.article-hero-inner{grid-template-columns:1fr}.article-hero-visual{min-height:360px}.hero-book-card{left:0}.article-hero-title{font-size:clamp(50px,10vw,86px)}}@media(max-width:640px){.article-hero-inner{padding-top:52px;padding-bottom:88px}.article-hero-desc{font-size:16px}.article-hero-visual{min-height:390px}.hero-book-card{grid-template-columns:1fr;width:100%;padding:18px}.book-cover{min-height:150px}.floating-note{display:none}.hero-orbit-a{right:20px;top:20px;width:210px;height:210px}.hero-orbit-b{right:74px;top:195px}.article-hero-actions{gap:10px}.article-hero-btn{width:100%}}

/* FIX: kategori populer interaktif mengikuti design file contoh */
.article-hero-btn.secondary{
    border:1px solid rgba(75,191,176,.25);
    cursor:pointer;
    font-family:inherit;
}
.article-popular-panel-design{
    margin-top:16px;
    padding:18px;
    border-radius:28px;
    background:
        radial-gradient(circle at 92% 10%, rgba(249,213,33,.22), transparent 8rem),
        linear-gradient(135deg,rgba(255,255,255,.94),rgba(248,255,253,.92));
    border:1px solid #D4EEEC;
    box-shadow:0 18px 45px rgba(42,61,60,.08);
}
.article-popular-panel-head{
    display:flex;
    justify-content:space-between;
    gap:16px;
    align-items:flex-start;
    color:#6B8A88;
}
.article-popular-panel-head b{
    display:block;
    color:#2A3D3C;
    font-weight:1000;
    font-size:1.05rem;
}
.article-popular-panel-head p{
    margin-top:4px;
    color:#6B8A88;
    font-weight:850;
}
.article-popular-panel-head span{
    display:inline-flex;
    min-height:34px;
    align-items:center;
    padding:0 12px;
    border-radius:999px;
    background:#FFF7CF;
    color:#A77D00;
    font-weight:1000;
    white-space:nowrap;
}
.article-popular-panel-grid{
    display:grid;
    grid-template-columns:repeat(3,minmax(0,1fr));
    gap:12px;
    margin-top:14px;
}
.article-popular-panel-grid button{
    display:flex;
    align-items:center;
    gap:12px;
    text-align:left;
    padding:16px;
    border-radius:22px;
    border:1px solid #D4EEEC;
    background:#fff;
    cursor:pointer;
    transition:.2s ease;
}
.article-popular-panel-grid button:hover{
    transform:translateY(-3px);
    border-color:#4BBFB0;
    box-shadow:0 16px 34px rgba(75,191,176,.14);
}
.article-popular-panel-grid button > span{
    width:46px;
    height:46px;
    border-radius:16px;
    display:flex;
    align-items:center;
    justify-content:center;
    background:#EEFFFB;
    flex:0 0 auto;
}
.article-popular-panel-grid b{
    display:block;
    color:#2A3D3C;
    font-weight:1000;
}
.article-popular-panel-grid small{
    display:block;
    margin-top:3px;
    color:#6B8A88;
    font-weight:850;
}
.article-hero-stats span:first-child{
    display:none!important;
}
.article-collection-wrap{
    border-radius:34px 34px 0 0;
}
.article-card-modern{
    text-decoration:none!important;
}
@media(max-width:900px){
    .article-popular-panel-grid{
        grid-template-columns:1fr;
    }
    .article-popular-panel-head{
        display:block;
    }
    .article-popular-panel-head span{
        margin-top:10px;
    }
}


/* FIX artikel: tombol Kategori Populer diganti, panel popular dihapus, visual buku dirapikan */
.article-popular-panel-design,
.article-popular-panel-head,
.article-popular-panel-grid{
    display:none !important;
}
.article-hero-btn.secondary{
    cursor:pointer;
    font-family:inherit;
}
.book-lines,
.book-lines i{
    display:none !important;
}
.article-hero-visual{
    min-height:380px !important;
}
.hero-book-card{
    left:18px !important;
    bottom:34px !important;
    width:min(520px, 94%) !important;
    grid-template-columns:170px 1fr !important;
    padding:24px !important;
    transform:rotate(-1deg) translateX(12px) !important;
}
.book-cover{
    min-height:160px !important;
    transform:rotate(-5deg) translateY(-2px) !important;
}
.book-icon{
    font-size:60px !important;
}
.note-one{
    right:16px !important;
    top:94px !important;
}
.note-two{
    left:50px !important;
    top:44px !important;
}
.note-three{
    right:86px !important;
    bottom:64px !important;
}
.hero-orbit-a{
    right:76px !important;
    top:30px !important;
}
.hero-orbit-b{
    right:145px !important;
    top:188px !important;
}
.article-hero-title{
    max-width:760px;
}
@media(max-width:1040px){
    .hero-book-card{
        left:0 !important;
        transform:none !important;
    }
}
@media(max-width:640px){
    .hero-book-card{
        width:100% !important;
        grid-template-columns:1fr !important;
        bottom:8px !important;
    }
    .book-cover{
        min-height:145px !important;
    }
}

</style>
@endsection
