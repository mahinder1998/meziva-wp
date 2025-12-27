(function () {
  function initAccordion() {
    const triggers = document.querySelectorAll("[data-mz-acc-trigger]");
    if (!triggers.length) return;

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

    triggers.forEach((btn) => {
      const item = btn.closest("[data-mz-acc-item]");
      const panel = item ? item.querySelector("[data-mz-acc-panel]") : null;
      const icon = btn.querySelector("[data-mz-acc-icon]");
      if (!panel) return;

      // initial state
      const isOpen = btn.getAttribute("aria-expanded") === "true";
      // if panel had auto height from PHP, normalize it
      if (isOpen) {
        panel.style.height = panel.scrollHeight + "px";
        panel.dataset.open = "1";
        if (icon) icon.textContent = "−";
        item.classList.add("mz-acc-open");
      } else {
        panel.style.height = "0px";
        panel.dataset.open = "0";
        if (icon) icon.textContent = "+";
        item.classList.remove("mz-acc-open");
      }

      btn.addEventListener("click", () => {
        const open = panel.dataset.open === "1";

        // close others (tabs style)
        triggers.forEach((b) => {
          if (b === btn) return;
          const it = b.closest("[data-mz-acc-item]");
          const p = it ? it.querySelector("[data-mz-acc-panel]") : null;
          const ic = b.querySelector("[data-mz-acc-icon]");
          if (!p) return;
          b.setAttribute("aria-expanded", "false");
          setPanel(p, false);
          if (ic) ic.textContent = "+";
          if (it) it.classList.remove("mz-acc-open");
        });

        if (open) {
          btn.setAttribute("aria-expanded", "false");
          setPanel(panel, false);
          if (icon) icon.textContent = "+";
          item.classList.remove("mz-acc-open");
        } else {
          btn.setAttribute("aria-expanded", "true");
          setPanel(panel, true);
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
