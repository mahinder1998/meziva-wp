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

    function setPanel(panel, open) {
      if (!panel) return;
      if (open) {
        panel.style.height = panel.scrollHeight + "px";
        panel.dataset.open = "1";
      } else {
        panel.style.height = "0px";
        panel.dataset.open = "0";
      }
    }

    function setIcon(iconEl, open) {
      if (!iconEl) return;
      iconEl.innerHTML = open ? MINUS_SVG : PLUS_SVG;
    }

    triggers.forEach((btn) => {
      const item = btn.closest("[data-mz-acc-item]");
      const panel = item ? item.querySelector("[data-mz-acc-panel]") : null;
      const icon = btn.querySelector("[data-mz-acc-icon]");
      if (!panel) return;

      // initial state
      const isOpen = btn.getAttribute("aria-expanded") === "true";
      setPanel(panel, isOpen);
      setIcon(icon, isOpen);
      if (item) item.classList.toggle("mz-acc-open", isOpen);

      btn.addEventListener("click", () => {
        const isCurrentlyOpen = panel.dataset.open === "1";
        const willOpen = !isCurrentlyOpen;

        // close others (tabs style)
        triggers.forEach((b) => {
          if (b === btn) return;
          const it = b.closest("[data-mz-acc-item]");
          const p = it ? it.querySelector("[data-mz-acc-panel]") : null;
          const ic = b.querySelector("[data-mz-acc-icon]");
          if (!p) return;

          b.setAttribute("aria-expanded", "false");
          setPanel(p, false);
          setIcon(ic, false);
          if (it) it.classList.remove("mz-acc-open");
        });

        // toggle current
        btn.setAttribute("aria-expanded", willOpen ? "true" : "false");
        setPanel(panel, willOpen);
        setIcon(icon, willOpen);
        if (item) item.classList.toggle("mz-acc-open", willOpen);
      });

      // keep height correct on resize
      window.addEventListener("resize", () => {
        if (panel.dataset.open === "1") {
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
  