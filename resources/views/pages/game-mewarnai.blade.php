@extends('layouts.app')
@section('title','Game Mewarnai — SobatAnak')
@section('content')
<section class="game-shell-page">
    <div class="game-shell-top">
        <a href="{{ route('mini-games') }}" class="game-back-link">← Kembali ke daftar game</a>
        <div class="game-point-chip">⭐ Poin Kamu: <b data-points>{{ number_format($authPoints ?? 0,0,',','.') }}</b></div>
    </div>
    <iframe class="game-frame" src="{{ asset('games/mewarnai/index.html') }}" title="Game Mewarnai SobatAnak" allow="fullscreen; autoplay"></iframe>
</section>
@include('partials.game-point-bridge', ['gameSlug' => 'mewarnai'])
@endsection

@push('game_styles')
<style>
    html,body{margin:0!important;min-height:100%!important;background:transparent!important;overflow-x:hidden!important}
    main.min-h-screen{background:transparent!important;padding:0!important}
    header.sticky,footer.site-footer{display:none!important}
    .game-shell-page{position:relative;background:transparent!important;min-height:100vh;padding:0}
    .game-shell-top{position:absolute;top:0;left:50%;transform:translateX(-50%);width:calc(100% - 48px);max-width:none;box-sizing:border-box;margin:0;padding:18px 0;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;z-index:50;pointer-events:none}
    .game-back-link{pointer-events:auto;display:inline-flex;align-items:center;border:1px solid #BFECE6;background:#fff;color:#2A3D3C;border-radius:999px;padding:10px 16px;font-weight:1000;text-decoration:none;white-space:nowrap;box-shadow:0 10px 25px rgba(42,61,60,.06)}
    .game-back-link:hover{background:#EEFFFB}
    .game-point-chip{pointer-events:auto;background:#fff;border:1px solid #f9a8d4;color:#2A3D3C;border-radius:999px;padding:10px 16px;font-weight:1000;white-space:nowrap;box-shadow:0 10px 25px rgba(42,61,60,.06)}
    .game-frame{display:block;width:100%;height:100vh;min-height:680px;margin:0;border:0;border-radius:28px;background:#fff;box-shadow:0 24px 70px rgba(42,61,60,.12);overflow:hidden}
    @media(max-width:640px){.game-shell-top{width:calc(100% - 16px);padding:10px 0}.game-frame{height:100vh;min-height:620px;border-radius:0}}
</style>
@endpush
