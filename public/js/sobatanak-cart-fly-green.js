// Global SobatAnak cart fly animation.
// FIX FINAL: cegah animasi keranjang dobel; hanya tampilkan 1 ikon hijau seperti halaman Produk.
(function(){
    if (window.__sobatCartFlyGreenGlobalLoaded) return;
    window.__sobatCartFlyGreenGlobalLoaded = true;

    // FIX: di beberapa halaman masih ada script lama yang membuat ikon orange/red `.cart-flyer`.
    // Script lama tetap boleh menjalankan tambah keranjang, tapi ikon lamanya disembunyikan
    // supaya yang terlihat hanya 1 animasi hijau seperti halaman Produk.
    function installLegacyFlyerGuard(){
        if (document.getElementById('sobat-cart-flyer-single-guard')) return;
        const style = document.createElement('style');
        style.id = 'sobat-cart-flyer-single-guard';
        style.textContent = `
            .cart-flyer,
            .sobat-green-cart-flyer{
                display:none!important;
                opacity:0!important;
                visibility:hidden!important;
                pointer-events:none!important;
            }
        `;
        document.head.appendChild(style);
    }
    installLegacyFlyerGuard();

    let lastFlyAt = 0;
    let activeFlyer = null;
    let activeTimer = null;

    function findCartTarget(){
        return document.querySelector('[data-cart-link]')
            || document.querySelector('.cart-nav-icon')
            || document.querySelector('.nav-cart')
            || document.querySelector('.cart-btn')
            || document.querySelector('a[href$="/cart"]')
            || document.querySelector('a[href*="/cart"]');
    }

    function textOf(el){
        return ((el && el.textContent) || '').trim().toLowerCase();
    }

    function isBadCartAction(el){
        if(!el) return true;

        const text = textOf(el);
        const aria = (el.getAttribute('aria-label') || '').toLowerCase();
        const name = (el.getAttribute('name') || '').toLowerCase();
        const value = (el.getAttribute('value') || '').toLowerCase();
        const form = el.closest('form');
        const action = form ? (form.getAttribute('action') || '').toLowerCase() : '';
        const method = form ? (form.querySelector('input[name="_method"]')?.value || '').toLowerCase() : '';

        const combined = [text, aria, name, value, action, method].join(' ');

        return (
            combined.includes('hapus') ||
            combined.includes('delete') ||
            combined.includes('remove') ||
            combined.includes('trash') ||
            combined.includes('kurang') ||
            combined.includes('minus') ||
            combined.includes('decrease') ||
            combined.includes('update') ||
            combined.includes('checkout') ||
            combined.includes('bayar') ||
            combined.includes('simpan') ||
            combined.includes('wishlist')
        );
    }

    function isAddToCartButton(el){
        if (!el || isBadCartAction(el)) return false;

        const form = el.closest('form');
        const action = form ? (form.getAttribute('action') || '').toLowerCase() : '';

        // Animasi tidak boleh muncul ketika klik link biasa.
        if (el.tagName && el.tagName.toLowerCase() === 'a') return false;

        if (el.matches('[data-buy], .prod-quick-add, .pdp-btn-cart, [data-add-cart], [data-add-to-cart]')) {
            return true;
        }

        // Fallback hanya untuk tombol submit pada form /cart/add.
        const isButton = el.matches('button, input[type="submit"], [role="button"]');
        const isAddCartForm = action.includes('/cart/add');
        return isButton && isAddCartForm;
    }

    function clearExistingFlyers(){
        document.querySelectorAll('.sobat-cart-flyer-green, .cart-flyer, .sobat-green-cart-flyer').forEach(function(el){
            if (el !== activeFlyer) el.remove();
        });
    }

    function fly(source){
        const cart = findCartTarget();
        if (!source || !cart) return;

        const now = Date.now();

        // Cegah dobel dari kombinasi onclick manual + listener global / double click cepat.
        if (activeFlyer || now - lastFlyAt < 900) return;
        lastFlyAt = now;
        clearExistingFlyers();

        const start = source.getBoundingClientRect();
        const end = cart.getBoundingClientRect();

        if (!start.width || !start.height || !end.width || !end.height) return;

        const size = window.innerWidth < 640 ? 40 : 46;
        const x1 = start.left + start.width / 2 - size / 2;
        const y1 = start.top + start.height / 2 - size / 2;
        const x2 = end.left + end.width / 2 - size / 2;
        const y2 = end.top + end.height / 2 - size / 2;

        const flyer = document.createElement('div');
        flyer.className = 'sobat-cart-flyer-green';
        flyer.style.left = x1 + 'px';
        flyer.style.top = y1 + 'px';
        flyer.style.setProperty('--fly-x', (x2 - x1) + 'px');
        flyer.style.setProperty('--fly-y', (y2 - y1) + 'px');

        activeFlyer = flyer;
        document.body.appendChild(flyer);

        cart.classList.remove('sobat-cart-target-pop');
        requestAnimationFrame(function(){
            flyer.classList.add('is-flying');
        });

        clearTimeout(activeTimer);
        activeTimer = setTimeout(function(){
            if (activeFlyer) {
                activeFlyer.remove();
                activeFlyer = null;
            }
            clearExistingFlyers();

            cart.classList.add('sobat-cart-target-pop');
            setTimeout(function(){
                cart.classList.remove('sobat-cart-target-pop');
            }, 700);
        }, 1300);
    }

    window.sobatCartFlyGreen = fly;
    window.sobatCartFlyAnimation = fly;

    document.addEventListener('click', function(e){
        const clicked = e.target.closest('button, a, [role="button"], input[type="submit"]');
        if (!clicked || clicked.disabled || clicked.classList.contains('disabled')) return;

        if (!isAddToCartButton(clicked)) return;

        fly(clicked);
    }, true);
})();
