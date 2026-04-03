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

/* ── Card variant selector ────────────────────────────────────────────── */
document.addEventListener('click', function (e) {
    const btn = e.target.closest('.card-variant-btn');
    if (!btn) return;

    const card = btn.closest('.group');
    if (!card) return;

    card.querySelectorAll('.card-variant-btn').forEach(function (b) {
        b.classList.remove('selected');
    });
    btn.classList.add('selected');

    const errMsg = card.querySelector('.card-variant-error');
    if (errMsg) errMsg.classList.add('hidden');
});

/* ── Add to cart (dalla card prodotto) ───────────────────────────────── */
document.addEventListener('click', function (e) {
    const btn = e.target.closest('[data-action="add-to-cart"]');
    if (!btn) return;

    const productId = btn.dataset.productId;
    const card = btn.closest('.group');

    // Controlla se la card ha varianti e se ne è stata selezionata una
    if (card) {
        const variantBtns = card.querySelectorAll('.card-variant-btn');
        if (variantBtns.length > 0) {
            const selected = card.querySelector('.card-variant-btn.selected');
            if (!selected) {
                const errMsg = card.querySelector('.card-variant-error');
                if (errMsg) errMsg.classList.remove('hidden');
                return;
            }
        }
    }

    const selectedVariant = card ? card.querySelector('.card-variant-btn.selected') : null;
    const body = { product_id: productId, quantity: 1 };
    if (selectedVariant) body.product_variant_id = selectedVariant.dataset.variantId;

    fetch('/carrello', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken(),
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(body),
    })
    .then(res => res.json())
    .then(data => {
        document.querySelectorAll('[data-cart-count]').forEach(el => {
            el.textContent = data.cart_count ?? '';
        });
    })
    .catch(() => {});
});

/* ── Filter form auto-submit (products/index) ────────────────────────── */
document.querySelectorAll('[data-auto-submit]').forEach(function (el) {
    el.addEventListener('change', function () {
        const form = el.closest('form');
        // Se il valore è vuoto e c'è un campo nascosto da rimuovere, lo rimuove prima di inviare
        if (el.value === '' && el.dataset.clearsOnEmpty) {
            const hidden = form.querySelector('[name="' + el.dataset.clearsOnEmpty + '"]');
            if (hidden) hidden.remove();
        }
        form.submit();
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
            b.classList.remove('selected');
        });
        btn.classList.add('selected');
        const sel = document.getElementById('selected-variant');
        if (sel) sel.value = btn.dataset.variantId;
        const label = document.getElementById('selected-variant-label');
        if (label) label.textContent = btn.dataset.variantName;
        const msg = document.getElementById('variant-required-msg');
        if (msg) msg.classList.add('hidden');
    });
});

document.addEventListener('DOMContentLoaded', function () {
    const addToCartForm = document.getElementById('add-to-cart-form');
    if (addToCartForm) {
        addToCartForm.addEventListener('submit', function (e) {
            const sel = document.getElementById('selected-variant');
            const hasVariants = document.querySelectorAll('.variant-btn').length > 0;
            if (hasVariants && (!sel || !sel.value)) {
                e.preventDefault();
                const msg = document.getElementById('variant-required-msg');
                if (msg) msg.classList.remove('hidden');
            }
        });
    }
});

/* ── Cart dropdown preview ───────────────────────────────────────────── */
(function () {
    const wrapper  = document.getElementById('cart-dropdown-wrapper');
    const dropdown = document.getElementById('cart-dropdown');
    if (!wrapper || !dropdown) return;

    let loaded = false;
    let hideTimer = null;

    const show = () => {
        clearTimeout(hideTimer);
        dropdown.style.display = 'block';
        if (loaded) return;
        loaded = true;

        fetch('/carrello/anteprima', {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken() },
        })
        .then(res => res.json())
        .then(data => {
            const inner = document.getElementById('cart-dropdown-inner');
            const totalEl = document.getElementById('cart-dropdown-total');

            if (!data.items.length) {
                inner.innerHTML = '<p class="text-xs text-gray-400 text-center py-4">Il carrello è vuoto.</p>';
                if (totalEl) totalEl.textContent = '€ 0,00';
                return;
            }

            inner.innerHTML = data.items.map(item => `
                <a href="${item.url}" class="cart-dropdown-item">
                    ${item.image
                        ? `<img src="${item.image}" alt="${item.name}" class="cart-dropdown-thumb">`
                        : `<div class="cart-dropdown-thumb"></div>`}
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-medium text-gray-900 truncate">${item.name}</p>
                        ${item.variant ? `<p class="text-xs text-gray-400">${item.variant}</p>` : ''}
                        <p class="text-xs text-gray-500 mt-0.5">${item.quantity} × € ${item.price}</p>
                    </div>
                    <span class="text-xs font-semibold text-gray-900 flex-shrink-0">€ ${item.subtotal}</span>
                </a>
            `).join('');

            if (totalEl) totalEl.textContent = '€ ' + data.total;
        })
        .catch(() => {
            document.getElementById('cart-dropdown-inner').innerHTML =
                '<p class="text-xs text-gray-400 text-center py-4">Errore nel caricamento.</p>';
        });
    };

    const hide = () => {
        hideTimer = setTimeout(() => { dropdown.style.display = 'none'; }, 150);
    };

    // Ricarica dati freschi ad ogni apertura
    wrapper.addEventListener('mouseenter', () => { loaded = false; show(); });
    wrapper.addEventListener('mouseleave', hide);
    dropdown.addEventListener('mouseenter', () => clearTimeout(hideTimer));
    dropdown.addEventListener('mouseleave', hide);
})();

/* ── Carousel infinito (clone-based) ─────────────────────────────────── */
document.querySelectorAll('[data-carousel]').forEach(function (carousel) {
    const track = carousel.querySelector('.carousel-track');
    if (!track) return;

    const originals = Array.from(track.children);
    const N = originals.length;
    if (N < 2) return;

    // Aggiunge una copia di tutti i prodotti in fondo e una in testa
    originals.forEach(card => track.appendChild(card.cloneNode(true)));
    [...originals].reverse().forEach(card => track.insertBefore(card.cloneNode(true), track.firstChild));

    const getStep = () => {
        const card = track.children[0];
        const gap = parseFloat(window.getComputedStyle(track).gap) || 24;
        return (card ? card.offsetWidth : 0) + gap;
    };

    // Posiziona all'inizio dei prodotti reali (salta i cloni in testa)
    const setWidth = () => getStep() * N;
    track.style.scrollBehavior = 'auto';
    track.scrollLeft = setWidth();
    track.style.scrollBehavior = '';

    // Dopo ogni animazione: se siamo nella zona cloni, riposiziona silenziosamente
    const reposition = () => {
        const sw = setWidth();
        if (track.scrollLeft < sw) {
            track.style.scrollBehavior = 'auto';
            track.scrollLeft += sw;
            track.style.scrollBehavior = '';
        } else if (track.scrollLeft >= sw * 2) {
            track.style.scrollBehavior = 'auto';
            track.scrollLeft -= sw;
            track.style.scrollBehavior = '';
        }
    };

    // scrollend (browser moderni) + fallback timeout con debounce
    let repositionTimer = null;
    const scheduleReposition = () => {
        clearTimeout(repositionTimer);
        repositionTimer = setTimeout(reposition, 450);
    };
    track.addEventListener('scrollend', reposition);

    carousel.querySelector('[data-carousel-prev]')?.addEventListener('click', function () {
        track.scrollBy({ left: -getStep(), behavior: 'smooth' });
        scheduleReposition();
    });

    carousel.querySelector('[data-carousel-next]')?.addEventListener('click', function () {
        track.scrollBy({ left: getStep(), behavior: 'smooth' });
        scheduleReposition();
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

/* ── Star rating selector (products/show — review form) ──────────────── */
(function () {
    var selector = document.getElementById('star-rating-selector');
    if (!selector) return;

    var labels = Array.from(selector.querySelectorAll('.star-label'));

    function paint(upToIndex) {
        labels.forEach(function (l, i) {
            l.classList.toggle('is-active', i <= upToIndex);
        });
    }

    function paintFromChecked() {
        var checked = selector.querySelector('input:checked');
        if (checked) {
            paint(parseInt(checked.value) - 1);
        } else {
            labels.forEach(function (l) { l.classList.remove('is-active'); });
        }
    }

    labels.forEach(function (label, index) {
        label.addEventListener('mouseenter', function () { paint(index); });
        label.addEventListener('click', function () {
            var radio = label.querySelector('input[type="radio"]');
            if (radio) radio.checked = true;
            paint(index);
        });
    });

    selector.addEventListener('mouseleave', paintFromChecked);
})();

/* ── Checkout ─────────────────────────────────────────────────────────── */
/* ── Checkout: sezione fatturazione ──────────────────────────────────── */
document.addEventListener('DOMContentLoaded', function () {
    var checkbox   = document.getElementById('billing-different-checkbox');
    var section    = document.getElementById('billing-section');
    var formFields = document.getElementById('billing-form-fields');

    if (!checkbox || !section || !formFields) return;

    checkbox.addEventListener('change', function () {
        section.classList.toggle('hidden', !this.checked);
    });

    document.querySelectorAll('.billing-saved-radio').forEach(function (radio) {
        radio.addEventListener('change', function () {
            formFields.classList.toggle('hidden', this.value !== 'new');
        });
    });
});

(function () {
    var form = document.getElementById('checkout-form');
    if (!form) return;

    var applyUrl  = form.dataset.discountApplyUrl;
    var removeUrl = form.dataset.discountRemoveUrl;

    // ── Selezione metodo di pagamento ──────────────────────────────────────
    document.querySelectorAll('input[name="payment_method"]').forEach(function (radio) {
        var label = radio.closest('.payment-method-option');
        if (!label) return;
        radio.addEventListener('change', function () {
            document.querySelectorAll('.payment-method-option').forEach(function (el) {
                el.classList.remove('payment-method-option--selected');
            });
            label.classList.add('payment-method-option--selected');
        });
        if (radio.checked) {
            label.classList.add('payment-method-option--selected');
        }
    });

    // ── Codice sconto ──────────────────────────────────────────────────────
    var applyBtn       = document.getElementById('apply-discount-btn');
    var discountInput  = document.getElementById('discount-input');
    var discountError  = document.getElementById('discount-error');
    var discountFormEl = document.getElementById('discount-form');
    var discountApplied    = document.getElementById('discount-applied');
    var discountRow        = document.getElementById('discount-row');
    var totalDisplay       = document.getElementById('total-display');
    var amountDisplay      = document.getElementById('discount-amount-display');
    var codeDisplay        = document.getElementById('discount-code-display');
    var labelDisplay       = document.getElementById('discount-label-display');
    var removeBtn          = document.getElementById('remove-discount-btn');

    function showDiscountError(msg) {
        if (!discountError) return;
        discountError.textContent = msg;
        discountError.classList.remove('hidden');
    }

    function hideDiscountError() {
        if (!discountError) return;
        discountError.classList.add('hidden');
    }

    if (applyBtn && discountInput) {
        applyBtn.addEventListener('click', function () {
            var code = discountInput.value.trim().toUpperCase();
            if (!code) return;

            applyBtn.disabled    = true;
            applyBtn.textContent = '...';
            hideDiscountError();

            fetch(applyUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken(),
                },
                body: JSON.stringify({ code: code }),
            })
            .then(function (r) {
                if (!r.ok && r.status !== 422) throw new Error('server_error');
                return r.json();
            })
            .then(function (data) {
                if (data.valid) {
                    if (codeDisplay)   codeDisplay.textContent   = data.code;
                    if (labelDisplay)  labelDisplay.textContent  = data.label;
                    if (amountDisplay) amountDisplay.textContent = '-\u20AC ' + data.discount_amount;
                    if (totalDisplay)  totalDisplay.textContent  = '\u20AC ' + data.new_total;
                    if (discountFormEl)  discountFormEl.classList.add('hidden');
                    if (discountApplied) discountApplied.classList.remove('hidden');
                    if (discountRow)     discountRow.classList.remove('hidden');
                } else {
                    showDiscountError(data.message || 'Codice non valido.');
                }
            })
            .catch(function () {
                showDiscountError('Errore durante la verifica. Riprova.');
            })
            .finally(function () {
                applyBtn.disabled    = false;
                applyBtn.textContent = 'Applica';
            });
        });

        discountInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') { e.preventDefault(); applyBtn.click(); }
        });
    }

    if (removeBtn) {
        removeBtn.addEventListener('click', function () {
            fetch(removeUrl, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken() },
            })
            .then(function () { window.location.reload(); })
            .catch(function () { window.location.reload(); });
        });
    }
})();
