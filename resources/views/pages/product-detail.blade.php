@extends('layouts.app')
@section('title', $product->name . ' — SobatAnak')
@section('content')

<div class="max-w-7xl mx-auto px-4 md:px-8 py-4">
    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-xs text-[#6B8A88] font-bold mb-5 flex-wrap">
        <a href="{{ route('home') }}" class="hover:text-teal">Home</a>
        <span>›</span>
        <a href="{{ route('products') }}" class="hover:text-teal">Produk</a>
        <span>›</span>
        <span class="hover:text-teal cursor-pointer" onclick="filterCategory('{{ $product->category }}')">{{ $product->category }}</span>
        <span>›</span>
        <span class="text-[#2A3D3C] line-clamp-1">{{ $product->name }}</span>
    </nav>

    {{-- Main Product Area --}}
    <div class="grid lg:grid-cols-[420px_1fr_300px] gap-6 items-start">

        {{-- Left: Gallery --}}
        <div class="pdp-gallery-wrap">
            @php
                $productGallery = collect($galleryImages ?? [$product->image])->filter()->unique()->values();
                $firstGalleryImage = $productGallery->first() ?: $product->image;
            @endphp
            <div class="pdp-main-img-wrap">
                <img id="pdp-main-img" src="{{ $firstGalleryImage }}" alt="{{ $product->name }}" class="pdp-main-img">
                @if($product->badge)
                    <span class="pdp-badge">{{ $product->badge }}</span>
                @endif
                @if((int)($product->stock ?? 0) <= 0)
                    <div class="pdp-sold-overlay"><span>Stok Habis</span></div>
                @endif
            </div>

            @if($productGallery->count() > 0)
            <div class="pdp-thumbs-row">
                @foreach($productGallery as $idx => $galleryImage)
                <button type="button" data-img="{{ $galleryImage }}" onclick="setPdpMainImage(this)"
                    class="pdp-thumb {{ $idx === 0 ? 'active' : '' }}" aria-label="Lihat foto produk {{ $idx + 1 }}">
                    <img src="{{ $galleryImage }}" alt="Foto produk {{ $idx + 1 }}">
                </button>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Middle: Product Info --}}
        <div class="pdp-info">
            <p class="text-xs text-[#6B8A88] font-black uppercase tracking-widest mb-1">{{ $product->category }}</p>
            <h1 class="font-display text-2xl md:text-3xl leading-tight">{{ $product->name }}</h1>

            {{-- Rating Row --}}
            <div class="flex items-center gap-3 mt-3 flex-wrap">
                <div class="flex items-center gap-1">
                    @for($i = 1; $i <= 5; $i++)
                        <span class="pdp-star {{ $i <= round($avgRating) ? 'filled' : '' }}">★</span>
                    @endfor
                </div>
                <span id="pdp-avg-rating" class="font-black text-[#2A3D3C]">{{ $avgRating }}</span>
                <span class="text-[#6B8A88] text-sm font-bold">(<span id="pdp-review-count">{{ $reviews->count() }}</span> ulasan)</span>
                <span class="pdp-dot">·</span>
                <span class="text-[#6B8A88] text-sm font-bold">{{ number_format($product->sold, 0, ',', '.') }}+ terjual</span>
            </div>

            {{-- Price --}}
            <div class="mt-4 mb-5">
                <div class="pdp-price">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                @if($product->badge && str_contains(strtolower($product->badge), 'diskon'))
                    <span class="pdp-orig-price">Rp {{ number_format($product->price * 1.2, 0, ',', '.') }}</span>
                @endif
            </div>

            <hr class="border-[#D4EEEC] mb-5">

            {{-- Stock Info --}}
            @php $stock = (int)($product->stock ?? 0); @endphp
            <div class="flex items-center gap-3 mb-4">
                <span class="text-sm font-black text-[#2A3D3C]">Stok:</span>
                @if($stock <= 0)
                    <span class="text-coral font-black text-sm">Habis</span>
                @elseif($stock <= 5)
                    <span class="text-yellow-600 font-black text-sm">Sisa {{ $stock }}</span>
                @else
                    <span class="text-teal font-black text-sm">Tersedia ({{ $stock }})</span>
                @endif
            </div>

            {{-- Quantity Stepper --}}
            @if($stock > 0)
            <div class="flex items-center gap-4 mb-5">
                <span class="text-sm font-black text-[#2A3D3C]">Jumlah:</span>
                <div class="pdp-stepper">
                    <button id="pdp-qty-minus" onclick="changeQty(-1)" class="pdp-stepper-btn">−</button>
                    <input id="pdp-qty" type="number" value="1" min="1" max="{{ $stock }}" readonly class="pdp-stepper-input">
                    <button id="pdp-qty-plus" onclick="changeQty(1)" class="pdp-stepper-btn">+</button>
                </div>
                <span class="text-xs text-[#6B8A88] font-bold">Stok: {{ $stock }}</span>
            </div>
            @endif

            {{-- Tabs: Detail / Spesifikasi --}}
            <div class="pdp-tabs mt-2">
                <button class="pdp-tab active" onclick="switchTab(this, 'tab-detail')">Detail Produk</button>
                <button class="pdp-tab" onclick="switchTab(this, 'tab-spec')">Spesifikasi</button>
            </div>

            <div id="tab-detail" class="pdp-tab-content active">
                <div class="pdp-detail-grid">
                    <div><span class="pdp-detail-label">Kondisi</span><span class="pdp-detail-val">Baru</span></div>
                    <div><span class="pdp-detail-label">Kategori</span><span class="pdp-detail-val">{{ $product->category }}</span></div>
                    <div><span class="pdp-detail-label">Min. Beli</span><span class="pdp-detail-val">1 Buah</span></div>
                    <div><span class="pdp-detail-label">Dikirim dari</span><span class="pdp-detail-val">Jakarta</span></div>
                </div>
                <p class="text-[#6B8A88] font-bold text-sm mt-3 leading-relaxed">
                    {{ $product->name }} adalah pilihan tepat dari kategori {{ $product->category }}. Produk berkualitas tinggi, aman untuk bayi dan anak, serta telah terjual lebih dari {{ number_format($product->sold, 0, ',', '.') }} unit. Dapatkan produk terbaik untuk si kecil dengan harga terjangkau di SobatAnak.
                </p>
            </div>
            <div id="tab-spec" class="pdp-tab-content hidden">
                <div class="pdp-detail-grid">
                    <div><span class="pdp-detail-label">Berat Satuan</span><span class="pdp-detail-val">200–500 g</span></div>
                    <div><span class="pdp-detail-label">SKU</span><span class="pdp-detail-val">SA-{{ str_pad($product->id, 4, '0', STR_PAD_LEFT) }}</span></div>
                    <div><span class="pdp-detail-label">Rating</span><span class="pdp-detail-val">⭐ {{ $product->rating }} / 5.0</span></div>
                    <div><span class="pdp-detail-label">Stok</span><span class="pdp-detail-val">{{ $stock }} unit</span></div>
                </div>
            </div>
        </div>

        {{-- Right: Purchase Panel --}}
        <div class="pdp-purchase-panel sticky top-[90px]">
            {{-- Shipping Address --}}
            <div class="pdp-shipping-box">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-black text-[#2A3D3C] uppercase tracking-wide">📦 Dikirim ke</span>
                    <button onclick="openAddressModal()" class="text-xs font-black text-teal hover:underline">
                        {{ $userAddress ? 'Ubah' : 'Pilih Alamat' }}
                    </button>
                </div>
                @if($userAddress)
                    <p class="text-sm font-bold text-[#2A3D3C]">{{ $userAddress->city }}, {{ $userAddress->province }}</p>
                    <p class="text-xs text-[#6B8A88] mt-0.5">Estimasi tiba: 2–4 hari kerja</p>
                    <p class="text-xs text-teal font-bold">Ongkir: Rp 15.000</p>
                @else
                    <p class="text-sm text-[#6B8A88] font-bold">Pilih alamat untuk cek ongkir</p>
                @endif
            </div>

            {{-- Subtotal --}}
            <div class="pdp-subtotal-box">
                <div class="flex justify-between items-center">
                    <span class="text-sm font-bold text-[#6B8A88]">Subtotal</span>
                    <span id="pdp-subtotal" class="font-black text-[#2A3D3C] text-lg">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                </div>
            </div>

            {{-- CTA Buttons --}}
            @if($stock > 0)
            <button id="pdp-btn-cart" onclick="pdpAddToCart()" class="pdp-btn-cart w-full">
                🛒 + Keranjang
            </button>
            <button id="pdp-btn-buy" onclick="pdpBuyNow()" class="pdp-btn-buy w-full mt-2">
                ⚡ Beli Langsung
            </button>
            @else
            <button disabled class="pdp-btn-cart w-full opacity-50 cursor-not-allowed">Stok Habis</button>
            @endif

            {{-- Wishlist/Share --}}
            <div class="flex gap-3 mt-3 justify-center">
                <button onclick="wishlistToggle(this)" class="pdp-action-btn" title="Wishlist">
                    <span>🤍</span> Wishlist
                </button>
                <button onclick="shareProduct()" class="pdp-action-btn" title="Share">
                    <span>🔗</span> Bagikan
                </button>
            </div>

            {{-- Store Info --}}
            <div class="pdp-store-box mt-3">
                <div class="flex items-center gap-3">
                    <div class="pdp-store-avatar">S</div>
                    <div>
                        <p class="font-black text-sm">SobatAnak Official</p>
                        <p class="text-xs text-[#6B8A88]">⭐ 4.9 · Jakarta</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Reviews Section --}}
    <div class="mt-10 scroll-mt-28" id="ulasan">
        <div class="pdp-review-header">
            <h2 class="font-display text-2xl">Ulasan <span class="text-teal">Pembeli</span></h2>
            <div class="flex items-center gap-4 mt-3">
                <div class="pdp-review-score">
                    <span id="pdp-review-score-big">{{ $avgRating }}</span>
                    <span class="pdp-review-score-max">/5</span>
                </div>
                <div>
                    <div class="flex items-center gap-1 mb-1">
                        @for($i = 1; $i <= 5; $i++)
                            <span class="pdp-star text-xl {{ $i <= round($avgRating) ? 'filled' : '' }}">★</span>
                        @endfor
                    </div>
                    <p class="text-sm text-[#6B8A88] font-bold">Berdasarkan {{ $reviews->count() }} ulasan</p>
                </div>
            </div>
        </div>

        {{-- Write Review --}}
        @if(session('user_id'))
            @if(empty($canReviewProduct))
            <div class="pdp-review-form-box mt-5 text-center">
                <div class="text-3xl mb-2">🛍️</div>
                <p class="text-[#2A3D3C] font-black">Ulasan hanya untuk pembeli.</p>
                <p class="text-xs text-[#6B8A88] font-bold mt-1">Kamu bisa memberi ulasan setelah checkout dan pembayaran produk ini berhasil.</p>
            </div>
            @elseif(!$userReview)
            <div class="pdp-review-form-box mt-5">
                <h3 class="font-display text-lg mb-3">Tulis Ulasan Anda</h3>
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-sm font-bold text-[#2A3D3C]">Rating:</span>
                    <div class="star-picker" id="star-picker">
                        @for($i = 1; $i <= 5; $i++)
                            <span data-star="{{ $i }}" onclick="setReviewStar({{ $i }})" class="star-pick-btn">★</span>
                        @endfor
                    </div>
                    <span id="star-label" class="text-xs text-[#6B8A88] font-bold ml-1">Pilih bintang</span>
                </div>
                <textarea id="review-body" rows="3" placeholder="Ceritakan pengalaman Anda dengan produk ini..."
                    class="w-full border border-[#D4EEEC] rounded-xl p-3 font-bold text-sm resize-none focus:outline-none focus:border-[#4BBFB0]"></textarea>
                <label class="pdp-review-image-picker mt-3" for="review-image">
                    <span class="pdp-upload-left">
                        <span class="pdp-upload-plus">+</span>
                        <span class="font-black">Upload foto produk</span>
                    </span>
                    <small>Opsional, maksimal 5 foto · JPG/PNG/WEBP 2MB/foto</small>
                    <input id="review-image" type="file" accept="image/jpeg,image/png,image/webp" multiple class="hidden" onchange="previewReviewImages(this, 'review-image-preview')">
                </label>
                <div id="review-image-preview" class="pdp-review-image-preview hidden"></div>
                <div class="flex justify-end mt-2">
                    <button onclick="submitReview({{ $product->id }})" class="btn-pill btn-teal text-sm py-2">
                        Kirim Ulasan
                    </button>
                </div>
            </div>
            @endif
        @else
            <div class="pdp-review-form-box mt-5 text-center">
                <p class="text-[#6B8A88] font-bold">
                    <a href="{{ route('login') }}" class="text-teal font-black hover:underline">Login</a> untuk memberikan ulasan
                </p>
            </div>
        @endif

        {{-- Review Filters: client-side, tidak reload halaman --}}
        <div class="pdp-review-filter-wrap mt-5" id="pdp-review-filter-wrap">
            <button type="button" data-filter="all" onclick="filterProductReviews('all', this)" class="pdp-review-filter active">Semua <b>{{ $reviewCount ?? $reviews->count() }}</b></button>
            <button type="button" data-filter="top" onclick="filterProductReviews('top', this)" class="pdp-review-filter">Top Comment <b>Like</b></button>
            <button type="button" data-filter="photo" onclick="filterProductReviews('photo', this)" class="pdp-review-filter">Ada Gambar <b>{{ $photoReviewCount ?? 0 }}</b></button>
            @for($star = 5; $star >= 1; $star--)
                <button type="button" data-filter="star-{{ $star }}" onclick="filterProductReviews('star-{{ $star }}', this)" class="pdp-review-filter">{{ $star }} ★ <b>{{ $starCounts[$star] ?? 0 }}</b></button>
            @endfor
        </div>

        {{-- Review List --}}
        <div id="pdp-reviews-list" class="mt-5 grid gap-4">
            @forelse($reviews as $r)
            @php
                $reviewUser = $r->user ?? null;
                $reviewDisplayName = trim((string)($reviewUser->name ?? 'Pengguna SobatAnak'));
                $reviewDisplayInitial = function_exists('mb_substr')
                    ? mb_strtoupper(mb_substr($reviewDisplayName, 0, 1))
                    : strtoupper(substr($reviewDisplayName, 0, 1));
                $reviewDisplayInitial = $reviewDisplayInitial ?: 'S';

                $reviewAvatarUrl = $reviewUser?->avatar_url;
                $reviewIsAdmin = strtolower((string)($reviewUser->role ?? 'user')) === 'admin';
                $reviewIsEdited = (bool)($r->is_edited ?? false);
                $reviewPhotos = collect($reviewImagesMap[$r->id] ?? [])->pluck('image')->filter();
                if(!empty($r->image)) { $reviewPhotos->prepend($r->image); }
                $reviewPhotos = $reviewPhotos->unique()->values();
            @endphp
            <div class="pdp-review-card" id="review-{{ $r->id }}" data-review-card data-rating="{{ (int) $r->rating }}" data-has-photo="{{ $reviewPhotos->count() ? 1 : 0 }}" data-likes="{{ (int)($r->likes_count ?? 0) }}">
                <div class="pdp-review-top">
                    <div class="pdp-review-userline">
                        <div class="pdp-review-avatar">
                            @if($reviewAvatarUrl)
                                <img src="{{ $reviewAvatarUrl }}" alt="{{ $reviewDisplayName }}">
                            @endif
                        </div>
                        <div class="pdp-review-meta">
                            <div class="pdp-review-name-row">
                                <span class="pdp-review-name">{{ $reviewDisplayName }}</span>
                                @if($reviewIsAdmin)<span class="admin-review-badge">Admin SobatAnak</span>@endif
                            </div>
                            <div class="pdp-review-date-row">
                                <span>{{ $r->created_at->format('d M Y') }}</span>
                                @if($reviewIsEdited)
                                    <span class="pdp-edited-badge">✦ Edited</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="pdp-review-stars" aria-label="Rating {{ (int) $r->rating }} dari 5">
                        @for($i = 1; $i <= 5; $i++)
                            <span class="{{ $i <= $r->rating ? 'is-active' : '' }}">★</span>
                        @endfor
                    </div>
                </div>
                <p class="pdp-review-body">{{ $r->body }}</p>
                @if($reviewPhotos->count())
                    <div class="pdp-review-photo-grid mt-3">
                        @foreach($reviewPhotos as $photo)
                            <button type="button" class="pdp-review-photo" onclick="openReviewPhoto('{{ asset($photo) }}')" data-existing-review-photo="{{ e($photo) }}">
                                <img src="{{ asset($photo) }}" alt="Foto ulasan {{ $reviewDisplayName }}" loading="lazy" onerror="this.closest('.pdp-review-photo')?.remove()">
                            </button>
                        @endforeach
                    </div>
                @endif

                <div class="pdp-review-footer">
                    <button type="button" onclick="toggleReviewLike({{ $r->id }}, this)" class="pdp-review-like-btn">
                        💕 Suka <b>{{ (int)($r->likes_count ?? 0) }}</b>
                    </button>
                    @if($authUser && $r->user_id == $authUser->id)
                        <div class="pdp-review-actions">
                            <button type="button" onclick="openEditReview()" class="pdp-review-action-link">✏️ Edit ulasan</button>
                            <button type="button" onclick="deleteReview({{ $product->id }})" class="pdp-review-action-link text-coral">🗑️ Hapus</button>
                        </div>
                    @endif
                </div>

                @if($authUser && $r->user_id == $authUser->id)
                <div id="my-review-tools" class="hidden mt-4 border-t border-[#D4EEEC] pt-4">
                    <h3 class="font-display text-lg mb-3">Edit Ulasan Anda</h3>
                    <div class="flex items-center gap-2 mb-3 flex-wrap">
                        <span class="text-sm font-bold text-[#2A3D3C]">Rating:</span>
                        <div class="star-picker" id="edit-star-picker">
                            @for($i = 1; $i <= 5; $i++)
                                <span data-star="{{ $i }}" onclick="setEditReviewStar({{ $i }})" class="star-pick-btn {{ $i <= $userReview->rating ? 'selected' : '' }}">★</span>
                            @endfor
                        </div>
                        <span id="edit-star-label" class="text-xs text-[#6B8A88] font-bold ml-1">{{ $userReview->rating }}/5</span>
                    </div>
                    <textarea id="edit-review-body" rows="3" placeholder="Edit pengalaman Anda dengan produk ini..."
                        class="w-full border border-[#D4EEEC] rounded-xl p-3 font-bold text-sm resize-none focus:outline-none focus:border-[#4BBFB0]">{{ $userReview->body }}</textarea>

                    @if($reviewPhotos->count())
                        <div class="pdp-existing-review-photos mt-3">
                            <div class="pdp-existing-review-head">
                                <span>Foto ulasan kamu</span>
                                <small>Pilih foto yang mau dihapus, lalu klik Simpan Edit.</small>
                            </div>
                            <div class="pdp-existing-review-grid">
                                @foreach($reviewPhotos as $photo)
                                    <label class="pdp-existing-review-photo" data-edit-photo-card>
                                        <input type="checkbox" value="{{ e($photo) }}" data-delete-review-image class="hidden">
                                        <img src="{{ asset($photo) }}" alt="Foto ulasan lama" loading="lazy" onerror="this.closest('[data-edit-photo-card]')?.remove()">
                                        <span class="pdp-delete-photo-pill">× Hapus</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <label class="pdp-review-image-picker mt-3" for="edit-review-image">
                        <span class="pdp-upload-left">
                            <span class="pdp-upload-plus">+</span>
                            <span class="font-black">Ganti / tambah foto produk</span>
                        </span>
                        <small>Opsional, tambah foto baru maksimal total 5 foto</small>
                        <input id="edit-review-image" type="file" accept="image/jpeg,image/png,image/webp" multiple class="hidden" onchange="previewReviewImages(this, 'edit-review-image-preview')">
                    </label>
                    <div id="edit-review-image-preview" class="pdp-review-image-preview hidden"></div>
                    <div class="flex justify-end gap-2 mt-3 flex-wrap">
                        <button type="button" onclick="cancelEditReview()" class="pdp-mini-btn pdp-mini-btn-soft">Batal</button>
                        <button type="button" onclick="updateReview({{ $product->id }})" class="pdp-mini-btn pdp-mini-btn-save">Simpan Edit</button>
                    </div>
                </div>
                @endif
            </div>
            @empty
            <div id="no-reviews-msg" class="text-center py-10 text-[#6B8A88] font-bold">
                <div class="text-4xl mb-2">💬</div>
                <p>Belum ada ulasan. Jadilah yang pertama!</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Related Products --}}
    @if($related->count())
    <div class="mt-12">
        <h2 class="font-display text-2xl mb-5">Produk <span class="text-teal">Serupa</span></h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
            @foreach($related as $rp)
            <a href="{{ route('product.show', $rp->id) }}" class="prod-card group">
                <div class="relative overflow-hidden prod-card-img-wrap">
                    <img src="{{ $rp->image }}" alt="{{ $rp->name }}" class="prod-card-img group-hover:scale-105 transition-transform duration-500">
                    @if($rp->badge)
                        <span class="prod-badge">{{ $rp->badge }}</span>
                    @endif
                </div>
                <div class="prod-card-body">
                    <h3 class="prod-card-name">{{ $rp->name }}</h3>
                    <div class="flex items-center gap-1 mt-1">
                        <span class="prod-star">★</span>
                        <span class="prod-rating-val">{{ $rp->rating }}</span>
                    </div>
                    <b class="prod-price mt-1 block">Rp {{ number_format($rp->price, 0, ',', '.') }}</b>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif
</div>

{{-- Review Photo Modal --}}
<div id="review-photo-modal" class="pdp-review-photo-modal hidden" onclick="closeReviewPhoto(event)">
    <div class="pdp-review-photo-box">
        <button type="button" onclick="closeReviewPhoto(event)" class="pdp-review-photo-close">×</button>
        <img id="review-photo-modal-img" src="" alt="Foto ulasan produk">
    </div>
</div>

{{-- Address Modal --}}
<div id="address-modal" class="pdp-modal-overlay hidden">
    <div class="pdp-modal-box">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-display text-xl">📍 Alamat Pengiriman</h3>
            <button onclick="closeAddressModal()" class="text-2xl text-[#6B8A88] hover:text-coral font-black leading-none">×</button>
        </div>
        <form id="address-form" onsubmit="saveAddress(event)">
            <div class="grid gap-3">
                <input name="recipient_name" value="{{ $userAddress?->recipient_name }}" placeholder="Nama Penerima *" required
                    class="auth-input">
                <input name="phone" value="{{ $userAddress?->phone }}" placeholder="No. HP Penerima *" required
                    class="auth-input">
                <textarea name="address" rows="2" placeholder="Alamat Lengkap (Jalan, No. Rumah, RT/RW) *" required
                    class="auth-input resize-none">{{ $userAddress?->address }}</textarea>
                <div class="grid grid-cols-2 gap-3">
                    <input name="city" value="{{ $userAddress?->city }}" placeholder="Kota/Kabupaten *" required
                        class="auth-input">
                    <input name="province" value="{{ $userAddress?->province }}" placeholder="Provinsi *" required
                        class="auth-input">
                </div>
                <input name="postal_code" value="{{ $userAddress?->postal_code }}" placeholder="Kode Pos"
                    class="auth-input">
            </div>
            <div class="flex gap-3 mt-5">
                <button type="button" onclick="closeAddressModal()" class="flex-1 btn-pill border border-[#D4EEEC] text-[#6B8A88] justify-center">Batal</button>
                <button type="submit" class="flex-1 btn-pill btn-teal justify-center">Simpan Alamat</button>
            </div>
        </form>
    </div>
</div>


<style>
.pdp-review-avatar{overflow:hidden;width:44px;height:44px;min-width:44px;border-radius:999px;background:linear-gradient(135deg,#DDF8F4,#FFF7EC);border:2px solid #D4EEEC;display:flex;align-items:center;justify-content:center;color:#2FA39B;font-weight:1000;box-shadow:0 10px 22px rgba(42,61,60,.08)}
.pdp-review-avatar img{width:100%;height:100%;border-radius:inherit;object-fit:cover;display:block}
.pdp-review-top{display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;margin-bottom:.8rem}.pdp-review-userline{display:flex;align-items:flex-start;gap:.85rem;min-width:0}.pdp-review-meta{min-width:0;padding-top:.05rem}.pdp-review-name-row{display:flex;align-items:center;gap:.5rem;flex-wrap:wrap;line-height:1.15}.pdp-review-name{font-size:.95rem;font-weight:1000;color:#2A3D3C}.pdp-review-date-row{display:flex;align-items:center;gap:.45rem;flex-wrap:wrap;margin-top:.38rem;color:#6B8A88;font-size:.78rem;font-weight:800}.pdp-review-stars{display:flex;align-items:center;gap:.08rem;white-space:nowrap;padding-top:.1rem}.pdp-review-stars span{font-size:.92rem;line-height:1;color:#D4EEEC}.pdp-review-stars span.is-active{color:#F7C34A;text-shadow:0 1px 0 rgba(154,91,0,.08)}.pdp-review-body{margin:.25rem 0 0;padding-left:calc(44px + .85rem);font-size:.92rem;line-height:1.75;color:#2A3D3C;font-weight:800}.pdp-review-footer{display:flex;align-items:center;justify-content:space-between;gap:.75rem;margin-top:1rem;padding-left:calc(44px + .85rem);flex-wrap:wrap}.pdp-review-actions{display:flex;align-items:center;justify-content:flex-end;gap:.55rem;flex-wrap:wrap;margin-left:auto}.admin-review-badge{display:inline-flex;align-items:center;width:max-content;border:1.5px solid #F7C34A;background:linear-gradient(135deg,#FFF7D9,#FFE7A6);color:#9A5B00;border-radius:999px;padding:.22rem .65rem;font-size:.66rem;font-weight:1000;text-transform:uppercase;letter-spacing:.08em;font-style:normal}.pdp-edited-badge{display:inline-flex;align-items:center;padding:.18rem .58rem;border-radius:999px;background:linear-gradient(135deg,#FFF0D7,#FFE2A8);border:1.5px solid #F7C34A;color:#B36A00;font-size:.68rem;font-weight:1000;letter-spacing:.04em;text-transform:uppercase;box-shadow:0 8px 20px rgba(247,195,74,.18)}

.pdp-review-filter-wrap{display:flex;gap:.65rem;overflow-x:auto;padding:.15rem .1rem .45rem;scrollbar-width:none}.pdp-review-filter-wrap::-webkit-scrollbar{display:none}.pdp-review-filter{display:inline-flex;align-items:center;gap:.45rem;white-space:nowrap;border:1.5px solid #D4EEEC;background:#FFFDF8;color:#6B8A88;border-radius:999px;padding:.72rem 1rem;font-weight:1000;font-size:.86rem;transition:.18s;text-decoration:none}.pdp-review-filter:hover{transform:translateY(-1px);border-color:#4BBFB0}.pdp-review-filter.active{background:linear-gradient(135deg,#4BBFB0,#2FA39B);border-color:#2FA39B;color:#fff;box-shadow:0 12px 26px rgba(47,163,155,.18)}.pdp-review-filter b{display:inline-flex;align-items:center;justify-content:center;min-width:1.55rem;height:1.55rem;border-radius:999px;background:#FDECEA;color:#E8756A;font-size:.75rem;padding:0 .35rem}.pdp-review-filter.active b{background:#fff;color:#E8756A}.pdp-review-image-picker{display:flex;align-items:center;justify-content:space-between;gap:.8rem;border:2px dashed #FFD2CC;background:linear-gradient(135deg,#FFF7EC,#F0FFFC);border-radius:18px;padding:.85rem 1rem;color:#2A3D3C;cursor:pointer;transition:.18s}.pdp-review-image-picker:hover{border-color:#FF8B78;transform:translateY(-1px);box-shadow:0 12px 28px rgba(255,139,120,.16)}.pdp-upload-left{display:flex;align-items:center;gap:.75rem;min-width:0}.pdp-upload-plus{width:38px;height:38px;min-width:38px;border-radius:14px;display:inline-flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#FF7D73,#FFB36B);color:#fff;font-size:1.55rem;font-weight:1000;line-height:1;box-shadow:0 10px 24px rgba(255,125,115,.28);transition:.18s}.pdp-review-image-picker:hover .pdp-upload-plus{transform:rotate(90deg) scale(1.05)}.pdp-review-image-picker small{color:#6B8A88;font-weight:800}.pdp-review-image-preview{margin-top:.75rem;display:grid;grid-template-columns:repeat(5,minmax(66px,92px));gap:.55rem}.pdp-review-image-preview.hidden{display:none}.pdp-review-image-preview img{width:100%;aspect-ratio:1/1;border-radius:18px;object-fit:cover;display:block;border:2px solid #D4EEEC;background:#fff;box-shadow:0 10px 22px rgba(42,61,60,.08)}.pdp-review-photo-grid{display:flex;gap:.65rem;flex-wrap:wrap}.pdp-review-photo{width:128px;height:128px;border:2px solid #D4EEEC;border-radius:24px;overflow:hidden;background:#fff;padding:0;display:block;box-shadow:0 14px 32px rgba(42,61,60,.08);transition:.18s}.pdp-review-photo:hover{transform:translateY(-2px);border-color:#4BBFB0}.pdp-review-photo img{width:100%;height:100%;object-fit:cover;display:block}.pdp-review-like-btn{display:inline-flex;align-items:center;gap:.4rem;border:1.5px solid #FFD2CC;background:#FFF4F2;color:#E8756A;border-radius:999px;padding:.45rem .85rem;font-size:.78rem;font-weight:1000;transition:.18s}.pdp-review-like-btn:hover,.pdp-review-like-btn.liked{background:#FDECEA;transform:translateY(-1px)}.pdp-review-photo-modal{position:fixed;inset:0;z-index:9999;background:rgba(42,61,60,.62);display:flex;align-items:center;justify-content:center;padding:1rem}.pdp-review-photo-modal.hidden{display:none}.pdp-review-photo-box{position:relative;max-width:min(92vw,720px);max-height:86vh;background:#fff;border-radius:28px;padding:.75rem;box-shadow:0 28px 80px rgba(0,0,0,.24)}.pdp-review-photo-box img{max-width:100%;max-height:78vh;display:block;border-radius:22px;object-fit:contain}.pdp-review-photo-close{position:absolute;right:-10px;top:-10px;width:38px;height:38px;border-radius:999px;background:#FF8B78;color:#fff;font-size:1.5rem;font-weight:1000;line-height:1;box-shadow:0 12px 28px rgba(255,139,120,.35)}.pdp-existing-review-photos{border:1.5px solid #D4EEEC;background:linear-gradient(135deg,#F7FFFD,#FFFDF8);border-radius:20px;padding:.9rem}.pdp-existing-review-head{display:flex;align-items:flex-start;justify-content:space-between;gap:.75rem;margin-bottom:.75rem}.pdp-existing-review-head span{font-weight:1000;color:#2A3D3C}.pdp-existing-review-head small{font-weight:800;color:#6B8A88;text-align:right}.pdp-existing-review-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(92px,112px));gap:.7rem}.pdp-existing-review-photo{position:relative;display:block;aspect-ratio:1/1;border:2px solid #D4EEEC;border-radius:22px;overflow:hidden;background:#fff;cursor:pointer;box-shadow:0 10px 24px rgba(42,61,60,.08);transition:.18s}.pdp-existing-review-photo:hover{transform:translateY(-2px);border-color:#FF8B78}.pdp-existing-review-photo img{width:100%;height:100%;object-fit:cover;display:block}.pdp-delete-photo-pill{position:absolute;left:.45rem;right:.45rem;bottom:.45rem;display:flex;align-items:center;justify-content:center;border-radius:999px;background:rgba(255,255,255,.94);color:#E8756A;font-size:.72rem;font-weight:1000;padding:.32rem .45rem;box-shadow:0 8px 18px rgba(42,61,60,.12);transition:.18s}.pdp-existing-review-photo.is-delete-selected{border-color:#FF7D73;box-shadow:0 0 0 4px rgba(255,125,115,.16),0 14px 30px rgba(255,125,115,.18);opacity:.76}.pdp-existing-review-photo.is-delete-selected:after{content:'Akan dihapus';position:absolute;inset:0;background:rgba(255,125,115,.28);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:1000;text-shadow:0 2px 8px rgba(42,61,60,.22)}.pdp-existing-review-photo.is-delete-selected .pdp-delete-photo-pill{background:#FF7D73;color:#fff}.pdp-review-card{overflow:hidden}.pdp-review-card .pdp-review-photo-grid{align-items:flex-start}.pdp-review-card .pdp-review-photo:empty{display:none}@media(max-width:640px){.pdp-review-top{flex-direction:column;gap:.65rem}.pdp-review-stars{padding-left:calc(44px + .85rem)}.pdp-review-body,.pdp-review-footer{padding-left:0}.pdp-review-footer{align-items:flex-start}.pdp-review-actions{width:100%;justify-content:flex-start;margin-left:0}.pdp-review-action-link{flex:1;justify-content:center;min-width:130px}.pdp-review-name{font-size:.9rem}.admin-review-badge,.pdp-edited-badge{font-size:.62rem}.pdp-review-image-picker{align-items:stretch;flex-direction:column}.pdp-upload-left{width:100%}.pdp-upload-plus{width:34px;height:34px;min-width:34px;font-size:1.35rem}.pdp-review-image-preview{grid-template-columns:repeat(3,minmax(74px,1fr))}.pdp-review-photo{width:96px;height:96px;border-radius:20px}.pdp-review-filter{padding:.62rem .85rem;font-size:.8rem}}

</style>
<script>
const PRODUCT_ID = {{ $product->id }};
const PRODUCT_PRICE = {{ $product->price }};
const MAX_STOCK = {{ $stock }};
let selectedRating = 0;
let editRating = {{ $userReview ? (int) $userReview->rating : 0 }};

document.addEventListener('DOMContentLoaded', () => {
    const params = new URLSearchParams(window.location.search);
    if (window.location.hash === '#ulasan' || params.get('review') === '1') {
        const reviewSection = document.getElementById('ulasan');
        if (reviewSection) {
            setTimeout(() => reviewSection.scrollIntoView({ behavior: 'smooth', block: 'start' }), 180);
        }
    }
});

// --- PRODUCT GALLERY ---
function setPdpMainImage(btn) {
    const mainImg = document.getElementById('pdp-main-img');
    if (!mainImg || !btn?.dataset?.img) return;
    mainImg.style.opacity = '0';
    setTimeout(() => {
        mainImg.src = btn.dataset.img;
        mainImg.style.opacity = '1';
    }, 120);
    document.querySelectorAll('.pdp-thumb').forEach(item => item.classList.remove('active'));
    btn.classList.add('active');
}

// --- QTY STEPPER ---
function changeQty(delta) {
    const input = document.getElementById('pdp-qty');
    if (!input) return;
    let val = parseInt(input.value) + delta;
    val = Math.max(1, Math.min(MAX_STOCK, val));
    input.value = val;
    updateSubtotal(val);
}
function updateSubtotal(qty) {
    const el = document.getElementById('pdp-subtotal');
    if (el) el.textContent = 'Rp ' + (PRODUCT_PRICE * qty).toLocaleString('id-ID');
}

// --- TAB SWITCHING ---
function switchTab(btn, tabId) {
    document.querySelectorAll('.pdp-tab').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.pdp-tab-content').forEach(t => t.classList.add('hidden'));
    btn.classList.add('active');
    document.getElementById(tabId)?.classList.remove('hidden');
    document.getElementById(tabId)?.classList.add('active');
}

// --- ADD TO CART ---
async function pdpAddToCart() {
    const qty = parseInt(document.getElementById('pdp-qty')?.value || 1);
    const btn = document.getElementById('pdp-btn-cart');
    btn.disabled = true;
    btn.textContent = 'Menambahkan...';

    // Add qty times (add one by one since API adds 1 per call)
    let success = false;
    for (let i = 0; i < qty; i++) {
        const res = await postJson('/cart/add', { product_id: PRODUCT_ID });
        if (!res) break;
        if (res.ok) {
            success = true;
            document.querySelectorAll('[data-cart-count]').forEach(e => e.textContent = res.cart_count);
        } else {
            if (res.redirect) { window.location.href = res.redirect; return; }
            showToast(res.message || 'Gagal menambah ke keranjang');
            break;
        }
    }

    if (success) {
        if (window.sobatCartFlyGreen) {
            window.sobatCartFlyGreen(btn);
        }
        showToast('🛒 Produk berhasil masuk keranjang!', 'success');
    }
    btn.textContent = '🛒 + Keranjang';
    btn.disabled = false;
}

// --- BUY NOW ---
async function pdpBuyNow() {
    const qty = parseInt(document.getElementById('pdp-qty')?.value || 1);
    const btn = document.getElementById('pdp-btn-buy');
    btn.disabled = true;
    btn.textContent = 'Memproses...';

    let success = false;
    for (let i = 0; i < qty; i++) {
        const res = await postJson('/cart/add', { product_id: PRODUCT_ID });
        if (!res) { btn.disabled = false; btn.textContent = '⚡ Beli Langsung'; return; }
        if (res.ok) {
            success = true;
            document.querySelectorAll('[data-cart-count]').forEach(e => e.textContent = res.cart_count);
        } else {
            if (res.redirect) { window.location.href = res.redirect; return; }
            showToast(res.message || 'Gagal'); break;
        }
    }

    btn.disabled = false;
    btn.textContent = '⚡ Beli Langsung';
    if (success) window.location.href = '/checkout';
}

// --- STAR RATING ---
function setReviewStar(n) {
    selectedRating = n;
    const labels = ['', 'Sangat Buruk', 'Buruk', 'Cukup', 'Bagus', 'Sangat Bagus'];
    const picker = document.getElementById('star-picker');
    if (picker) picker.querySelectorAll('[data-star]').forEach(s => {
        s.classList.toggle('selected', parseInt(s.dataset.star) <= n);
    });
    const lbl = document.getElementById('star-label');
    if (lbl) lbl.textContent = labels[n] || '';
}

// --- SUBMIT REVIEW ---
async function submitReview(productId) {
    if (!selectedRating) { showToast('Pilih rating bintang dulu'); return; }
    const body = document.getElementById('review-body')?.value?.trim();
    if (!body) { showToast('Tulis ulasan dulu ya'); return; }

    const formData = new FormData();
    formData.append('rating', selectedRating);
    formData.append('body', body);
    const images = Array.from(document.getElementById('review-image')?.files || []).slice(0, 5);
    images.forEach(file => formData.append('images[]', file));

    const res = await postForm(`/products/${productId}/reviews`, formData);
    if (!res) return;
    if (res.ok) {
        showToast('✅ ' + res.message, 'success');
        setTimeout(() => { window.location.href = window.location.pathname + '?review=1#ulasan'; }, 650);
    } else {
        if (res.redirect) { window.location.href = res.redirect; return; }
        showToast(res.message || 'Gagal menyimpan ulasan');
    }
}


// --- EDIT REVIEW ---
function openEditReview() {
    const tools = document.getElementById('my-review-tools');
    if (!tools) return;
    tools.classList.remove('hidden');
    setEditReviewStar(editRating || 5);
    tools.scrollIntoView({ behavior: 'smooth', block: 'center' });
    setTimeout(() => document.getElementById('edit-review-body')?.focus(), 350);
}
function cancelEditReview() {
    document.getElementById('my-review-tools')?.classList.add('hidden');
}
function setEditReviewStar(n) {
    editRating = n;
    const labels = ['', 'Sangat Buruk', 'Buruk', 'Cukup', 'Bagus', 'Sangat Bagus'];
    const picker = document.getElementById('edit-star-picker');
    if (picker) picker.querySelectorAll('[data-star]').forEach(s => {
        s.classList.toggle('selected', parseInt(s.dataset.star) <= n);
    });
    const lbl = document.getElementById('edit-star-label');
    if (lbl) lbl.textContent = `${n}/5 · ${labels[n] || ''}`;
}
async function updateReview(productId) {
    if (!editRating) { showToast('Pilih rating bintang dulu'); return; }
    const body = document.getElementById('edit-review-body')?.value?.trim();
    if (!body) { showToast('Tulis ulasan dulu ya'); return; }

    const formData = new FormData();
    formData.append('rating', editRating);
    formData.append('body', body);
    const images = Array.from(document.getElementById('edit-review-image')?.files || []).slice(0, 5);
    images.forEach(file => formData.append('images[]', file));
    document.querySelectorAll('[data-delete-review-image]:checked').forEach(input => {
        formData.append('delete_review_images[]', input.value);
    });

    const res = await postForm(`/products/${productId}/reviews`, formData);
    if (!res) return;
    if (res.ok) {
        showToast('✅ ' + res.message, 'success');
        setTimeout(() => location.reload(), 700);
    } else {
        if (res.redirect) { window.location.href = res.redirect; return; }
        showToast(res.message || 'Gagal mengedit ulasan');
    }
}

// --- DELETE REVIEW ---
async function deleteReview(productId) {
    const ok = await window.sobatCuteConfirm({
        title: 'Hapus ulasan produk?',
        detail: 'Ulasan kamu akan dihapus permanen dari produk ini.',
        confirmText: 'Ya, hapus',
        cancelText: 'Batal',
        emoji: '🧹'
    });
    if (!ok) return;
    const res = await postJson(`/products/${productId}/reviews`, {}, 'DELETE');
    if (res?.ok) {
        showToast('Ulasan dihapus', 'success');
        location.reload();
    }
}

// --- ADDRESS MODAL ---
function openAddressModal() {
    document.getElementById('address-modal')?.classList.remove('hidden');
    document.body.classList.add('modal-lock');
}
function closeAddressModal() {
    document.getElementById('address-modal')?.classList.add('hidden');
    document.body.classList.remove('modal-lock');
}
async function saveAddress(e) {
    e.preventDefault();
    const form = document.getElementById('address-form');
    const data = Object.fromEntries(new FormData(form));
    const res = await postJson('/address', data);
    if (res?.ok) {
        showToast('📍 ' + res.message, 'success');
        closeAddressModal();
        setTimeout(() => location.reload(), 800);
    } else {
        showToast(res?.message || 'Gagal menyimpan alamat');
    }
}

// --- WISHLIST & SHARE ---
function wishlistToggle(btn) {
    btn.querySelector('span').textContent = btn.querySelector('span').textContent === '🤍' ? '❤️' : '🤍';
    showToast(btn.querySelector('span').textContent === '❤️' ? '❤️ Ditambahkan ke wishlist' : 'Dihapus dari wishlist', 'success');
}
function shareProduct() {
    if (navigator.share) {
        navigator.share({ title: '{{ addslashes($product->name) }}', url: window.location.href });
    } else {
        navigator.clipboard?.writeText(window.location.href);
        showToast('🔗 Link berhasil disalin!', 'success');
    }
}


function previewReviewImages(input, targetId) {
    const box = document.getElementById(targetId);
    const files = Array.from(input?.files || []);
    if (!box || !files.length) return;
    if (files.length > 5) {
        showToast('Maksimal 5 foto ulasan ya');
        input.value = '';
        box.classList.add('hidden');
        box.innerHTML = '';
        return;
    }
    box.innerHTML = files.map((file, index) => `<img src="${URL.createObjectURL(file)}" alt="Preview foto ulasan ${index + 1}">`).join('');
    box.classList.remove('hidden');
}
function openReviewPhoto(src) {
    const modal = document.getElementById('review-photo-modal');
    const img = document.getElementById('review-photo-modal-img');
    if (!modal || !img) return;
    img.src = src;
    modal.classList.remove('hidden');
    document.body.classList.add('modal-lock');
}
function closeReviewPhoto(event) {
    if (event) event.stopPropagation();
    document.getElementById('review-photo-modal')?.classList.add('hidden');
    document.body.classList.remove('modal-lock');
}
async function toggleReviewLike(reviewId, btn) {
    const res = await postJson(`/product-reviews/${reviewId}/like`, {});
    if (!res) return;
    if (res.ok) {
        btn?.classList.toggle('liked', !!res.liked);
        const count = btn?.querySelector('b');
        if (count) count.textContent = res.likes_count;
        const card = btn?.closest('[data-review-card]');
        if (card) card.dataset.likes = res.likes_count;
    } else {
        if (res.redirect) { window.location.href = res.redirect; return; }
        showToast(res.message || 'Gagal menyukai ulasan');
    }
}


document.addEventListener('change', (event) => {
    const input = event.target.closest('[data-delete-review-image]');
    if (!input) return;
    const card = input.closest('[data-edit-photo-card]');
    if (card) card.classList.toggle('is-delete-selected', input.checked);
});

// --- FILTER REVIEW TANPA RELOAD ---
function filterProductReviews(filter, btn = null) {
    const list = document.getElementById('pdp-reviews-list');
    if (!list) return;
    const cards = Array.from(list.querySelectorAll('[data-review-card]'));
    const empty = document.getElementById('no-reviews-msg');

    document.querySelectorAll('#pdp-review-filter-wrap .pdp-review-filter').forEach(el => {
        el.classList.toggle('active', el.dataset.filter === filter);
    });

    let visibleCount = 0;
    cards.forEach(card => {
        const rating = parseInt(card.dataset.rating || '0');
        const hasPhoto = card.dataset.hasPhoto === '1';
        let show = true;
        if (filter === 'photo') show = hasPhoto;
        if (filter.startsWith('star-')) show = rating === parseInt(filter.replace('star-', ''));
        card.classList.toggle('hidden', !show);
        if (show) visibleCount++;
    });

    if (filter === 'top') {
        cards.sort((a, b) => parseInt(b.dataset.likes || '0') - parseInt(a.dataset.likes || '0'));
        cards.forEach(card => list.appendChild(card));
    } else {
        cards.sort((a, b) => parseInt(a.dataset.originalOrder || '0') - parseInt(b.dataset.originalOrder || '0'));
        cards.forEach(card => list.appendChild(card));
    }

    if (empty) {
        empty.classList.toggle('hidden', visibleCount > 0);
        empty.querySelector('p').textContent = visibleCount > 0 ? '' : 'Belum ada ulasan sesuai filter ini.';
    }

    const url = new URL(window.location.href);
    url.searchParams.set('review', '1');
    url.searchParams.set('review_filter', filter);
    history.replaceState(null, '', url.pathname + '?' + url.searchParams.toString() + '#ulasan');
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-review-card]').forEach((card, index) => {
        card.dataset.originalOrder = index;
    });
    const initialFilter = new URLSearchParams(window.location.search).get('review_filter') || 'all';
    const initialBtn = document.querySelector(`#pdp-review-filter-wrap .pdp-review-filter[data-filter="${initialFilter}"]`);
    if (initialBtn) filterProductReviews(initialFilter, initialBtn);
});

// --- HELPERS ---
async function postJson(url, data = {}, method = 'POST') {
    try {
        const res = await fetch(url, {
            method,
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
            body: JSON.stringify(data)
        });
        return await res.json();
    } catch { return null; }
}
async function postForm(url, formData, method = 'POST') {
    try {
        const res = await fetch(url, {
            method,
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
            body: formData
        });
        return await res.json();
    } catch { return null; }
}
function showToast(msg, type = 'error') {
    const existing = document.querySelector('.pdp-toast');
    if (existing) existing.remove();
    const el = document.createElement('div');
    el.className = 'pdp-toast ' + (type === 'success' ? 'pdp-toast-success' : 'pdp-toast-error');
    el.textContent = msg;
    document.body.appendChild(el);
    setTimeout(() => el.classList.add('show'), 10);
    setTimeout(() => { el.classList.remove('show'); setTimeout(() => el.remove(), 300); }, 2500);
}

// Close address modal on backdrop click
document.getElementById('address-modal')?.addEventListener('click', function(e) {
    if (e.target === this) closeAddressModal();
});
</script>
@endsection
