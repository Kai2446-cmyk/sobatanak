@extends('layouts.app')
@section('title','Game Mewarnai — SobatAnak')
@section('content')
<section class="game-shell-page">
    <div class="game-shell-top">
        <a href="{{ route('mini-games') }}" class="game-back-link" data-game-back>← Kembali ke daftar game</a>
        <div class="game-point-chip">⭐ Poin Kamu: <b data-points>{{ number_format($authPoints ?? 0,0,',','.') }}</b></div>
    </div>
    <iframe class="game-frame" data-game-frame src="{{ asset('games/mewarnai/index.html') }}" title="Game Mewarnai SobatAnak" allow="fullscreen; autoplay"></iframe>
</section>
@include('partials.game-point-bridge', ['gameSlug' => 'mewarnai'])

<script>
document.addEventListener('DOMContentLoaded', () => {
    const backButton = document.querySelector('[data-game-back]');
    const gameFrame = document.querySelector('[data-game-frame]');
    if (!backButton || !gameFrame) return;

    window.addEventListener('message', (event) => {
        if (event.origin !== window.location.origin || event.source !== gameFrame.contentWindow) return;
        const data = event.data || {};
        if (data.type === 'sobatanak:mewarnai-screen') {
            backButton.classList.toggle('is-hidden', data.screen !== 'home');
        }
    });
});
</script>
@endsection

@push('game_styles')
<style>
    html,body{margin:0!important;min-height:100%!important;background:transparent!important;overflow-x:hidden!important}
    main.min-h-screen{background:transparent!important;padding:0!important}
    header.sticky,footer.site-footer{display:none!important}
    .game-shell-page{position:relative;background:transparent!important;min-height:100vh;padding:0}
    .game-shell-top{position:absolute;inset:0;z-index:50;pointer-events:none}
    .game-back-link{position:absolute;top:20px;left:clamp(145px,10vw,190px);pointer-events:auto;display:inline-flex;align-items:center;border:1px solid #BFECE6;background:#fff;color:#2A3D3C;border-radius:999px;padding:10px 16px;font-weight:1000;text-decoration:none;white-space:nowrap;box-shadow:0 10px 25px rgba(42,61,60,.06)}
    .game-back-link:hover{background:#EEFFFB}
    .game-back-link.is-hidden{display:none!important}
    .game-point-chip{position:fixed;right:24px;bottom:22px;z-index:60;pointer-events:auto;background:rgba(255,255,255,.96);border:1px solid #f9a8d4;color:#2A3D3C;border-radius:999px;padding:10px 16px;font-weight:1000;white-space:nowrap;box-shadow:0 10px 25px rgba(42,61,60,.08);backdrop-filter:blur(6px)}
    .game-frame{display:block;width:100%;height:100vh;min-height:680px;margin:0;border:0;border-radius:28px;background:#fff;box-shadow:0 24px 70px rgba(42,61,60,.12);overflow:hidden}
    @media(max-width:640px){.game-back-link{top:12px;left:12px;padding:9px 14px;font-size:13px}.game-point-chip{right:12px;bottom:12px;padding:9px 14px;font-size:13px}.game-frame{height:100vh;min-height:620px;border-radius:0}}
</style>
@endpush
