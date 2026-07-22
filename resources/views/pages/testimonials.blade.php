@extends('layouts.app')
@section('title','Ulasan Website — SobatAnak')

@section('content')
<style>
.review-page{background:linear-gradient(135deg,#fff8ef 0%,#f7fffd 55%,#eefcf9 100%);min-height:75vh;padding:4.5rem 0 5rem}.review-shell{max-width:1180px;margin:auto;padding:0 1.5rem}.review-head{display:flex;justify-content:space-between;gap:2rem;align-items:flex-end;margin-bottom:2rem}.review-eyebrow{color:#ff7e78;font-weight:900;text-transform:uppercase;letter-spacing:.18em;font-size:.78rem}.review-title{font-family:'Fredoka',sans-serif;font-size:clamp(2.25rem,5vw,4.2rem);line-height:1;color:#2a3d3c;font-weight:900;margin:.7rem 0}.review-title span{color:#4bbfb0}.review-summary{color:#718987;font-weight:700}.review-form,.review-filter,.review-card{background:rgba(255,255,255,.92);border:1px solid rgba(75,191,176,.25);box-shadow:0 18px 45px rgba(42,61,60,.08);border-radius:28px}.review-form{padding:1.5rem;margin-bottom:1.5rem}.review-form-grid{display:grid;grid-template-columns:220px 1fr auto;gap:1rem;align-items:end}.review-label{display:block;color:#2a3d3c;font-weight:900;margin-bottom:.4rem}.review-input{width:100%;border:1px solid #cfe9e5;border-radius:16px;padding:.85rem 1rem;background:white;font-weight:700;outline:none}.review-input:focus{border-color:#4bbfb0;box-shadow:0 0 0 3px rgba(75,191,176,.15)}.review-btn{display:inline-flex;justify-content:center;align-items:center;border:0;border-radius:999px;padding:.85rem 1.35rem;background:#ff8f78;color:white;font-weight:900;cursor:pointer}.review-filter{display:flex;justify-content:space-between;gap:1rem;flex-wrap:wrap;padding:1rem 1.25rem;margin-bottom:1.5rem}.review-filter-group{display:flex;gap:.55rem;flex-wrap:wrap}.review-chip{border:1px solid #c9e9e4;border-radius:999px;padding:.55rem .9rem;color:#58716f;background:white;font-weight:900;font-size:.86rem}.review-chip.active{background:#4bbfb0;color:white;border-color:#4bbfb0}.review-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:1.25rem}.review-card{padding:1.45rem;display:flex;flex-direction:column;min-height:250px}.review-top{display:flex;justify-content:space-between;gap:1rem}.review-stars{color:#ffbf4b;font-size:1.25rem;letter-spacing:.05em}.review-like{border:1px solid #ffd0c8;background:#fff7f4;color:#ff7e78;border-radius:999px;padding:.4rem .75rem;font-weight:900;cursor:pointer}.review-like.liked{background:#ff8f78;color:white}.review-message{color:#607a78;font-weight:800;font-size:1.08rem;line-height:1.65;margin:1.2rem 0;flex:1}.review-user{display:flex;align-items:center;gap:.8rem}.review-avatar{width:48px;height:48px;border-radius:16px;object-fit:cover;background:linear-gradient(135deg,#4bbfb0,#ff9b80);display:grid;place-items:center;color:white;font-weight:900}.review-name{font-weight:900;color:#2a3d3c}.review-date{font-size:.86rem;color:#79908e;font-weight:800}.review-badge{display:inline-flex;width:max-content;border:1px solid #bde8e2;background:#effbf9;color:#319b8e;border-radius:999px;padding:.25rem .65rem;font-size:.7rem;font-weight:900;text-transform:uppercase;letter-spacing:.08em;margin-bottom:.8rem}.review-actions{display:flex;gap:.6rem;margin-top:1rem}.review-action{border:0;background:#effaf8;color:#319b8e;border-radius:999px;padding:.45rem .8rem;font-weight:900;cursor:pointer}.review-action.danger{background:#fff0ed;color:#e1665d}.review-edit{display:none;margin-top:1rem}.review-edit.open{display:block}.review-empty{text-align:center;padding:3rem;background:white;border-radius:28px;font-weight:900;color:#718987}.review-pagination{margin-top:2rem}.review-pagination nav>div:first-child{display:none}@media(max-width:850px){.review-head{align-items:flex-start;flex-direction:column}.review-form-grid{grid-template-columns:1fr}.review-grid{grid-template-columns:1fr}}
</style>

<section class="review-page">
    <div class="review-shell">
        <div class="review-head">
            <div>
                <span class="review-eyebrow">Ulasan Website</span>
                <h1 class="review-title">Apa Kata <span>Mereka</span></h1>
                <p class="review-summary">{{ $totalReviews }} ulasan · Rata-rata {{ number_format($averageRating,1) }}/5</p>
            </div>
            <a href="{{ route('home') }}" class="review-chip">← Kembali ke Beranda</a>
        </div>

        @if($errors->any())
            <div class="review-form" style="background:#fff1ee;color:#cf584f;font-weight:900">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('testimonials.store.user') }}" class="review-form">
            @csrf
            <div class="review-form-grid">
                <div>
                    <label class="review-label">Rating</label>
                    <select name="rating" class="review-input">
                        @for($i=5;$i>=1;$i--)
                            <option value="{{ $i }}" @selected(old('rating',5)==$i)>{{ str_repeat('★',$i) }}{{ str_repeat('☆',5-$i) }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="review-label">Ulasan kamu</label>
                    <textarea name="message" class="review-input" rows="2" maxlength="400" required placeholder="Ceritakan pengalaman menggunakan SobatAnak...">{{ old('message') }}</textarea>
                </div>
                <button class="review-btn" type="submit">Kirim Ulasan</button>
            </div>
        </form>

        <div class="review-filter">
            <div class="review-filter-group">
                <a class="review-chip {{ $activeSort==='liked'?'active':'' }}" href="{{ route('testimonials.index',['sort'=>'liked','rating'=>$activeRating]) }}">Paling Disukai</a>
                <a class="review-chip {{ $activeSort==='newest'?'active':'' }}" href="{{ route('testimonials.index',['sort'=>'newest','rating'=>$activeRating]) }}">Terbaru</a>
            </div>
            <div class="review-filter-group">
                <a class="review-chip {{ $activeRating==='all'?'active':'' }}" href="{{ route('testimonials.index',['sort'=>$activeSort,'rating'=>'all']) }}">Semua</a>
                @for($i=5;$i>=1;$i--)
                    <a class="review-chip {{ (string)$activeRating===(string)$i?'active':'' }}" href="{{ route('testimonials.index',['sort'=>$activeSort,'rating'=>$i]) }}">{{ $i }} ★ ({{ $ratingCounts[$i] ?? 0 }})</a>
                @endfor
            </div>
        </div>

        <div class="review-grid">
            @forelse($testimonials as $testimonial)
                @php
                    $testimonialUser = $testimonial->user ?? null;
                    $displayName = $testimonialUser?->name ?: ($testimonial->name ?: 'Pengguna SobatAnak');
                    $avatarPath = $testimonialUser?->avatar;
                    $avatarUrl = $avatarPath ? ((str_starts_with($avatarPath,'http://') || str_starts_with($avatarPath,'https://') || str_starts_with($avatarPath,'/')) ? $avatarPath : asset($avatarPath)) : null;
                    $isOwner = (int)$testimonial->user_id === (int)$authUser->id;
                    $displayLikes = max((int)($testimonial->likes_count ?? 0),(int)($testimonial->real_likes_count ?? 0));
                    $liked = in_array($testimonial->id,$likedTestimonialIds ?? []);
                @endphp
                <article class="review-card">
                    <div class="review-top">
                        <div class="review-stars">{{ str_repeat('★',(int)($testimonial->rating ?? 5)) }}{{ str_repeat('☆',5-(int)($testimonial->rating ?? 5)) }}</div>
                        <button type="button" class="review-like {{ $liked?'liked':'' }}" data-like-url="{{ route('testimonials.like',$testimonial) }}">♥ <span>{{ $displayLikes }}</span></button>
                    </div>
                    <p class="review-message">“{{ $testimonial->message }}”</p>
                    @if($testimonial->is_edited ?? false)<span class="review-badge">Edited</span>@endif
                    <div class="review-user">
                        @if($avatarUrl)
                            <img class="review-avatar" src="{{ $avatarUrl }}" alt="Foto profil {{ $displayName }}">
                        @else
                            <span class="review-avatar">{{ strtoupper(mb_substr($displayName,0,1)) }}</span>
                        @endif
                        <div><div class="review-name">{{ $displayName }}</div><div class="review-date">{{ optional($testimonial->created_at)->format('d M Y') }}</div></div>
                    </div>
                    @if($isOwner)
                        <div class="review-actions">
                            <button type="button" class="review-action" onclick="document.getElementById('edit-{{ $testimonial->id }}').classList.toggle('open')">Edit</button>
                            <form method="POST" action="{{ route('testimonials.destroy.user',$testimonial) }}" data-cute-confirm="Hapus ulasan website ini?">@csrf @method('DELETE')<button class="review-action danger" type="submit">Hapus</button></form>
                        </div>
                        <form id="edit-{{ $testimonial->id }}" class="review-edit" method="POST" action="{{ route('testimonials.update',$testimonial) }}">
                            @csrf @method('PATCH')
                            <select name="rating" class="review-input" style="margin-bottom:.7rem">@for($i=5;$i>=1;$i--)<option value="{{ $i }}" @selected((int)$testimonial->rating===$i)>{{ $i }} Bintang</option>@endfor</select>
                            <textarea name="message" class="review-input" rows="3" maxlength="400" required>{{ $testimonial->message }}</textarea>
                            <button class="review-btn" style="margin-top:.7rem" type="submit">Simpan Perubahan</button>
                        </form>
                    @endif
                </article>
            @empty
                <div class="review-empty">Belum ada ulasan pada filter ini.</div>
            @endforelse
        </div>
        <div class="review-pagination">{{ $testimonials->links() }}</div>
    </div>
</section>

<script>
document.querySelectorAll('[data-like-url]').forEach(function(button){
    button.addEventListener('click', async function(){
        try{
            const response=await fetch(button.dataset.likeUrl,{method:'POST',headers:{'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content,'Accept':'application/json'}});
            const data=await response.json();
            if(response.status===401 && data.redirect){ window.location.href=data.redirect; return; }
            if(!response.ok || !data.ok) return;
            button.classList.toggle('liked',data.liked);
            button.querySelector('span').textContent=data.likes_count;
        }catch(error){}
    });
});
</script>
@endsection
