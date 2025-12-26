(function () {
  // ---------- Autoplay plugin ----------
  function autoplayPlugin(interval = 3500) {
    return (slider) => {
      let timeout;
      let mouseOver = false;

      function clearNextTimeout() {
        clearTimeout(timeout);
      }

      function nextTimeout() {
        clearTimeout(timeout);
        if (mouseOver) return;
        timeout = setTimeout(() => {
          slider.next();
        }, interval);
      }

      slider.on("created", () => {
        slider.container.addEventListener("mouseenter", () => {
          mouseOver = true;
          clearNextTimeout();
        });
        slider.container.addEventListener("mouseleave", () => {
          mouseOver = false;
          nextTimeout();
        });

        // touch devices: pause while touching
        slider.container.addEventListener("touchstart", () => {
          mouseOver = true;
          clearNextTimeout();
        }, { passive: true });
        slider.container.addEventListener("touchend", () => {
          mouseOver = false;
          nextTimeout();
        }, { passive: true });

        nextTimeout();
      });

      slider.on("dragStarted", clearNextTimeout);
      slider.on("animationEnded", nextTimeout);
      slider.on("updated", nextTimeout);
    };
  }

  // ---------- Keen Slider: Reviews ----------
  function initKeen() {
    if (typeof KeenSlider === "undefined") return;

    document.querySelectorAll(".mz-keen-reviews").forEach((sliderEl) => {
      const dotsWrap = sliderEl.parentElement.querySelector("[data-keen-dots]");
      const dots = dotsWrap ? Array.from(dotsWrap.querySelectorAll(".mz-dot")) : [];

      const slider = new KeenSlider(
        sliderEl,
        {
          loop: true,
          slides: { perView: 1 },
        },
        [autoplayPlugin(3500)]
      );

      // If dots not found, nothing to bind
      if (!dots.length) return;

      function setActive(relIdx) {
        dots.forEach((d, idx) => {
          d.classList.toggle("mz-bg-[#C58BAA]", idx === relIdx);
          d.classList.toggle("mz-bg-[#C58BAA]", idx !== relIdx);
        });
      }

      dots.forEach((d, i) => {
        d.addEventListener("click", () => slider.moveToIdx(i));
      });

      slider.on("created", () => setActive(slider.track.details.rel));
      slider.on("slideChanged", () => setActive(slider.track.details.rel));
    });
  }

  // ---------- Before/After Compare ----------
  function initCompare() {
    document.querySelectorAll("[data-compare]").forEach((wrap) => {
      const handle = wrap.querySelector(".mz-compare-handle");
      if (!handle) return;

      let dragging = false;

      const clamp = (n, min, max) => Math.max(min, Math.min(max, n));

      const setCut = (clientX) => {
        const rect = wrap.getBoundingClientRect();
        const x = clientX - rect.left;
        const pct = clamp((x / rect.width) * 100, 0, 100);
        wrap.style.setProperty("--mz-cut", pct + "%");
      };

      // default center
      requestAnimationFrame(() => {
        const rect = wrap.getBoundingClientRect();
        setCut(rect.left + rect.width / 2);
      });

      // click/tap anywhere to set position
      wrap.addEventListener("click", (e) => {
        if (e.target.closest(".mz-compare-handle")) return;
        setCut(e.clientX);
      });

      // drag only on handle
      handle.addEventListener("pointerdown", (e) => {
        dragging = true;
        handle.setPointerCapture?.(e.pointerId);
        setCut(e.clientX);
        e.preventDefault();
      });

      window.addEventListener(
        "pointermove",
        (e) => {
          if (!dragging) return;
          setCut(e.clientX);
        },
        { passive: true }
      );

      window.addEventListener(
        "pointerup",
        () => {
          dragging = false;
        },
        { passive: true }
      );

      window.addEventListener(
        "pointercancel",
        () => {
          dragging = false;
        },
        { passive: true }
      );
    });
  }

  // ---------- Init ----------
  function initAll() {
    initKeen();
    initCompare();
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initAll);
  } else {
    initAll();
  }
})();
