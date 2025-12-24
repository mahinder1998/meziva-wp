(function () {
  function ready(fn) {
    if (document.readyState !== "loading") fn();
    else document.addEventListener("DOMContentLoaded", fn);
  }

  ready(function () {
    const header = document.querySelector("[data-meziva-header]");
    const openBtn = document.querySelector("[data-meziva-menu-open]");
    const closeBtn = document.querySelector("[data-meziva-menu-close]");
    const overlay = document.querySelector("[data-meziva-overlay]");
    const drawer = document.querySelector("[data-meziva-drawer]");

    if (!header) return;

    function openMenu() {
      if (!drawer) return;
      drawer.classList.remove("mz-translate-x-full");
      overlay && overlay.classList.remove("mz-hidden");
      drawer.setAttribute("aria-hidden", "false");
      document.documentElement.classList.add("mz-overflow-hidden");
    }

    function closeMenu() {
      if (!drawer) return;
      drawer.classList.add("mz-translate-x-full");
      overlay && overlay.classList.add("mz-hidden");
      drawer.setAttribute("aria-hidden", "true");
      document.documentElement.classList.remove("mz-overflow-hidden");
    }

    openBtn && openBtn.addEventListener("click", openMenu);
    closeBtn && closeBtn.addEventListener("click", closeMenu);
    overlay && overlay.addEventListener("click", closeMenu);

    // Sticky: shadow + shrink (custom class)
    function onScroll() {
      const y = window.scrollY || 0;
      header.classList.toggle("mz-shadow-md", y > 8);
      header.classList.toggle("meziva-header--shrink", y > 40);
    }
    window.addEventListener("scroll", onScroll, { passive: true });
    onScroll();

    // Smart anchor same-page smooth scroll
    document.addEventListener("click", function (e) {
      const link = e.target.closest('a[href*="#"]');
      if (!link) return;

      const href = link.getAttribute("href");
      if (!href || href === "#") return;

      const url = new URL(href, window.location.origin);
      const samePage =
        url.pathname.replace(/\/$/, "") === window.location.pathname.replace(/\/$/, "");

      if (!samePage) return;

      const id = url.hash.replace("#", "");
      if (!id) return;

      const target = document.getElementById(id);
      if (!target) return;

      e.preventDefault();
      closeMenu();

      const offset = header.offsetHeight + 8;
      const top = target.getBoundingClientRect().top + window.pageYOffset - offset;
      window.scrollTo({ top, behavior: "smooth" });
    });

    // Mobile menu accordion (+ only if submenu)
    if (drawer) {
      const ul = drawer.querySelector(".meziva-mobile-menu");
      if (ul) {
        ul.classList.add("mz-flex", "mz-flex-col");

        const items = ul.querySelectorAll(":scope > li");
        items.forEach((li) => {
          li.classList.add("mz-border-b", "mz-border-white/10", "mz-py-3");

          const a = li.querySelector(":scope > a");
          if (a) a.classList.add("mz-flex", "mz-items-center", "mz-justify-between", "mz-text-white", "mz-text-base");

          const sub = li.querySelector(":scope > ul");
          if (sub) {
            sub.classList.add("mz-mt-2", "mz-pl-3", "mz-hidden", "mz-flex", "mz-flex-col", "mz-gap-2");

            const btn = document.createElement("button");
            btn.type = "button";
            btn.className =
              "mz-ml-3 mz-h-8 mz-w-8 mz-rounded-full mz-flex mz-items-center mz-justify-center hover:mz-bg-white/10 mz-transition";
            btn.setAttribute("aria-label", "Toggle submenu");
            btn.innerHTML = `<span class="mz-text-xl mz-leading-none">+</span>`;

            if (a) a.appendChild(btn);

            btn.addEventListener("click", function (ev) {
              ev.preventDefault();
              ev.stopPropagation();
              const opening = sub.classList.contains("mz-hidden");
              sub.classList.toggle("mz-hidden", !opening);
              btn.innerHTML = opening
                ? `<span class="mz-text-xl mz-leading-none">−</span>`
                : `<span class="mz-text-xl mz-leading-none">+</span>`;
            });

            sub.querySelectorAll("a").forEach((sa) => {
              sa.classList.add("mz-text-white/80", "hover:mz-text-white", "mz-transition", "mz-text-[15px]");
            });
          }
        });
      }
    }
  });
})();
