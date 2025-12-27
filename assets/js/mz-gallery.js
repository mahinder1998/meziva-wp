(function () {
  function bindFancybox() {
    if (!window.Fancybox) return;

    // (Re)bind to our gallery
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
    const mainImg = document.querySelector("[data-mz-main-img]");
    const mainLink = document.querySelector("[data-mz-main-link]");
    const zoomBtn = document.querySelector("[data-mz-zoom]");
    const thumbs = document.querySelectorAll("[data-mz-thumb]");

    if (!mainImg || !mainLink) return;

    // Thumb click -> swap main
    thumbs.forEach((btn) => {
      btn.addEventListener("click", () => {
        const large = btn.getAttribute("data-large");
        const full = btn.getAttribute("data-full");

        if (large) mainImg.src = large;
        if (full) mainLink.href = full;

        thumbs.forEach((b) => b.classList.remove("mz-ring-2", "mz-ring-gray-900"));
        btn.classList.add("mz-ring-2", "mz-ring-gray-900");
      });
    });

    // main image click -> open fancybox of current href
    mainLink.addEventListener("click", (e) => {
      // if fancybox exists, open explicitly (most reliable)
      if (window.Fancybox) {
        e.preventDefault();

        // Collect all gallery links
        const items = Array.from(document.querySelectorAll('[data-fancybox="mz-product"]'))
          .map((a) => ({ src: a.getAttribute("href"), type: "image" }))
          .filter((x) => x.src);

        // Find current index by matching href
        const current = mainLink.getAttribute("href");
        const startIndex = Math.max(0, items.findIndex((it) => it.src === current));

        Fancybox.show(items, { startIndex });
      }
    });

    // Zoom button -> trigger same click
    if (zoomBtn) {
      zoomBtn.addEventListener("click", (e) => {
        e.preventDefault();
        mainLink.click();
      });
    }

    // Keen slider thumbs
    const thumbsEl = document.querySelector("[data-mz-thumbs]");
    if (thumbsEl && window.KeenSlider) {
      // eslint-disable-next-line no-new
      new KeenSlider(thumbsEl, {
        slides: { perView: 5, spacing: 10, },
        breakpoints: {
          "(max-width: 767px)": { slides: { perView: 4, spacing: 8 } },
          "(min-width: 1024px)": { slides: { perView: 6, spacing: 12 } },
        },
      });
    }

    // ✅ bind fancybox AFTER DOM + scripts
    bindFancybox();
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initMzGallery);
  } else {
    initMzGallery();
  }
})();
