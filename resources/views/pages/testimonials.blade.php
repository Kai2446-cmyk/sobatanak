@extends('layouts.admin')@section('title','Admin Testimonial — SobatAnak')
@section('page-title','Ulasan')
@section('breadcrumb','Kelola ulasan/testimonial website')@section('admin-content')
<section class="max-w-7xl mx-auto px-6 md:px-12 py-10"><div class="mb-6"><span class="text-coral font-black uppercase tracking-widest text-xs">Admin CRUD</span><h1 class="font-display section-title">Kelola <span class="text-teal">Testimonial</span></h1></div><div class="grid lg:grid-cols-2 gap-6"><div class="card p-6"><h2 class="font-display text-3xl mb-4">Tambah Testimonial</h2><form method="POST" action="{{ route('admin.testimonials.store') }}" class="grid gap-4">@csrf<input name="name" class="auth-input" placeholder="Nama" required><textarea name="message" class="auth-input min-h-[150px]" placeholder="Isi testimonial" required></textarea><button class="btn-pill btn-coral w-fit">Tambah</button></form></div><div class="card p-6"><h2 class="font-display text-3xl mb-4">Data Testimonial</h2>
        <div class="admin-testimonial-toolbar">
            <div class="admin-testimonial-filter-group">
                <span>Urutkan</span>
                <a href="{{ route('admin.testimonials', ['sort' => 'liked', 'rating' => $rating ?? 'all']) }}"
                   class="{{ ($sort ?? 'liked') === 'liked' ? 'active' : '' }}">❤️ Like terbanyak</a>
                <a href="{{ route('admin.testimonials', ['sort' => 'newest', 'rating' => $rating ?? 'all']) }}"
                   class="{{ ($sort ?? 'liked') === 'newest' ? 'active' : '' }}">Terbaru</a>
            </div>

            <div class="admin-testimonial-filter-group">
                <span>Rating</span>
                <a href="{{ route('admin.testimonials', ['sort' => $sort ?? 'liked', 'rating' => 'all']) }}"
                   class="rating-filter-all {{ ($rating ?? 'all') === 'all' ? 'active' : '' }}">Semua Rating</a>
                @for($i = 5; $i >= 1; $i--)
                    <a href="{{ route('admin.testimonials', ['sort' => $sort ?? 'liked', 'rating' => $i]) }}"
                       class="rating-filter-pill {{ (string)($rating ?? 'all') === (string)$i ? 'active' : '' }}">
                        <span class="rating-filter-stars" aria-hidden="true">
                            @for($star = 1; $star <= 5; $star++)
                                <i class="{{ $star <= $i ? 'filled' : '' }}">★</i>
                            @endfor
                        </span>
                        <b>{{ $i }}</b>
                        @if(isset($ratingCounts))
                            <small>{{ $ratingCounts[$i] ?? 0 }}</small>
                        @endif
                    </a>
                @endfor
            </div>
        </div>
@foreach($testimonials as $testimonial)<div class="admin-list">@php
    $testimonialUser = $testimonial->user ?? null;
    $avatarUrl = $testimonialUser?->avatar_url;
@endphp
<span class="profile-avatar admin-testimonial-avatar">
    @if($avatarUrl)
        <img src="{{ $avatarUrl }}" alt="Foto profil {{ $displayName }}">
    @endif
</span>
<div class="flex-1">
    <b>{{ $displayName }}</b>
    <p>{{ $testimonial->message }}</p>
    <div class="admin-testimonial-meta">
        <span>⭐ {{ $testimonial->rating ?? 5 }}/5</span>
        <span>❤️ {{ $testimonial->real_likes_count ?? $testimonial->likes_count ?? 0 }} like</span>
    </div>
</div><form method="POST" action="{{ route('admin.testimonials.destroy',$testimonial) }}">@csrf @method('DELETE')<button type="submit" class="btn-pill btn-coral text-xs py-2" data-confirm="Hapus ulasan ini dari halaman admin?">Hapus</button></form></div>@endforeach
@if($testimonials->count() === 0)
    <div class="admin-testimonial-empty">
        <b>Belum ada ulasan pada filter ini.</b>
        <p>Coba pilih rating lain atau kembali ke Semua.</p>
    </div>
@endif
</div></div></section>

<style>
/* FIX admin ulasan: tampilkan foto profile user */
.admin-testimonial-avatar{
    width:52px !important;
    height:52px !important;
    min-width:52px !important;
    overflow:hidden !important;
    padding:0 !important;
    display:flex !important;
    align-items:center !important;
    justify-content:center !important;
    border-radius:999px !important;
    background:linear-gradient(135deg,#4BBFB0,#E8756A) !important;
    color:#fff !important;
    font-weight:1000 !important;
}
.admin-testimonial-avatar img{
    width:100% !important;
    height:100% !important;
    object-fit:cover !important;
    display:block !important;
}
</style>


<style>
/* FIX admin ulasan: filter like terbanyak dan rating */
.admin-testimonial-toolbar{
    display:grid;
    gap:12px;
    margin:0 0 18px;
    padding:14px;
    border:1px solid #D4EEEC;
    background:#F8FFFD;
    border-radius:22px;
}
.admin-testimonial-filter-group{
    display:flex;
    flex-wrap:wrap;
    align-items:center;
    gap:8px;
}
.admin-testimonial-filter-group span{
    color:#2A3D3C;
    font-weight:1000;
    margin-right:4px;
}
.admin-testimonial-filter-group a{
    display:inline-flex;
    align-items:center;
    gap:5px;
    min-height:36px;
    padding:0 13px;
    border-radius:999px;
    border:1px solid #D4EEEC;
    background:#fff;
    color:#6B8A88;
    font-weight:1000;
    text-decoration:none;
    transition:.2s ease;
}
.admin-testimonial-filter-group a:hover,
.admin-testimonial-filter-group a.active{
    background:#4BBFB0;
    border-color:#4BBFB0;
    color:#fff;
    box-shadow:0 10px 24px rgba(75,191,176,.18);
}
.admin-testimonial-filter-group a small{
    opacity:.85;
    font-size:.75rem;
}
.admin-testimonial-meta{
    display:flex;
    flex-wrap:wrap;
    gap:7px;
    margin-top:8px;
}
.admin-testimonial-meta span{
    display:inline-flex;
    align-items:center;
    min-height:28px;
    padding:0 10px;
    border-radius:999px;
    background:#FFF8D7;
    color:#6B5A18;
    font-weight:1000;
    font-size:.78rem;
}
.admin-testimonial-meta span + span{
    background:#FDECEA;
    color:#E8756A;
}
.admin-testimonial-empty{
    text-align:center;
    padding:24px;
    border:1px dashed #D4EEEC;
    border-radius:20px;
    color:#6B8A88;
    font-weight:900;
}
.admin-testimonial-empty b{
    display:block;
    color:#2A3D3C;
    font-size:1.05rem;
}
</style>


<style>
/* FIX admin ulasan: filter rating bintang lebih cantik dan table/list lebih rapi */
.admin-testimonial-toolbar{
    gap:16px !important;
    padding:18px !important;
    border-radius:26px !important;
    background:linear-gradient(135deg,rgba(248,255,253,.96),rgba(255,250,245,.92)) !important;
}
.admin-testimonial-filter-group{
    gap:10px !important;
    padding:4px 0 !important;
}
.admin-testimonial-filter-group > span{
    min-width:86px;
    font-size:1.05rem;
}
.admin-testimonial-filter-group a{
    min-height:42px !important;
    padding:0 15px !important;
}
.admin-testimonial-filter-group a.rating-filter-all{
    padding:0 18px !important;
}
.rating-filter-pill{
    gap:8px !important;
    padding:0 12px !important;
}
.rating-filter-stars{
    display:inline-flex;
    align-items:center;
    gap:1px;
    letter-spacing:-2px;
    font-size:.92rem;
    line-height:1;
}
.rating-filter-stars i{
    font-style:normal;
    color:#D7E6E3;
    text-shadow:none;
}
.rating-filter-stars i.filled{
    color:#FFC247;
    text-shadow:0 2px 7px rgba(255,194,71,.22);
}
.rating-filter-pill b{
    font-size:.92rem;
    color:inherit;
    font-weight:1000;
}
.rating-filter-pill small{
    min-width:22px;
    height:22px;
    border-radius:999px;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    background:#EEF9F6;
    color:#6B8A88;
    font-size:.72rem !important;
    font-weight:1000;
    letter-spacing:0;
    opacity:1 !important;
}
.admin-testimonial-filter-group a.active .rating-filter-stars i{
    color:rgba(255,255,255,.42);
}
.admin-testimonial-filter-group a.active .rating-filter-stars i.filled{
    color:#FFE08A;
    text-shadow:0 2px 8px rgba(255,224,138,.28);
}
.admin-testimonial-filter-group a.active small{
    background:rgba(255,255,255,.24);
    color:#fff;
}
.card:has(.admin-testimonial-toolbar) .flex.items-center.gap-4{
    padding:16px 0 !important;
    align-items:center !important;
}
.card:has(.admin-testimonial-toolbar) .flex.items-center.gap-4:not(:last-child){
    border-bottom:1px solid #D4EEEC !important;
}
.admin-testimonial-avatar{
    box-shadow:0 10px 22px rgba(42,61,60,.08) !important;
}
.admin-testimonial-meta{
    margin-top:9px !important;
}
.admin-testimonial-meta span{
    min-height:30px !important;
}
@media(max-width:768px){
    .admin-testimonial-filter-group{
        align-items:flex-start !important;
    }
    .admin-testimonial-filter-group > span{
        width:100%;
        min-width:0;
    }
    .rating-filter-stars{
        font-size:.82rem;
    }
}
</style>

@endsection
