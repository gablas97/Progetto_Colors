const csrfToken = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';

/* ── Image hide on error ──────────────────────────────────────────────── */
document.addEventListener('error', function (e) {
    if (e.target.tagName === 'IMG' && e.target.hasAttribute('data-hide-on-error')) {
        e.target.style.display = 'none';
    }
}, true);

/* ── Mobile menu toggle (navbar) ──────────────────────────────────────── */
const mobileMenuBtn = document.getElementById('mobile-menu-btn');
const mobileMenu    = document.getElementById('mobile-menu');
if (mobileMenuBtn && mobileMenu) {
    mobileMenuBtn.addEventListener('click', function () {
        const open = !mobileMenu.classList.contains('hidden');
        mobileMenu.classList.toggle('hidden', open);
        mobileMenuBtn.setAttribute('aria-expanded', String(!open));
    });
}

/* ── Wishlist toggle ─────────────────────────────────────────────────── */
document.addEventListener('click', function (e) {
    const btn = e.target.closest('[data-action="wishlist"]');
    if (!btn) return;

    const productId = btn.dataset.productId;

    fetch(`/account/wishlist/${productId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken(),
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        },
    })
    .then(res => {
        if (res.status === 401 || res.status === 403) {
            window.location.href = '/login';
            return null;
        }
        return res.json();
    })
    .then(data => {
        if (!data) return;
        const svg = btn.querySelector('svg');
        if (data.added) {
            svg.setAttribute('fill', 'currentColor');
            svg.classList.replace('text-gray-400', 'text-red-400');
        } else {
            svg.setAttribute('fill', 'none');
            svg.classList.replace('text-red-400', 'text-gray-400');
        }
    })
    .catch(() => {});
});

/* ── Add to cart (dalla card prodotto) ───────────────────────────────── */
document.addEventListener('click', function (e) {
    const btn = e.target.closest('[data-action="add-to-cart"]');
    if (!btn) return;

    const productId = btn.dataset.productId;

    fetch('/carrello', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken(),
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ product_id: productId, quantity: 1 }),
    })
    .then(res => res.json())
    .then(data => {
        document.querySelectorAll('[data-cart-count]').forEach(el => {
            el.textContent = data.cart_count ?? '';
        });

        btn.classList.add('bg-primary');
        btn.classList.remove('bg-gray-900');
        setTimeout(() => {
            btn.classList.remove('bg-primary');
            btn.classList.add('bg-gray-900');
        }, 800);
    })
    .catch(() => {});
});

/* ── Filter form auto-submit (products/index) ────────────────────────── */
document.querySelectorAll('[data-auto-submit]').forEach(function (el) {
    el.addEventListener('change', function () {
        el.closest('form').submit();
    });
});

/* ── Product image gallery thumbnails (products/show) ────────────────── */
document.addEventListener('click', function (e) {
    const btn = e.target.closest('[data-thumb]');
    if (!btn) return;
    const mainImg = document.getElementById('main-image');
    if (mainImg) mainImg.src = btn.dataset.thumb;
});

/* ── Quantity selector (products/show) ───────────────────────────────── */
const qtyInput = document.getElementById('qty-input');
if (qtyInput) {
    document.getElementById('qty-minus')?.addEventListener('click', function () {
        if (qtyInput.value > 1) qtyInput.value = parseInt(qtyInput.value) - 1;
    });
    document.getElementById('qty-plus')?.addEventListener('click', function () {
        if (qtyInput.value < 99) qtyInput.value = parseInt(qtyInput.value) + 1;
    });
}

/* ── Variant selector (products/show) ────────────────────────────────── */
document.querySelectorAll('.variant-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
        document.querySelectorAll('.variant-btn').forEach(function (b) {
            b.classList.remove('border-primary', 'text-primary');
        });
        btn.classList.add('border-primary', 'text-primary');
        const sel = document.getElementById('selected-variant');
        if (sel) sel.value = btn.dataset.variantId;
    });
});

/* ── Delete account toggle (account/profile) ─────────────────────────── */
const deleteToggleBtn   = document.getElementById('delete-toggle-btn');
const deleteAccountForm = document.getElementById('delete-account-form');
if (deleteToggleBtn && deleteAccountForm) {
    deleteToggleBtn.addEventListener('click', function () {
        const isHidden = deleteAccountForm.classList.toggle('hidden');
        deleteToggleBtn.textContent = isHidden ? 'Procedi' : 'Annulla';
    });
}
