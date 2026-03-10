(function () {
  function bindFancybox() {
    if (!window.Fancybox) return;

    Fancybox.bind('[data-fancybox="mz-product"]', {
      animated: true,
      dragToClose: true,
      placeFocusBack: false,
      Image: { zoom: true },
      Thumbs: { autoStart: true },
      Toolbar: {
        display: {
          left: ["infobar"],
          middle: [],
          right: ["close"],
        },
      },
    });
  }

  function initMzGallery() {
    const mainSliderEl = document.querySelector("[data-mz-main-slider]");
    const thumbsEl = document.querySelector("[data-mz-thumbs]");
    const zoomBtn = document.querySelector("[data-mz-zoom]");
    const dotsWrap = document.querySelector("[data-mz-slider-dots]");
    const thumbButtons = Array.from(document.querySelectorAll("[data-mz-thumb]"));
    const fancyLinks = Array.from(document.querySelectorAll('[data-fancybox="mz-product"]'));

    if (!mainSliderEl || !window.KeenSlider) {
      bindFancybox();
      return;
    }

    let currentIndex = 0;
    let autoplayTimer = null;
    let mainSlider = null;
    let thumbsSlider = null;

    function clearAutoplay() {
      if (autoplayTimer) {
        clearInterval(autoplayTimer);
        autoplayTimer = null;
      }
    }

    function startAutoplay() {
      clearAutoplay();
      autoplayTimer = setInterval(function () {
        if (!mainSlider) return;
        const nextIndex =
          mainSlider.track.details.rel + 1 >= mainSlider.track.details.slides.length
            ? 0
            : mainSlider.track.details.rel + 1;
        mainSlider.moveToIdx(nextIndex);
      }, 3000);
    }

    function updateActiveState(index) {
      currentIndex = index;

      thumbButtons.forEach((btn) => {
        const btnIndex = parseInt(btn.getAttribute("data-index"), 10) || 0;
        btn.classList.remove("mz-ring-2", "mz-ring-gray-900", "mz-opacity-100");
        btn.classList.add("mz-opacity-70");

        if (btnIndex === index) {
          btn.classList.add("mz-ring-2", "mz-ring-gray-900", "mz-opacity-100");
          btn.classList.remove("mz-opacity-70");
        }
      });

      const dots = dotsWrap ? dotsWrap.querySelectorAll("[data-mz-dot]") : [];
      dots.forEach((dot) => {
        const dotIndex = parseInt(dot.getAttribute("data-index"), 10) || 0;
        dot.classList.remove("mz-bg-brand-accent", "mz-w-5");
        dot.classList.add("mz-bg-white/70", "mz-w-2.5");

        if (dotIndex === index) {
          dot.classList.add("mz-bg-brand-accent", "mz-w-5");
          dot.classList.remove("mz-bg-white/70", "mz-w-2.5");
        }
      });

      if (thumbsSlider) {
        thumbsSlider.moveToIdx(index);
      }
    }

    function buildDots(slideCount) {
      if (!dotsWrap) return;
      dotsWrap.innerHTML = "";

      for (let i = 0; i < slideCount; i++) {
        const dot = document.createElement("button");
        dot.type = "button";
        dot.setAttribute("data-mz-dot", "1");
        dot.setAttribute("data-index", i);
        dot.className =
          "mz-h-2.5 mz-rounded-full mz-transition-all mz-duration-300 mz-bg-white/70 mz-w-2.5";

        dot.addEventListener("click", function () {
          if (!mainSlider) return;
          mainSlider.moveToIdx(i);
          startAutoplay();
        });

        dotsWrap.appendChild(dot);
      }
    }

    mainSlider = new KeenSlider(mainSliderEl, {
      loop: true,
      rubberband: true,
      slideChanged(slider) {
        updateActiveState(slider.track.details.rel);
      },
      created(slider) {
        buildDots(slider.track.details.slides.length);
        updateActiveState(slider.track.details.rel);
        startAutoplay();
      },
      dragStarted() {
        clearAutoplay();
      },
      animationEnded() {
        startAutoplay();
      },
      updated(slider) {
        buildDots(slider.track.details.slides.length);
        updateActiveState(slider.track.details.rel);
      },
    });

    if (thumbsEl) {
      thumbsSlider = new KeenSlider(thumbsEl, {
        slides: { perView: 4, spacing: 8 },
        breakpoints: {
          "(min-width: 768px)": {
            slides: { perView: 5, spacing: 10 },
          },
          "(min-width: 1024px)": {
            slides: { perView: 6, spacing: 12 },
          },
        },
      });
    }

    thumbButtons.forEach((btn) => {
      btn.addEventListener("click", function () {
        const index = parseInt(btn.getAttribute("data-index"), 10) || 0;
        if (!mainSlider) return;
        mainSlider.moveToIdx(index);
        startAutoplay();
      });
    });

    const mainLinks = document.querySelectorAll("[data-mz-main-link]");
    mainLinks.forEach((link) => {
      link.addEventListener("click", function (e) {
        if (!window.Fancybox) return;

        e.preventDefault();

        const items = fancyLinks
          .map((a) => ({
            src: a.getAttribute("href"),
            type: "image",
            caption: a.getAttribute("data-caption") || "",
          }))
          .filter((x) => x.src);

        Fancybox.show(items, { startIndex: currentIndex });
      });
    });

    if (zoomBtn) {
      zoomBtn.addEventListener("click", function (e) {
        e.preventDefault();

        if (!window.Fancybox) return;

        const items = fancyLinks
          .map((a) => ({
            src: a.getAttribute("href"),
            type: "image",
            caption: a.getAttribute("data-caption") || "",
          }))
          .filter((x) => x.src);

        Fancybox.show(items, { startIndex: currentIndex });
      });
    }

    mainSliderEl.addEventListener("mouseenter", clearAutoplay);
    mainSliderEl.addEventListener("mouseleave", startAutoplay);

    bindFancybox();
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initMzGallery);
  } else {
    initMzGallery();
  }
})(); 