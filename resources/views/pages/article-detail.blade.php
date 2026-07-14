@extends('layouts.app')
@section('title', $article->title . ' — SobatAnak')
@section('content')
@php
    use Illuminate\Support\Str;

    $preparedArticle = class_exists(\App\Support\SobatArticleContent::class)
        ? \App\Support\SobatArticleContent::find($article->slug)
        : null;

    $rawContent = trim((string) $article->content);
    $relatedSafe = isset($related) ? $related : collect();

    $articleCaption = null;
    $contentForRender = $rawContent;

    if (preg_match('/\[caption\](.*?)\[\/caption\]/is', $contentForRender, $captionMatch)) {
        $articleCaption = trim($captionMatch[1]);
        $contentForRender = trim(str_replace($captionMatch[0], '', $contentForRender));
    } else {
        $linesForCaption = preg_split('/\r\n|\r|\n/', $contentForRender);
        foreach ($linesForCaption as $lineIndex => $lineValue) {
            if (preg_match('/^\s*(caption|caption cover|caption gambar|keterangan gambar)\s*:\s*(.+)$/i', $lineValue, $captionLineMatch)) {
                $articleCaption = trim($captionLineMatch[2]);
                unset($linesForCaption[$lineIndex]);
                $contentForRender = trim(implode("\n", $linesForCaption));
                break;
            }
        }
    }

    $renderInline = function (?string $text) {
        $text = e((string) $text);

        $text = preg_replace_callback('/\[([^\]]+)\]\(([^\)]+)\)/', function ($m) {
            $label = $m[1];
            $url = trim($m[2]);
            $safeUrl = e($url);
            return '<a href="'.$safeUrl.'" class="article-inline-link" target="_blank" rel="noopener">'.$label.'</a>';
        }, $text);

        $text = preg_replace('/\*\*(.*?)\*\*/s', '<strong>$1</strong>', $text);
        $text = preg_replace('/__(.*?)__/s', '<strong>$1</strong>', $text);

        return $text;
    };

    $blocks = [];
    $toc = [];
    $paragraphBuffer = [];
    $currentList = [];

    $flushParagraph = function () use (&$paragraphBuffer, &$blocks) {
        if (!empty($paragraphBuffer)) {
            $blocks[] = ['type' => 'paragraph', 'text' => trim(implode(' ', $paragraphBuffer))];
            $paragraphBuffer = [];
        }
    };

    $flushList = function () use (&$currentList, &$blocks) {
        if (!empty($currentList)) {
            $blocks[] = ['type' => 'list', 'items' => $currentList];
            $currentList = [];
        }
    };


    // Normalisasi konten HTML dari editor admin menjadi baris artikel.
    // Tidak mengubah database, hanya cara render di halaman detail.
    if ($contentForRender !== strip_tags($contentForRender)) {
        $contentForRender = preg_replace('/<h2[^>]*>(.*?)<\/h2>/is', "\n## $1\n", $contentForRender);
        $contentForRender = preg_replace('/<h3[^>]*>(.*?)<\/h3>/is', "\n### $1\n", $contentForRender);
        $contentForRender = preg_replace('/<li[^>]*>(.*?)<\/li>/is', "\n- $1", $contentForRender);
        $contentForRender = preg_replace('/<br\s*\/?>/i', "\n", $contentForRender);
        $contentForRender = preg_replace('/<\/(p|div|blockquote|ul|ol)>/i', "\n", $contentForRender);
        $contentForRender = trim(html_entity_decode(strip_tags($contentForRender), ENT_QUOTES, 'UTF-8'));
    }

    $lines = preg_split('/\r\n|\r|\n/', $contentForRender);
    foreach ($lines as $line) {
        $trim = trim($line);

        if ($trim === '') {
            $flushParagraph();
            $flushList();
            continue;
        }

        if (preg_match('/^#{2,3}\s+(.+)$/', $trim, $headingMatch)) {
            $flushParagraph();
            $flushList();
            $title = trim($headingMatch[1]);
            $id = 'subbab-' . (count($toc) + 1) . '-' . Str::slug(strip_tags($title));
            $toc[] = ['id' => $id, 'title' => preg_replace('/\*\*|__/', '', $title)];
            $blocks[] = ['type' => 'heading', 'id' => $id, 'text' => $title];
            continue;
        }

        if (preg_match('/^(Baca juga|Read also)\s*:\s*(.+)$/i', $trim, $readAlsoMatch)) {
            $flushParagraph();
            $flushList();
            $blocks[] = ['type' => 'read_also', 'text' => trim($readAlsoMatch[2])];
            continue;
        }

        if (preg_match('/^[-*]\s+(.+)$/', $trim, $listMatch)) {
            $flushParagraph();
            $currentList[] = trim($listMatch[1]);
            continue;
        }

        $flushList();
        $paragraphBuffer[] = $trim;
    }
    $flushParagraph();
    $flushList();

    if (empty($toc)) {
        $firstParagraphIndex = null;
        foreach ($blocks as $idx => $block) {
            if (($block['type'] ?? '') === 'paragraph') {
                $firstParagraphIndex = $idx;
                break;
            }
        }
        if ($firstParagraphIndex !== null) {
            $leadText = $blocks[$firstParagraphIndex]['text'];
        } else {
            $leadText = 'Artikel ini berisi panduan ringan dari SobatAnak untuk membantu orang tua memilih kebutuhan anak dengan lebih tenang.';
        }
    } else {
        $leadText = null;
        foreach ($blocks as $block) {
            if (($block['type'] ?? '') === 'paragraph') {
                $leadText = $block['text'];
                break;
            }
            if (($block['type'] ?? '') === 'heading') {
                break;
            }
        }
    }

    $displayCaption = $articleCaption ?: ($preparedArticle['lead'] ?? $leadText);
@endphp

<section class="article-detail-hero bg-gradient-to-br from-[#D0F0ED] via-white to-[#FDECEA] py-8 md:py-9">
    <div class="max-w-4xl mx-auto px-6 md:px-10">
        <span class="text-coral font-black uppercase tracking-widest text-xs">{{ $article->category_name }}</span>
        <h1 class="font-display article-detail-title mt-3 leading-tight">{{ $article->title }}</h1>
        <p class="article-meta mt-3">📅 {{ optional($article->published_at ?? $article->created_at)->format('d M Y') ?? 'SobatAnak' }} · 👁️ {{ $article->counter }} dibaca · SobatAnak Editorial</p>
    </div>
</section>

<section class="max-w-4xl mx-auto px-6 md:px-10 py-9 article-detail-page">
    @if($article->image)
        <figure class="article-cover-wrap">
            <img class="w-full rounded-[1.7rem] shadow-soft max-h-[380px] object-cover" src="{{ $article->image }}" alt="{{ $article->title }}" onerror="this.onerror=null;this.src='{{ asset('images/article-placeholder.jpg') }}';">
            @if($articleCaption)
                <figcaption class="article-cover-caption">{!! $renderInline($articleCaption) !!}</figcaption>
            @endif
        </figure>
    @endif

    @if(!$articleCaption && $displayCaption)
        <p class="article-caption">{!! $renderInline($displayCaption) !!}</p>
        <div class="article-caption-line"></div>
    @elseif($articleCaption)
        <div class="article-caption-line"></div>
    @endif

    <article class="article-body p-0 mt-7">
        @if($preparedArticle)
            @php
                $sections = $preparedArticle['sections'] ?? [];
                $tips = $preparedArticle['tips'] ?? [];
                $closing = $preparedArticle['closing'] ?? null;
                $readAlsoLimit = min(2, max(0, (int) ($preparedArticle['read_also_limit'] ?? 1)));
                $readAlsoSlots = [];

                if ($readAlsoLimit === 1) {
                    $readAlsoSlots = [max(1, (int) ceil(max(count($sections), 1) / 2))];
                } elseif ($readAlsoLimit === 2) {
                    $readAlsoSlots = [2, max(3, count($sections))];
                }

                $readAlsoShown = 0;
            @endphp

            @if(count($sections))
                <div class="article-toc" id="pembahasan-artikel">
                    <div class="article-toc-title">Pembahasan dalam artikel</div>
                    <ul>
                        @foreach($sections as $section)
                            <li><a href="#subbab-{{ $loop->iteration }}">{{ $section['title'] }}</a></li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @foreach($sections as $section)
                <section id="subbab-{{ $loop->iteration }}" class="article-section">
                    <h2>{{ $section['title'] }}</h2>

                    @php
                        $bodyParagraphs = is_array($section['body'] ?? null)
                            ? $section['body']
                            : [($section['body'] ?? '')];
                    @endphp

                    @foreach($bodyParagraphs as $paragraph)
                        @if(trim((string) $paragraph) !== '')
                            <p>{{ $paragraph }}</p>
                        @endif
                    @endforeach

                    @php
                        $shouldShowReadAlso = in_array($loop->iteration, $readAlsoSlots, true)
                            && $readAlsoShown < $readAlsoLimit
                            && $relatedSafe->count();

                        $readMore = $shouldShowReadAlso
                            ? $relatedSafe->values()->get($readAlsoShown % max($relatedSafe->count(), 1))
                            : null;

                        if ($readMore) {
                            $readAlsoShown++;
                        }
                    @endphp

                    @if($readMore)
                        <div class="article-read-also">
                            <span>Baca juga:</span>
                            <a href="{{ route('article.show', $readMore->slug ?: $readMore->id) }}">{{ $readMore->title }}</a>
                        </div>
                    @endif
                </section>
            @endforeach

            @if(!empty($tips))
                <div class="article-note-box">
                    <h3>Catatan kecil untuk orang tua</h3>
                    <ul>
                        @foreach($tips as $tip)
                            <li>{{ $tip }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if($closing)
                <p>{{ $closing }}</p>
            @endif
        @else

@if(count($toc))
            <div class="article-toc" id="pembahasan-artikel">
                <div class="article-toc-title">Pembahasan dalam artikel</div>
                <ul>
                    @foreach($toc as $item)
                        <li><a href="#{{ $item['id'] }}">{{ $item['title'] }}</a></li>
                    @endforeach
                </ul>
            </div>
        @endif

        @forelse($blocks as $block)
            @if($block['type'] === 'heading')
                <div id="{{ $block['id'] }}" class="article-section">
                    <h2>{!! $renderInline($block['text']) !!}</h2>
                </div>
            @elseif($block['type'] === 'paragraph')
                <p>{!! $renderInline($block['text']) !!}</p>
            @elseif($block['type'] === 'list')
                <ul class="article-bullet-list">
                    @foreach($block['items'] as $item)
                        <li>{!! $renderInline($item) !!}</li>
                    @endforeach
                </ul>
            @elseif($block['type'] === 'read_also')
                <div class="article-read-also">
                    <span>Baca juga:</span>
                    {!! $renderInline($block['text']) !!}
                </div>
            @endif
        @empty
            <div class="article-toc" id="pembahasan-artikel">
                <div class="article-toc-title">Pembahasan dalam artikel</div>
                <ul>
                    <li><a href="#subbab-1">Mulai dari kebutuhan harian</a></li>
                    <li><a href="#subbab-2">Utamakan aman dan nyaman</a></li>
                    <li><a href="#subbab-3">Belanja secukupnya dulu</a></li>
                </ul>
            </div>
            <div id="subbab-1" class="article-section"><h2>Mulai dari kebutuhan harian</h2><p>Untuk memilih perlengkapan anak, mulai dulu dari rutinitas yang paling sering dilakukan di rumah.</p></div>
            <div id="subbab-2" class="article-section"><h2>Utamakan aman dan nyaman</h2><p>Pilih produk yang bahannya aman, mudah dibersihkan, dan nyaman digunakan si kecil.</p></div>
            <div id="subbab-3" class="article-section"><h2>Belanja secukupnya dulu</h2><p>Mulai dari jumlah kecil dulu, terutama untuk produk yang baru pertama kali dicoba.</p></div>
        @endforelse
        @endif
    </article>

    @if($relatedSafe->count())
    <div class="mt-12">
        <span class="text-coral font-black uppercase tracking-widest text-xs">Artikel Terkait</span>
        <h2 class="font-display text-3xl mt-2">Baca juga</h2>
        <div class="grid md:grid-cols-3 gap-5 mt-5">
            @foreach($relatedSafe as $item)
                <a href="{{ route('article.show', $item->slug ?: $item->id) }}" class="card overflow-hidden block article-related-card">
                    <img class="w-full h-40 object-cover" src="{{ $item->image }}" alt="{{ $item->title }}" onerror="this.onerror=null;this.src='{{ asset('images/article-placeholder.jpg') }}';">
                    <div class="p-5">
                        <span class="text-coral font-black text-xs uppercase">{{ $item->category_name }}</span>
                        <h3 class="font-display text-lg mt-2">{{ $item->title }}</h3>
                        <p class="text-[#6B8A88] text-sm mt-2">{{ $item->excerpt }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
    @endif
</section>

<style>
html{scroll-behavior:smooth}
.article-detail-title{font-size:clamp(2rem,4.6vw,4.1rem);color:#29413f;letter-spacing:-.04em}
.article-meta{color:#6B8A88;font-weight:700;font-size:.95rem}
.article-detail-page{color:#263f3d}
.article-cover-wrap{margin:0}
.article-cover-caption{max-width:820px;margin:1rem auto .45rem;text-align:center;font-style:italic;font-weight:500;font-size:.95rem;line-height:1.65;color:#5d7775}
.article-caption{max-width:820px;margin:1.45rem auto .7rem;text-align:center;font-style:italic;font-weight:400;font-size:clamp(.92rem,1.35vw,1.08rem);line-height:1.65;color:#2f3f3d}
.article-caption-line{width:32px;height:2px;background:#263f3d;border-radius:999px;margin:1.25rem auto 1.8rem;opacity:.75}
.article-body{background:transparent!important;border:none!important;box-shadow:none!important;outline:none!important;color:#263f3d;font-weight:400;line-height:1.85;font-size:1.07rem;transform:none!important}
.article-body:hover,.article-body:focus,.article-body:focus-within{background:transparent!important;border:none!important;box-shadow:none!important;outline:none!important;transform:none!important}
.article-body,.article-body *{transition:none!important;animation:none!important}
.article-body p{margin-bottom:1.05rem;font-weight:400}
.article-body strong,.article-body b{font-weight:900;color:#203735}
.article-body h2,.article-body h3,.article-toc-title,.article-read-also span{font-weight:900}
.article-body h2{font-family:var(--font-display,inherit);font-size:1.45rem;line-height:1.25;margin-top:2rem;margin-bottom:.85rem;color:#263f3d;scroll-margin-top:120px}
.article-body h3{font-family:var(--font-display,inherit);font-size:1.25rem;margin-bottom:.65rem;color:#263f3d}
.article-toc{background:#fff;border:1px solid #e6efee;border-radius:0;padding:1.15rem 1.25rem;margin:1.35rem 0 2rem;box-shadow:none;transform:none!important}
.article-toc:hover,.article-toc:focus-within{border-color:#e6efee;box-shadow:none;transform:none!important;outline:none}
.article-toc-title{color:#263f3d;margin-bottom:.65rem}
.article-toc ul{padding-left:1.25rem;margin:0;list-style:disc}
.article-toc li{margin:.35rem 0;color:#4a706d}
.article-toc a,.article-read-also a,.article-inline-link{color:#159fe8;text-decoration:underline;text-underline-offset:3px;font-weight:700;transform:none!important}
.article-toc a:hover,.article-read-also a:hover,.article-inline-link:hover{text-decoration:underline;transform:none!important;color:#0b83c7}
.article-section{border-top:1px solid #edf3f2;padding-top:.65rem;margin-top:1.25rem;box-shadow:none!important;outline:none!important;transform:none!important}
.article-section:hover,.article-section:focus-within{box-shadow:none!important;outline:none!important;transform:none!important;border-top-color:#edf3f2}
.article-read-also{background:transparent;border-left:none;border-radius:0;padding:.55rem 0;margin:1.05rem 0 .65rem;color:#263f3d;box-shadow:none!important;transform:none!important}

.article-read-also + .article-section{margin-top:.35rem;padding-top:.45rem}
.article-read-also + .article-section h2{margin-top:.25rem}
.article-read-also span{margin-right:.35rem}
.article-bullet-list{padding-left:1.35rem;margin:0 0 1.1rem;list-style:disc;color:#52706d}
.article-bullet-list li{margin:.35rem 0}
.article-related-card,.article-related-card:hover,.article-related-card:focus{transform:none!important;box-shadow:none!important;outline:none!important}
@media(max-width:768px){.article-detail-hero{padding-top:2rem;padding-bottom:2rem}.article-body{font-size:1rem}.article-body h2{font-size:1.28rem}.article-toc{padding:1rem}.article-caption,.article-cover-caption{font-size:.95rem;margin-top:1.1rem}}

/* FIX article detail: isi artikel dari admin/support muncul penuh */
.article-note-box{
    margin:2rem 0;
    padding:1.25rem 1.35rem;
    border-radius:1.25rem;
    background:linear-gradient(135deg,#F1FFFC 0%,#FFF8EC 100%);
    border:1px solid #D4EEEC;
    box-shadow:0 14px 35px rgba(42,61,60,.06);
}
.article-note-box h3{
    margin:0 0 .8rem;
    color:#243D3B;
    font-family:var(--font-display,inherit);
    font-weight:1000;
    font-size:1.35rem;
}
.article-note-box ul{
    margin:0;
    padding-left:1.15rem;
}
.article-note-box li{
    margin:.45rem 0;
    color:#5D7775;
    font-weight:800;
}

</style>
@endsection
