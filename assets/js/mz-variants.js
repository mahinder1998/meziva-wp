(function () {
  function qs(sel, root = document) { return root.querySelector(sel); }
  function qsa(sel, root = document) { return Array.from(root.querySelectorAll(sel)); }

  // ---- MAIN IMAGE HELPERS (your custom gallery) ----
  function storeInitialMainImage() {
    const mainImg = qs("[data-mz-main-img]");
    const mainLink = qs("[data-mz-main-link]");
    if (!mainImg || !mainLink) return;

    if (!mainImg.dataset.mzInitialSrc) mainImg.dataset.mzInitialSrc = mainImg.getAttribute("src") || "";
    if (!mainLink.dataset.mzInitialHref) mainLink.dataset.mzInitialHref = mainLink.getAttribute("href") || "";
  }

  function setMainImage(src, href) {
    const mainImg = qs("[data-mz-main-img]");
    const mainLink = qs("[data-mz-main-link]");
    if (!mainImg || !mainLink) return;

    if (src) mainImg.src = src;
    if (href) mainLink.href = href;
  }

  function restoreInitialMainImage() {
    const mainImg = qs("[data-mz-main-img]");
    const mainLink = qs("[data-mz-main-link]");
    if (!mainImg || !mainLink) return;

    const src = mainImg.dataset.mzInitialSrc;
    const href = mainLink.dataset.mzInitialHref;
    if (src) mainImg.src = src;
    if (href) mainLink.href = href;
  }

  // ---- READ VARIATION IMAGE FROM DOM (works even if events fail) ----
  function getVariationImageFromDom(form) {
    // Woo often stores variation in data-product_variations on form
    // But easiest: Woo injects the selected variation into .single_variation_wrap
    const wrap = qs(".single_variation_wrap", form);
    if (!wrap) return null;

    // 1) if Woo outputs variation template with image element
    const imgEl =
      qs(".woocommerce-product-gallery__image img", document) ||
      qs(".single_variation_wrap img", form);

    // Not reliable if you use custom gallery, so we parse variation JSON instead:
    // 2) Read selected attributes and match against form's data-product_variations
    const variationsJson = form.getAttribute("data-product_variations");
    if (!variationsJson) return null;

    let variations;
    try {
      variations = JSON.parse(variationsJson);
    } catch (e) {
      return null;
    }

    const selects = qsa("select", form).filter(s => s.name && s.name.indexOf("attribute_") === 0);
    const selectedAttrs = {};
    selects.forEach(s => { selectedAttrs[s.name] = s.value; });

    // Find matching variation
    const match = variations.find(v => {
      if (!v || !v.attributes) return false;
      return Object.keys(v.attributes).every(key => {
        // v.attributes uses keys like "attribute_pa_color"
        // compare with selectedAttrs key
        const selectedVal = selectedAttrs[key];
        return selectedVal && selectedVal === v.attributes[key];
      });
    });

    if (!match || !match.image) return null;

    return {
      src: match.image.src || match.image.full_src || match.image.url || null,
      full: match.image.full_src || match.image.src || match.image.url || null
    };
  }

  function updateImageFromCurrentSelection(form) {
    const data = getVariationImageFromDom(form);
    if (data && data.src) {
      setMainImage(data.src, data.full || data.src);
    }
  }

  // ---- SWATCH / SIZE UI ----
  const COLOR_MAP = {
    red: "#ef4444",
    blue: "#3b82f6",
    green: "#22c55e",
    black: "#111827",
    white: "#ffffff",
    grey: "#9ca3af",
    gray: "#9ca3af",
    yellow: "#facc15",
    orange: "#fb923c",
    pink: "#ec4899",
    purple: "#a855f7",
    brown: "#7c4a2d",
    beige: "#e7d3b1",
    nude: "#e3b8a5",
    maroon: "#7f1d1d"
  };

  function inferColorHexFromName(name) {
    if (!name) return null;
    const k = String(name).trim().toLowerCase();
    if (COLOR_MAP[k]) return COLOR_MAP[k];
    for (const key in COLOR_MAP) {
      if (k.includes(key)) return COLOR_MAP[key];
    }
    return null;
  }

  function renderButtonsForSelect(block, selectEl) {
    const type = block.getAttribute("data-mz-type") || "text";
    const optionsWrap = qs("[data-mz-options]", block);
    const selectedLabel = qs("[data-mz-selected-label]", block);
    if (!optionsWrap || !selectEl) return;

    optionsWrap.innerHTML = "";

    const placeholder = selectEl.querySelector('option[value=""]');
    const placeholderText = placeholder ? placeholder.textContent.trim() : "Choose";

    function updateSelectedText() {
      const val = selectEl.value;
      const opt = selectEl.querySelector(`option[value="${CSS.escape(val)}"]`);
      if (!selectedLabel) return;
      if (!val || !opt) selectedLabel.textContent = `: ${placeholderText}`;
      else selectedLabel.textContent = `: ${opt.textContent.trim()}`;
    }

    const opts = Array.from(selectEl.options).filter(o => o.value !== "");
    opts.forEach((opt) => {
      const value = opt.value;
      const text = opt.textContent.trim();

      const btn = document.createElement("button");
      btn.type = "button";
      btn.setAttribute("data-mz-value", value);
      btn.className = "mz-variant-btn mz-relative mz-transition";

      if (type === "color") {
        const hex = inferColorHexFromName(text);
        btn.className += " mz-w-10 mz-h-10 mz-rounded-full";
        //btn.innerHTML = `<span class="mz-sr-only">${text}</span>`;
        btn.innerHTML = ``;
        btn.style.background = hex ? hex : "#f3f4f6";
        if (hex && hex.toLowerCase() === "#ffffff") btn.style.borderColor = "#d1d5db";
      } else {
        btn.className += " mz-px-4 mz-py-4 mz-rounded-xl  mz-bg-white mz-text-sm mz-font-semibold mz-text-gray-800";
        btn.textContent = text;
      }

      btn.addEventListener("click", () => {
        selectEl.value = value;
        // ✅ Trigger both native + jQuery change (Woo uses jQuery)
        selectEl.dispatchEvent(new Event("change", { bubbles: true }));
        if (window.jQuery) window.jQuery(selectEl).trigger("change");


        qsa(".mz-variant-btn", optionsWrap).forEach(b => b.classList.remove("mz-ring-2", "mz-ring-gray-900"));
        btn.classList.add("mz-ring-2", "mz-ring-gray-900");
        updateSelectedText();

        const form = selectEl.closest("form.variations_form");
        const reset = form ? qs("[data-mz-reset]", form) : null;
        if (reset) reset.style.visibility = "visible";

        // ✅ Force update after change (important)
        if (form) {
          setTimeout(() => updateImageFromCurrentSelection(form), 80);
        }
      });

      optionsWrap.appendChild(btn);
    });

    updateSelectedText();
  }

  function syncUIFromSelect(block, selectEl) {
    const optionsWrap = qs("[data-mz-options]", block);
    const selectedLabel = qs("[data-mz-selected-label]", block);
    if (!optionsWrap || !selectEl) return;

    const val = selectEl.value;

    qsa(".mz-variant-btn", optionsWrap).forEach((b) => {
      const v = b.getAttribute("data-mz-value");
      if (v === val && val) b.classList.add("mz-ring-2", "mz-ring-gray-900");
      else b.classList.remove("mz-ring-2", "mz-ring-gray-900");
    });

    if (selectedLabel) {
      const opt = selectEl.querySelector(`option[value="${CSS.escape(val)}"]`);
      if (!val || !opt) selectedLabel.textContent = `: ${selectEl.options[0]?.textContent?.trim() || "Choose"}`;
      else selectedLabel.textContent = `: ${opt.textContent.trim()}`;
    }
  }

  function initSwatchesAndImageSync() {
    const form = qs("form.variations_form");
    if (!form) return;

    storeInitialMainImage();

    // build UI
    const blocks = qsa("[data-mz-attr]", form);
    blocks.forEach((block) => {
      const select = qs("select", block);
      if (!select) return;

      renderButtonsForSelect(block, select);

      // when select changes (dropdown hidden but still changes)
      select.addEventListener("change", () => {
        syncUIFromSelect(block, select);
        setTimeout(() => updateImageFromCurrentSelection(form), 80);
      });
    });

    // reset
    const reset = qs(".reset_variations", form);
    if (reset) {
     reset.addEventListener("click", () => {
        setTimeout(() => {
          reset.style.visibility = "hidden";

          blocks.forEach((b) => {
            const s = qs("select", b);
            if (s) {
              syncUIFromSelect(b, s);
              // ✅ ensure Woo receives reset/change properly
              s.dispatchEvent(new Event("change", { bubbles: true }));
              if (window.jQuery) window.jQuery(s).trigger("change");
            }
          });

          restoreInitialMainImage(); 
        }, 80);
      });

    }

    // ✅ MutationObserver: if Woo updates variations internally, we still sync image
    const observer = new MutationObserver(() => {
      updateImageFromCurrentSelection(form);
    });

    // observe changes in form/variation wrapper
    const target = qs(".single_variation_wrap", form) || form;
    observer.observe(target, { childList: true, subtree: true, attributes: true });

    // initial attempt
    setTimeout(() => updateImageFromCurrentSelection(form), 200);
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initSwatchesAndImageSync);
  } else {
    initSwatchesAndImageSync();
  }
})();
  


(function(){
  if (!window.jQuery) return;

  jQuery(function($){
    const $form = $('form.variations_form');
    if (!$form.length) return;

    const $top = $('[data-mz-top-price]');
    if (!$top.length) return;

    const initial = $top.html();

    $form.on('found_variation', function(e, variation){
      if (variation && variation.price_html) {
        $top.html(variation.price_html);
      }
    });

    $form.on('reset_data', function(){
      $top.html(initial);
    });
  });
})();



(function () {
  function initMezivaVariantActive() {
    const forms = document.querySelectorAll("form.variations_form");
    if (!forms.length) return;

    forms.forEach((form) => {
      // your color buttons wrapper
      const optionWrap = form.querySelector("[data-mz-options]");
      if (!optionWrap) return;

      const buttons = optionWrap.querySelectorAll("[data-mz-value]");
      if (!buttons.length) return;

      // Try detect attribute name from hidden select: pa_color / attribute_pa_color / etc.
      // Prefer the first select inside variations.
      const select = form.querySelector(".variations select");
      if (!select) return;

      const setActive = (value) => {
        buttons.forEach((btn) => {
          const v = (btn.getAttribute("data-mz-value") || "").toLowerCase().trim();
          const isOn = v && value && v === value.toLowerCase().trim();
          btn.classList.toggle("is-active", !!isOn);
          btn.setAttribute("aria-pressed", isOn ? "true" : "false");
        });
      };

      // On button click -> set select value + trigger Woo change
      buttons.forEach((btn) => {
        btn.addEventListener("click", () => {
          const value = btn.getAttribute("data-mz-value");
          if (!value) return;

          // set value in select
          select.value = value;

          // trigger woo handlers
          select.dispatchEvent(new Event("change", { bubbles: true }));

          // update active UI immediately
          setActive(value);
        });
      });

      // When Woo updates variation selection (auto-select, page reload, etc.)
      select.addEventListener("change", () => setActive(select.value));

      // When variation found / reset / etc.
      form.addEventListener("reset_data", () => setActive(""));

      // Initial state (preselected / default)
      setActive(select.value);
    });
  }

  // DOM ready
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initMezivaVariantActive);
  } else {
    initMezivaVariantActive();
  }
})();
