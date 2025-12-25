(() => {
  const openBtn = document.querySelector('[data-meziva-menu-open]');
  const closeBtn = document.querySelector('[data-meziva-menu-close]');
  const overlay = document.querySelector('[data-meziva-overlay]');
  const drawer = document.querySelector('[data-meziva-drawer]');

  const lock = () => document.body.classList.add('overflow-hidden');
  const unlock = () => document.body.classList.remove('overflow-hidden');

  const open = () => {
    if (!drawer || !overlay) return;
    drawer.classList.remove('mz-translate-x-full');
    drawer.setAttribute('aria-hidden', 'false');
    overlay.classList.remove('mz-hidden');
    lock();
  };

  const close = () => {
    if (!drawer || !overlay) return;
    drawer.classList.add('mz-translate-x-full');
    drawer.setAttribute('aria-hidden', 'true');
    overlay.classList.add('mz-hidden');
    unlock();
  };

  if (openBtn) openBtn.addEventListener('click', open);
  if (closeBtn) closeBtn.addEventListener('click', close);
  if (overlay) overlay.addEventListener('click', close);

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') close();
  });

  // ✅ Smooth scroll only when SAME page
  const smoothScrollToHash = (hash) => {
    const el = document.querySelector(hash);
    if (!el) return false;

    // small offset for sticky header
    const y = el.getBoundingClientRect().top + window.pageYOffset - 90;
    window.scrollTo({ top: y, behavior: 'smooth' });
    return true;
  };

  // ✅ If page loads with hash, scroll smoothly (after load)
  window.addEventListener('load', () => {
    if (window.location.hash) smoothScrollToHash(window.location.hash);
  });

  // ✅ Intercept clicks
  document.addEventListener('click', (e) => {
    const a = e.target.closest('a');
    if (!a) return;

    const href = a.getAttribute('href');
    if (!href || href[0] === '#') return; // ignore pure hash

    // Only handle links having #something
    if (!href.includes('#')) return;

    let url;
    try { url = new URL(href, window.location.origin); } catch { return; }

    // Same page? smooth scroll + close drawer
    if (url.pathname === window.location.pathname && url.hash) {
      const ok = smoothScrollToHash(url.hash);
      if (ok) {
        e.preventDefault();
        close();
      }
      return;
    }

    // Other page (example: /#ingredients) -> allow default navigation
    // but close drawer for UX
    close();
  });
})();
