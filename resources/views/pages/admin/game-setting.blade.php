@extends('layouts.admin')
@section('title', 'Setting Game — Admin SobatAnak')
@section('page-title', 'Setting Game')

@section('admin-content')
<section class="game-setting-page">
    <div class="game-setting-wrap">
        <div class="game-setting-head">
            <div>
                <a href="{{ route('admin.games') }}" class="game-setting-back">← Kembali ke Game Center</a>
                <span class="game-setting-eyebrow">Pengaturan Mini Game</span>
                <h1>Setting <span>{{ $gameSetting->title }}</span></h1>
                <p>Atur informasi, status, poin, cover, dan path game tanpa mengubah coding atau gameplay game.</p>
            </div>

            @if($gameSetting->game_path)
                <a href="{{ url('/mini-games/'.$gameSetting->slug) }}" target="_blank" rel="noopener" class="game-setting-preview">
                    Lihat Game →
                </a>
            @endif
        </div>

        @if(session('success'))
            <div class="game-setting-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="game-setting-error">
                <strong>Data belum dapat disimpan.</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.games.update', $gameSetting) }}" method="POST" enctype="multipart/form-data" class="game-setting-form">
            @csrf
            @method('PATCH')

            <div class="game-setting-grid">
                <div class="game-setting-main">
                    <div class="setting-card">
                        <div class="setting-card-title">
                            <div>
                                <span>Informasi utama</span>
                                <h2>Identitas Game</h2>
                            </div>
                            <span class="game-slug">{{ $gameSetting->slug }}</span>
                        </div>

                        <div class="form-grid two-columns">
                            <label class="form-field">
                                <span>Nama game</span>
                                <input type="text" name="title" value="{{ old('title', $gameSetting->title) }}" maxlength="120" required>
                            </label>

                            <label class="form-field">
                                <span>Ikon / Emoji</span>
                                <input type="text" name="icon" value="{{ old('icon', $gameSetting->icon) }}" maxlength="16" required>
                            </label>

                            <label class="form-field">
                                <span>Warna tema</span>
                                <select name="color" required>
                                    @foreach(['teal' => 'Teal', 'purple' => 'Ungu', 'orange' => 'Oranye', 'green' => 'Hijau', 'blue' => 'Biru', 'pink' => 'Pink'] as $value => $label)
                                        <option value="{{ $value }}" @selected(old('color', $gameSetting->color) === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </label>

                            <label class="form-field">
                                <span>Urutan tampil</span>
                                <input type="number" name="sort_order" value="{{ old('sort_order', $gameSetting->sort_order) }}" min="1" max="99" required>
                            </label>
                        </div>

                        <label class="form-field">
                            <span>Deskripsi</span>
                            <textarea name="description" rows="4" maxlength="500" required>{{ old('description', $gameSetting->description) }}</textarea>
                        </label>

                        <label class="form-field">
                            <span>Path game</span>
                            <input type="text" name="game_path" value="{{ old('game_path', $gameSetting->game_path) }}" maxlength="255" placeholder="Contoh: /mini-games/memory-card">
                            <small>Biarkan kosong hanya apabila game belum memiliki halaman tujuan.</small>
                        </label>
                    </div>

                    <div class="setting-card">
                        <div class="setting-card-title">
                            <div>
                                <span>Reward permainan</span>
                                <h2>Pengaturan Poin</h2>
                            </div>
                        </div>

                        <div class="form-grid two-columns">
                            <label class="form-field">
                                <span>Poin per permainan</span>
                                <input type="number" name="points_per_play" value="{{ old('points_per_play', $gameSetting->points_per_play) }}" min="0" max="999" required>
                            </label>

                            <label class="form-field">
                                <span>Maksimal poin</span>
                                <input type="number" name="max_points" value="{{ old('max_points', $gameSetting->max_points) }}" min="0" max="9999" required>
                            </label>
                        </div>
                    </div>

                    @if($gameSetting->slug === 'puzzle-edukatif')
                        <div class="setting-card">
                            <div class="setting-card-title">
                                <div>
                                    <span>Khusus Puzzle Edukatif</span>
                                    <h2>Gambar Puzzle</h2>
                                </div>
                            </div>

                            <div class="puzzle-image-grid">
                                @for($i = 1; $i <= 3; $i++)
                                    @php($settingKey = 'puzzle_image_'.$i)
                                    @php($currentImage = $gameSetting->getSetting($settingKey))
                                    <label class="upload-box">
                                        <span>Gambar Puzzle {{ $i }}</span>
                                        @if($currentImage)
                                            <img src="{{ asset(ltrim($currentImage, '/')) }}" alt="Gambar Puzzle {{ $i }}">
                                        @else
                                            <div class="empty-upload">🧩</div>
                                        @endif
                                        <input type="file" name="{{ $settingKey }}" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                                        <small>JPG, PNG, atau WebP. Maksimal 4 MB.</small>
                                    </label>
                                @endfor
                            </div>
                        </div>
                    @endif
                </div>

                <aside class="game-setting-side">
                    <div class="setting-card sticky-card">
                        <div class="setting-card-title">
                            <div>
                                <span>Preview</span>
                                <h2>Cover Game</h2>
                            </div>
                        </div>

                        <div class="cover-preview color-{{ $gameSetting->color ?: 'teal' }}">
                            @if($gameSetting->cover_image)
                                <img src="{{ asset(ltrim($gameSetting->cover_image, '/')) }}" alt="{{ $gameSetting->title }}">
                            @else
                                <span>{{ $gameSetting->icon ?: '🎮' }}</span>
                            @endif
                        </div>

                        <label class="form-field upload-field">
                            <span>Ganti cover</span>
                            <input type="file" name="cover_image_file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                            <small>JPG, PNG, atau WebP. Maksimal 4 MB.</small>
                        </label>

                        <label class="active-toggle">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $gameSetting->is_active))>
                            <span class="toggle-ui"></span>
                            <span>
                                <b>Game aktif</b>
                                <small>Jika dimatikan, game ditampilkan sebagai maintenance.</small>
                            </span>
                        </label>

                        <button type="submit" class="save-game-setting">Simpan Perubahan</button>
                    </div>
                </aside>
            </div>
        </form>
    </div>
</section>

<style>
.game-setting-page{min-height:calc(100vh - 110px);padding:42px 0 72px;background:linear-gradient(135deg,#f7fffd 0%,#fff 48%,#fff5ee 100%)}
.game-setting-wrap{max-width:1180px;margin:auto;padding:0 24px}.game-setting-head{display:flex;justify-content:space-between;gap:22px;align-items:flex-end;margin-bottom:26px}.game-setting-back,.game-setting-preview{display:inline-flex;align-items:center;border:1px solid #d4eeec;border-radius:999px;background:#fff;color:#2a3d3c;text-decoration:none;font-weight:900;padding:12px 16px;box-shadow:0 12px 28px rgba(42,61,60,.06)}.game-setting-eyebrow{display:block;margin-top:18px;color:#e8756a;font-size:12px;text-transform:uppercase;letter-spacing:.18em;font-weight:1000}.game-setting-head h1{font-family:var(--font-display,inherit);font-size:clamp(2.4rem,5vw,4.5rem);line-height:1;color:#2a3d3c;font-weight:1000;letter-spacing:-.055em;margin-top:9px}.game-setting-head h1 span{color:#4bbfb0}.game-setting-head p{max-width:720px;color:#6b8a88;font-weight:850;line-height:1.65;margin-top:10px}.game-setting-success,.game-setting-error{border-radius:22px;padding:18px 20px;margin-bottom:18px;font-weight:850}.game-setting-success{background:#effdf8;border:1px solid #b9eee1;color:#247c70}.game-setting-error{background:#fff0ee;border:1px solid #f2bbb5;color:#a9433a}.game-setting-error ul{margin:8px 0 0 20px}.game-setting-grid{display:grid;grid-template-columns:minmax(0,1fr) 350px;gap:22px;align-items:start}.game-setting-main{display:grid;gap:22px}.setting-card{background:#fff;border:1px solid #d4eeec;border-radius:30px;padding:24px;box-shadow:0 20px 60px rgba(42,61,60,.07)}.setting-card-title{display:flex;align-items:flex-start;justify-content:space-between;gap:15px;margin-bottom:20px}.setting-card-title span{display:block;color:#e8756a;font-size:11px;text-transform:uppercase;letter-spacing:.14em;font-weight:1000}.setting-card-title h2{margin-top:4px;color:#2a3d3c;font-size:1.55rem;font-weight:1000}.setting-card-title .game-slug{background:#f7fffd;border:1px solid #d4eeec;color:#6b8a88;border-radius:999px;padding:8px 11px;text-transform:none;letter-spacing:0}.form-grid{display:grid;gap:16px}.two-columns{grid-template-columns:repeat(2,minmax(0,1fr))}.form-field{display:grid;gap:8px;margin-bottom:16px}.form-field>span,.upload-box>span{color:#2a3d3c;font-size:13px;font-weight:1000}.form-field input,.form-field select,.form-field textarea{width:100%;border:1px solid #d4eeec;border-radius:16px;background:#fbfffe;color:#2a3d3c;padding:13px 14px;font:inherit;font-weight:750;outline:none;transition:.18s}.form-field textarea{resize:vertical;min-height:110px}.form-field input:focus,.form-field select:focus,.form-field textarea:focus{border-color:#4bbfb0;box-shadow:0 0 0 4px rgba(75,191,176,.12)}.form-field small,.upload-box small{color:#789391;font-size:11px;font-weight:750;line-height:1.45}.cover-preview{height:220px;border-radius:24px;overflow:hidden;background:linear-gradient(135deg,#eafffb,#fff8ee);border:1px solid #d4eeec;display:grid;place-items:center;margin-bottom:18px}.cover-preview img{width:100%;height:100%;object-fit:cover}.cover-preview>span{font-size:5rem}.upload-field input{padding:10px}.active-toggle{display:flex;gap:13px;align-items:center;border:1px solid #d4eeec;background:#f7fffd;border-radius:18px;padding:14px;margin:5px 0 18px;cursor:pointer}.active-toggle>input[type=checkbox]{position:absolute;opacity:0;pointer-events:none}.toggle-ui{width:48px;height:27px;flex:none;border-radius:999px;background:#cbd9d7;position:relative;transition:.2s}.toggle-ui:after{content:"";position:absolute;width:21px;height:21px;left:3px;top:3px;border-radius:50%;background:#fff;box-shadow:0 3px 8px rgba(0,0,0,.16);transition:.2s}.active-toggle input[type=checkbox]:checked+.toggle-ui{background:#4bbfb0}.active-toggle input[type=checkbox]:checked+.toggle-ui:after{transform:translateX(21px)}.active-toggle b{display:block;color:#2a3d3c;font-size:13px}.active-toggle small{display:block;color:#789391;font-size:11px;line-height:1.35;margin-top:2px}.save-game-setting{width:100%;border:0;border-radius:999px;background:#4bbfb0;color:#fff;padding:14px 18px;font:inherit;font-weight:1000;cursor:pointer;box-shadow:0 16px 34px rgba(75,191,176,.24);transition:.18s}.save-game-setting:hover{transform:translateY(-2px)}.sticky-card{position:sticky;top:22px}.puzzle-image-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px}.upload-box{display:grid;gap:9px;padding:13px;border:1px solid #d4eeec;border-radius:20px;background:#fbfffe}.upload-box img,.empty-upload{width:100%;height:130px;object-fit:cover;border-radius:14px;background:#eff9f7}.empty-upload{display:grid;place-items:center;font-size:2.5rem}.upload-box input{width:100%;font-size:11px}
@media(max-width:960px){.game-setting-grid{grid-template-columns:1fr}.sticky-card{position:static}.game-setting-side{order:-1}.cover-preview{height:260px}}
@media(max-width:720px){.game-setting-page{padding-top:26px}.game-setting-head{display:block}.game-setting-preview{margin-top:14px}.two-columns,.puzzle-image-grid{grid-template-columns:1fr}.setting-card{padding:19px;border-radius:24px}.game-setting-wrap{padding:0 15px}}
</style>
@endsection
