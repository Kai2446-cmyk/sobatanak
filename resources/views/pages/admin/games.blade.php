@extends('layouts.admin')
@section('title','Game Center — Admin SobatAnak')
@section('page-title','Game Center')
@section('admin-content')
<section class="admin-game-page">
    <div class="admin-game-wrap">
        <div class="admin-game-head">
            <div>
                <a href="{{ route('admin.dashboard') }}" class="admin-game-back">← Admin Dashboard</a>
                <span class="admin-game-eyebrow">Game Center</span>
                <h1>Pilih <span>Mini Game</span></h1>
                <p>Klik salah satu game untuk masuk ke halaman setting khusus game tersebut. Dari sana admin bisa mengatur nama, icon, status aktif, poin, cover, path game, dan setting tambahan.</p>
            </div>
            <a href="{{ route('mini-games') }}" class="admin-game-preview">Lihat Halaman Mini Game →</a>
        </div>

        @if(session('success'))
            <div class="admin-game-success">{{ session('success') }}</div>
        @endif

        @if(!empty($tableMissing))
            <div class="admin-game-alert">
                <b>Tabel setting game belum ada.</b>
                <span>Import file SQL <code>database/sql/2026_06_18_create_game_settings.sql</code> dulu lewat phpMyAdmin, lalu refresh halaman ini.</span>
            </div>
        @else
            <div class="admin-game-summary">
                <div><b>{{ $games->count() }}</b><span>Total Game</span></div>
                <div><b>{{ $games->where('is_active', true)->count() }}</b><span>Game Aktif</span></div>
                <div><b>{{ $games->where('is_active', false)->count() }}</b><span>Maintenance</span></div>
            </div>

            <div class="admin-game-list-grid">
                @forelse($games as $game)
                    <a class="admin-game-menu-card color-{{ $game->color ?: 'teal' }}" href="{{ route('admin.games.edit', $game) }}">
                        <div class="game-menu-cover">
                            @if($game->cover_image)
                                <img src="{{ asset(ltrim($game->cover_image, '/')) }}" alt="{{ $game->title }}">
                            @else
                                <span>{{ $game->icon }}</span>
                            @endif
                            <i class="{{ $game->is_active ? 'active' : 'off' }}">{{ $game->is_active ? 'Aktif' : 'Maintenance' }}</i>
                        </div>
                        <div class="game-menu-content">
                            <small>{{ $game->slug }}</small>
                            <h2>{{ $game->title }}</h2>
                            <p>{{ $game->description }}</p>
                            <div class="game-menu-meta">
                                <span>🎁 {{ $game->points_per_play }} poin/main</span>
                                <span>↕ Urutan {{ $game->sort_order }}</span>
                            </div>
                            <strong>Buka Setting →</strong>
                        </div>
                    </a>
                @empty
                    <div class="admin-game-alert">
                        <b>Data game belum ada.</b>
                        <span>Isi tabel <code>game_settings</code> terlebih dahulu supaya daftar game tampil di sini.</span>
                    </div>
                @endforelse
            </div>
        @endif
    </div>
</section>

<style>
.admin-game-page{background:linear-gradient(135deg,#f7fffd 0%,#fff 48%,#fff5ee 100%);min-height:calc(100vh - 110px);padding:46px 0 70px}.admin-game-wrap{max-width:1180px;margin:auto;padding:0 24px}.admin-game-head{display:flex;justify-content:space-between;gap:22px;align-items:flex-end;margin-bottom:28px}.admin-game-back,.admin-game-preview{display:inline-flex;align-items:center;border:1px solid #d4eeec;border-radius:999px;background:#fff;color:#2a3d3c;text-decoration:none;font-weight:1000;padding:12px 16px;box-shadow:0 12px 28px rgba(42,61,60,.06)}.admin-game-eyebrow{display:block;margin-top:20px;color:#e8756a;font-size:12px;text-transform:uppercase;letter-spacing:.18em;font-weight:1000}.admin-game-head h1{font-family:var(--font-display,inherit);font-size:clamp(2.7rem,5.7vw,5rem);line-height:.95;color:#2a3d3c;font-weight:1000;letter-spacing:-.06em;margin-top:10px}.admin-game-head h1 span{color:#4bbfb0}.admin-game-head p{max-width:720px;color:#6b8a88;font-weight:900;line-height:1.7;margin-top:12px}.admin-game-alert,.admin-game-success{background:#fff6ec;border:1px solid #f6c69c;border-radius:26px;padding:22px;color:#2a3d3c;font-weight:900}.admin-game-success{background:#effdf8;border-color:#b9eee1;margin-bottom:18px;color:#247c70}.admin-game-alert b{display:block;font-size:1.2rem}.admin-game-alert span{display:block;color:#6b8a88;margin-top:6px}.admin-game-summary{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px;margin-bottom:22px}.admin-game-summary>div{background:#fff;border:1px solid #d4eeec;border-radius:24px;padding:20px;box-shadow:0 14px 36px rgba(42,61,60,.05)}.admin-game-summary b{display:block;font-size:2.1rem;font-weight:1000;color:#4bbfb0}.admin-game-summary span{display:block;color:#6b8a88;font-weight:1000}.admin-game-list-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:20px}.admin-game-menu-card{position:relative;overflow:hidden;display:flex;flex-direction:column;min-height:430px;text-decoration:none;background:#fff;border:1px solid #d4eeec;border-radius:34px;box-shadow:0 24px 70px rgba(42,61,60,.08);transition:.22s}.admin-game-menu-card:hover{transform:translateY(-5px);box-shadow:0 32px 85px rgba(42,61,60,.13)}.game-menu-cover{position:relative;height:180px;margin:16px 16px 0;border-radius:26px;background:linear-gradient(135deg,#eafffb,#fff8ee);display:grid;place-items:center;overflow:hidden;border:1px solid rgba(212,238,236,.9)}.game-menu-cover img{width:100%;height:100%;object-fit:cover}.game-menu-cover>span{font-size:4.5rem;filter:drop-shadow(0 12px 20px rgba(42,61,60,.12))}.game-menu-cover i{position:absolute;top:14px;right:14px;border-radius:999px;padding:8px 12px;font-style:normal;font-size:12px;font-weight:1000;background:#fff;color:#2a3d3c;border:1px solid #d4eeec}.game-menu-cover i.active{background:#eafffb;color:#159284;border-color:#b9eee1}.game-menu-cover i.off{background:#fff0ee;color:#d85d52;border-color:#f2bbb5}.game-menu-content{padding:20px 20px 22px;display:flex;flex-direction:column;gap:10px;flex:1}.game-menu-content small{color:#e8756a;text-transform:uppercase;letter-spacing:.14em;font-size:11px;font-weight:1000}.game-menu-content h2{font-size:1.65rem;font-weight:1000;color:#2a3d3c;letter-spacing:-.04em}.game-menu-content p{color:#6b8a88;font-weight:850;line-height:1.55;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden}.game-menu-meta{display:flex;flex-wrap:wrap;gap:8px;margin-top:auto}.game-menu-meta span{border-radius:999px;background:#f7fffd;border:1px solid #d4eeec;color:#2a3d3c;padding:8px 10px;font-size:12px;font-weight:1000}.game-menu-content strong{display:inline-flex;align-items:center;justify-content:center;margin-top:8px;border-radius:999px;background:#4bbfb0;color:#fff;padding:13px 16px;font-weight:1000;box-shadow:0 16px 34px rgba(75,191,176,.22)}.color-purple .game-menu-content strong{background:#8b7cf6}.color-orange .game-menu-content strong{background:#e8756a}.color-green .game-menu-content strong{background:#7cc870}.color-blue .game-menu-content strong{background:#5aa9e6}.color-pink .game-menu-content strong{background:#ec7ca6}@media(max-width:1000px){.admin-game-list-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:720px){.admin-game-head{display:block}.admin-game-preview{margin-top:16px}.admin-game-summary,.admin-game-list-grid{grid-template-columns:1fr}.admin-game-menu-card{min-height:auto}}
</style>
@endsection
