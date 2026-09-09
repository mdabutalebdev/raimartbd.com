import './bootstrap';
import '@fortawesome/fontawesome-free/js/all.js';

import Swiper from 'swiper';
import { Navigation, Pagination, Autoplay } from 'swiper/modules';

import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';

    document.addEventListener('livewire:navigated', () => {
    // Hero Banner Swiper
    new Swiper('.hero-swiper', {
        modules: [Navigation, Pagination, Autoplay],
        loop: true,
        autoplay: { delay: 4000, disableOnInteraction: false },
        pagination: { el: '.swiper-pagination', clickable: true },
        navigation: { nextEl: '.hero-next', prevEl: '.hero-prev' },
    });

    // Product Grid/Row Swipers (Best Selling, New Arrivals)
    const productSwipers = document.querySelectorAll('.product-swiper');
    productSwipers.forEach(el => {
        new Swiper(el, {
            modules: [Navigation],
            slidesPerView: 2,
            spaceBetween: 12,
            loop: true,
            navigation: {
                nextEl: el.parentElement.querySelector('[aria-label="Scroll right"], [data-suggest-next]'),
                prevEl: el.parentElement.querySelector('[aria-label="Scroll left"], [data-suggest-prev]'),
            },
            breakpoints: {
                640: { slidesPerView: 3, spaceBetween: 16 },
                1024: { slidesPerView: 5, spaceBetween: 16 }
            }
        });
    });

    // Category Swiper
    const categorySwipers = document.querySelectorAll('.category-swiper');
    categorySwipers.forEach(el => {
        new Swiper(el, {
            modules: [Navigation],
            slidesPerView: 3,
            spaceBetween: 12,
            loop: true,
            navigation: {
                nextEl: el.parentElement.querySelector('.category-next'),
                prevEl: el.parentElement.querySelector('.category-prev'),
            },
            breakpoints: {
                480: { slidesPerView: 4, spaceBetween: 12 },
                640: { slidesPerView: 6, spaceBetween: 16 },
                768: { slidesPerView: 8, spaceBetween: 16 },
                1024: { slidesPerView: 10, spaceBetween: 16 }
            }
        });
    });
    
    // Suggest Swiper (Cart Suggestions)
    const suggestSwipers = document.querySelectorAll('.suggest-swiper');
    suggestSwipers.forEach(el => {
        new Swiper(el, {
            modules: [Navigation],
            slidesPerView: 'auto',
            spaceBetween: 8,
            loop: true,
            navigation: {
                nextEl: el.parentElement.querySelector('[data-suggest-next]'),
                prevEl: el.parentElement.querySelector('[data-suggest-prev]'),
            }
        });
    });

    // Testimonials Swiper
    new Swiper('.testimonial-swiper', {
        modules: [Navigation, Autoplay],
        slidesPerView: 1,
        spaceBetween: 20,
        loop: true,
        autoplay: { delay: 4000, disableOnInteraction: false },
        navigation: {
            nextEl: '.testi-next',
            prevEl: '.testi-prev',
        },
        breakpoints: {
            768: { slidesPerView: 2 },
            1024: { slidesPerView: 3 }
        }
    });
});

// ---------------------------------------------------------------------------
// GA4 helper: push an Enhanced Ecommerce event (built server-side and returned
// in an AJAX response's `ga` field) into the dataLayer for GTM to pick up.
// The `ecommerce: null` reset stops GTM merging leftover data between events.
// ---------------------------------------------------------------------------
window.gaPush = function (ga) {
    if (!ga || !ga.event) return;
    window.dataLayer = window.dataLayer || [];
    window.dataLayer.push({ ecommerce: null });
    window.dataLayer.push({ event: ga.event, ecommerce: ga.ecommerce || {} });
};

// Read the GA4 client_id / session_id from the browser cookies so the server
// can attribute its Measurement Protocol `purchase` hit to the same user/session.
// _ga        = "GA1.1.<clientId1>.<clientId2>"
// _ga_XXXXXX = "GS1.1.<sessionId>.<...>"
window.gaClientId = function () {
    try {
        const m = document.cookie.match(/(?:^|;\s*)_ga=([^;]+)/);
        if (!m) return '';
        const parts = m[1].split('.');
        return parts.length >= 4 ? parts.slice(-2).join('.') : '';
    } catch (e) { return ''; }
};
window.gaSessionId = function () {
    try {
        const m = document.cookie.match(/(?:^|;\s*)_ga_[^=]+=([^;]+)/);
        if (!m) return '';
        const parts = m[1].split('.');
        return parts.length >= 3 ? parts[2] : '';
    } catch (e) { return ''; }
};

// select_item — clicking a product card's link. Delegated at document level so
// it survives SPA navigation; the item comes from the card's data-ga-item attr.
document.addEventListener('click', (event) => {
    const link = event.target.closest('a[href]');
    if (!link) return;
    const card = link.closest('[data-ga-item]');
    if (!card) return;
    try {
        const item = JSON.parse(card.dataset.gaItem);
        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({ ecommerce: null });
        window.dataLayer.push({ event: 'select_item', ecommerce: { items: [item] } });
    } catch (e) { /* ignore */ }
});

// Promotions: fire view_promotion for each banner once it's on the page, and
// select_promotion when one is clicked. Banner elements carry data-ga-promotion.
document.addEventListener('livewire:navigated', () => {
    document.querySelectorAll('[data-ga-promotion]').forEach((el) => {
        if (el.dataset.gaPromoSeen) return;
        el.dataset.gaPromoSeen = '1';
        try {
            const promo = JSON.parse(el.dataset.gaPromotion);
            window.dataLayer = window.dataLayer || [];
            window.dataLayer.push({ ecommerce: null });
            window.dataLayer.push({ event: 'view_promotion', ecommerce: promo });
        } catch (e) { /* ignore */ }
    });
});
document.addEventListener('click', (event) => {
    const el = event.target.closest('[data-ga-promotion]');
    if (!el) return;
    try {
        const promo = JSON.parse(el.dataset.gaPromotion);
        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({ ecommerce: null });
        window.dataLayer.push({ event: 'select_promotion', ecommerce: promo });
    } catch (e) { /* ignore */ }
});

document.addEventListener('livewire:navigated', () => {
    const menu = document.getElementById('categories-menu');
    if (!menu) return;

    const toggle = document.getElementById('categories-toggle');
    const panel = document.getElementById('categories-panel');
    const subPanel = document.getElementById('subcategories-panel');
    const items = menu.querySelectorAll('.category-item');
    const shopUrl = menu.dataset.shopUrl;

    const highlight = (item) => {
        items.forEach((i) => i.classList.remove('bg-brand-bg', 'text-brand-orange'));
        item?.classList.add('bg-brand-bg', 'text-brand-orange');
    };

    const open = () => {
        panel.classList.remove('invisible', 'opacity-0');
        panel.classList.add('opacity-100');
        toggle.setAttribute('aria-expanded', 'true');
        toggle.classList.add('text-brand-orange');
    };

    const close = () => {
        panel.classList.add('invisible', 'opacity-0');
        panel.classList.remove('opacity-100');
        toggle.setAttribute('aria-expanded', 'false');
        toggle.classList.remove('text-brand-orange');
        subPanel.textContent = 'Select a category to view subcategories';
        highlight(null);
    };

    const isDesktop = () => window.matchMedia('(min-width: 768px)').matches;

    toggle.addEventListener('click', (event) => {
        event.stopPropagation();

        if (!isDesktop()) {
            document.getElementById('category-drawer')?.classList.remove('hidden');
            return;
        }

        toggle.getAttribute('aria-expanded') === 'true' ? close() : open();
    });

    items.forEach((item) => {
        item.addEventListener('mouseenter', () => {
            highlight(item);
            const subcategories = JSON.parse(item.dataset.subcategories || '[]');
            subPanel.innerHTML = subcategories
                .map((sub) => `<a href="${shopUrl}?category=${encodeURIComponent(sub.slug)}" class="block rounded-md px-3 py-2 text-gray-600 hover:bg-brand-bg hover:text-brand-orange">${sub.name}</a>`)
                .join('');
        });
    });

    document.addEventListener('click', (event) => {
        if (!menu.contains(event.target)) close();
    });
});

document.addEventListener('livewire:navigated', () => {
    const menuToggle = document.getElementById('main-menu-toggle');
    const menuPanel = document.getElementById('main-menu-panel');
    if (!menuToggle || !menuPanel) return;

    const iconOpen = document.getElementById('main-menu-icon-open');
    const iconClose = document.getElementById('main-menu-icon-close');

    menuToggle.addEventListener('click', () => {
        const isOpen = menuToggle.getAttribute('aria-expanded') === 'true';
        menuToggle.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
        menuPanel.classList.toggle('hidden', isOpen);
        iconOpen.classList.toggle('!hidden', !isOpen);
        iconClose.classList.toggle('!hidden', isOpen);
    });
});

document.addEventListener('livewire:navigated', () => {
    document.querySelectorAll('[data-drawer-open]').forEach((button) => {
        button.addEventListener('click', () => {
            const targetId = button.dataset.drawerOpen || 'category-drawer';
            const drawer = document.getElementById(targetId) || document.getElementById('category-drawer');
            if (drawer) drawer.classList.remove('hidden');
        });
    });

    document.querySelectorAll('[data-drawer-close]').forEach((button) => {
        button.addEventListener('click', () => {
            button.closest('.fixed')?.classList.add('hidden');
        });
    });
});


document.addEventListener('livewire:navigated', () => {
    document.querySelectorAll('[data-filter-tabs]').forEach((tabs) => {
        const section = tabs.closest('section');
        const items = section.querySelectorAll('[data-featured-item]');
        const emptyState = section.querySelector('[data-filter-empty]');

        const activeClasses = ['bg-brand-orange', 'text-white', 'hover:bg-brand-navy'];
        const inactiveClasses = ['border', 'border-brand-navy/15', 'text-brand-navy', 'hover:border-brand-orange', 'hover:text-brand-orange'];

        const setActive = (tab) => {
            tabs.querySelectorAll('.filter-tab').forEach((t) => {
                t.classList.remove(...activeClasses);
                t.classList.add(...inactiveClasses);
            });
            tab.classList.remove(...inactiveClasses);
            tab.classList.add(...activeClasses);
        };

        setActive(tabs.querySelector('[data-filter="all"]'));

        tabs.querySelectorAll('.filter-tab').forEach((tab) => {
            tab.addEventListener('click', () => {
                setActive(tab);
                const filter = tab.dataset.filter;
                let visibleCount = 0;

                items.forEach((item) => {
                    const matches = filter === 'all' || item.dataset.category === filter;
                    item.classList.toggle('hidden', !matches);
                    if (matches) visibleCount++;
                });

                emptyState?.classList.toggle('hidden', visibleCount > 0);
            });
        });
    });
});

document.addEventListener('livewire:navigated', () => {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    const setButtonState = (button, inWishlist) => {
        button.classList.toggle('text-brand-orange', inWishlist);
        button.classList.toggle('text-brand-navy', !inWishlist);
        button.setAttribute('aria-pressed', inWishlist ? 'true' : 'false');
        button.setAttribute('aria-label', inWishlist ? 'Remove from wishlist' : 'Add to wishlist');

        // FontAwesome JS replaces <i> with <svg> and moves fa-solid/fa-regular to data-prefix="fas"/"far"
        const solid = button.querySelector('[data-prefix="fas"]');
        const regular = button.querySelector('[data-prefix="far"]');
        
        if (solid) solid.style.display = inWishlist ? '' : 'none';
        if (regular) regular.style.display = inWishlist ? 'none' : '';
    };

    const updateCountBadges = (count) => {
        document.querySelectorAll('[data-wishlist-count]').forEach((badge) => {
            badge.textContent = count;
            badge.classList.toggle('hidden', count === 0);
        });
    };

    document.addEventListener('click', (event) => {
        const button = event.target.closest('[data-wishlist-toggle]');
        if (!button) return;

        event.preventDefault();

        fetch(button.dataset.toggleUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                Accept: 'application/json',
            },
        })
            .then((response) => response.json())
            .then((data) => {
                setButtonState(button, data.in_wishlist);
                updateCountBadges(data.count);
                window.gaPush(data.ga);

                const wishlistCard = button.closest('[data-wishlist-page] [data-wishlist-card]');
                if (!data.in_wishlist && wishlistCard) {
                    wishlistCard.classList.add('opacity-0', 'scale-95');
                    setTimeout(() => {
                        const grid = wishlistCard.closest('[data-wishlist-page]');
                        wishlistCard.remove();
                        if (grid && grid.children.length === 0) {
                            document.querySelector('[data-wishlist-empty]')?.classList.remove('hidden');
                            grid.classList.add('hidden');
                        }
                    }, 300);
                }
            })
            .catch(() => {});
    });
});

// ---------------------------------------------------------------------------
// Drag-to-scroll: make every horizontal slider draggable with the mouse.
// Touch devices already scroll natively, so this only engages for a mouse.
// ---------------------------------------------------------------------------
document.addEventListener('livewire:navigated', () => {
    const enableDragScroll = (el) => {
        if (el.dataset.dragBound) return;
        el.dataset.dragBound = '1';

        let isDown = false;
        let startX = 0;
        let startScroll = 0;

        el.addEventListener('pointerdown', (event) => {
            if (event.pointerType !== 'mouse' || event.button !== 0) return;
            isDown = true;
            startX = event.clientX;
            startScroll = el.scrollLeft;
            el.style.scrollBehavior = 'auto';
        });

        el.addEventListener('pointermove', (event) => {
            if (!isDown) return;
            const dx = event.clientX - startX;
            if (Math.abs(dx) > 6) el.classList.add('is-dragging');
            el.scrollLeft = startScroll - dx;
        });

        const stop = () => {
            if (!isDown) return;
            isDown = false;
            el.style.scrollBehavior = '';
            el.classList.remove('is-dragging');
        };

        el.addEventListener('pointerup', stop);
        el.addEventListener('pointerleave', stop);
        el.addEventListener('pointercancel', stop);

        // Swallow the click after a drag ONLY when the track actually scrolled,
        // so a plain click/tap on a card always opens its link.
        el.addEventListener('click', (event) => {
            if (Math.abs(el.scrollLeft - startScroll) > 5) {
                event.preventDefault();
                event.stopPropagation();
            }
        }, true);
    };

    document.querySelectorAll('.overflow-x-auto').forEach(enableDragScroll);
});

// ---------------------------------------------------------------------------
// Slide-out cart drawer (AJAX). Opens from the right like a mini-cart; add /
// update / remove happen without a page reload. Document-level delegation so
// it survives Livewire SPA navigation.
// ---------------------------------------------------------------------------
(() => {
    const csrf = () => document.querySelector('meta[name="csrf-token"]')?.content;
    const drawer = () => document.getElementById('cart-drawer');
    const panel = () => document.getElementById('cart-drawer-panel');
    const backdrop = () => drawer()?.querySelector('[data-cart-backdrop]');
    const body = () => document.getElementById('cart-drawer-body');

    const updateCount = (count) => {
        document.querySelectorAll('[data-cart-count]').forEach((el) => {
            el.textContent = count;
            el.classList.toggle('hidden', !count);
        });
    };

    const applyPayload = (data) => {
        if (!data) return;
        if (typeof data.count !== 'undefined') updateCount(data.count);
        if (typeof data.html !== 'undefined' && body()) body().innerHTML = data.html;
    };

    const showDrawer = () => {
        const d = drawer();
        if (!d) return;
        d.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        requestAnimationFrame(() => {
            panel()?.classList.remove('translate-x-full');
            backdrop()?.classList.remove('opacity-0');
        });
    };

    const closeDrawer = () => {
        const d = drawer();
        if (!d) return;
        panel()?.classList.add('translate-x-full');
        backdrop()?.classList.add('opacity-0');
        document.body.style.overflow = '';
        setTimeout(() => d.classList.add('hidden'), 300);
    };

    const refreshCart = async () => {
        try {
            const res = await fetch('/cart/partial', { headers: { 'Accept': 'application/json' } });
            applyPayload(await res.json());
        } catch (e) { /* ignore */ }
    };

    const openCart = async () => { showDrawer(); await refreshCart(); };

    const send = async (url, method, payload) => {
        const res = await fetch(url, {
            method,
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf(),
                ...(payload ? { 'Content-Type': 'application/json' } : {}),
            },
            body: payload ? JSON.stringify(payload) : null,
        });
        return res.json();
    };

    // Open / close
    document.addEventListener('click', (event) => {
        if (event.target.closest('[data-cart-open]')) { event.preventDefault(); openCart(); return; }
        if (event.target.closest('[data-cart-close]')) { event.preventDefault(); closeDrawer(); }
    });

    // "You may also like" arrows (delegated — the drawer body is replaced on refresh)
    document.addEventListener('click', (event) => {
        const prev = event.target.closest('[data-suggest-prev]');
        const next = event.target.closest('[data-suggest-next]');
        if (!prev && !next) return;

        const rail = document.getElementById('cart-suggestions');
        if (!rail) return;
        const first = rail.firstElementChild;
        const step = first ? first.getBoundingClientRect().width + 8 : 176;
        rail.scrollBy({ left: (next ? 1 : -1) * step, behavior: 'smooth' });
    });
    document.addEventListener('keydown', (event) => { if (event.key === 'Escape') closeDrawer(); });

    // Quantity +/- and remove inside the drawer (delegated — body is replaced on refresh)
    document.addEventListener('click', async (event) => {
        const inc = event.target.closest('[data-cart-inc]');
        const dec = event.target.closest('[data-cart-dec]');
        const rm = event.target.closest('[data-cart-remove]');
        if (inc) {
            const qty = parseInt(inc.dataset.qty, 10) + 1;
            if (qty > parseInt(inc.dataset.max || '99999', 10)) return;
            applyPayload(await send('/cart/' + inc.dataset.id, 'PATCH', { quantity: qty }));
        } else if (dec) {
            applyPayload(await send('/cart/' + dec.dataset.id, 'PATCH', { quantity: parseInt(dec.dataset.qty, 10) - 1 }));
        } else if (rm) {
            const data = await send('/cart/' + rm.dataset.id, 'DELETE');
            applyPayload(data);
            window.gaPush(data.ga);
        }
    });

    // Intercept "Add to cart" forms -> AJAX add, then pop the drawer open
    document.addEventListener('submit', async (event) => {
        const form = event.target.closest('form[data-add-to-cart]');
        if (!form) return;
        // Let the "Buy Now" button (name="redirect") post normally → server redirects to checkout.
        if (event.submitter && event.submitter.name === 'redirect') return;
        event.preventDefault();
        try {
            const res = await fetch(form.action, {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf() },
                body: new FormData(form),
            });
            const data = await res.json();
            applyPayload(data);
            window.gaPush(data.ga);
            showDrawer();
        } catch (e) { form.submit(); }
    });
})();

// ---------------------------------------------------------------------------
// Checkout: fill the GA4 client_id/session_id hidden inputs (for the server-side
// purchase) and fire the add_shipping_info / add_payment_info funnel steps.
// Re-runs on SPA navigation; reads its ecommerce data from #ga-checkout-data.
// ---------------------------------------------------------------------------
document.addEventListener('livewire:navigated', () => {
    const form = document.getElementById('checkout-form');
    const dataEl = document.getElementById('ga-checkout-data');
    if (!form || !dataEl) return;

    let data;
    try { data = JSON.parse(dataEl.textContent); } catch (e) { return; }

    const fillIds = () => {
        const c = document.getElementById('ga_client_id');
        const s = document.getElementById('ga_session_id');
        if (c && window.gaClientId) c.value = window.gaClientId();
        if (s && window.gaSessionId) s.value = window.gaSessionId();
    };

    const push = (event, extra) => {
        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({ ecommerce: null });
        window.dataLayer.push({
            event,
            ecommerce: Object.assign({ currency: data.currency, value: data.value, items: data.items }, extra || {}),
        });
    };

    fillIds();

    // District → delivery area + live delivery cost / total.
    // Dhaka bills the "inside" rate, every other district the "outside" one.
    // The server recalculates the same way on submit, so this is display only.
    const district = document.getElementById('district-select');
    const areaInput = document.getElementById('delivery_area');
    const shippingEl = document.querySelector('[data-shipping-amount]');
    const totalEl = document.querySelector('[data-total-amount]');
    // We will compute goodsTotal dynamically inside refreshTotals

    const taka = (n) => '৳' + Math.round(n).toLocaleString('en-US');

    let thanaMap = {};
    try { thanaMap = JSON.parse(document.getElementById('thana-map')?.textContent || '{}'); } catch (e) { /* ignore */ }

    /** Refill the thana picker for the given district — runs immediately, no waiting on Alpine. */
    const refillThanas = (value) => {
        const box = form.querySelector('[data-thana-select]');
        if (!box || !window.Alpine) return;
        try {
            Alpine.$data(box).setOptions(thanaMap[value] || []);
        } catch (e) { /* component not mounted yet */ }
    };

    const refreshTotals = (value) => {
        if (!district) return;
        const chosen = value ?? district.value;
        const inside = chosen === 'Dhaka';
        if (areaInput) areaInput.value = inside ? 'inside' : 'outside';

        if (!shippingEl || !totalEl) return;
        
        const goodsTotal = Math.max(0, (data.value || 0) - (data.discount || 0));
        const freeShipping = !!data.freeShipping;

        const fee = freeShipping
            ? 0
            : parseFloat(inside ? district.dataset.insideFee : district.dataset.outsideFee) || 0;

        shippingEl.innerHTML = fee > 0 ? taka(fee) : '<span class="font-semibold text-green-600">FREE</span>';
        totalEl.textContent = taka(goodsTotal + fee);
    };

    // The district picker is a hidden input driven by the searchable dropdown, and
    // x-model only writes it on Alpine's next tick — so use the value carried on the
    // event instead, which keeps the thana list and totals in step instantly.
    district?.addEventListener('change', () => refreshTotals());
    form.addEventListener('select-changed', (event) => {
        if (!event.target.closest('[data-district-select]')) return; // thana picks need no refill

        const value = event.detail;
        if (district) district.value = value;
        refreshTotals(value);
        refillThanas(value);
    });

    if (district?.value) refreshTotals();

    // Live character counter for the order note.
    const notes = document.getElementById('checkout-notes');
    const notesCount = document.getElementById('notes-count');
    notes?.addEventListener('input', () => {
        if (notesCount) notesCount.textContent = notes.value.length;
    });

    // Quantity +/- and remove inside "Order review": update the cart, then fetch
    // the new HTML components and swap them in to avoid a full page reload.
    const updateLine = async (url, method, payload) => {
        try {
            const resData = await fetch(url, {
                method,
                headers: {
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                    ...(payload ? { 'Content-Type': 'application/json' } : {}),
                },
                body: payload ? JSON.stringify(payload) : null,
            });
            const parsedData = await resData.json();

            if (parsedData.count === 0) {
                window.location.href = '/';
                return;
            }

            const resHtml = await fetch(window.location.href, {
                headers: { 'Accept': 'text/html' }
            });
            const text = await resHtml.text();
            
            const parser = new DOMParser();
            const doc = parser.parseFromString(text, 'text/html');
            
            const newOrderReview = doc.getElementById('checkout-order-review');
            const newTotals = doc.getElementById('checkout-totals');
            
            if (newOrderReview) document.getElementById('checkout-order-review').innerHTML = newOrderReview.innerHTML;
            if (newTotals) document.getElementById('checkout-totals').innerHTML = newTotals.innerHTML;
            
            const newDataEl = doc.getElementById('ga-checkout-data');
            if (newDataEl) {
                document.getElementById('ga-checkout-data').textContent = newDataEl.textContent;
                data = JSON.parse(newDataEl.textContent);
                refreshTotals();
            }

            if (typeof parsedData.count !== 'undefined') {
                document.querySelectorAll('[data-cart-count]').forEach((el) => {
                    el.textContent = parsedData.count;
                    el.classList.toggle('hidden', !parsedData.count);
                });
            }

        } catch (e) { /* leave the page as-is on a network error */ }
    };

    form.addEventListener('click', (event) => {
        const inc = event.target.closest('[data-checkout-inc]');
        const dec = event.target.closest('[data-checkout-dec]');
        const rm = event.target.closest('[data-checkout-remove]');

        if (inc) {
            const qty = parseInt(inc.dataset.qty, 10) + 1;
            if (qty > parseInt(inc.dataset.max || '99999', 10)) return;
            updateLine('/cart/' + inc.dataset.id, 'PATCH', { quantity: qty });
        } else if (dec) {
            updateLine('/cart/' + dec.dataset.id, 'PATCH', { quantity: parseInt(dec.dataset.qty, 10) - 1 });
        } else if (rm) {
            updateLine('/cart/' + rm.dataset.id, 'DELETE');
        }
    });

    let paymentType = null;
    form.querySelectorAll('input[name="payment_method"]').forEach((radio) => {
        radio.addEventListener('change', () => {
            if (paymentType === radio.value) return;
            paymentType = radio.value;
            push('add_payment_info', { payment_type: radio.value });
        });
    });

    form.addEventListener('submit', () => {
        fillIds(); // capture client_id now, in case app.js loaded after first paint
        push('add_shipping_info', { shipping_tier: 'Standard' });
        if (!paymentType) {
            const checked = form.querySelector('input[name="payment_method"]:checked');
            push('add_payment_info', { payment_type: checked ? checked.value : 'cod' });
        }
    });

    // ---------------------------------------------------------------------------
    // Coupon apply / remove via AJAX — no page reload, no form data loss.
    // ---------------------------------------------------------------------------

    const couponApplyForm = document.getElementById('coupon-form');
    const couponRemoveForm = document.getElementById('coupon-remove-form');

    const handleCouponResponse = (result) => {
        // Rebuild the coupon section content
        const couponBody = document.querySelector('[x-data*="accordion"] .border-t.border-brand-navy\\/10');
        if (couponBody) {
            if (result.coupon) {
                couponBody.innerHTML = `
                    <div class="flex items-center justify-between gap-2 rounded border border-green-200 bg-green-50 px-3 py-2.5">
                        <span class="text-sm text-green-700">
                            <i class="fa-solid fa-tag text-xs"></i>
                            <span class="font-bold">${result.coupon.code}</span> — ${result.coupon.label}
                        </span>
                        <button type="button" data-coupon-remove-btn class="shrink-0 text-xs font-semibold text-red-500 hover:underline">Remove</button>
                    </div>`;
            } else {
                couponBody.innerHTML = `
                    <div class="flex gap-2">
                        <input type="text" name="code" form="coupon-form" placeholder="Enter coupon code"
                            class="w-full rounded border border-brand-navy/15 px-3 py-2.5 text-sm uppercase placeholder:normal-case placeholder:text-brand-navy/40 focus:border-brand-orange focus:outline-none">
                        <button type="submit" form="coupon-form"
                            class="shrink-0 rounded bg-brand-navy px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-orange">Apply</button>
                    </div>`;
            }
        }

        // Show flash message
        const existing = document.getElementById('coupon-flash');
        if (existing) existing.remove();
        const flash = document.createElement('div');
        flash.id = 'coupon-flash';
        flash.className = result.ok
            ? 'mt-2 rounded-lg border border-green-200 bg-green-50 px-4 py-2 text-sm text-green-700'
            : 'mt-2 rounded-lg border border-red-200 bg-red-50 px-4 py-2 text-sm text-red-600';
        flash.textContent = result.message;
        couponBody?.appendChild(flash);
        setTimeout(() => flash.remove(), 4000);

        // Update totals
        const totalsBox = document.getElementById('checkout-totals');
        if (totalsBox) {
            let totalsHtml = `<div class="space-y-2.5 text-sm">
                <div class="flex justify-between">
                    <span class="text-brand-navy/60">Sub total</span>
                    <span class="font-medium">${taka(result.subtotal)}</span>
                </div>`;

            if (result.discount > 0) {
                totalsHtml += `<div class="flex justify-between text-green-600">
                    <span>Discount${result.coupon ? ' (' + result.coupon.code + ')' : ''}</span>
                    <span>− ${taka(result.discount)}</span>
                </div>`;
            }

            totalsHtml += `<div class="flex justify-between">
                    <span class="text-brand-navy/60">Delivery cost</span>
                    <span class="font-medium" data-shipping-amount>${result.freeShipping ? '<span class="font-semibold text-green-600">FREE</span>' : taka(result.shippingFee)}</span>
                </div>
                <div class="flex justify-between border-t border-brand-navy/10 pt-3 text-lg font-bold text-brand-navy">
                    <span>Total</span>
                    <span data-total-amount>${taka(result.total)}</span>
                </div>
            </div>`;
            totalsBox.innerHTML = totalsHtml;
        }

        // Update GA4 data so delivery recalculation stays correct
        data.discount = result.discount;
        data.freeShipping = result.freeShipping;
    };

    // Apply coupon
    document.addEventListener('submit', (e) => {
        if (!e.target.closest('[data-coupon-apply]') && !(e.submitter && e.submitter.getAttribute('form') === 'coupon-form')) return;
        const applyForm = document.getElementById('coupon-form');
        if (!applyForm || !e.target.closest('#coupon-form') && e.target !== applyForm) {
            // Check if the submit button belongs to the coupon-form via form="" attribute
            if (!e.submitter || e.submitter.getAttribute('form') !== 'coupon-form') return;
        }
        e.preventDefault();

        const codeInput = document.querySelector('input[name="code"][form="coupon-form"]');
        const code = codeInput?.value?.trim();
        if (!code) return;

        fetch(applyForm.action, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
            },
            body: JSON.stringify({ code }),
        })
        .then(r => r.json())
        .then(handleCouponResponse)
        .catch(() => {});
    });

    // Remove coupon (delegated, since the remove button is dynamically inserted)
    document.addEventListener('click', (e) => {
        const removeBtn = e.target.closest('[data-coupon-remove-btn]') || e.target.closest('button[form="coupon-remove-form"]');
        if (!removeBtn) return;
        e.preventDefault();

        const removeForm = document.getElementById('coupon-remove-form');
        if (!removeForm) return;

        fetch(removeForm.action, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
            },
        })
        .then(r => r.json())
        .then(handleCouponResponse)
        .catch(() => {});
    });
});

// ---------------------------------------------------------------------------
// Reusable slider carousel (bullet dots + arrows + auto-slide). Attach with
// x-data="carousel()" on a wrapper whose scroll track has x-ref="slider".
// Plain global (not Alpine.data) so it works with the double Alpine load.
// ---------------------------------------------------------------------------
// Simple tab filter for the home "Featured" grid. A plain global (not an inline
// x-data object) so its reactivity survives this app's double Alpine load.
window.tabFilter = function () {
    return { tab: 'all' };
};

// Searchable single-select (checkout district / thana pickers).
// `groups` maps a district to its thanas so the thana list can follow the district.
window.searchSelect = function (config) {
    return {
        open: false,
        query: '',
        selected: config.selected || '',
        options: config.options || [],
        placeholder: config.placeholder || 'Select',

        get filtered() {
            const q = this.query.trim().toLowerCase();

            return q ? this.options.filter((o) => o.toLowerCase().includes(q)) : this.options;
        },
        toggle() {
            this.open = !this.open;
            this.query = '';
            if (this.open) this.$nextTick(() => this.$refs.search?.focus());
        },
        choose(option) {
            this.selected = option;
            this.open = false;
            this.query = '';
            // Let listeners (delivery cost, dependent thana list) react.
            this.$el.dispatchEvent(new CustomEvent('select-changed', { detail: option, bubbles: true }));
        },
        /** Swap the list when the parent select changes (district → thanas). */
        setOptions(options) {
            this.options = options || [];
            this.selected = '';
            this.query = '';
        },
    };
};

// Admin → Create Order: line items with live subtotal / delivery / total.
window.manualOrder = function (products, insideFee, outsideFee) {
    return {
        products,
        lines: [{ product_id: '', quantity: 1 }],
        district: '',
        discount: 0,
        shipping: insideFee,

        addLine() { this.lines.push({ product_id: '', quantity: 1 }); },
        removeLine(index) { this.lines.splice(index, 1); },

        lineTotal(line) {
            const product = this.products[line.product_id];

            return product ? Math.round(product.price * (line.quantity || 0)) : 0;
        },
        get subtotal() {
            return this.lines.reduce((sum, line) => sum + this.lineTotal(line), 0);
        },
        get total() {
            return Math.max(0, this.subtotal - (this.discount || 0)) + (this.shipping || 0);
        },

        init() {
            // Keep the delivery charge in step with the chosen district.
            this.$watch('district', (value) => {
                this.shipping = value === 'Dhaka' ? insideFee : outsideFee;
            });
        },
    };
};

// Simple open/close panel (checkout coupon box). Plain global so its reactivity
// survives this app's double Alpine load — inline x-data objects don't.
window.accordion = function (open = false) {
    return {
        open: !!open,
        toggle() { this.open = !this.open; },
    };
};

// Client-side pager: reveals `perPage` items at a time and builds the numbered
// page list (with … gaps) for grids like "Just for you".
window.paginator = function (total, perPage) {
    return {
        page: 1,
        total,
        perPage,

        get pages() {
            return Math.max(1, Math.ceil(this.total / this.perPage));
        },
        /** Is the item at this 0-based index on the current page? */
        shows(index) {
            const start = (this.page - 1) * this.perPage;

            return index >= start && index < start + this.perPage;
        },
        /** Page numbers to render, with '…' where the run is broken. */
        get numbers() {
            const last = this.pages;
            const current = this.page;

            if (last <= 7) {
                return Array.from({ length: last }, (_, i) => i + 1);
            }

            let from = Math.max(2, current - 1);
            let to = Math.min(last - 1, current + 1);

            if (current <= 3) { from = 2; to = 4; }
            if (current >= last - 2) { from = last - 3; to = last - 1; }

            const out = [1];
            if (from > 2) out.push('…');
            for (let i = from; i <= to; i++) out.push(i);
            if (to < last - 1) out.push('…');
            out.push(last);

            return out;
        },
        go(n) {
            if (n === '…' || n < 1 || n > this.pages) return;
            this.page = n;
            this.$refs.top?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        },
        next() { this.go(this.page + 1); },
        prev() { this.go(this.page - 1); },
    };
};

// Floating quick-contact bubble (Messenger / WhatsApp / call).
window.quickContact = function () {
    return { open: false };
};
