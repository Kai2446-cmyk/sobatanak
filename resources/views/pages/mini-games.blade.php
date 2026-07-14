@extends('layouts.app')
@section('title','Mini Game — SobatAnak')
@section('content')

@php
    $gamesCollection = collect($games ?? []);
    $sortedGames = $gamesCollection
        ->sortBy(fn($g) => empty($g['is_active']) ? 1 : 0)
        ->values();
    $activeGameCount = $activeGameCount ?? $gamesCollection->where('is_active', true)->count();

    $playCounter = function ($game) {
        return (int) (
            data_get($game, 'play_count')
            ?? data_get($game, 'plays_count')
            ?? data_get($game, 'played_count')
            ?? data_get($game, 'total_played')
            ?? data_get($game, 'total_play')
            ?? data_get($game, 'plays')
            ?? data_get($game, 'views')
            ?? 0
        );
    };

    $firstGame = $gamesCollection->firstWhere('is_active', true) ?: $gamesCollection->first();
    $mostPlayedGame = $gamesCollection
        ->filter(fn($g) => !empty($g['is_active']))
        ->sortByDesc(fn($g) => $playCounter($g))
        ->first() ?: $firstGame;

    $featuredGame = $mostPlayedGame ?: [
        'title' => 'Mini Game SobatAnak',
        'desc' => 'Pilih game favorit, kumpulkan poin, dan tukarkan reward.',
        'url' => '#daftar-game',
        'icon' => '🎮',
        'color' => 'teal',
        'available' => true,
        'is_active' => true,
        'cover_image' => null,
    ];

    $featuredPlayed = $playCounter($featuredGame);

    $gameMeta = function ($title) {
        $name = strtolower($title ?? '');

        if (str_contains($name, 'memory')) {
            return ['age' => '6–10 thn', 'age_key' => '6-10', 'difficulty' => 'Sulit', 'difficulty_key' => 'sulit', 'tag' => 'Daya Ingat'];
        }
        if (str_contains($name, 'warna') || str_contains($name, 'mewarnai')) {
            return ['age' => '6–10 thn', 'age_key' => '6-10', 'difficulty' => 'Sulit', 'difficulty_key' => 'sulit', 'tag' => 'Kreatif'];
        }
        if (str_contains($name, 'sikat')) {
            return ['age' => '4–8 thn', 'age_key' => '4-8', 'difficulty' => 'Sedang', 'difficulty_key' => 'sedang', 'tag' => 'Kebiasaan'];
        }
        if (str_contains($name, 'puzzle')) {
            return ['age' => '5–8 thn', 'age_key' => '5-8', 'difficulty' => 'Sedang', 'difficulty_key' => 'sedang', 'tag' => 'Logika'];
        }
        if (str_contains($name, 'tap')) {
            return ['age' => '4–8 thn', 'age_key' => '4-8', 'difficulty' => 'Mudah', 'difficulty_key' => 'mudah', 'tag' => 'Refleks'];
        }
        if (str_contains($name, 'keranjang')) {
            return ['age' => '3–7 thn', 'age_key' => '3-7', 'difficulty' => 'Mudah', 'difficulty_key' => 'mudah', 'tag' => 'Edukasi'];
        }

        return ['age' => '4–8 thn', 'age_key' => '4-8', 'difficulty' => 'Mudah', 'difficulty_key' => 'mudah', 'tag' => 'Seru'];
    };
@endphp

<section class="gamex-hero">
    <div class="gamex-planet gamex-planet-a">🪐</div>
    <div class="gamex-planet gamex-planet-b">✦</div>
    <div class="gamex-cloud gamex-cloud-a"></div>
    <div class="gamex-cloud gamex-cloud-b"></div>

    <div class="max-w-7xl mx-auto px-6 md:px-12 gamex-hero-grid">
        <div class="gamex-hero-copy">
            <span>Let the games begin</span>
            <h1>LET THE<br>GAMES BEGIN</h1>
            <p>Mainkan mini game SobatAnak, kumpulkan poin, naik ke papan peringkat, lalu tukarkan reward lucu untuk si kecil.</p>

            <div class="gamex-hero-actions">
                <a class="gamex-btn-yellow" href="#daftar-game">LIHAT GAME</a>
                <div class="gamex-points-pill">⭐ Poin Kamu: <b data-points>{{ number_format($authPoints ?: 0,0,',','.') }}</b></div>
            </div>
        </div>

        <div class="gamex-mascot-card gamex-duo-stage" aria-hidden="true">
            <div class="gamex-orbit gamex-orbit-one"></div>
            <div class="gamex-orbit gamex-orbit-two"></div>
            <div class="gamex-soft-circle gamex-soft-circle-a"></div>
            <div class="gamex-soft-circle gamex-soft-circle-b"></div>
            <div class="gamex-console">🎮</div>
            <div class="gamex-dpad">✚</div>
            <div class="gamex-spark gamex-spark-a">✦</div>
            <div class="gamex-spark gamex-spark-b">★</div>
            <div class="gamex-mini-cloud gamex-mini-cloud-a"></div>
            <div class="gamex-mini-cloud gamex-mini-cloud-b"></div>
            <img class="gamex-hero-mascot gamex-selena" src="{{ asset('assets/mini-games/hero/selena-happy.png') }}" alt="">
            <img class="gamex-hero-mascot gamex-solis" src="{{ asset('assets/mini-games/hero/solis-happy.png') }}" alt="">
            <div class="gamex-mascot-shadow gamex-duo-shadow"></div>
        </div>
    </div>
</section>

<section class="gamex-feature-area">
    <div class="max-w-7xl mx-auto px-6 md:px-12 gamex-feature-grid">
        <div class="gamex-trial-card">
            <div class="gamex-trial-art">
                <div class="gamex-small-mascot">🕹️</div>
            </div>
            <div class="gamex-trial-content">
                <span class="gamex-most-played-badge">🔥 Paling Banyak Dimainkan</span>
                <h2>{{ $featuredGame['title'] ?? 'Mini Game SobatAnak' }}</h2>
                <div class="gamex-played-count">
                    @if($featuredPlayed > 0)
                        {{ number_format($featuredPlayed, 0, ',', '.') }}x dimainkan
                    @else
                        Game favorit SobatAnak
                    @endif
                </div>
                <p>{{ $featuredGame['desc'] ?? 'Mainkan game seru dan kumpulkan poin SobatAnak.' }}</p>
                @if(!empty($featuredGame['is_active']))
                    <a href="{{ $featuredGame['url'] }}">PLAY</a>
                @else
                    <a href="#daftar-game">CEK GAME</a>
                @endif
            </div>
        </div>

        <aside class="gamex-season-card">
            <div class="gamex-season-item">
                <div>🏆</div>
                <span>
                    <b>All game, all season</b>
                    <small>Pilih game aktif dan kumpulkan poin sebanyak mungkin.</small>
                </span>
            </div>
            <div class="gamex-season-item">
                <div>⭐</div>
                <span>
                    <b>Top leaderboard</b>
                    <small>Nama user dengan poin tertinggi tampil di papan peringkat.</small>
                </span>
            </div>
            <div class="gamex-season-item">
                <div>🎁</div>
                <span>
                    <b>Reward point</b>
                    <small>Tukarkan poin game dengan voucher dan hadiah SobatAnak.</small>
                </span>
            </div>
            <a href="#daftar-game">+ MORE GAMES</a>
        </aside>
    </div>
</section>

<section id="daftar-game" class="gamex-service-section">
    <div class="max-w-7xl mx-auto px-6 md:px-12">
        <div class="gamex-service-head">
            <span>Let the games begin</span>
            <h2>MEET OUR APPS<br>SERVICE</h2>
            <p class="gamex-service-sub">Pilih game sesuai umur anak dan tingkat kesulitannya.</p>
        </div>

        <div class="gamex-game-filter gamex-filter-simple" data-game-filter>
            <div class="gamex-filter-card">
                <div class="gamex-filter-title">
                    <span>👶</span>
                    <b>Pilih Umur</b>
                    <small>Sesuaikan dengan usia anak</small>
                </div>
                <div class="gamex-filter-buttons">
                    <button type="button" class="active" data-filter-type="age" data-filter-value="all">Semua</button>
                    <button type="button" data-filter-type="age" data-filter-value="3-7">3–7 thn</button>
                    <button type="button" data-filter-type="age" data-filter-value="4-8">4–8 thn</button>
                    <button type="button" data-filter-type="age" data-filter-value="5-8">5–8 thn</button>
                    <button type="button" data-filter-type="age" data-filter-value="6-10">6–10 thn</button>
                </div>
            </div>
            <div class="gamex-filter-card">
                <div class="gamex-filter-title">
                    <span>🎯</span>
                    <b>Tingkat Game</b>
                    <small>Pilih level bermain</small>
                </div>
                <div class="gamex-filter-buttons">
                    <button type="button" class="active" data-filter-type="difficulty" data-filter-value="all">Semua</button>
                    <button type="button" data-filter-type="difficulty" data-filter-value="mudah">Mudah</button>
                    <button type="button" data-filter-type="difficulty" data-filter-value="sedang">Sedang</button>
                    <button type="button" data-filter-type="difficulty" data-filter-value="sulit">Sulit</button>
                </div>
            </div>
        </div>

        <div class="gamex-filter-info" data-filter-info>
            🎮 Menampilkan semua game SobatAnak.
        </div>

        <div class="gamex-service-grid">
            @foreach(($sortedGames ?? collect($games ?? [])) as $game)
                @php
                    $isActive = !empty($game['is_active']);
                    $isReady = !empty($game['available']);
                    $meta = $gameMeta($game['title'] ?? '');
                    $playedTotal = $playCounter($game);
                @endphp

                @if($isActive)
                    <a href="{{ $game['url'] }}" class="gamex-service-card gamex-{{ $game['color'] }}" data-game-card data-age="{{ $meta['age_key'] }}" data-difficulty="{{ $meta['difficulty_key'] }}" aria-label="Main {{ $game['title'] }}">
                @else
                    <div class="gamex-service-card gamex-{{ $game['color'] }} gamex-maintenance" data-game-card data-age="{{ $meta['age_key'] }}" data-difficulty="{{ $meta['difficulty_key'] }}" aria-disabled="true">
                @endif
                    <div class="gamex-service-icon">
                        @if(!empty($game['cover_image']))
                            <img src="{{ asset(ltrim($game['cover_image'], '/')) }}" alt="{{ $game['title'] }}">
                        @else
                            <span>{{ $game['icon'] }}</span>
                        @endif
                    </div>

                    @if(!$isActive)
                        <em>Maintenance</em>
                    @elseif(!$isReady)
                        <em>Cek Build</em>
                    @else
                        <em>Aktif</em>
                    @endif

                    <div class="gamex-game-meta">
                        <span>👶 {{ $meta['age'] }}</span>
                        <span>🎯 {{ $meta['difficulty'] }}</span>
                        @if($playedTotal > 0)
                            <span>🔥 {{ number_format($playedTotal, 0, ',', '.') }}x</span>
                        @endif
                    </div>

                    <h3>{{ $game['title'] }}</h3>
                    <p>{{ $game['desc'] }}</p>

                    @if(!$isActive)
                        <span class="gamex-play-cta gamex-play-disabled">Belum Bisa Dimainkan</span>
                    @elseif(!$isReady)
                        <span class="gamex-play-cta">Lihat Game</span>
                    @else
                        <span class="gamex-play-cta">Ayo Main 🎮</span>
                    @endif
                @if($isActive)
                    </a>
                @else
                    </div>
                @endif
            @endforeach
        </div>
        <div class="gamex-game-empty" data-game-empty style="display:none;">
            <div>🔎</div>
            <h3>Game belum ada di filter ini</h3>
            <p>Coba pilih umur atau tingkat kesulitan lain ya.</p>
        </div>
    </div>
</section>


<section class="gamex-top-rank-reward">
    <div class="max-w-7xl mx-auto px-6 md:px-12 gamex-bottom-grid">
        <div class="gamex-leaderboard-card">
            <div class="gamex-leaderboard-head">
                <span>🏆 Papan Peringkat</span>
                <h3>Top 10 Sobat Poin</h3>
            </div>
            <div class="gamex-leaderboard-list">
                @forelse($leaderboard as $index => $point)
                    <div class="gamex-rank-item {{ $index < 3 ? 'is-winner' : '' }}">
                        <div class="gamex-rank-number">
                            @if($index === 0) 🥇
                            @elseif($index === 1) 🥈
                            @elseif($index === 2) 🥉
                            @else {{ $index + 1 }}
                            @endif
                        </div>
                        @php($rankAvatarUrl = $point->user?->avatar_url)
                        <div class="gamex-rank-avatar {{ $rankAvatarUrl ? 'has-photo' : 'is-empty' }}">
                            @if($rankAvatarUrl)
                                <img src="{{ $rankAvatarUrl }}" alt="Foto profil {{ $point->user->name ?? 'User' }}">
                            @endif
                        </div>
                        <div class="gamex-rank-info">
                            <b>{{ $point->user->name ?? 'User' }}</b>
                            <span>{{ number_format($point->points, 0, ',', '.') }} poin</span>
                        </div>
                    </div>
                @empty
                    <div class="gamex-empty-rank">Belum ada data poin. Ayo mulai main!</div>
                @endforelse
            </div>
        </div>

        <div class="gamex-reward-box">
            <div>🎁</div>
            <h3>Tukar Poin Jadi Voucher</h3>
            <p>Poin dari mini game bisa dipakai untuk menukar voucher dan hadiah SobatAnak.</p>
            @if(session('user_id'))
                <a href="{{ route('profile.rewards') }}">Tukar Reward →</a>
            @else
                <a href="{{ route('login') }}">Login untuk Tukar →</a>
            @endif
        </div>
    </div>
</section>



<script>
document.addEventListener('DOMContentLoaded', () => {
    const filterWrap = document.querySelector('[data-game-filter]');
    const cards = Array.from(document.querySelectorAll('[data-game-card]'));
    const grid = document.querySelector('.gamex-service-grid');
    const empty = document.querySelector('[data-game-empty]');
    const info = document.querySelector('[data-filter-info]');

    let activeAge = 'all';
    let activeDifficulty = 'all';

    const ageLabel = {
        all: 'semua umur',
        '3-7': 'umur 3–7 thn',
        '4-8': 'umur 4–8 thn',
        '5-8': 'umur 5–8 thn',
        '6-10': 'umur 6–10 thn'
    };

    const difficultyLabel = {
        all: 'semua level',
        mudah: 'level Mudah',
        sedang: 'level Sedang',
        sulit: 'level Sulit'
    };

    if (!filterWrap || !cards.length || !grid) return;

    function moveMaintenanceToBack() {
        const sorted = cards.slice().sort((a, b) => {
            const aMaintenance = a.classList.contains('gamex-maintenance') ? 1 : 0;
            const bMaintenance = b.classList.contains('gamex-maintenance') ? 1 : 0;
            return aMaintenance - bMaintenance;
        });

        sorted.forEach(card => grid.appendChild(card));
    }

    function updateInfo(shown) {
        if (!info) return;

        if (!shown) {
            info.textContent = `🔎 Belum ada game untuk ${ageLabel[activeAge] || 'umur ini'} dan ${difficultyLabel[activeDifficulty] || 'level ini'}.`;
            return;
        }

        if (activeAge === 'all' && activeDifficulty === 'all') {
            info.textContent = '🎮 Menampilkan semua game SobatAnak.';
            return;
        }

        info.textContent = `✨ Menampilkan ${shown} game untuk ${ageLabel[activeAge] || 'umur ini'} dan ${difficultyLabel[activeDifficulty] || 'level ini'}.`;
    }

    function applyGameFilter() {
        let shown = 0;

        cards.forEach(card => {
            const ageOk = activeAge === 'all' || card.dataset.age === activeAge;
            const difficultyOk = activeDifficulty === 'all' || card.dataset.difficulty === activeDifficulty;
            const isVisible = ageOk && difficultyOk;

            card.hidden = !isVisible;
            card.style.display = isVisible ? '' : 'none';
            card.classList.toggle('is-filtered-match', isVisible);

            if (isVisible) shown++;
        });

        moveMaintenanceToBack();

        if (empty) empty.style.display = shown ? 'none' : 'block';
        updateInfo(shown);
    }

    filterWrap.addEventListener('click', (event) => {
        const button = event.target.closest('button[data-filter-type]');
        if (!button) return;

        const type = button.dataset.filterType;
        const value = button.dataset.filterValue;

        filterWrap.querySelectorAll(`button[data-filter-type="${type}"]`).forEach(item => {
            item.classList.remove('active');
        });

        button.classList.add('active');

        if (type === 'age') activeAge = value;
        if (type === 'difficulty') activeDifficulty = value;

        applyGameFilter();
    });

    moveMaintenanceToBack();
    applyGameFilter();

    // Custom cursor kecil khusus kartu pada halaman menu Mini Game.
    const cursorImages = [
        "{{ asset('assets/mini-games/cursors/kursor1.png') }}",
        "{{ asset('assets/mini-games/cursors/kursor2.png') }}"
    ];
    const errorCursorImage = "{{ asset('assets/mini-games/cursors/kursor-error.png') }}";
    let cursorFollower = null;
    let activeCursorCard = null;
    let isNavigatingToGame = false;

    [...cursorImages, errorCursorImage].forEach(src => {
        const preload = new Image();
        preload.src = src;
    });

    function ensureCursorFollower() {
        if (cursorFollower) return cursorFollower;
        cursorFollower = document.createElement('img');
        cursorFollower.className = 'gamex-menu-cursor';
        cursorFollower.alt = '';
        cursorFollower.setAttribute('aria-hidden', 'true');
        document.body.appendChild(cursorFollower);
        return cursorFollower;
    }

    function setFollowerImage(card) {
        const isMaintenance = card.classList.contains('gamex-maintenance') || card.getAttribute('aria-disabled') === 'true';
        const image = isMaintenance
            ? errorCursorImage
            : cursorImages[Math.floor(Math.random() * cursorImages.length)];
        const follower = ensureCursorFollower();
        follower.src = image;
        follower.classList.toggle('is-error', isMaintenance);
        follower.classList.add('is-visible');
        card.classList.add('gamex-hide-native-cursor');
        activeCursorCard = card;
    }

    function hideFollower() {
        if (cursorFollower && !isNavigatingToGame) cursorFollower.classList.remove('is-visible');
        if (activeCursorCard) activeCursorCard.classList.remove('gamex-hide-native-cursor');
        activeCursorCard = null;
    }

    document.addEventListener('pointermove', event => {
        const card = event.target.closest('[data-game-card]');
        if (!card) {
            hideFollower();
            return;
        }

        if (activeCursorCard !== card) {
            if (activeCursorCard) activeCursorCard.classList.remove('gamex-hide-native-cursor');
            setFollowerImage(card);
        }

        const follower = ensureCursorFollower();
        follower.style.left = `${event.clientX}px`;
        follower.style.top = `${event.clientY}px`;
    }, { passive: true });

    document.addEventListener('pointerleave', hideFollower);

    // Saat diklik, kursor tetap terlihat sebentar agar efek benar-benar tampak sebelum pindah halaman.
    document.addEventListener('click', event => {
        const card = event.target.closest('[data-game-card]');
        if (!card || isNavigatingToGame) return;

        const isMaintenance = card.classList.contains('gamex-maintenance') || card.getAttribute('aria-disabled') === 'true';
        const follower = ensureCursorFollower();
        follower.style.left = `${event.clientX}px`;
        follower.style.top = `${event.clientY}px`;
        setFollowerImage(card);
        follower.classList.add('is-clicked');
        window.setTimeout(() => follower.classList.remove('is-clicked'), 260);

        if (isMaintenance) {
            event.preventDefault();
            event.stopPropagation();
            return;
        }

        const href = card.getAttribute('href');
        if (!href || href.startsWith('#')) return;

        event.preventDefault();
        event.stopPropagation();
        isNavigatingToGame = true;
        window.setTimeout(() => window.location.assign(href), 420);
    }, true);
});
</script>

<style>
.gamex-hero{position:relative;overflow:hidden;min-height:720px;padding:74px 0 185px;background:linear-gradient(135deg,#FF8FA3 0%,#F8B4D1 38%,#7EDDD4 100%);color:#fff}
.gamex-hero:before{content:'';position:absolute;inset:0;background:radial-gradient(circle at 20% 18%,rgba(255,255,255,.10),transparent 10rem),radial-gradient(circle at 86% 12%,rgba(255,255,255,.18),transparent 12rem);opacity:.95}
.gamex-hero:after{content:'';position:absolute;left:-8%;right:-8%;bottom:-98px;height:210px;background:#fff;border-radius:50% 50% 0 0/100% 100% 0 0}
.gamex-cloud{position:absolute;border-radius:999px;background:rgba(255,255,255,.12);filter:blur(.2px)}
.gamex-cloud:before,.gamex-cloud:after{content:'';position:absolute;border-radius:inherit;background:inherit}
.gamex-cloud-a{width:210px;height:54px;left:16%;top:120px}.gamex-cloud-a:before{width:82px;height:82px;left:44px;top:-35px}.gamex-cloud-a:after{width:116px;height:116px;right:18px;top:-58px}
.gamex-cloud-b{width:170px;height:46px;right:12%;top:190px;background:rgba(255,255,255,.24)}.gamex-cloud-b:before{width:74px;height:74px;left:32px;top:-32px}.gamex-cloud-b:after{width:94px;height:94px;right:8px;top:-50px}
.gamex-planet{position:absolute;z-index:1;opacity:.45;font-size:70px}.gamex-planet-a{left:37%;top:150px}.gamex-planet-b{right:22%;top:150px}
.gamex-hero-grid{position:relative;z-index:3;display:grid;grid-template-columns:minmax(0,1fr) 520px;gap:50px;align-items:center}
.gamex-hero-copy span,.gamex-service-head span,.gamex-how-grid>div>span{display:block;text-transform:uppercase;letter-spacing:.22em;font-size:12px;font-weight:1000;color:#FFEFB2;margin-bottom:14px}
.gamex-hero-copy h1{font-family:var(--font-display,inherit);font-size:clamp(3.8rem,8vw,7.3rem);line-height:.94;letter-spacing:-.05em;font-weight:1000;text-shadow:0 18px 40px rgba(42,61,60,.16)}
.gamex-hero-copy p{max-width:600px;margin-top:20px;line-height:1.75;color:rgba(255,255,255,.85);font-weight:850}
.gamex-hero-actions{display:flex;align-items:center;gap:14px;flex-wrap:wrap;margin-top:34px}
.gamex-btn-yellow{display:inline-flex;align-items:center;justify-content:center;min-height:58px;padding:0 34px;border-radius:12px;background:#FFD617;color:#222;text-decoration:none;font-weight:1000;box-shadow:0 10px 0 #CA9B00,0 22px 36px rgba(42,61,60,.16)}
.gamex-points-pill{display:inline-flex;align-items:center;gap:8px;border-radius:999px;background:rgba(255,255,255,.16);border:1px solid rgba(255,255,255,.28);padding:14px 18px;color:#fff;font-weight:1000;backdrop-filter:blur(10px)}
.gamex-mascot-card{position:relative;min-height:500px;display:flex;align-items:center;justify-content:center}
.gamex-mascot-head{width:360px;height:360px;border-radius:46% 46% 48% 48%;background:linear-gradient(180deg,#6ED7E7,#35B6D3);box-shadow:0 36px 60px rgba(63,0,54,.26);display:flex;align-items:center;justify-content:center;transform:rotate(-4deg)}
.gamex-mascot-head:before,.gamex-mascot-head:after{content:'';position:absolute;top:-34px;width:88px;height:88px;border-radius:50%;background:#35B6D3;border:18px solid #1689AF}
.gamex-mascot-head:before{left:58px}.gamex-mascot-head:after{right:58px}
.gamex-mascot-face{font-size:8rem;filter:drop-shadow(0 16px 18px rgba(0,0,0,.18))}
.gamex-mascot-body{position:absolute;width:220px;height:150px;bottom:36px;border-radius:48% 48% 36% 36%;background:#FF804F;box-shadow:0 25px 35px rgba(63,0,54,.16)}
.gamex-mascot-shadow{position:absolute;width:340px;height:48px;bottom:20px;border-radius:50%;background:rgba(90,0,42,.20);filter:blur(10px)}
.gamex-feature-area{position:relative;z-index:5;margin-top:-128px;padding-bottom:70px}
.gamex-feature-grid{display:grid;grid-template-columns:minmax(0,1fr) 360px;gap:28px;align-items:center}
.gamex-trial-card{min-height:360px;border-radius:28px;background:#fff;box-shadow:0 30px 80px rgba(42,61,60,.12);display:grid;grid-template-columns:.85fr 1fr;align-items:center;overflow:hidden;position:relative}
.gamex-trial-card:before{content:'🪐';position:absolute;left:30px;top:28px;font-size:52px;opacity:.22}.gamex-trial-art{display:flex;align-items:center;justify-content:center}.gamex-small-mascot{width:220px;height:220px;border-radius:42px;display:flex;align-items:center;justify-content:center;font-size:8rem;background:linear-gradient(180deg,#6ED7E7,#35B6D3);box-shadow:0 25px 55px rgba(42,61,60,.16);transform:rotate(-5deg)}
.gamex-trial-content{padding:38px}.gamex-trial-content span{font-weight:1000;color:#FFB800}.gamex-trial-content h2{font-family:var(--font-display,inherit);font-size:2.35rem;line-height:1.02;color:#1F2E2D;font-weight:1000;margin-top:10px}.gamex-trial-content p{color:#6B8A88;font-weight:850;line-height:1.6;margin:12px 0 20px}.gamex-trial-content a{display:inline-flex;min-height:46px;align-items:center;justify-content:center;border-radius:10px;background:#FFD617;color:#222;text-decoration:none;font-weight:1000;padding:0 28px;box-shadow:0 8px 0 #CA9B00}
.gamex-season-card{border-radius:28px;background:linear-gradient(135deg,#FF8FA3 0%,#F7A8C8 34%,#9FE7DF 100%);color:#fff;padding:28px;box-shadow:0 28px 70px rgba(42,61,60,.14)}.gamex-season-item{display:grid;grid-template-columns:58px 1fr;gap:14px;align-items:center;margin-bottom:22px}.gamex-season-item div{width:58px;height:58px;border-radius:50%;background:#FFD617;color:#222;display:flex;align-items:center;justify-content:center;font-size:1.8rem;box-shadow:0 12px 24px rgba(0,0,0,.16)}.gamex-season-item b{display:block;text-transform:uppercase;font-weight:1000;font-size:.95rem}.gamex-season-item small{display:block;color:rgba(255,255,255,.76);font-weight:800;line-height:1.45;margin-top:3px}.gamex-season-card>a{display:inline-flex;border:1px solid rgba(255,255,255,.36);border-radius:999px;color:#fff;text-decoration:none;font-weight:1000;padding:12px 18px}
.gamex-service-section{padding:54px 0 90px;background:#fff}.gamex-service-head span{color:#E8756A}.gamex-service-head h2{font-family:var(--font-display,inherit);font-size:clamp(2.5rem,5vw,4rem);line-height:.95;color:#1F2E2D;font-weight:1000;letter-spacing:-.04em}.gamex-service-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:22px;margin-top:36px}.gamex-service-card{position:relative;min-height:210px;background:#fff;border-radius:20px;padding:22px;text-decoration:none;color:#1F2E2D;box-shadow:0 20px 60px rgba(42,61,60,.10);border:1px solid #F1F1F1;overflow:hidden;transition:.25s}.gamex-service-card:hover{transform:translateY(-8px);box-shadow:0 32px 80px rgba(42,61,60,.15)}.gamex-service-card:before{content:'';position:absolute;right:-44px;top:-50px;width:150px;height:150px;border-radius:42px;background:linear-gradient(135deg,var(--mini-accent,#4BBFB0),var(--mini-accent-2,#2fa99c));opacity:.18;transform:rotate(20deg)}.gamex-service-icon{width:74px;height:74px;border-radius:20px;background:#FFF8E6;display:flex;align-items:center;justify-content:center;font-size:2.5rem;box-shadow:0 14px 28px rgba(42,61,60,.08);position:relative;z-index:2}.gamex-service-icon img{width:100%;height:100%;object-fit:cover;border-radius:inherit}.gamex-service-card em{display:inline-flex;margin-top:14px;color:#E8756A;font-style:normal;text-transform:uppercase;font-size:10px;font-weight:1000;letter-spacing:.14em}.gamex-service-card h3{font-size:1.15rem;font-weight:1000;line-height:1.2;margin-top:8px;position:relative;z-index:2}.gamex-service-card p{color:#6B8A88;font-weight:800;line-height:1.45;font-size:.9rem;margin-top:8px;position:relative;z-index:2}.gamex-service-card>b{position:absolute;left:22px;bottom:18px;width:34px;height:34px;border-radius:50%;background:#FFD617;color:#222;display:flex;align-items:center;justify-content:center;font-weight:1000;box-shadow:0 7px 0 #CA9B00}.gamex-maintenance{opacity:.65;filter:saturate(.7);cursor:not-allowed}
.gamex-teal{--mini-accent:#4BBFB0;--mini-accent-2:#2fa99c}.gamex-purple{--mini-accent:#9B7BFF;--mini-accent-2:#7457E8}.gamex-orange{--mini-accent:#FF9B55;--mini-accent-2:#E8756A}.gamex-green{--mini-accent:#45C879;--mini-accent-2:#2FAB61}.gamex-blue{--mini-accent:#5EA8FF;--mini-accent-2:#3F83E6}.gamex-pink{--mini-accent:#FF80B5;--mini-accent-2:#E85D96}
.gamex-how-section{position:relative;overflow:hidden;padding:88px 0;background:linear-gradient(135deg,#FF8FA3 0%,#F7A8C8 34%,#9FE7DF 100%);color:#fff}.gamex-how-section:before{content:'☁';position:absolute;left:36%;top:60px;font-size:9rem;color:rgba(255,255,255,.10)}.gamex-how-grid{display:grid;grid-template-columns:230px minmax(0,1fr) 300px;gap:28px;align-items:center}.gamex-how-grid h2{font-family:var(--font-display,inherit);font-size:2.5rem;line-height:1;color:#fff;font-weight:1000}.gamex-tabs{margin-top:28px;background:rgba(255,255,255,.08);border-radius:24px;padding:18px;display:grid;gap:12px}.gamex-tabs button{border:1px solid rgba(255,255,255,.45);background:transparent;color:#fff;border-radius:10px;padding:11px;font-weight:900}.gamex-how-card{background:#fff;color:#1F2E2D;border-radius:16px;overflow:hidden;padding:34px 38px;box-shadow:0 28px 70px rgba(42,61,60,.16)}.gamex-how-card small{color:#FFB800;font-weight:1000}.gamex-how-card h3{font-family:var(--font-display,inherit);font-size:2.4rem;line-height:1.04;font-weight:1000;margin-top:8px}.gamex-how-card p{color:#6B8A88;font-weight:850;line-height:1.6;margin-top:12px}.gamex-how-card a{display:inline-flex;margin-top:22px;background:#FFD617;color:#222;border-radius:8px;text-decoration:none;font-weight:1000;padding:14px 24px}.gamex-how-mascot{font-size:12rem;filter:drop-shadow(0 30px 34px rgba(0,0,0,.20));animation:miniFloat 3s ease-in-out infinite}
.gamex-bottom-section{padding:78px 0;background:#fff}.gamex-bottom-grid{display:grid;grid-template-columns:minmax(0,1fr) 380px;gap:28px}.gamex-leaderboard-card,.gamex-reward-box{background:#fff;border:1px solid #E9F2F0;border-radius:24px;box-shadow:0 18px 55px rgba(42,61,60,.08);overflow:hidden}.gamex-leaderboard-head{padding:22px 24px;background:linear-gradient(135deg,#EEFFFB,#fff);border-bottom:1px solid #D4EEEC}.gamex-leaderboard-head span{color:#E8756A;font-size:11px;font-weight:1000;text-transform:uppercase;letter-spacing:.14em}.gamex-leaderboard-head h3{font-size:1.45rem;font-weight:1000;color:#2A3D3C;margin-top:5px}.gamex-leaderboard-list{padding:12px}.gamex-rank-item{display:grid;grid-template-columns:34px 44px 1fr;align-items:center;gap:10px;padding:10px;border-radius:18px}.gamex-rank-item.is-winner{background:#FFF8E6}.gamex-rank-number{text-align:center;font-weight:1000;color:#6B8A88}.gamex-rank-avatar{width:44px;height:44px;border-radius:50%;display:flex;align-items:center;justify-content:center;background:#F4F7F6;overflow:hidden;border:2px solid rgba(255,255,255,.78)}.gamex-rank-avatar.is-empty{box-shadow:inset 0 0 0 1px #DCE8E6}.gamex-rank-avatar img{width:100%;height:100%;object-fit:cover}.gamex-rank-info b{display:block;color:#2A3D3C}.gamex-rank-info span{display:block;color:#6B8A88;font-weight:900;font-size:12px}.gamex-empty-rank{text-align:center;color:#6B8A88;font-weight:900;padding:22px}.gamex-reward-box{padding:34px;text-align:center;background:radial-gradient(circle at 60% 14%,rgba(255,214,23,.22),transparent 8rem),#fff}.gamex-reward-box>div{font-size:4rem}.gamex-reward-box h3{font-family:var(--font-display,inherit);font-size:2rem;color:#1F2E2D;font-weight:1000}.gamex-reward-box p{color:#6B8A88;font-weight:850;line-height:1.65;margin:10px 0 18px}.gamex-reward-box a{display:inline-flex;background:linear-gradient(135deg,#FF8FA3 0%,#F7A8C8 34%,#9FE7DF 100%);color:#fff;text-decoration:none;border-radius:999px;padding:13px 20px;font-weight:1000}
@media(max-width:1100px){.gamex-hero-grid,.gamex-feature-grid,.gamex-how-grid,.gamex-bottom-grid{grid-template-columns:1fr}.gamex-service-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.gamex-mascot-card{min-height:420px}.gamex-how-mascot{display:none}}
@media(max-width:640px){.gamex-hero{min-height:auto;padding:52px 0 145px}.gamex-hero-copy h1{font-size:3.6rem}.gamex-mascot-head{width:250px;height:250px}.gamex-mascot-body{width:170px}.gamex-feature-area{margin-top:-110px}.gamex-trial-card{grid-template-columns:1fr}.gamex-service-grid{grid-template-columns:1fr}.gamex-season-card{border-radius:22px}.gamex-how-section{padding:60px 0}}

/* Perbaikan tone warna: dibuat gradasi lucu, bukan merah flat */
.gamex-hero{
    background:
        radial-gradient(circle at 18% 18%, rgba(255,255,255,.18), transparent 12rem),
        radial-gradient(circle at 82% 18%, rgba(255,244,168,.26), transparent 12rem),
        linear-gradient(135deg,#FF8FA3 0%,#F8B4D1 38%,#7EDDD4 100%) !important;
}
.gamex-season-card{
    background:linear-gradient(135deg,#FF8FA3 0%,#FFB3C7 48%,#65D0C3 100%) !important;
}
.gamex-how-section{
    background:
        radial-gradient(circle at 74% 12%, rgba(255,244,168,.20), transparent 13rem),
        linear-gradient(135deg,#FF8FA3 0%,#F8B4D1 42%,#65D0C3 100%) !important;
}
.gamex-hero-copy h1{
    text-shadow:0 18px 40px rgba(42,61,60,.14) !important;
}
.gamex-mascot-head{
    background:linear-gradient(180deg,#7DE2EE,#45BFD4) !important;
}
.gamex-mascot-body{
    background:linear-gradient(180deg,#FF9B65,#FF7A5D) !important;
}

/* Papan peringkat dan voucher ditarik ke atas setelah featured game */
.gamex-top-rank-reward{
    padding:28px 0 62px !important;
    background:#fff !important;
    margin-top:-22px;
}
.gamex-top-rank-reward .gamex-bottom-grid{
    align-items:stretch;
}
.gamex-top-rank-reward .gamex-leaderboard-card,
.gamex-top-rank-reward .gamex-reward-box{
    border-radius:28px;
    box-shadow:0 22px 65px rgba(42,61,60,.10);
}
.gamex-service-section{
    padding-top:58px !important;
}
@media(max-width:1100px){
    .gamex-top-rank-reward{
        margin-top:0;
    }
}


/* FIX: munculkan papan peringkat dan tukar voucher di area atas */
.gamex-top-rank-reward{
    display:block !important;
    position:relative;
    z-index:8;
    padding:34px 0 66px !important;
    background:#fff !important;
    margin-top:-18px;
}
.gamex-top-rank-reward .gamex-bottom-grid{
    display:grid !important;
    grid-template-columns:minmax(0,1fr) 380px;
    gap:28px;
    align-items:stretch;
}
.gamex-top-rank-reward .gamex-leaderboard-card,
.gamex-top-rank-reward .gamex-reward-box{
    display:block !important;
    border-radius:28px;
    box-shadow:0 22px 65px rgba(42,61,60,.10);
}
.gamex-top-rank-reward .gamex-reward-box{
    background:radial-gradient(circle at 60% 14%,rgba(255,214,23,.22),transparent 8rem),#fff !important;
}
.gamex-service-section{
    padding-top:58px !important;
}
@media(max-width:1100px){
    .gamex-top-rank-reward{margin-top:0}
    .gamex-top-rank-reward .gamex-bottom-grid{
        grid-template-columns:1fr !important;
    }
}


/* FIX CARD GAME: lebih lucu, icon besar, tombol plus kuning dihapus */
.gamex-service-grid{
    grid-template-columns:repeat(3,minmax(0,1fr)) !important;
    gap:26px !important;
}
.gamex-service-card{
    min-height:330px !important;
    padding:24px !important;
    border-radius:34px !important;
    background:
        radial-gradient(circle at 88% 13%, rgba(255,230,111,.35), transparent 5.5rem),
        linear-gradient(180deg,#FFFFFF 0%,#FFFDF9 100%) !important;
    border:2px solid rgba(212,238,236,.95) !important;
    box-shadow:0 22px 65px rgba(42,61,60,.10) !important;
    display:flex !important;
    flex-direction:column !important;
}
.gamex-service-card:before{
    right:-34px !important;
    top:-44px !important;
    width:190px !important;
    height:190px !important;
    border-radius:54px !important;
    opacity:.22 !important;
}
.gamex-service-card:after{
    content:'✨';
    position:absolute;
    right:20px;
    bottom:20px;
    font-size:28px;
    opacity:.18;
    pointer-events:none;
}
.gamex-service-card:hover{
    transform:translateY(-10px) rotate(-.6deg) !important;
    box-shadow:0 32px 85px rgba(42,61,60,.16) !important;
}
.gamex-service-icon{
    width:132px !important;
    height:132px !important;
    border-radius:34px !important;
    font-size:4.8rem !important;
    margin-bottom:14px !important;
    background:
        radial-gradient(circle at 30% 25%,rgba(255,255,255,.95),transparent 3rem),
        linear-gradient(135deg,#FFF8D7,#EEFFFB) !important;
    border:2px solid rgba(255,255,255,.85) !important;
    box-shadow:0 20px 42px rgba(42,61,60,.13) !important;
    transform:rotate(-4deg);
}
.gamex-service-card:nth-child(2n) .gamex-service-icon{
    transform:rotate(4deg);
}
.gamex-service-icon img{
    width:100% !important;
    height:100% !important;
    object-fit:cover !important;
    border-radius:inherit !important;
}
.gamex-service-icon span{
    font-size:4.8rem !important;
    line-height:1 !important;
}
.gamex-service-card em{
    margin-top:2px !important;
    width:max-content;
    max-width:100%;
    padding:7px 11px;
    border-radius:999px;
    background:#FFF1EE;
    color:#E8756A !important;
    font-size:10px !important;
    box-shadow:0 8px 20px rgba(232,117,106,.10);
}
.gamex-service-card h3{
    font-size:1.55rem !important;
    line-height:1.12 !important;
    margin-top:13px !important;
    color:#243D3B !important;
}
.gamex-service-card p{
    font-size:1rem !important;
    line-height:1.55 !important;
    color:#6B8A88 !important;
    margin-top:10px !important;
    flex:1 !important;
}
.gamex-service-card>b{
    display:none !important;
}
.gamex-play-cta{
    position:relative;
    z-index:3;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    width:100%;
    min-height:50px;
    margin-top:18px;
    border-radius:999px;
    background:linear-gradient(135deg,var(--mini-accent,#4BBFB0),var(--mini-accent-2,#2fa99c));
    color:#fff;
    font-weight:1000;
    box-shadow:0 16px 32px rgba(75,191,176,.22);
    transition:.22s ease;
}
.gamex-service-card:hover .gamex-play-cta{
    transform:translateY(-2px);
    box-shadow:0 20px 38px rgba(42,61,60,.18);
}
.gamex-play-disabled{
    background:linear-gradient(135deg,#CBDCD9,#AFC4C0) !important;
    color:#526B68 !important;
    box-shadow:none !important;
}
.gamex-maintenance{
    opacity:.82 !important;
}
.gamex-maintenance .gamex-service-icon{
    filter:grayscale(.2);
}
@media(max-width:1100px){
    .gamex-service-grid{
        grid-template-columns:repeat(2,minmax(0,1fr)) !important;
    }
}
@media(max-width:640px){
    .gamex-service-grid{
        grid-template-columns:1fr !important;
    }
    .gamex-service-card{
        min-height:300px !important;
    }
    .gamex-service-icon{
        width:116px !important;
        height:116px !important;
        font-size:4rem !important;
    }
}


/* FIX: tombol arahan Ayo Main di card game */
.gamex-service-card>b{
    display:none !important;
}
.gamex-play-cta{
    position:relative;
    z-index:5;
    display:inline-flex !important;
    align-items:center;
    justify-content:center;
    width:100%;
    min-height:52px;
    margin-top:18px;
    border-radius:999px;
    background:linear-gradient(135deg,var(--mini-accent,#4BBFB0),var(--mini-accent-2,#2fa99c));
    color:#fff;
    font-weight:1000;
    text-align:center;
    box-shadow:0 16px 32px rgba(75,191,176,.24);
    transition:.22s ease;
}
.gamex-service-card:hover .gamex-play-cta{
    transform:translateY(-2px);
    box-shadow:0 20px 40px rgba(42,61,60,.18);
}
.gamex-play-disabled{
    background:linear-gradient(135deg,#CBDCD9,#AFC4C0) !important;
    color:#526B68 !important;
    box-shadow:none !important;
}


/* FIX FINAL: tombol hero jadi LIHAT GAME dan tombol AYO MAIN muncul di card */
.gamex-btn-yellow{
    text-transform:uppercase;
}
.gamex-service-card>b{
    display:none !important;
}
.gamex-play-cta{
    position:relative;
    z-index:10;
    display:inline-flex !important;
    align-items:center;
    justify-content:center;
    width:100%;
    min-height:52px;
    margin-top:18px;
    border-radius:999px;
    background:linear-gradient(135deg,var(--mini-accent,#4BBFB0),var(--mini-accent-2,#2fa99c));
    color:#fff !important;
    font-weight:1000;
    text-align:center;
    text-decoration:none;
    box-shadow:0 16px 32px rgba(75,191,176,.24);
    transition:.22s ease;
}
.gamex-service-card:hover .gamex-play-cta{
    transform:translateY(-2px);
    box-shadow:0 20px 40px rgba(42,61,60,.18);
}
.gamex-play-disabled{
    background:linear-gradient(135deg,#CBDCD9,#AFC4C0) !important;
    color:#526B68 !important;
    box-shadow:none !important;
}


/* FIX: background area game dibuat tidak terlalu putih, lebih lucu dan soft */
.gamex-service-section{
    position:relative !important;
    overflow:hidden !important;
    background:
        radial-gradient(circle at 9% 18%, rgba(255, 182, 193, .28), transparent 18rem),
        radial-gradient(circle at 88% 22%, rgba(255, 230, 160, .30), transparent 17rem),
        radial-gradient(circle at 18% 86%, rgba(167, 223, 255, .30), transparent 20rem),
        radial-gradient(circle at 82% 88%, rgba(143, 231, 216, .28), transparent 19rem),
        linear-gradient(135deg,#FFF7FB 0%,#F4FFFC 48%,#FFF8EC 100%) !important;
}
.gamex-service-section:before{
    content:'🌸';
    position:absolute;
    left:5%;
    top:10%;
    font-size:54px;
    opacity:.28;
    transform:rotate(-12deg);
    pointer-events:none;
}
.gamex-service-section:after{
    content:'🌈';
    position:absolute;
    right:6%;
    top:13%;
    font-size:58px;
    opacity:.22;
    transform:rotate(8deg);
    pointer-events:none;
}
.gamex-service-head{
    position:relative;
    z-index:2;
}
.gamex-service-head:after{
    content:'☁️  ✨  🌸';
    display:block;
    margin-top:16px;
    color:#E8756A;
    font-size:28px;
    letter-spacing:.45em;
    opacity:.55;
}
.gamex-service-grid{
    position:relative;
    z-index:2;
}
.gamex-service-grid:before{
    content:'🧸';
    position:absolute;
    left:-70px;
    bottom:18%;
    font-size:76px;
    opacity:.12;
    transform:rotate(-10deg);
    pointer-events:none;
}
.gamex-service-grid:after{
    content:'🍭';
    position:absolute;
    right:-64px;
    bottom:8%;
    font-size:72px;
    opacity:.14;
    transform:rotate(12deg);
    pointer-events:none;
}
.gamex-top-rank-reward{
    background:
        radial-gradient(circle at 15% 20%, rgba(255, 244, 168, .25), transparent 15rem),
        radial-gradient(circle at 84% 70%, rgba(255, 182, 193, .22), transparent 16rem),
        linear-gradient(135deg,#F4FFFC 0%,#FFF9F1 100%) !important;
}
.gamex-top-rank-reward:before{
    content:'🌷';
    position:absolute;
    left:4%;
    top:20%;
    font-size:48px;
    opacity:.20;
    pointer-events:none;
}
.gamex-top-rank-reward:after{
    content:'⭐';
    position:absolute;
    right:4%;
    bottom:18%;
    font-size:46px;
    opacity:.18;
    pointer-events:none;
}
.gamex-leaderboard-card,
.gamex-reward-box{
    background:rgba(255,255,255,.82) !important;
    backdrop-filter:blur(12px);
}


/* FIX: section How It Works dihapus, lanjut langsung ke footer/section berikutnya */
.gamex-how-section{
    display:none !important;
}
.gamex-bottom-section,
.gamex-top-rank-reward{
    margin-bottom:0 !important;
}


/* Tambahan: game paling banyak dimainkan + filter umur/kesulitan */
.gamex-most-played-badge{
    display:inline-flex !important;
    align-items:center;
    width:max-content;
    max-width:100%;
    background:#FFF8D7;
    color:#E8756A !important;
    border:1px solid #FFE49B;
    border-radius:999px;
    padding:8px 12px;
    font-size:12px;
    letter-spacing:.08em;
    text-transform:uppercase;
}
.gamex-played-count{
    display:inline-flex;
    align-items:center;
    width:max-content;
    max-width:100%;
    margin-top:10px;
    border-radius:999px;
    background:#EEFFFB;
    border:1px solid #BFECE6;
    color:#2A3D3C;
    padding:8px 12px;
    font-weight:1000;
}
.gamex-service-sub{
    margin-top:12px;
    color:#6B8A88;
    font-weight:850;
    max-width:560px;
}
.gamex-game-filter{
    position:relative;
    z-index:3;
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:16px;
    margin:28px 0 28px;
}
.gamex-game-filter>div{
    background:rgba(255,255,255,.78);
    border:1px solid #D4EEEC;
    border-radius:24px;
    padding:14px;
    display:flex;
    flex-wrap:wrap;
    align-items:center;
    gap:9px;
    box-shadow:0 14px 38px rgba(42,61,60,.06);
    backdrop-filter:blur(12px);
}
.gamex-game-filter b{
    margin-right:8px;
    color:#2A3D3C;
    font-weight:1000;
}
.gamex-game-filter button{
    border:1px solid #D4EEEC;
    background:#fff;
    color:#6B8A88;
    border-radius:999px;
    padding:9px 13px;
    font-weight:1000;
    transition:.2s ease;
}
.gamex-game-filter button:hover,
.gamex-game-filter button.active{
    background:#4BBFB0;
    border-color:#4BBFB0;
    color:#fff;
    box-shadow:0 10px 24px rgba(75,191,176,.18);
}
.gamex-game-meta{
    position:relative;
    z-index:2;
    display:flex;
    flex-wrap:wrap;
    gap:7px;
    margin-top:10px;
}
.gamex-game-meta span{
    display:inline-flex;
    align-items:center;
    gap:4px;
    border-radius:999px;
    padding:6px 9px;
    background:#F7FFFD;
    border:1px solid #D4EEEC;
    color:#6B8A88;
    font-size:11px;
    font-weight:1000;
}
.gamex-game-empty{
    position:relative;
    z-index:2;
    text-align:center;
    margin-top:22px;
    padding:34px 20px;
    background:rgba(255,255,255,.80);
    border:1px dashed #BFECE6;
    border-radius:28px;
    color:#2A3D3C;
}
.gamex-game-empty div{
    font-size:3rem;
}
.gamex-game-empty h3{
    font-size:1.5rem;
    font-weight:1000;
    margin-top:8px;
}
.gamex-game-empty p{
    color:#6B8A88;
    font-weight:850;
    margin-top:4px;
}
@media(max-width:900px){
    .gamex-game-filter{
        grid-template-columns:1fr;
    }
}


/* FIX filter game lebih rapi, mudah dipakai, dan kategori sesuai game */
.gamex-filter-simple{
    grid-template-columns:1fr 1fr !important;
    gap:18px !important;
    margin:26px 0 12px !important;
}
.gamex-filter-simple .gamex-filter-card{
    display:grid !important;
    grid-template-columns:180px 1fr;
    gap:14px;
    align-items:center;
    padding:16px !important;
    border-radius:28px !important;
    background:rgba(255,255,255,.82) !important;
    border:1px solid #D4EEEC !important;
    box-shadow:0 16px 45px rgba(42,61,60,.07) !important;
}
.gamex-filter-title{
    display:grid;
    grid-template-columns:40px 1fr;
    column-gap:10px;
    align-items:center;
}
.gamex-filter-title span{
    width:40px;
    height:40px;
    border-radius:15px;
    background:#FFF8D7;
    display:flex;
    align-items:center;
    justify-content:center;
    box-shadow:0 10px 22px rgba(42,61,60,.06);
}
.gamex-filter-title b{
    color:#243D3B;
    font-weight:1000;
    line-height:1.1;
}
.gamex-filter-title small{
    grid-column:2;
    color:#7C9693;
    font-weight:850;
    font-size:11px;
    margin-top:2px;
}
.gamex-filter-buttons{
    display:flex;
    flex-wrap:wrap;
    gap:9px;
}
.gamex-filter-simple button{
    min-height:42px;
    border:1px solid #D4EEEC !important;
    background:#fff !important;
    color:#6B8A88 !important;
    border-radius:999px !important;
    padding:0 15px !important;
    font-weight:1000 !important;
    box-shadow:none !important;
}
.gamex-filter-simple button:hover,
.gamex-filter-simple button.active{
    background:linear-gradient(135deg,#4BBFB0,#62D4C7) !important;
    color:#fff !important;
    border-color:#4BBFB0 !important;
    box-shadow:0 12px 26px rgba(75,191,176,.20) !important;
}
.gamex-filter-info{
    position:relative;
    z-index:3;
    width:max-content;
    max-width:100%;
    margin:14px 0 28px;
    padding:12px 16px;
    border-radius:999px;
    background:#FFF8D7;
    border:1px solid #FFE49B;
    color:#6B5A18;
    font-weight:1000;
    box-shadow:0 12px 28px rgba(42,61,60,.06);
}
.gamex-game-meta span{
    background:#FFFFFF !important;
}
.gamex-service-card[data-difficulty="Sulit"] .gamex-game-meta span:nth-child(2){
    background:#FFF1F7 !important;
    border-color:#FFC7DD !important;
    color:#D84E86 !important;
}
.gamex-service-card[data-difficulty="Sedang"] .gamex-game-meta span:nth-child(2){
    background:#FFF8D7 !important;
    border-color:#FFE49B !important;
    color:#977800 !important;
}
.gamex-service-card[data-difficulty="Mudah"] .gamex-game-meta span:nth-child(2){
    background:#EFFFF7 !important;
    border-color:#BFEED2 !important;
    color:#2DAA62 !important;
}
.gamex-service-card.is-filtered-match{
    animation:gamexPop .24s ease both;
}
@keyframes gamexPop{
    from{opacity:.45;transform:translateY(8px) scale(.98)}
    to{opacity:1;transform:translateY(0) scale(1)}
}
@media(max-width:1100px){
    .gamex-filter-simple{
        grid-template-columns:1fr !important;
    }
}
@media(max-width:720px){
    .gamex-filter-simple .gamex-filter-card{
        grid-template-columns:1fr;
    }
    .gamex-filter-title small{
        grid-column:2;
    }
}


/* FIX FINAL filter benar-benar berfungsi + maintenance selalu paling belakang */
.gamex-service-card[hidden]{
    display:none !important;
}
.gamex-maintenance{
    order:99 !important;
}
.gamex-maintenance .gamex-play-cta{
    pointer-events:none !important;
}
.gamex-game-filter button.active{
    pointer-events:none;
}
.gamex-filter-info{
    display:inline-flex;
    align-items:center;
    min-height:44px;
}


/* HERO MINI GAME — DUO MASKOT SOLIS & SELENA */
.gamex-hero{
    min-height:680px !important;
    padding:70px 0 170px !important;
    background:
        radial-gradient(circle at 14% 20%,rgba(255,255,255,.22),transparent 11rem),
        radial-gradient(circle at 88% 18%,rgba(255,245,183,.30),transparent 13rem),
        linear-gradient(135deg,#ff91ad 0%,#f8b6d5 42%,#7edbd4 100%) !important;
}
.gamex-hero-grid{grid-template-columns:minmax(0,1fr) 570px !important;gap:34px !important}
.gamex-duo-stage{min-height:510px !important;isolation:isolate}
.gamex-duo-stage:before{
    content:'';position:absolute;inset:62px 38px 44px 48px;border-radius:46% 54% 44% 56%;
    background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.34);
    box-shadow:inset 0 0 50px rgba(255,255,255,.18),0 28px 70px rgba(82,78,140,.12);
    backdrop-filter:blur(3px);z-index:-3;animation:gamexBlob 7s ease-in-out infinite;
}
.gamex-soft-circle{position:absolute;border-radius:50%;background:rgba(255,255,255,.25);border:1px solid rgba(255,255,255,.30);z-index:-2}
.gamex-soft-circle-a{width:190px;height:190px;right:36px;top:52px;animation:gamexCircle 6s ease-in-out infinite}
.gamex-soft-circle-b{width:115px;height:115px;left:82px;bottom:72px;animation:gamexCircle 5s ease-in-out infinite reverse}
.gamex-orbit{position:absolute;border:2px dashed rgba(255,255,255,.30);border-radius:50%;z-index:-1;animation:gamexSpin 22s linear infinite}
.gamex-orbit-one{width:410px;height:410px;right:48px;top:48px}
.gamex-orbit-two{width:300px;height:300px;left:80px;top:118px;animation-direction:reverse;animation-duration:17s}
.gamex-hero-mascot{position:absolute;object-fit:contain;filter:drop-shadow(0 24px 22px rgba(63,49,104,.22));will-change:transform}
.gamex-selena{width:365px;right:-8px;top:8px;z-index:5;animation:gamexMascotSelena 3.6s ease-in-out infinite;transform-origin:50% 80%}
.gamex-solis{width:345px;left:-2px;bottom:6px;z-index:4;animation:gamexMascotSolis 4.1s ease-in-out infinite;transform-origin:50% 80%}
.gamex-console{position:absolute;right:12px;bottom:54px;width:88px;height:66px;border-radius:24px;background:linear-gradient(145deg,#7154bd,#4c3794);display:flex;align-items:center;justify-content:center;font-size:42px;box-shadow:0 18px 32px rgba(66,45,130,.25);transform:rotate(10deg);z-index:6;animation:gamexConsole 4s ease-in-out infinite}
.gamex-dpad{position:absolute;left:18px;top:116px;width:56px;height:56px;border-radius:18px;background:rgba(255,255,255,.84);display:flex;align-items:center;justify-content:center;color:#5d49a8;font-size:30px;font-weight:900;box-shadow:0 14px 28px rgba(61,79,123,.15);z-index:3;animation:gamexFloat 3.4s ease-in-out infinite}
.gamex-spark{position:absolute;color:#fff4a2;text-shadow:0 8px 18px rgba(104,80,120,.18);z-index:7;animation:gamexTwinkle 2.2s ease-in-out infinite}
.gamex-spark-a{font-size:34px;right:88px;top:32px}.gamex-spark-b{font-size:25px;left:96px;top:70px;animation-delay:.8s}
.gamex-mini-cloud{position:absolute;width:82px;height:28px;border-radius:999px;background:rgba(255,255,255,.58);filter:drop-shadow(0 10px 18px rgba(65,84,116,.08));z-index:1;animation:gamexCloudDrift 7s ease-in-out infinite}
.gamex-mini-cloud:before,.gamex-mini-cloud:after{content:'';position:absolute;border-radius:50%;background:inherit;bottom:0}
.gamex-mini-cloud:before{width:38px;height:38px;left:12px}.gamex-mini-cloud:after{width:48px;height:48px;right:8px}
.gamex-mini-cloud-a{right:18px;top:170px}.gamex-mini-cloud-b{left:16px;bottom:96px;transform:scale(.72);animation-delay:1.1s}
.gamex-duo-shadow{width:420px !important;height:42px !important;bottom:12px !important;background:rgba(75,64,112,.17) !important;filter:blur(13px) !important;z-index:-1}
@keyframes gamexMascotSelena{0%,100%{transform:translate(0,0) rotate(1deg)}25%{transform:translate(-7px,-11px) rotate(-1deg)}50%{transform:translate(3px,-20px) rotate(1.5deg)}75%{transform:translate(8px,-9px) rotate(-.5deg)}}
@keyframes gamexMascotSolis{0%,100%{transform:translate(0,0) rotate(-2deg)}25%{transform:translate(8px,-8px) rotate(1deg)}50%{transform:translate(-2px,-17px) rotate(-1deg)}75%{transform:translate(-8px,-7px) rotate(1.5deg)}}
@keyframes gamexConsole{0%,100%{transform:translateY(0) rotate(10deg)}50%{transform:translateY(-12px) rotate(5deg)}}
@keyframes gamexFloat{0%,100%{transform:translateY(0) rotate(-6deg)}50%{transform:translateY(-10px) rotate(4deg)}}
@keyframes gamexTwinkle{0%,100%{opacity:.55;transform:scale(.8) rotate(0)}50%{opacity:1;transform:scale(1.15) rotate(20deg)}}
@keyframes gamexCloudDrift{0%,100%{translate:0 0}50%{translate:16px -7px}}
@keyframes gamexCircle{0%,100%{transform:scale(1)}50%{transform:scale(1.08)}}
@keyframes gamexSpin{to{transform:rotate(360deg)}}
@keyframes gamexBlob{0%,100%{transform:rotate(-2deg) scale(1)}50%{transform:rotate(2deg) scale(1.025)}}
@media(max-width:1100px){
    .gamex-hero-grid{grid-template-columns:1fr !important}
    .gamex-duo-stage{max-width:620px;width:100%;margin:0 auto;min-height:480px !important}
}
@media(max-width:640px){
    .gamex-hero{padding:48px 0 135px !important}
    .gamex-duo-stage{min-height:350px !important;margin-top:14px}
    .gamex-duo-stage:before{inset:38px 6px 28px 10px}
    .gamex-selena{width:245px;right:-10px;top:0}
    .gamex-solis{width:225px;left:-8px;bottom:0}
    .gamex-orbit-one{width:285px;height:285px;right:8px;top:25px}
    .gamex-orbit-two{width:220px;height:220px;left:18px;top:76px}
    .gamex-console{width:64px;height:50px;font-size:31px;right:0;bottom:34px}
    .gamex-dpad{width:44px;height:44px;font-size:23px;left:2px;top:78px}
    .gamex-mini-cloud-a{right:0;top:124px}.gamex-mini-cloud-b{left:-8px;bottom:62px}
    .gamex-duo-shadow{width:280px !important}
}
@media(prefers-reduced-motion:reduce){
    .gamex-duo-stage *{animation:none !important}
}



/* Custom cursor kecil khusus kartu di menu Mini Game */
.gamex-menu-cursor{
    position:fixed;
    z-index:2147483647;
    width:42px;
    height:42px;
    object-fit:contain;
    pointer-events:none;
    user-select:none;
    opacity:0;
    transform:translate(7px,7px) scale(.78);
    transform-origin:center;
    filter:drop-shadow(0 5px 8px rgba(39,54,72,.22));
    transition:opacity .1s ease,transform .14s ease;
}
.gamex-menu-cursor.is-visible{
    opacity:1;
    transform:translate(7px,7px) scale(1);
}
.gamex-menu-cursor.is-clicked{
    transform:translate(7px,7px) scale(.82) rotate(-7deg);
}
.gamex-menu-cursor.is-error{
    width:46px;
    height:46px;
    animation:gamexCursorErrorShake .18s ease-in-out 2;
}
.gamex-hide-native-cursor,
.gamex-hide-native-cursor *{
    cursor:none !important;
}
@keyframes gamexCursorErrorShake{
    0%,100%{margin-left:0}
    50%{margin-left:6px}
}
@media(max-width:640px){
    .gamex-menu-cursor{width:36px;height:36px}
    .gamex-menu-cursor.is-error{width:40px;height:40px}
}

</style>
@endsection
