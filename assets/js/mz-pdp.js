(function () {
  function initAccordion() {
    const triggers = document.querySelectorAll("[data-mz-acc-trigger]");
    if (!triggers.length) return;

    triggers.forEach((btn) => {
      const item = btn.closest("div");
      const panel = item ? item.querySelector("[data-mz-acc-panel]") : null;
      const icon = btn.querySelector("[data-mz-acc-icon]");
      if (!panel) return;

      // initial state based on aria-expanded
      const isOpen = btn.getAttribute("aria-expanded") === "true";
      if (isOpen) {
        panel.style.height = panel.scrollHeight + "px";
        panel.dataset.open = "1";
        if (icon) icon.textContent = "−";
      } else {
        panel.style.height = "0px";
        panel.dataset.open = "0";
        if (icon) icon.textContent = "+";
      }

      btn.addEventListener("click", () => {
        const open = panel.dataset.open === "1";

        // close all others (optional; comment out if multi-open needed)
        triggers.forEach((b) => {
          if (b === btn) return;
          const it = b.closest("div");
          const p = it ? it.querySelector("[data-mz-acc-panel]") : null;
          const ic = b.querySelector("[data-mz-acc-icon]");
          if (!p) return;
          b.setAttribute("aria-expanded", "false");
          p.dataset.open = "0";
          p.style.height = "0px";
          if (ic) ic.textContent = "+";
          it.classList.remove("mz-acc-open");
        });

        if (open) {
          btn.setAttribute("aria-expanded", "false");
          panel.dataset.open = "0";
          panel.style.height = "0px";
          if (icon) icon.textContent = "+";
          item.classList.remove("mz-acc-open");
        } else {
          btn.setAttribute("aria-expanded", "true");
          panel.dataset.open = "1";
          panel.style.height = panel.scrollHeight + "px";
          if (icon) icon.textContent = "−";
          item.classList.add("mz-acc-open");
        }
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
 