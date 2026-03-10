(function () {
  function initAccordion() {
    const triggers = document.querySelectorAll("[data-mz-acc-trigger]");
    if (!triggers.length) return;

    const PLUS_SVG = `
      <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24">
        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-7 7V5"></path>
      </svg>
    `;

    const MINUS_SVG = `
      <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24">
        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14"></path>
      </svg>
    `;

    // Sticky header offset
    function getStickyOffset() {
      const selectors = [
        '.site-header',
        '.ast-sticky-active',
        '.main-header-bar',
        '[data-sticky-header]',
        '.header-sticky',
        '.sticky-header',
        'header'
      ];

      let maxHeight = 0;

      selectors.forEach((selector) => {
        document.querySelectorAll(selector).forEach((el) => {
          const style = window.getComputedStyle(el);
          const isStickyLike =
            style.position === "sticky" || style.position === "fixed";

          if (isStickyLike) {
            const rect = el.getBoundingClientRect();
            if (rect.height > maxHeight) {
              maxHeight = rect.height;
            }
          }
        });
      });

      return maxHeight + 12; // little breathing space
    }

    function setIcon(iconEl, open) {
      if (!iconEl) return;
      iconEl.innerHTML = open ? MINUS_SVG : PLUS_SVG;
    }

    function openPanel(panel) {
      if (!panel) return;

      panel.style.overflow = "hidden";
      panel.style.height = panel.scrollHeight + "px";
      panel.dataset.open = "1";

      const onEnd = function (e) {
        if (e.propertyName !== "height") return;
        if (panel.dataset.open === "1") {
          panel.style.height = "auto";
        }
        panel.removeEventListener("transitionend", onEnd);
      };

      panel.addEventListener("transitionend", onEnd);
    }

    function closePanel(panel) {
      if (!panel) return;

      panel.style.overflow = "hidden";

      // if panel already auto, first lock current height
      panel.style.height = panel.scrollHeight + "px";

      requestAnimationFrame(() => {
        panel.style.height = "0px";
      });

      panel.dataset.open = "0";
    }

    function keepTriggerVisible(btn, beforeTop) {
      const stickyOffset = getStickyOffset();

      // frame 1
      requestAnimationFrame(() => {
        const afterTop1 = btn.getBoundingClientRect().top;
        const delta1 = afterTop1 - beforeTop;
        if (Math.abs(delta1) > 1) {
          window.scrollBy(0, delta1);
        }

        // frame 2
        requestAnimationFrame(() => {
          const afterTop2 = btn.getBoundingClientRect().top;
          const delta2 = afterTop2 - beforeTop;
          if (Math.abs(delta2) > 1) {
            window.scrollBy(0, delta2);
          }
        });

        // after transition completes, ensure button is not hidden behind sticky header
        setTimeout(() => {
          const rect = btn.getBoundingClientRect();

          if (rect.top < stickyOffset) {
            window.scrollBy({
              top: rect.top - stickyOffset,
              behavior: "auto",
            });
          }
        }, 320);
      });
    }

    triggers.forEach((btn) => {
      const item = btn.closest("[data-mz-acc-item]");
      const panel = item ? item.querySelector("[data-mz-acc-panel]") : null;
      const icon = btn.querySelector("[data-mz-acc-icon]");
      if (!panel) return;

      const isOpen = btn.getAttribute("aria-expanded") === "true";

      if (isOpen) {
        panel.dataset.open = "1";
        panel.style.height = "auto";
      } else {
        panel.dataset.open = "0";
        panel.style.height = "0px";
      }

      setIcon(icon, isOpen);
      if (item) item.classList.toggle("mz-acc-open", isOpen);

      btn.addEventListener("click", () => {
        const beforeTop = btn.getBoundingClientRect().top;

        const isCurrentlyOpen = panel.dataset.open === "1";
        const willOpen = !isCurrentlyOpen;

        // close others
        triggers.forEach((b) => {
          if (b === btn) return;

          const it = b.closest("[data-mz-acc-item]");
          const p = it ? it.querySelector("[data-mz-acc-panel]") : null;
          const ic = b.querySelector("[data-mz-acc-icon]");
          if (!p) return;

          b.setAttribute("aria-expanded", "false");
          closePanel(p);
          setIcon(ic, false);
          if (it) it.classList.remove("mz-acc-open");
        });

        // toggle current
        btn.setAttribute("aria-expanded", willOpen ? "true" : "false");

        if (willOpen) {
          openPanel(panel);
        } else {
          closePanel(panel);
        }

        setIcon(icon, willOpen);
        if (item) item.classList.toggle("mz-acc-open", willOpen);

        // important fix
        keepTriggerVisible(btn, beforeTop);
      });
    });

    window.addEventListener("resize", () => {
      document.querySelectorAll("[data-mz-acc-panel]").forEach((panel) => {
        if (panel.dataset.open === "1" && panel.style.height !== "auto") {
          panel.style.height = panel.scrollHeight + "px";
        }
      });
    });
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initAccordion);
  } else {
    initAccordion();
  }
})(); 