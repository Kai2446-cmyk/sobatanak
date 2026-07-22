@extends('layouts.app')
@section('title','Memory Card — SobatAnak')
@section('content')
<section class="game-shell-page">
    <div class="game-shell-top">
        <a href="{{ route('mini-games') }}" class="game-back-link">← Kembali ke daftar game</a>
        <div class="game-point-chip">⭐ Poin Kamu: <b data-points>{{ number_format($authPoints ?? 0,0,',','.') }}</b></div>
    </div>
    <div id="root"></div>
</section>
@include('partials.game-point-bridge', ['gameSlug' => 'memory-card'])
@endsection

@push('game_assets')
<link rel="stylesheet" href="{{ asset('games/memory-card/dist/assets/index-BR0o6_QI.css') }}">
<script type="module" src="{{ asset('games/memory-card/dist/assets/index-B5xTYyJf.js') }}"></script>
@endpush

@push('game_styles')
<style>
    html,body{margin:0!important;min-height:100%!important;background:transparent!important;overflow-x:hidden!important}
    main.min-h-screen{background:transparent!important;padding:0!important}
    header.sticky,footer.site-footer{display:none!important}
    .game-shell-page{position:relative;background:transparent!important;min-height:100vh;padding:0}
    .game-shell-top{position:absolute;top:0;left:50%;transform:translateX(-50%);width:calc(100% - 48px);max-width:none;box-sizing:border-box;margin:0;padding:18px 0;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;z-index:50;pointer-events:none}
    .game-back-link{pointer-events:auto;display:inline-flex;align-items:center;border:1px solid #BFECE6;background:#fff;color:#2A3D3C;border-radius:999px;padding:10px 16px;font-weight:1000;text-decoration:none;white-space:nowrap;box-shadow:0 10px 25px rgba(42,61,60,.06)}
    .game-back-link:hover{background:#EEFFFB}
    .game-point-chip{pointer-events:auto;background:#fff;border:1px solid #a5b4fc;color:#2A3D3C;border-radius:999px;padding:10px 16px;font-weight:1000;white-space:nowrap;box-shadow:0 10px 25px rgba(42,61,60,.06)}
    #root{min-height:100vh;width:100%}
    #app_root{min-height:100vh;background:transparent!important;padding-top:0!important}
    @media(max-width:640px){.game-shell-top{width:calc(100% - 20px);padding:10px 0;gap:8px}.game-back-link,.game-point-chip{padding:9px 12px;font-size:13px}}
</style>
@endpush
