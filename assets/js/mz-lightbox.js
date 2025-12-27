(function () {
  // Wait for DOM
  document.addEventListener("DOMContentLoaded", () => {
    // Woo product gallery anchors usually: .woocommerce-product-gallery__image a
    const anchors = document.querySelectorAll(
      ".woocommerce-product-gallery__image a, .woocommerce-product-gallery a"
    );
    if (!anchors.length) return;

    // Group all images in one gallery
    anchors.forEach((a) => {
      a.setAttribute("data-fancybox", "product");
      a.setAttribute("data-caption", a.getAttribute("title") || "");
    });

    if (!window.Fancybox) return;

    Fancybox.bind('[data-fancybox="product"]', {
      // smooth open/close like ref
      animated: true,
      showClass: "f-fadeIn",
      hideClass: "f-fadeOut",

      // UI
      Toolbar: {
        display: {
          left: [],
          middle: ["zoomIn", "zoomOut"],
          right: ["close"],
        },
      },

      Thumbs: {
        autoStart: true,   // thumbnails bottom like ref
        type: "classic",
      },

      // Nice transition
      Image: {
        zoom: true,
        click: "zoom",
        wheel: "zoom",
      },

      // backdrop feel
      backdropClick: "close",
      closeButton: true,

      // Prevent jumpy scroll issues
      placeFocusBack: false,
    });
  });
})();
