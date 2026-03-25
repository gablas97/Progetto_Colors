const csrfToken = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';

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
        // Aggiorna badge carrello nella navbar
        document.querySelectorAll('[data-cart-count]').forEach(el => {
            el.textContent = data.cart_count ?? '';
        });

        // Feedback visivo temporaneo sul bottone
        btn.classList.add('bg-primary');
        btn.classList.remove('bg-gray-900');
        setTimeout(() => {
            btn.classList.remove('bg-primary');
            btn.classList.add('bg-gray-900');
        }, 800);
    })
    .catch(() => {});
});
