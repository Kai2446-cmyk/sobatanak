@php
    $mode = $mode ?? 'create';
    $article = $article ?? new \App\Models\Post();
    $categories = $categories ?? collect();
    $isCreate = $mode === 'create';
@endphp

@extends('layouts.admin')
@section('title', ($isCreate ? 'Tambah' : 'Edit').' Artikel — Admin SobatAnak')
@section('page-title', ($isCreate ? 'Tambah' : 'Edit').' Artikel')

@section('admin-content')
<style>
    .admin-article-page{
        background:
            radial-gradient(circle at 12% 8%, rgba(75,191,176,.18), transparent 28%),
            radial-gradient(circle at 92% 12%, rgba(232,117,106,.16), transparent 26%),
            linear-gradient(180deg,#FAFCFC 0%,#F6FBFA 100%);
        min-height: calc(100vh - 100px);
        padding: 34px 0 56px;
    }
    .admin-article-wrap{width:min(1180px, calc(100% - 40px));margin:0 auto;}
    .admin-article-nav{display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap;margin-bottom:22px;}
    .admin-article-breadcrumb{display:flex;gap:10px;align-items:center;flex-wrap:wrap;}
    .admin-article-back{
        display:inline-flex;align-items:center;gap:8px;border:1px solid #D4EEEC;background:#fff;color:#2A3D3C;
        padding:11px 18px;border-radius:999px;font-weight:900;box-shadow:0 12px 28px rgba(42,61,60,.06);transition:.2s;
    }
    .admin-article-back:hover{transform:translateY(-2px);border-color:#4BBFB0;background:#F3FFFD;}
    .admin-article-hero{
        position:relative;overflow:hidden;border:1px solid rgba(212,238,236,.9);border-radius:30px;padding:30px;
        background:linear-gradient(135deg,rgba(255,255,255,.96),rgba(240,252,250,.96));box-shadow:0 24px 70px rgba(42,61,60,.08);
        margin-bottom:22px;
    }
    .admin-article-hero:after{content:"";position:absolute;right:-60px;top:-75px;width:230px;height:230px;border-radius:999px;background:rgba(232,117,106,.13);}
    .admin-article-kicker{display:inline-flex;align-items:center;gap:8px;color:#E8756A;font-size:.76rem;font-weight:1000;letter-spacing:.13em;text-transform:uppercase;position:relative;z-index:1;}
    .admin-article-title{font-size:clamp(2.2rem,5vw,4.6rem);line-height:.98;font-weight:1000;color:#2A3D3C;margin-top:10px;position:relative;z-index:1;letter-spacing:-.04em;}
    .admin-article-title span{color:#4BBFB0;}
    .admin-article-desc{max-width:780px;color:#5E7977;font-weight:800;margin-top:14px;position:relative;z-index:1;}
    .admin-article-shell{display:grid;grid-template-columns:minmax(0,1fr) 350px;gap:22px;align-items:start;}
    .admin-article-card{background:rgba(255,255,255,.98);border:1px solid #D4EEEC;border-radius:28px;padding:24px;box-shadow:0 20px 55px rgba(42,61,60,.07);}
    .admin-article-card-head{display:flex;align-items:flex-start;justify-content:space-between;gap:14px;margin-bottom:20px;padding-bottom:18px;border-bottom:1px dashed #D4EEEC;}
    .admin-article-card-head small{color:#E8756A;text-transform:uppercase;letter-spacing:.12em;font-weight:1000;font-size:.7rem;}
    .admin-article-card-head h2{font-size:1.35rem;font-weight:1000;color:#2A3D3C;margin-top:2px;}
    .admin-form-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
    .admin-form-field{display:flex;flex-direction:column;gap:8px;margin-bottom:16px;color:#2A3D3C;font-weight:1000;}
    .admin-form-field.full{grid-column:1/-1;}
    .admin-form-field small{font-weight:800;color:#6B8A88;line-height:1.45;}
    .admin-form-field input,.admin-form-field select,.admin-form-field textarea{
        width:100%;border:1.5px solid #D4EEEC;background:#FAFCFC;border-radius:18px;padding:14px 16px;font-weight:850;color:#2A3D3C;outline:none;transition:.2s;
    }
    .admin-form-field textarea{min-height:300px;resize:vertical;line-height:1.65;}
    .article-hidden-textarea{display:none!important;}
    .article-doc-editor{
        width:100%;min-height:430px;max-height:680px;overflow:auto;border:1.5px solid #D4EEEC;background:#fff;border-radius:20px;
        padding:22px 24px;color:#2A3D3C;outline:none;line-height:1.78;font-weight:750;box-shadow:inset 0 1px 0 rgba(255,255,255,.9);
    }
    .article-doc-editor:focus{border-color:#4BBFB0;box-shadow:0 0 0 5px rgba(75,191,176,.13);}
    .article-doc-editor:empty:before{content:attr(data-placeholder);color:#9AAEAC;font-weight:800;}
    .article-doc-editor h2{font-size:1.45rem;line-height:1.28;margin:1.35rem 0 .65rem;font-weight:1000;color:#2A3D3C;}
    .article-doc-editor p{margin:.75rem 0;}
    .article-doc-editor strong{font-weight:1000;color:#203635;}
    .article-doc-editor a{color:#159FE8;text-decoration:underline;text-underline-offset:3px;font-weight:900;}
    .article-doc-editor ul{padding-left:1.35rem;margin:.75rem 0;list-style:disc;}
    .article-doc-editor li{margin:.3rem 0;}
    .article-doc-editor .cover-caption-chip{display:block;margin:.9rem 0;padding:12px 14px;border:1px dashed #F2C48C;background:#FFF8E8;border-radius:16px;color:#8B6C2D;font-weight:850;font-style:italic;}
    .article-doc-editor .read-also-chip{display:block;margin:1rem 0;padding:12px 14px;border-left:4px solid #4BBFB0;background:#F3FFFD;border-radius:14px;font-weight:900;color:#2A3D3C;}
    .admin-form-field input:focus,.admin-form-field select:focus,.admin-form-field textarea:focus{border-color:#4BBFB0;box-shadow:0 0 0 5px rgba(75,191,176,.13);background:#fff;}
    .admin-form-field input::placeholder,.admin-form-field textarea::placeholder{color:#9AAEAC;font-weight:800;}
    .admin-side-card{position:sticky;top:18px;}
    .admin-image-box{border:2px dashed #D4EEEC;background:linear-gradient(135deg,#F7FFFE,#fff);border-radius:24px;min-height:245px;display:flex;align-items:center;justify-content:center;overflow:hidden;text-align:center;color:#6B8A88;font-weight:900;margin-bottom:16px;}
    .admin-image-box img{width:100%;height:245px;object-fit:cover;display:block;}
    .admin-image-empty{padding:22px;}
    .admin-image-empty b{display:block;color:#2A3D3C;font-size:1.15rem;margin-bottom:6px;}
    .admin-file-input{background:#fff!important;cursor:pointer;}
    .admin-file-input::file-selector-button{border:0;background:#4BBFB0;color:#fff;border-radius:999px;padding:10px 14px;font-weight:1000;margin-right:12px;cursor:pointer;}
    .admin-submit-btn{width:100%;border:0;background:#E8756A;color:#fff;border-radius:999px;padding:16px 20px;font-weight:1000;font-size:1.02rem;box-shadow:0 16px 34px rgba(232,117,106,.24);transition:.2s;}
    .admin-submit-btn:hover{background:#D05A50;transform:translateY(-2px);}
    .admin-note{font-weight:850;color:#6B8A88;line-height:1.5;margin-top:14px;text-align:center;}
    .admin-alert-error{background:#FFF1F1;border:1px solid rgba(232,117,106,.45);color:#B94138;border-radius:22px;padding:14px 18px;font-weight:1000;margin-bottom:18px;}

    .article-import-box{border:1.5px dashed #BFE9E5;background:linear-gradient(135deg,#F5FFFE,#FFF8EE);border-radius:24px;padding:18px;margin-bottom:18px;display:grid;gap:12px;}
    .article-import-head{display:flex;gap:12px;align-items:flex-start;}
    .article-import-icon{width:44px;height:44px;border-radius:16px;background:#FFF1C9;display:grid;place-items:center;font-size:1.35rem;box-shadow:0 10px 24px rgba(42,61,60,.07);}
    .article-import-title{font-weight:1000;color:#2A3D3C;font-size:1.05rem;margin-bottom:3px;}
    .article-import-text{font-weight:800;color:#6B8A88;font-size:.88rem;line-height:1.45;}
    .article-import-actions{display:grid;grid-template-columns:1fr auto;gap:10px;align-items:center;}
    .article-import-input{width:100%;border:1.5px solid #D4EEEC;background:#fff;border-radius:999px;padding:10px 12px;font-weight:850;color:#2A3D3C;}
    .article-import-input::file-selector-button{border:0;background:#4BBFB0;color:#fff;border-radius:999px;padding:9px 14px;font-weight:1000;margin-right:10px;cursor:pointer;}
    .article-import-btn{border:0;background:#4BBFB0;color:#fff;border-radius:999px;padding:13px 18px;font-weight:1000;box-shadow:0 12px 24px rgba(75,191,176,.2);transition:.2s;white-space:nowrap;}
    .article-import-btn:hover{transform:translateY(-1px);background:#3DAFA1;}
    .article-import-btn:disabled{opacity:.65;cursor:not-allowed;transform:none;}
    .article-import-message{display:none;border-radius:18px;padding:11px 13px;font-weight:900;font-size:.86rem;line-height:1.45;}
    .article-import-message.ok{display:block;background:#ECFFFA;color:#2E8C7F;border:1px solid #BFE9E5;}
    .article-import-message.warn{display:block;background:#FFF8DF;color:#9D6B12;border:1px solid #F4DB8F;}
    .article-import-message.err{display:block;background:#FFF1F1;color:#B94138;border:1px solid rgba(232,117,106,.35);}
    .article-block-help{border:1px solid #D4EEEC;background:#FFFFFF;border-radius:22px;padding:16px;margin-bottom:14px;}
    .article-block-help h3{font-size:1rem;font-weight:1000;color:#2A3D3C;margin-bottom:5px;}
    .article-block-help p{color:#6B8A88;font-weight:800;font-size:.88rem;line-height:1.5;margin:0;}
    .sobat-block-editor{position:relative;border:1.5px solid #D4EEEC;background:#fff;border-radius:22px;overflow:hidden;}
    .sobat-block-editor-head{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:14px 16px;border-bottom:1px solid #E4F4F2;background:linear-gradient(135deg,#F8FFFE,#FFFDF8);}
    .sobat-block-title{display:flex;align-items:center;gap:9px;color:#2A3D3C;font-weight:1000;}
    .sobat-plus-wrap{position:relative;}
    .sobat-plus-btn{border:0;background:#4BBFB0;color:#fff;border-radius:999px;padding:12px 16px;font-weight:1000;box-shadow:0 12px 24px rgba(75,191,176,.18);cursor:pointer;transition:.18s;}
    .sobat-plus-btn:hover{transform:translateY(-1px);background:#3AAFA1;}
    .sobat-block-menu{position:absolute;right:0;top:calc(100% + 10px);z-index:30;width:min(285px,78vw);display:none;background:#fff;border:1px solid #D4EEEC;border-radius:22px;padding:10px;box-shadow:0 24px 60px rgba(42,61,60,.16);}
    .sobat-block-menu.is-open{display:grid;gap:7px;}
    .sobat-block-menu button{width:100%;border:1px solid transparent;background:#F8FFFE;color:#2A3D3C;border-radius:16px;padding:11px 12px;font-weight:1000;text-align:left;cursor:pointer;transition:.16s;display:flex;gap:10px;align-items:center;}
    .sobat-block-menu button small{display:block;color:#6B8A88;font-size:.74rem;font-weight:800;margin-top:2px;}
    .sobat-block-menu button:hover{background:#ECFFFA;border-color:#BFE9E5;transform:translateX(2px);}
    .sobat-floating-plus-wrap{position:absolute;right:18px;bottom:18px;z-index:26;}
    .sobat-floating-plus-btn{
        width:56px;height:56px;border:0;border-radius:999px;background:#4BBFB0;color:#fff;font-size:1.65rem;font-weight:1000;
        display:grid;place-items:center;box-shadow:0 18px 42px rgba(75,191,176,.28);cursor:pointer;transition:.18s;
    }
    .sobat-floating-plus-btn:hover{transform:translateY(-2px) scale(1.03);background:#3AAFA1;}
    .sobat-floating-plus-btn span{display:block;transform:translateY(-1px);}
    .sobat-floating-label{
        position:absolute;right:66px;top:50%;transform:translateY(-50%);white-space:nowrap;background:#fff;color:#2A3D3C;
        border:1px solid #D4EEEC;border-radius:999px;padding:8px 12px;font-weight:1000;font-size:.82rem;box-shadow:0 12px 28px rgba(42,61,60,.08);
        opacity:0;pointer-events:none;transition:.18s;
    }
    .sobat-floating-plus-wrap:hover .sobat-floating-label{opacity:1;}
    .sobat-floating-plus-wrap .sobat-block-menu{right:0;bottom:calc(100% + 12px);top:auto;}
    .sobat-block-editor.is-typing .sobat-floating-plus-wrap{opacity:.58;}
    .sobat-block-editor.is-typing .sobat-floating-plus-wrap:hover{opacity:1;}

    .sobat-format-toolbar{
        display:flex;align-items:center;gap:8px;flex-wrap:wrap;padding:10px 14px;border-bottom:1px solid #E4F4F2;
        background:linear-gradient(135deg,#FFFFFF,#F7FFFE);position:sticky;top:0;z-index:18;
    }
    .sobat-format-toolbar-label{font-size:.78rem;font-weight:1000;color:#6B8A88;margin-right:2px;white-space:nowrap;}
    .sobat-format-btn{
        min-width:38px;height:38px;border:1px solid #D4EEEC;background:#fff;color:#2A3D3C;border-radius:13px;
        display:inline-flex;align-items:center;justify-content:center;gap:5px;padding:0 10px;font-weight:1000;cursor:pointer;transition:.16s;
        box-shadow:0 8px 18px rgba(42,61,60,.04);
    }
    .sobat-format-btn:hover{background:#ECFFFA;border-color:#BFE9E5;transform:translateY(-1px);}
    .sobat-align-toolbar .sobat-format-btn{min-width:auto;padding:0 13px;}
    .sobat-format-btn b{font-size:1rem;}
    .sobat-format-divider{width:1px;height:28px;background:#D4EEEC;margin:0 2px;}
    @media(max-width:760px){.sobat-format-toolbar{gap:6px;padding:9px}.sobat-format-toolbar-label{width:100%;}.sobat-format-btn{height:36px;min-width:36px;padding:0 9px}}

    @media(max-width:760px){.sobat-floating-plus-wrap{right:14px;bottom:14px}.sobat-floating-label{display:none}.sobat-floating-plus-btn{width:52px;height:52px}}
    .article-doc-editor{border:0;border-radius:0;box-shadow:none;min-height:430px;max-height:680px;}
    .article-doc-editor [data-block]{position:relative;border-radius:16px;padding:4px 6px;}
    .article-doc-editor [data-block]:hover{background:#F7FFFE;outline:1px dashed #D4EEEC;}
    .article-doc-editor h2[data-block]{font-size:1.45rem;line-height:1.28;margin:1.35rem 0 .65rem;font-weight:1000;color:#2A3D3C;}
    .article-doc-editor p[data-block]{margin:.75rem 0;min-height:28px;}
    .article-doc-editor .read-also-chip + p[data-block]{margin-top:1rem;min-height:42px;}
    .article-block-note{margin-top:10px;background:#F8FFFE;border:1px solid #E3F3F1;border-radius:16px;padding:11px 13px;color:#6B8A88;font-weight:850;font-size:.82rem;line-height:1.5;}
    .article-block-note b{color:#2A3D3C;}
    .sobat-article-modal{position:fixed;inset:0;background:rgba(20,38,37,.45);z-index:9999;display:none;align-items:center;justify-content:center;padding:18px;}
    .sobat-article-modal.is-open{display:flex;}
    .sobat-article-dialog{width:min(460px,100%);background:#fff;border:1px solid #D4EEEC;border-radius:28px;padding:22px;box-shadow:0 30px 90px rgba(42,61,60,.22);animation:sobatModalPop .18s ease-out;}
    @keyframes sobatModalPop{from{opacity:0;transform:translateY(10px) scale(.98)}to{opacity:1;transform:translateY(0) scale(1)}}
    .sobat-article-dialog h3{font-size:1.25rem;font-weight:1000;color:#2A3D3C;margin:0 0 5px;}
    .sobat-article-dialog p{margin:0 0 14px;color:#6B8A88;font-weight:800;line-height:1.45;font-size:.9rem;}
    .sobat-article-modal-fields{display:grid;gap:10px;}
    .sobat-article-modal-fields label{display:grid;gap:6px;color:#2A3D3C;font-weight:1000;font-size:.9rem;}
    .sobat-article-modal-fields input,.sobat-article-modal-fields textarea{width:100%;border:1.5px solid #D4EEEC;background:#FAFCFC;border-radius:16px;padding:12px 14px;color:#2A3D3C;font-weight:850;outline:none;}
    .sobat-article-modal-fields textarea{min-height:90px;resize:vertical;}
    .sobat-article-modal-fields input:focus,.sobat-article-modal-fields textarea:focus{border-color:#4BBFB0;box-shadow:0 0 0 5px rgba(75,191,176,.12);background:#fff;}
    .sobat-article-modal-actions{display:flex;justify-content:flex-end;gap:10px;margin-top:16px;}
    .sobat-article-modal-btn{border:0;border-radius:999px;padding:12px 18px;font-weight:1000;cursor:pointer;}
    .sobat-article-modal-btn.cancel{background:#F7FFFE;color:#2A3D3C;border:1px solid #D4EEEC;}
    .sobat-article-modal-btn.ok{background:#4BBFB0;color:#fff;box-shadow:0 12px 24px rgba(75,191,176,.2);}
    @media(max-width:760px){.article-import-actions{grid-template-columns:1fr}.article-import-btn{width:100%}}
    @media(max-width:960px){.admin-article-shell{grid-template-columns:1fr}.admin-side-card{position:static}.admin-form-grid{grid-template-columns:1fr}.admin-article-card{padding:18px}.admin-article-hero{padding:24px}.admin-article-wrap{width:min(100% - 24px,1180px)}}
</style>

<section class="admin-article-page">
    <div class="admin-article-wrap">
        <div class="admin-article-nav">
            <div class="admin-article-breadcrumb">
                <a href="{{ route('admin.dashboard') }}" class="admin-article-back">← Admin Dashboard</a>
                <a href="{{ route('admin.articles') }}" class="admin-article-back">← Daftar Artikel</a>
            </div>
        </div>

        <div class="admin-article-hero">
            <span class="admin-article-kicker">✦ Admin Postingan Berita</span>
            <h1 class="admin-article-title">{{ $isCreate ? 'Buat Artikel' : 'Edit Artikel' }} <span>{{ $isCreate ? 'Baru' : 'SobatAnak' }}</span></h1>
            <p class="admin-article-desc">Isi artikel parenting, kesehatan anak, produk, atau tips Mom & Baby Care. Tampilan ini sudah dirapikan supaya form tidak dempet dan lebih enak dipakai admin.</p>
        </div>

        @if($errors->any())
            <div class="admin-alert-error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" enctype="multipart/form-data" action="{{ $isCreate ? route('admin.articles.store') : route('admin.articles.update', $article) }}" class="admin-article-shell">
            @csrf
            @if(!$isCreate) @method('PATCH') @endif

            <div class="admin-article-card">
                <div class="admin-article-card-head">
                    <div>
                        <small>Konten Artikel</small>
                        <h2>{{ $isCreate ? 'Tulis postingan baru' : 'Perbarui postingan' }}</h2>
                    </div>
                </div>

                <label class="admin-form-field full">Judul Postingan
                    <input name="title" value="{{ old('title', $article->title) }}" required placeholder="Contoh: Tips Memilih Popok Newborn yang Aman">
                </label>

                <label class="admin-form-field full">Slug URL
                    <input name="slug" value="{{ old('slug', $article->slug) }}" placeholder="Kosongkan untuk dibuat otomatis">
                    <small>Slug dipakai untuk URL artikel. Contoh: tips-memilih-popok-newborn</small>
                </label>

                <div class="admin-form-grid">
                    <label class="admin-form-field">Kategori
                        <select name="category_id" required>
                            <option value="">Pilih kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" @selected((string) old('category_id', $article->category_id) === (string) $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </label>

                    <label class="admin-form-field">Status
                        <select name="status" required>
                            <option value="published" @selected(old('status', $article->status ?: 'published') === 'published')>Published</option>
                            <option value="draft" @selected(old('status', $article->status) === 'draft')>Draft</option>
                        </select>
                        <small>Draft tidak tampil di halaman artikel user.</small>
                    </label>
                </div>

                <label class="admin-form-field full">Tags
                    <input name="tags" value="{{ old('tags', $article->tags) }}" placeholder="parenting, bayi, mom tips">
                    <small>Pisahkan tag dengan koma agar AI dan pencarian artikel lebih mudah menemukan konten.</small>
                </label>

                @php
                    $articleContentSupport = \App\Support\SobatArticleContent::class;
                    $hasArticleContentSupport = class_exists($articleContentSupport);
                    $preparedAdminArticle = (!$isCreate && $hasArticleContentSupport)
                        ? $articleContentSupport::find($article->slug)
                        : null;
                    $storedArticleContent = ($preparedAdminArticle && $hasArticleContentSupport)
                        ? $articleContentSupport::toPlainText($preparedAdminArticle)
                        : $article->content;
                    $adminArticleContent = old('content', $storedArticleContent);
                @endphp

                <div class="admin-form-field full">
                    <span>Isi Artikel / Content</span>

                    <div class="article-import-box">
                        <div class="article-import-head">
                            <div class="article-import-icon">📄</div>
                            <div>
                                <div class="article-import-title">Upload artikel dari DOCX / PDF / TXT</div>
                                <div class="article-import-text">Pilih file, lalu klik Rapikan Otomatis. Hasilnya langsung muncul di editor di bawah, jadi admin bisa edit manual seperti Google Docs sebelum disimpan.</div>
                            </div>
                        </div>
                        <div class="article-import-actions">
                            <input class="article-import-input" id="articleImportFile" type="file" accept=".docx,.pdf,.txt,.md,.markdown,.html,.htm">
                            <button class="article-import-btn" type="button" id="articleImportBtn">Rapikan Otomatis</button>
                        </div>
                        <div id="articleImportMessage" class="article-import-message"></div>
                    </div>

                    <div class="article-block-help">
                        <h3>Editor isi artikel model blok</h3>
                        <p>Gunakan tombol <b>+ Tambah Blok</b> untuk memasukkan paragraf, subjudul, teks tebal, link, Baca Juga, caption cover, atau bullet. Setelah blok muncul, admin tinggal ketik dan rapikan manual.</p>
                    </div>

                    <textarea id="articleContentEditor" class="article-hidden-textarea" name="content" required>{{ $adminArticleContent }}</textarea>
                    <div class="sobat-block-editor">
                        <div class="sobat-block-editor-head">
                            <div class="sobat-block-title">📝 Isi Artikel</div>
                            <div class="article-import-text">Klik area artikel, lalu pilih rata teks jika ingin dirapikan.</div>
                        </div>
                        <div class="sobat-format-toolbar sobat-align-toolbar" aria-label="Toolbar rata teks artikel">
                            <span class="sobat-format-toolbar-label">Rata teks:</span>
                            <button type="button" class="sobat-format-btn" data-format="justifyLeft" title="Rata kiri">☰ Kiri</button>
                            <button type="button" class="sobat-format-btn" data-format="justifyCenter" title="Rata tengah">≡ Tengah</button>
                            <button type="button" class="sobat-format-btn" data-format="justifyRight" title="Rata kanan">☷ Kanan</button>
                            <button type="button" class="sobat-format-btn" data-format="justifyFull" title="Rata kanan kiri">☷☰ Rata semua</button>
                        </div>
                        <div id="articleVisualEditor" class="article-doc-editor" contenteditable="true" data-placeholder="Klik di sini untuk mulai menulis artikel..."></div>
                        <div class="sobat-floating-plus-wrap">
                            <span class="sobat-floating-label">Tambah Blok</span>
                            <button type="button" class="sobat-floating-plus-btn" id="sobatAddBlockBtn" title="Tambah Blok"><span>+</span></button>
                            <div class="sobat-block-menu" id="sobatBlockMenu" aria-label="Pilih blok artikel">
                                <button type="button" data-insert="paragraph">🧾 <span>Paragraf<small>Teks biasa untuk isi artikel.</small></span></button>
                                <button type="button" data-insert="heading">🔖 <span>Subjudul<small>Judul kecil, otomatis masuk pembahasan.</small></span></button>
                                <button type="button" data-insert="bold">🅱️ <span>Teks Tebal<small>Untuk kalimat penting.</small></span></button>
                                <button type="button" data-insert="link">🔗 <span>Link Biru<small>Untuk link biasa di isi artikel.</small></span></button>
                                <button type="button" data-insert="readalso">📚 <span>Baca Juga<small>Rekomendasi artikel lain.</small></span></button>
                                <button type="button" data-insert="caption">🖼️ <span>Caption Cover<small>Keterangan di bawah gambar cover.</small></span></button>
                                <button type="button" data-insert="bullet">• <span>Bullet List<small>Daftar poin pembahasan.</small></span></button>
                                <button type="button" data-insert="spacer">↵ <span>Jarak Kosong<small>Memberi ruang antar bagian.</small></span></button>
                            </div>
                        </div>
                    </div>
                    <div class="article-block-note"><b>Catatan Baca Juga:</b> pakai link artikel seperti <b>/artikel/slug-artikel</b>. Contoh: <b>/artikel/mainan-edukatif-anak</b>. Semua hasil editor tetap disimpan ke kolom isi artikel yang lama, jadi database tidak berubah.</div>
                </div>
            </div>

            <aside class="admin-article-card admin-side-card">
                <div class="admin-article-card-head">
                    <div>
                        <small>Media Artikel</small>
                        <h2>Gambar Utama</h2>
                    </div>
                </div>

                <div class="admin-image-box" id="articleImagePreview">
                    @if(!$isCreate && $article->image)
                        <img src="{{ $article->image }}" alt="Preview artikel" onerror="this.src='{{ asset('images/logo-cropped.png') }}'">
                    @else
                        <div class="admin-image-empty">
                            <b>Preview Gambar</b>
                            <small>Gambar artikel akan muncul di sini setelah dipilih.</small>
                        </div>
                    @endif
                </div>

                <label class="admin-form-field full">Upload Gambar
                    <input class="admin-file-input" id="articleImageInput" type="file" name="image_file" accept="image/png,image/jpeg,image/jpg,image/webp" {{ $isCreate ? 'required' : '' }}>
                    <small>Format JPG, PNG, WEBP. Maksimal 4MB. {{ !$isCreate ? 'Kosongkan kalau gambar tidak diganti.' : '' }}</small>
                </label>

                <button class="admin-submit-btn" type="submit">{{ $isCreate ? 'Simpan Artikel' : 'Update Artikel' }}</button>
                <p class="admin-note">Pastikan judul, kategori, gambar, dan isi artikel sudah sesuai sebelum publish.</p>
            </aside>
        </form>
    </div>
</section>

<div class="sobat-article-modal" id="sobatArticleModal" aria-hidden="true">
    <div class="sobat-article-dialog" role="dialog" aria-modal="true" aria-labelledby="sobatArticleModalTitle">
        <h3 id="sobatArticleModalTitle">Tambah Format</h3>
        <p id="sobatArticleModalDesc">Isi data di bawah ini.</p>
        <div class="sobat-article-modal-fields" id="sobatArticleModalFields"></div>
        <div class="sobat-article-modal-actions">
            <button type="button" class="sobat-article-modal-btn cancel" id="sobatArticleModalCancel">Batal</button>
            <button type="button" class="sobat-article-modal-btn ok" id="sobatArticleModalOk">Masukkan</button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const input = document.getElementById('articleImageInput');
        const preview = document.getElementById('articleImagePreview');
        const editor = document.getElementById('articleContentEditor');
        const visualEditor = document.getElementById('articleVisualEditor');
        const articleForm = document.querySelector('form.admin-article-shell');
        const titleInput = document.querySelector('input[name="title"]');
        const importFile = document.getElementById('articleImportFile');
        const importBtn = document.getElementById('articleImportBtn');
        const importMsg = document.getElementById('articleImportMessage');
        const articleModal = document.getElementById('sobatArticleModal');
        const articleModalTitle = document.getElementById('sobatArticleModalTitle');
        const articleModalDesc = document.getElementById('sobatArticleModalDesc');
        const articleModalFields = document.getElementById('sobatArticleModalFields');
        const articleModalOk = document.getElementById('sobatArticleModalOk');
        const articleModalCancel = document.getElementById('sobatArticleModalCancel');
        const addBlockBtn = document.getElementById('sobatAddBlockBtn');
        const blockMenu = document.getElementById('sobatBlockMenu');
        const formatButtons = document.querySelectorAll('.sobat-format-btn[data-format]');
        let articleModalResolver = null;
        let savedArticleRange = null;

        function saveArticleSelection(){
            if (!visualEditor) return;
            const selection = window.getSelection();
            if (!selection || !selection.rangeCount) return;
            const range = selection.getRangeAt(0);
            const common = range.commonAncestorContainer;
            const parent = common.nodeType === Node.ELEMENT_NODE ? common : common.parentElement;
            if (parent && visualEditor.contains(parent)) {
                savedArticleRange = range.cloneRange();
            }
        }

        function restoreArticleSelection(){
            if (!visualEditor || !savedArticleRange) return false;
            const selection = window.getSelection();
            selection.removeAllRanges();
            selection.addRange(savedArticleRange);
            visualEditor.focus();
            return true;
        }

        function escapeHtml(text){
            const div = document.createElement('div');
            div.textContent = text || '';
            return div.innerHTML;
        }

        function markdownInlineToHtml(text){
            let html = escapeHtml(text || '');
            html = html.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
            html = html.replace(/__([^_]+)__/g, '<strong>$1</strong>');
            html = html.replace(/\[([^\]]+)\]\(([^\)]+)\)/g, '<a href="$2">$1</a>');
            return html;
        }

        function markdownToHtml(markdown){
            const lines = (markdown || '').replace(/\r\n/g, '\n').replace(/\r/g, '\n').split('\n');
            let html = '';
            let paragraph = [];
            let list = [];
            function flushParagraph(){
                if (paragraph.length) {
                    html += '<p>' + markdownInlineToHtml(paragraph.join(' ')) + '</p>';
                    paragraph = [];
                }
            }
            function flushList(){
                if (list.length) {
                    html += '<ul>' + list.map(item => '<li>' + markdownInlineToHtml(item) + '</li>').join('') + '</ul>';
                    list = [];
                }
            }
            lines.forEach(line => {
                const trim = line.trim();
                if (!trim) { flushParagraph(); flushList(); return; }
                let m;
                if ((m = trim.match(/^\[caption\](.*?)\[\/caption\]$/i))) {
                    flushParagraph(); flushList();
                    html += '<div class="cover-caption-chip" data-sobat-type="caption">' + markdownInlineToHtml(m[1]) + '</div>';
                } else if ((m = trim.match(/^#{2,3}\s+(.+)$/))) {
                    flushParagraph(); flushList();
                    html += '<h2>' + markdownInlineToHtml(m[1]) + '</h2>';
                } else if ((m = trim.match(/^(Baca juga|Read also)\s*:\s*(.+)$/i))) {
                    flushParagraph(); flushList();
                    html += '<div class="read-also-chip" data-sobat-type="readalso" contenteditable="false">Baca juga: ' + markdownInlineToHtml(m[2]) + '</div>';
                } else if ((m = trim.match(/^[-*]\s+(.+)$/))) {
                    flushParagraph();
                    list.push(m[1]);
                } else {
                    flushList();
                    paragraph.push(trim);
                }
            });
            flushParagraph(); flushList();
            return html;
        }

        function nodeInlineToMarkdown(container){
            let out = '';
            container.childNodes.forEach(node => {
                if (node.nodeType === Node.TEXT_NODE) {
                    out += node.textContent;
                } else if (node.nodeType === Node.ELEMENT_NODE) {
                    const tag = node.tagName.toLowerCase();
                    if (tag === 'strong' || tag === 'b') {
                        out += '**' + nodeInlineToMarkdown(node).trim() + '**';
                    } else if (tag === 'a') {
                        const label = nodeInlineToMarkdown(node).trim() || node.textContent.trim();
                        const href = node.getAttribute('href') || '#';
                        out += '[' + label + '](' + href + ')';
                    } else if (tag === 'br') {
                        out += '\n';
                    } else {
                        out += nodeInlineToMarkdown(node);
                    }
                }
            });
            return out.replace(/\u00a0/g, ' ');
        }

        function htmlToMarkdown(root){
            const lines = [];
            Array.from(root.childNodes).forEach(node => {
                if (node.nodeType === Node.TEXT_NODE) {
                    const t = node.textContent.trim();
                    if (t) lines.push(t);
                    return;
                }
                if (node.nodeType !== Node.ELEMENT_NODE) return;
                const tag = node.tagName.toLowerCase();
                const text = nodeInlineToMarkdown(node).trim();
                if (!text) return;
                if (tag === 'h1' || tag === 'h2' || tag === 'h3') {
                    lines.push('## ' + text.replace(/^#+\s*/, ''));
                } else if (tag === 'ul') {
                    node.querySelectorAll(':scope > li').forEach(li => {
                        const item = nodeInlineToMarkdown(li).trim();
                        if (item) lines.push('- ' + item);
                    });
                } else if (node.dataset && node.dataset.sobatType === 'caption') {
                    lines.push('[caption]' + text.replace(/^caption\s*:\s*/i, '') + '[/caption]');
                } else if (node.dataset && node.dataset.sobatType === 'readalso') {
                    lines.push(text.match(/^Baca juga\s*:/i) ? text : 'Baca juga: ' + text);
                } else if (tag === 'div' && node.classList.contains('cover-caption-chip')) {
                    lines.push('[caption]' + text.replace(/^caption\s*:\s*/i, '') + '[/caption]');
                } else if (tag === 'div' && node.classList.contains('read-also-chip')) {
                    lines.push(text.match(/^Baca juga\s*:/i) ? text : 'Baca juga: ' + text);
                } else {
                    lines.push(text);
                }
            });
            return lines.join('\n\n').replace(/\n{3,}/g, '\n\n').trim();
        }

        function syncVisualToTextarea(){
            if (!visualEditor || !editor) return;
            editor.value = htmlToMarkdown(visualEditor);
        }

        function setEditorFromMarkdown(markdown){
            if (!visualEditor || !editor) return;
            editor.value = markdown || '';
            visualEditor.innerHTML = markdownToHtml(markdown || '');
        }

        if (editor && visualEditor) {
            setEditorFromMarkdown(editor.value || '');
            visualEditor.addEventListener('input', function(){ syncVisualToTextarea(); saveArticleSelection(); });
            visualEditor.addEventListener('keyup', saveArticleSelection);
            visualEditor.addEventListener('mouseup', saveArticleSelection);
            visualEditor.addEventListener('touchend', saveArticleSelection);
            visualEditor.addEventListener('focus', function(){
                const editorBox = visualEditor.closest('.sobat-block-editor');
                if (editorBox) editorBox.classList.add('is-typing');
            });
            visualEditor.addEventListener('blur', function(){
                syncVisualToTextarea();
                const editorBox = visualEditor.closest('.sobat-block-editor');
                if (editorBox) setTimeout(function(){ editorBox.classList.remove('is-typing'); }, 140);
            });
            visualEditor.addEventListener('keydown', function(e){
                const readAlso = currentReadAlsoElement();
                if (readAlso && e.key === 'Enter') {
                    e.preventDefault();
                    moveAfterReadAlso(readAlso);
                    return;
                }
            });
            if (articleForm) articleForm.addEventListener('submit', syncVisualToTextarea);
        }

        function showImportMessage(type, message){
            if (!importMsg) return;
            importMsg.className = 'article-import-message ' + type;
            importMsg.textContent = message;
        }

        function ensureFocus(){
            if (!visualEditor) return;
            if (!restoreArticleSelection()) visualEditor.focus();
        }

        function insertHtmlAtCursor(html){
            ensureFocus();
            document.execCommand('insertHTML', false, html);
            saveArticleSelection();
            syncVisualToTextarea();
        }
        function placeCursorInside(element){
            if (!element) return;
            const range = document.createRange();
            const selection = window.getSelection();
            range.selectNodeContents(element);
            range.collapse(false);
            selection.removeAllRanges();
            selection.addRange(range);
            visualEditor.focus();
        }

        function insertNodeAtCursor(node){
            ensureFocus();
            const selection = window.getSelection();
            if (!selection || !selection.rangeCount) {
                visualEditor.appendChild(node);
                return;
            }
            const range = selection.getRangeAt(0);
            range.deleteContents();
            range.insertNode(node);
            range.setStartAfter(node);
            range.setEndAfter(node);
            selection.removeAllRanges();
            selection.addRange(range);
            saveArticleSelection();
        }

        function makeParagraph(text){
            const p = document.createElement('p');
            p.setAttribute('data-block', 'paragraph');
            p.innerHTML = text || '<br>';
            return p;
        }

        function insertReadAlsoBlock(label, url){
            const wrapper = document.createElement('div');
            wrapper.className = 'read-also-chip';
            wrapper.setAttribute('data-sobat-type', 'readalso');
            wrapper.setAttribute('data-block', 'readalso');
            wrapper.setAttribute('contenteditable', 'false');
            wrapper.innerHTML = 'Baca juga: <a href="' + escapeHtml(url) + '">' + escapeHtml(label) + '</a>';

            const nextParagraph = makeParagraph('<br>');
            insertNodeAtCursor(wrapper);
            wrapper.after(nextParagraph);
            placeCursorInside(nextParagraph);
            syncVisualToTextarea();
        }

        function currentReadAlsoElement(){
            const selection = window.getSelection();
            if (!selection || !selection.rangeCount) return null;
            let node = selection.anchorNode;
            if (node && node.nodeType === Node.TEXT_NODE) node = node.parentElement;
            if (!node || !visualEditor.contains(node)) return null;
            return node.closest ? node.closest('.read-also-chip') : null;
        }

        function moveAfterReadAlso(readAlso){
            if (!readAlso) return;
            let next = readAlso.nextElementSibling;
            if (!next || next.matches('.read-also-chip') || next.classList.contains('cover-caption-chip')) {
                next = makeParagraph('<br>');
                readAlso.after(next);
            }
            placeCursorInside(next);
            syncVisualToTextarea();
        }


        function closeArticleModal(value){
            if (!articleModal) return;
            articleModal.classList.remove('is-open');
            articleModal.setAttribute('aria-hidden', 'true');
            if (articleModalResolver) articleModalResolver(value || null);
            articleModalResolver = null;
        }

        function openArticleModal(config){
            saveArticleSelection();
            if (!articleModal || !articleModalFields) return Promise.resolve(null);
            articleModalTitle.textContent = config.title || 'Tambah Format';
            articleModalDesc.textContent = config.desc || 'Isi data di bawah ini.';
            articleModalFields.innerHTML = (config.fields || []).map(function(field){
                const value = escapeHtml(field.value || '');
                const placeholder = escapeHtml(field.placeholder || '');
                if (field.type === 'textarea') {
                    return '<label>' + escapeHtml(field.label) + '<textarea data-field="' + escapeHtml(field.name) + '" placeholder="' + placeholder + '">' + value + '</textarea></label>';
                }
                return '<label>' + escapeHtml(field.label) + '<input data-field="' + escapeHtml(field.name) + '" value="' + value + '" placeholder="' + placeholder + '"></label>';
            }).join('');
            articleModal.classList.add('is-open');
            articleModal.setAttribute('aria-hidden', 'false');
            setTimeout(function(){
                const firstInput = articleModalFields.querySelector('input, textarea');
                if (firstInput) firstInput.focus();
            }, 30);
            return new Promise(function(resolve){
                articleModalResolver = function(result){ resolve(result); };
            });
        }

        if (articleModalOk) {
            articleModalOk.addEventListener('click', function(){
                const data = {};
                articleModalFields.querySelectorAll('[data-field]').forEach(function(input){
                    data[input.dataset.field] = input.value.trim();
                });
                closeArticleModal(data);
            });
        }
        if (articleModalCancel) articleModalCancel.addEventListener('click', function(){ closeArticleModal(null); });
        if (articleModal) articleModal.addEventListener('click', function(e){ if (e.target === articleModal) closeArticleModal(null); });
        document.addEventListener('keydown', function(e){ if (e.key === 'Escape' && articleModal && articleModal.classList.contains('is-open')) closeArticleModal(null); });


        function applyEditorFormat(format){
            if (!visualEditor) return;
            ensureFocus();
            if (format === 'createLink') {
                const selected = (window.getSelection() || '').toString().trim();
                openArticleModal({
                    title: 'Tambah Link',
                    desc: 'Blok teks dulu kalau ingin teks tertentu menjadi link. Link artikel bisa dipilih/manual memakai format /artikel/slug-artikel.',
                    fields: [
                        {name:'label', label:'Teks yang tampil', value:selected || 'Judul artikel'},
                        {name:'url', label:'Link tujuan', value:'/artikel/slug-artikel', placeholder:'/artikel/slug-artikel'}
                    ]
                }).then(function(data){
                    if (!data || !data.url) return;
                    ensureFocus();
                    if (selected) {
                        document.execCommand('createLink', false, data.url);
                        const selection = window.getSelection();
                        const anchor = selection && selection.anchorNode ? (selection.anchorNode.nodeType === Node.ELEMENT_NODE ? selection.anchorNode : selection.anchorNode.parentElement) : null;
                        const link = anchor && anchor.closest ? anchor.closest('a') : null;
                        if (link) link.textContent = data.label || selected;
                    } else {
                        document.execCommand('insertHTML', false, '<a href="' + escapeHtml(data.url) + '">' + escapeHtml(data.label || data.url) + '</a>');
                    }
                    saveArticleSelection();
                    syncVisualToTextarea();
                });
                return;
            }

            if (format.indexOf('formatBlock:') === 0) {
                const block = format.split(':')[1];
                document.execCommand('formatBlock', false, block);
            } else {
                document.execCommand(format, false, null);
            }
            saveArticleSelection();
            syncVisualToTextarea();
        }

        formatButtons.forEach(function(button){
            button.addEventListener('mousedown', function(e){
                e.preventDefault();
                saveArticleSelection();
            });
            button.addEventListener('click', function(e){
                e.preventDefault();
                applyEditorFormat(this.dataset.format);
            });
        });

        if (addBlockBtn && blockMenu) {
            addBlockBtn.addEventListener('mousedown', function(){ saveArticleSelection(); });
            addBlockBtn.addEventListener('click', function(e){
                e.stopPropagation();
                saveArticleSelection();
                blockMenu.classList.toggle('is-open');
            });
            document.addEventListener('click', function(e){
                if (!blockMenu.contains(e.target) && e.target !== addBlockBtn) blockMenu.classList.remove('is-open');
            });
        }

        document.querySelectorAll('.sobat-block-menu button[data-insert]').forEach(function(button){
            button.addEventListener('mousedown', function(e){ saveArticleSelection(); });
            button.addEventListener('click', async function(){
                if (!visualEditor) return;
                const type = this.dataset.insert;
                if (blockMenu) blockMenu.classList.remove('is-open');
                ensureFocus();

                if (type === 'paragraph') {
                    insertHtmlAtCursor('<p data-block="paragraph">Tulis paragraf artikel di sini.</p>');
                } else if (type === 'bold') {
                    insertHtmlAtCursor('<p data-block="bold"><strong>Tulis kalimat penting di sini.</strong></p>');
                } else if (type === 'spacer') {
                    insertHtmlAtCursor('<p data-block="spacer"><br></p>');
                } else if (type === 'link') {
                    const selected = (window.getSelection() || '').toString().trim();
                    const data = await openArticleModal({
                        title: 'Tambah Link Biru',
                        desc: 'Isi teks dan link tujuan. Link artikel biasanya memakai /artikel/slug-artikel.',
                        fields: [
                            {name:'label', label:'Teks yang tampil', value:selected || 'Judul artikel'},
                            {name:'url', label:'Link tujuan', value:'/artikel/slug-artikel', placeholder:'/artikel/slug-artikel'}
                        ]
                    });
                    if (data && data.url) insertHtmlAtCursor('<p data-block="link"><a href="' + escapeHtml(data.url) + '">' + escapeHtml(data.label || selected || 'Judul artikel') + '</a></p>');
                } else if (type === 'heading') {
                    insertHtmlAtCursor('<h2 data-block="heading">Subjudul artikel</h2><p data-block="paragraph">Tulis isi pembahasannya di sini.</p>');
                } else if (type === 'readalso') {
                    const data = await openArticleModal({
                        title: 'Tambah Baca Juga',
                        desc: 'Dipakai untuk rekomendasi artikel lain di dalam isi artikel.',
                        fields: [
                            {name:'label', label:'Judul artikel', value:'Mainan Edukatif untuk Tumbuh Kembang Anak'},
                            {name:'url', label:'Link artikel', value:'/artikel/mainan-edukatif-anak', placeholder:'/artikel/slug-artikel'}
                        ]
                    });
                    if (data && data.url) insertReadAlsoBlock(data.label || 'Judul artikel lain', data.url);
                } else if (type === 'caption') {
                    const data = await openArticleModal({
                        title: 'Tambah Caption Cover',
                        desc: 'Caption ini akan tampil di bawah gambar cover artikel.',
                        fields: [
                            {name:'caption', label:'Caption cover', type:'textarea', value:'Rekomendasi pilihan produk yang aman dan nyaman untuk si kecil.'}
                        ]
                    });
                    if (data && data.caption) insertHtmlAtCursor('<div class="cover-caption-chip" data-sobat-type="caption" data-block="caption">' + escapeHtml(data.caption) + '</div><p data-block="spacer"><br></p>');
                } else if (type === 'bullet') {
                    insertHtmlAtCursor('<ul data-block="bullet"><li>Poin pembahasan</li></ul>');
                }
                syncVisualToTextarea();
            });
        });


        function simpleImportedTextToMarkdown(text){
            text = (text || '').replace(/\r\n/g, '\n').replace(/\r/g, '\n');
            text = text.replace(/[ \t]+/g, ' ');
            text = text.replace(/\n{3,}/g, '\n\n').trim();
            const lines = text.split('\n');
            const out = [];
            let skipToc = false;
            let firstContentSeen = false;
            lines.forEach(function(rawLine){
                let line = (rawLine || '').trim();
                if (!line) { if (out.length && out[out.length - 1] !== '') out.push(''); return; }
                line = line.replace(/^[•–—]\s*/u, '- ');
                if (/^tema\s*:/i.test(line)) return;
                if (/^pembahasan dalam artikel\s*:?$/i.test(line)) { skipToc = true; return; }
                if (skipToc && /^[-*]\s+/.test(line)) return;
                if (skipToc && !/^[-*]\s+/.test(line)) skipToc = false;
                if (!firstContentSeen) {
                    firstContentSeen = true;
                    if (line.length <= 100 && !/[.!?]$/.test(line)) return;
                }
                let m;
                if ((m = line.match(/^(caption cover|caption)\s*:\s*(.+)$/i))) {
                    out.push('[caption]' + m[2].trim() + '[/caption]');
                    return;
                }
                if ((m = line.match(/^(baca juga|read also)\s*:\s*(.+)$/i))) {
                    out.push('Baca juga: ' + m[2].trim());
                    return;
                }
                if (/^[-*]\s+/.test(line) || /^#{2,3}\s+/.test(line)) { out.push(line); return; }
                const looksHeading = line.length <= 78 && !/[.!?]$/.test(line) && line.split(/\s+/).length <= 9;
                out.push(looksHeading ? '## ' + line.replace(/^#+\s*/, '') : line);
            });
            return out.join('\n').replace(/\n{3,}/g, '\n\n').trim();
        }

        function loadPdfJs(){
            if (window.pdfjsLib) return Promise.resolve(window.pdfjsLib);
            return new Promise(function(resolve, reject){
                const existing = document.querySelector('script[data-sobat-pdfjs="1"]');
                if (existing) {
                    existing.addEventListener('load', function(){ resolve(window.pdfjsLib); });
                    existing.addEventListener('error', reject);
                    return;
                }
                const script = document.createElement('script');
                script.src = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js';
                script.async = true;
                script.setAttribute('data-sobat-pdfjs', '1');
                script.onload = function(){
                    if (window.pdfjsLib) {
                        window.pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
                        resolve(window.pdfjsLib);
                    } else {
                        reject(new Error('PDF reader belum siap.'));
                    }
                };
                script.onerror = function(){ reject(new Error('PDF reader gagal dimuat.')); };
                document.head.appendChild(script);
            });
        }

        async function readPdfInBrowser(file){
            const pdfjsLib = await loadPdfJs();
            const buffer = await file.arrayBuffer();
            const pdf = await pdfjsLib.getDocument({ data: buffer }).promise;
            const pages = [];
            for (let pageNumber = 1; pageNumber <= pdf.numPages; pageNumber++) {
                const page = await pdf.getPage(pageNumber);
                const content = await page.getTextContent();
                const rows = [];
                content.items.forEach(function(item){
                    const y = Math.round((item.transform && item.transform[5]) ? item.transform[5] : 0);
                    let row = rows.find(function(r){ return Math.abs(r.y - y) <= 3; });
                    if (!row) { row = { y: y, items: [] }; rows.push(row); }
                    row.items.push({ x: (item.transform && item.transform[4]) ? item.transform[4] : 0, text: item.str || '' });
                });
                rows.sort(function(a,b){ return b.y - a.y; });
                const pageText = rows.map(function(row){
                    row.items.sort(function(a,b){ return a.x - b.x; });
                    return row.items.map(function(it){ return it.text; }).join(' ').replace(/\s+/g, ' ').trim();
                }).filter(Boolean).join('\n');
                pages.push(pageText);
            }
            return pages.join('\n\n').trim();
        }

        if (importBtn && importFile && editor) {
            importBtn.addEventListener('click', async function(){
                const file = importFile.files && importFile.files[0];
                if (!file) {
                    showImportMessage('err', 'Pilih file artikel dulu ya. Bisa DOCX, PDF, TXT, MD, atau HTML.');
                    return;
                }
                const ext = (file.name.split('.').pop() || '').toLowerCase();
                importBtn.disabled = true;
                importBtn.textContent = 'Merapikan...';
                showImportMessage('warn', 'Sebentar ya, isi artikel sedang dibaca. Setelah selesai akan langsung muncul di editor.');
                try {
                    if (ext === 'pdf') {
                        try {
                            const pdfText = await readPdfInBrowser(file);
                            if (pdfText && pdfText.trim().length > 20) {
                                const markdown = simpleImportedTextToMarkdown(pdfText);
                                setEditorFromMarkdown(markdown);
                                showImportMessage('ok', 'Berhasil! PDF sudah dibaca dari browser dan masuk ke editor. Silakan rapikan manual sebelum simpan.');
                                return;
                            }
                        } catch (pdfError) {
                            // Kalau browser gagal membaca PDF, lanjut coba server Laravel di bawah.
                        }
                    }

                    if (['txt', 'md', 'markdown', 'html', 'htm'].includes(ext)) {
                        const plain = await file.text();
                        setEditorFromMarkdown(simpleImportedTextToMarkdown(plain));
                        showImportMessage('ok', 'Berhasil! Isi file sudah masuk ke editor. Silakan rapikan manual sebelum simpan.');
                        return;
                    }

                    const formData = new FormData();
                    formData.append('article_file', file);
                    const response = await fetch('{{ route('admin.articles.import-document') }}', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                        body: formData
                    });
                    let data = null;
                    try { data = await response.json(); } catch (jsonError) { data = {}; }
                    if (!response.ok || !data.ok) throw new Error(data.message || 'File belum bisa dibaca otomatis. Untuk PDF, coba simpan ulang sebagai DOCX/TXT lalu upload lagi.');
                    setEditorFromMarkdown(data.content || '');
                    if (titleInput && !titleInput.value.trim() && data.title) {
                        titleInput.value = data.title;
                    }
                    showImportMessage(data.warning ? 'warn' : 'ok', data.warning || 'Berhasil! Isi artikel sudah muncul di editor. Silakan rapikan manual sebelum simpan.');
                } catch (error) {
                    showImportMessage('err', error.message || 'File belum bisa dibaca otomatis. Untuk PDF, coba simpan ulang sebagai DOCX/TXT lalu upload lagi.');
                } finally {
                    importBtn.disabled = false;
                    importBtn.textContent = 'Rapikan Otomatis';
                }
            });
        }

        if (!input || !preview) return;
        input.addEventListener('change', function () {
            const file = this.files && this.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function (event) {
                preview.innerHTML = '<img src="' + event.target.result + '" alt="Preview artikel">';
            };
            reader.readAsDataURL(file);
        });
    });
</script>
@endsection
