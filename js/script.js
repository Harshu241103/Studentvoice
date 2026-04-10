// ============================================================
// js/script.js — StudentVoice client-side logic
// ============================================================

/* ── Theme Toggle ── */
(function () {
    const saved = localStorage.getItem('sv_theme') || 'dark';
    document.documentElement.setAttribute('data-theme', saved);

    window.toggleTheme = function () {
        const current = document.documentElement.getAttribute('data-theme');
        const next = current === 'dark' ? 'light' : 'dark';
        document.documentElement.setAttribute('data-theme', next);
        localStorage.setItem('sv_theme', next);
        const btn = document.getElementById('themeToggle');
        if (btn) btn.textContent = next === 'dark' ? '☀️' : '🌙';
    };

    document.addEventListener('DOMContentLoaded', () => {
        const btn = document.getElementById('themeToggle');
        if (btn) btn.textContent = saved === 'dark' ? '☀️' : '🌙';
    });
})();

/* ── Category Filter ── */
document.addEventListener('DOMContentLoaded', function () {
    const filterBtns = document.querySelectorAll('.filter-btn');
    const cards = document.querySelectorAll('.card[data-category]');

    filterBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            filterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            const cat = this.dataset.filter;
            let visible = 0;

            cards.forEach(card => {
                const show = cat === 'all' || card.dataset.category === cat;
                card.style.display = show ? '' : 'none';
                if (show) visible++;
            });

            const empty = document.getElementById('emptyState');
            if (empty) empty.style.display = visible === 0 ? 'block' : 'none';
        });
    });
});

/* ── AJAX Vote ── */
function castVote(problemId, btn) {
    if (btn.classList.contains('voted') || btn.classList.contains('loading')) return;
    btn.classList.add('loading');

    const countEl = btn.querySelector('.vote-count');
    const currentCount = parseInt(countEl.textContent, 10);

    fetch('vote.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `id=${encodeURIComponent(problemId)}&csrf=${encodeURIComponent(window._csrf || '')}`
    })
    .then(r => r.json())
    .then(data => {
        btn.classList.remove('loading');
        if (data.success) {
            countEl.textContent = data.votes;
            btn.classList.add('voted');
            btn.title = 'You voted!';
        } else {
            showToast(data.message || 'Vote failed', 'error');
        }
    })
    .catch(() => {
        btn.classList.remove('loading');
        showToast('Network error, please try again.', 'error');
    });
}

/* ── Toast Notification ── */
function showToast(message, type = 'info') {
    const existing = document.getElementById('sv-toast');
    if (existing) existing.remove();

    const t = document.createElement('div');
    t.id = 'sv-toast';
    t.textContent = message;
    t.style.cssText = `
        position:fixed; bottom:24px; right:24px; z-index:9999;
        background:${type === 'error' ? '#ef4444' : '#22c55e'};
        color:#fff; padding:12px 20px; border-radius:10px;
        font-family:'DM Sans',sans-serif; font-size:0.875rem; font-weight:600;
        box-shadow:0 8px 30px rgba(0,0,0,0.3);
        animation:fadeUp 0.3s ease;
        pointer-events:none;
    `;
    document.body.appendChild(t);
    setTimeout(() => { if (t.parentNode) t.remove(); }, 3500);
}

/* ── Form Validation ── */
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('form[data-validate]').forEach(form => {
        form.addEventListener('submit', function (e) {
            const inputs = form.querySelectorAll('[required]');
            let valid = true;
            inputs.forEach(input => {
                if (!input.value.trim()) {
                    input.style.borderColor = 'var(--red)';
                    valid = false;
                } else {
                    input.style.borderColor = '';
                }
            });
            if (!valid) {
                e.preventDefault();
                showToast('Please fill in all required fields.', 'error');
            }
        });
    });
});
