@extends('layouts.app')
@section('title','Puzzle Edukatif — SobatAnak')
@section('content')
<section class="game-shell-page">
    <div class="game-shell-top">
        <a href="{{ route('mini-games') }}" class="game-back-link">← Kembali ke daftar game</a>
        <div class="game-point-chip">⭐ Poin Kamu: <b data-points>{{ number_format($authPoints ?? 0,0,',','.') }}</b></div>
    </div>
    <div id="root"></div>
</section>
@include('partials.game-point-bridge', ['gameSlug' => 'puzzle-edukatif'])
@endsection

@push('game_assets')
@php
    $puzzleSettings = $gameSetting?->settings ?: [];
    $adminPuzzleImages = [];
    for ($i = 1; $i <= 3; $i++) {
        $img = $puzzleSettings['puzzle_image_'.$i] ?? null;
        if ($img) {
            $adminPuzzleImages[] = [
                'id' => $i,
                'title' => 'Puzzle Admin '.$i,
                'image' => asset(ltrim($img, '/')),
                'difficulty' => 'custom'
            ];
        }
    }
@endphp
@if(count($adminPuzzleImages))
<script>
window.SOBAT_PUZZLE_IMAGES = @json($adminPuzzleImages);
</script>
@endif
<script type="module" src="{{ asset('games/puzzle/dist/assets/index-PU_zX7_M.js') }}"></script>
@endpush

@push('game_styles')
<style>
    html, body {
        width: 100%;
        height: 100%;
        margin: 0;
        overflow: hidden !important;
        background: #fff !important;
    }

    header.sticky,
    footer.site-footer {
        display: none !important;
    }

    main.min-h-screen {
        width: 100%;
        height: 100vh !important;
        min-height: 100vh !important;
        overflow: hidden !important;
        background: #fff !important;
    }

    .game-shell-page {
        width: 100%;
        height: 100vh !important;
        min-height: 100vh !important;
        overflow: hidden !important;
        position: relative;
        background: #fff !important;
    }

    /* Kontrol ditumpuk di atas game, tanpa strip/background tambahan. */
    .game-shell-top {
        position: absolute;
        inset: 0 0 auto 0;
        z-index: 20;
        box-sizing: border-box;
        margin: 0;
        padding: 14px clamp(24px, 15vw, 280px);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: nowrap;
        background: transparent !important;
        border: 0 !important;
        pointer-events: none;
    }

    .game-shell-top > * {
        pointer-events: auto;
    }

    .game-back-link,
    .game-point-chip {
        flex: 0 0 auto;
        display: inline-flex;
        align-items: center;
        min-height: 46px;
        box-sizing: border-box;
        background: #fff;
        color: #2A3D3C;
        border-radius: 999px;
        padding: 10px 18px;
        font-weight: 1000;
        line-height: 1;
        white-space: nowrap;
        text-decoration: none;
        box-shadow: 0 8px 22px rgba(42, 61, 60, .06);
    }

    .game-back-link {
        border: 1px solid #BFECE6;
    }

    .game-back-link:hover {
        background: #EEFFFB;
    }

    .game-point-chip {
        border: 1px solid #a5b4fc;
    }

    #root {
        width: 100%;
        height: 100vh !important;
        min-height: 0 !important;
        max-width: 100vw !important;
        overflow: hidden !important;
        background: #fff !important;
    }

    #app_root {
        width: 100% !important;
        height: 100% !important;
        min-height: 0 !important;
        overflow: hidden !important;
        background: transparent !important;
        padding-top: 0 !important;
    }

    * {
        scrollbar-width: none;
    }

    *::-webkit-scrollbar {
        display: none !important;
        width: 0 !important;
        height: 0 !important;
    }

    @media (max-width: 900px) {
        .game-shell-top {
            padding: 10px 16px;
        }

        .game-back-link,
        .game-point-chip {
            min-height: 42px;
            padding: 9px 14px;
            font-size: 14px;
        }

    }
</style>
@endpush
