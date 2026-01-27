<?php
/**
 * Astra Child Theme functions and definitions (Optimized + Safe)
 * - No layout / functionality break
 * - Only performance + duplication cleanup + safer hooks
 */

defined('ABSPATH') || exit;

define('CHILD_THEME_ASTRA_CHILD_VERSION', '1.0.0');

/**
 * =========================
 * Assets: CSS + JS
 * =========================
 */

/** Enqueue child theme CSS */
add_action('wp_enqueue_scripts', function () {
  $style_path = get_stylesheet_directory() . '/style.css';
  wp_enqueue_style(
    'astra-child-theme-css',
    get_stylesheet_uri(),
    ['astra-theme-css'],
    file_exists($style_path) ? filemtime($style_path) : CHILD_THEME_ASTRA_CHILD_VERSION
  );
}, 15);

/** Enqueue Tailwind CSS output */
add_action('wp_enqueue_scripts', function () {
  $css_path = get_stylesheet_directory() . '/assets/tailwind.css';
  if (!file_exists($css_path)) return;

  wp_enqueue_style(
    'astra-child-tailwind',
    get_stylesheet_directory_uri() . '/assets/tailwind.css',
    [],
    filemtime($css_path)
  );
}, 20);

/** Enqueue header JS (footer + defer) */
add_action('wp_enqueue_scripts', function () {
  if (is_admin()) return;

  $js_path = get_stylesheet_directory() . '/assets/js/header.js';
  if (!file_exists($js_path)) return;

  wp_enqueue_script(
    'meziva-header',
    get_stylesheet_directory_uri() . '/assets/js/header.js',
    [],
    filemtime($js_path),
    true // footer
  );
}, 25);

/** Add defer attribute to header script */
add_filter('script_loader_tag', function ($tag, $handle, $src) {
  if ($handle !== 'meziva-header') return $tag;
  return '<script src="' . esc_url($src) . '" defer></script>';
}, 10, 3);


/**
 * =========================
 * Register menu
 * =========================
 */
add_action('after_setup_theme', function () {
  register_nav_menus([
    'meziva_primary' => __('Meziva Primary Menu', 'meziva'),
  ]);
});

/**
 * Add class to menu links (for underline animation)
 */
add_filter('nav_menu_link_attributes', function ($atts, $item, $args) {
  if (!isset($args->theme_location) || $args->theme_location !== 'meziva_primary') return $atts;

  $existing = isset($atts['class']) ? $atts['class'] : '';
  $atts['class'] = trim($existing . ' meziva-navlink');
  return $atts;
}, 10, 3);


/**
 * =========================
 * Disable Astra default header + footer
 * =========================
 */
add_filter('astra_primary_header_display', '__return_false');
add_filter('astra_footer_display', '__return_false');


/**
 * =========================
 * Helper: Front page ID
 * =========================
 */
function meziva_front_page_id() {
  $id = (int) get_option('page_on_front');
  return $id > 0 ? $id : null;
}


/**
 * =========================
 * Safe ACF getter
 * (kept compatible with your existing usage)
 * =========================
 */
function mz_get_acf($key, $post_id = null) {
  if (function_exists('get_field')) return get_field($key, $post_id);
  $post_id = $post_id ?: get_the_ID();
  return get_post_meta($post_id, $key, true);
}


/**
 * =========================
 * Announcement bar (ACF FREE) - sitewide (from Front Page fields)
 * =========================
 */
add_action('astra_header_before', function () {
  if (is_admin()) return;
  if (!function_exists('get_field')) return;

  $front_id = meziva_front_page_id();
  if (!$front_id) return;

  $enabled = (bool) get_field('announcement_enable', $front_id);
  if (!$enabled) return;

  $text      = (string) get_field('announcement_text', $front_id);
  $code      = (string) get_field('announcement_code', $front_id);
  $linkText  = (string) (get_field('announcement_link_text', $front_id) ?: 'Shop Now');
  $linkUrl   = (string) get_field('announcement_link_url', $front_id);
  $bg        = (string) (get_field('announcement_bg', $front_id) ?: '#9B4A6A');
  $textColor = (string) (get_field('announcement_text_color', $front_id) ?: '#ffffff');

  if (!$text) return;
  ?>
  <div class="mz-w-full mz-text-center mz-text-sm md:mz-text-[14px] mz-font-body"
       style="background: <?php echo esc_attr($bg); ?>; color: <?php echo esc_attr($textColor); ?>;">
    <div class="mz-max-w-[1240px] mz-mx-auto mz-px-4 mz-py-2 mz-inline-block mz-items-center mz-justify-center mz-gap-2 mz-flex-wrap mz-flex">
      <span class="mz-tracking-wide">
        <?php echo esc_html($text); ?>
        <?php if ($code): ?>
          <span class="mz-font-semibold"> <?php echo esc_html($code); ?></span>
        <?php endif; ?>
      </span>

      <?php if (!empty($linkUrl)): ?>
        <a href="<?php echo esc_url($linkUrl); ?>"
           class="mz-font-semibold mz-underline-offset-4 hover:mz-opacity-90 mz-transition">
          <?php echo esc_html($linkText); ?>
        </a>
      <?php endif; ?>
    </div>
  </div>
  <?php
}, 1);


/**
 * =========================
 * Custom header include
 * =========================
 */
add_action('astra_header_before', function () {
  if (is_admin()) return;
  $file = get_stylesheet_directory() . '/template-parts/meziva-header.php';
  if (file_exists($file)) include $file;
}, 2);


/**
 * =========================
 * Success Stories / Reviews slider assets
 * (same functionality, add filemtime for cache-bust)
 * =========================
 */
add_action('wp_enqueue_scripts', function () {
  if (is_admin()) return;

  // Keep CDN as you had (no change)
  wp_enqueue_style('keen-slider', 'https://cdn.jsdelivr.net/npm/keen-slider@6.8.6/keen-slider.min.css', [], null);
  wp_enqueue_script('keen-slider', 'https://cdn.jsdelivr.net/npm/keen-slider@6.8.6/keen-slider.min.js', [], null, true);

  $path = get_stylesheet_directory() . '/assets/js/success-stories.js';
  if (file_exists($path)) {
    wp_enqueue_script(
      'mz-success-stories',
      get_stylesheet_directory_uri() . '/assets/js/success-stories.js',
      ['keen-slider'],
      filemtime($path),
      true
    );
  }
}, 30);


/**
 * =========================
 * ACF options page
 * =========================
 */
if (function_exists('acf_add_options_page')) {
  acf_add_options_page([
    'page_title' => 'Footer Settings',
    'menu_title' => 'Footer Settings',
    'menu_slug'  => 'footer-settings',
    'capability' => 'manage_options',
    'redirect'   => false,
  ]);
}


/**
 * =========================
 * WooCommerce PDP setup: we render summary in our template
 * =========================
 */
add_action('after_setup_theme', function () {
  remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_title', 5);
  remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10);
  remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);
  remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20);
  remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
  remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);

  remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10);
  remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);
}, 20);


/**
 * =========================
 * PDP Assets (single place only)
 * =========================
 */ 
add_action('wp_enqueue_scripts', function () {
  if (!function_exists('is_product') || !is_product()) return;

  // libs
  wp_enqueue_style('mz-fancybox', 'https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css', [], null);
  wp_enqueue_script('mz-fancybox', 'https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js', [], null, true);

  wp_enqueue_style('mz-keen', 'https://cdn.jsdelivr.net/npm/keen-slider@6.8.6/keen-slider.min.css', [], null);
  wp_enqueue_script('mz-keen', 'https://cdn.jsdelivr.net/npm/keen-slider@6.8.6/keen-slider.min.js', [], null, true);

  // Woo variation script (must for variable products)
  wp_enqueue_script('wc-add-to-cart-variation');

  // gallery
  $gpath = get_stylesheet_directory() . '/assets/js/mz-gallery.js';
  if (file_exists($gpath)) {
    wp_enqueue_script(
      'mz-gallery',
      get_stylesheet_directory_uri() . '/assets/js/mz-gallery.js',
      ['mz-keen', 'mz-fancybox'],
      filemtime($gpath),
      true
    );
  }

  // variants UI (your swatches)
  $vpath = get_stylesheet_directory() . '/assets/js/mz-variants.js';
  if (file_exists($vpath)) {
    wp_enqueue_script(
      'mz-variants',
      get_stylesheet_directory_uri() . '/assets/js/mz-variants.js',
      ['jquery', 'wc-add-to-cart-variation'],
      filemtime($vpath),
      true
    );
  }

  // PDP main js
  $apath = get_stylesheet_directory() . '/assets/js/mz-pdp.js';
  if (file_exists($apath)) {
    wp_enqueue_script(
      'mz-pdp',
      get_stylesheet_directory_uri() . '/assets/js/mz-pdp.js',
      [],
      filemtime($apath),
      true
    );
  }
}, 50);


/**
 * =========================
 * Helper: Parse "Label: Value" lines from a textarea
 * =========================
 */
function mz_parse_label_value_lines($text) {
  $out = [];
  if (!$text) return $out;

  $lines = preg_split("/\r\n|\n|\r/", trim((string)$text));
  foreach ($lines as $line) {
    $line = trim($line);
    if ($line === '') continue;

    $parts = explode(':', $line, 2);
    if (count($parts) === 2) {
      $label = trim($parts[0]);
      $value = trim($parts[1]);
      if ($label !== '' && $value !== '') {
        $out[] = ['label' => $label, 'value' => $value];
      }
    } else {
      $out[] = ['label' => '', 'value' => $line];
    }
  }
  return $out;
}


/**
 * =========================
 * PDP add to cart qty buttons (your existing)
 * =========================
 */
add_action('wp_footer', function () {
  if (!function_exists('is_product') || !is_product()) return;
  ?>
  <script>
    (function(){
      function findQtyInput(scope){
        return scope.querySelector('input.qty')
          || scope.querySelector('input[name="quantity"]')
          || scope.querySelector('input[type="number"]');
      }

      function triggerAll(input){
        input.dispatchEvent(new Event('input', { bubbles: true }));
        input.dispatchEvent(new Event('change', { bubbles: true }));
        if (window.jQuery) {
          window.jQuery(input).trigger('input').trigger('change');
        }
      }

      document.addEventListener('click', function(e){
        const btn = e.target.closest('.mz-qty-btn');
        if(!btn) return;

        const form = btn.closest('form.cart') || btn.closest('.cart');
        if(!form) return;

        const input = findQtyInput(form);
        if(!input) return;

        const type = btn.getAttribute('data-type');
        const step = parseFloat(input.getAttribute('step')) || 1;
        const min  = input.getAttribute('min') !== null ? parseFloat(input.getAttribute('min')) : 1;
        const max  = input.getAttribute('max') !== null ? parseFloat(input.getAttribute('max')) : Infinity;

        let val = parseFloat(input.value);
        if (isNaN(val)) val = min;

        if(type === 'plus') val += step;
        if(type === 'minus') val -= step;

        if(val < min) val = min;
        if(val > max) val = max;

        input.value = val;
        triggerAll(input);
      });
    })();
  </script>
  <?php
}, 90);


/**
 * ===============================
 * CART: AJAX qty + coupon (KEEP)
 * ===============================
 */

/** Hide Update Cart button (only on cart) */
add_action('wp_head', function () {
  if (!function_exists('is_cart') || !is_cart()) return;
  echo '<style>button[name="update_cart"]{display:none!important;}</style>';
});

/** Helpers */
if (!function_exists('mz_cart_render_totals_html')) {
  function mz_cart_render_totals_html() {
    ob_start();
    wc_get_template('cart/cart-totals.php');
    return ob_get_clean();
  }
}
if (!function_exists('mz_cart_render_notices_html')) {
  function mz_cart_render_notices_html() {
    ob_start();
    wc_print_notices();
    return ob_get_clean();
  }
}

/** AJAX: Update qty */
if (!function_exists('mz_update_cart_qty')) {
  add_action('wp_ajax_mz_update_cart_qty', 'mz_update_cart_qty');
  add_action('wp_ajax_nopriv_mz_update_cart_qty', 'mz_update_cart_qty');

  function mz_update_cart_qty() {
    check_ajax_referer('mz_cart_nonce', 'nonce');

    $cart_item_key = isset($_POST['cart_item_key']) ? wc_clean(wp_unslash($_POST['cart_item_key'])) : '';
    $qty           = isset($_POST['quantity']) ? (int) $_POST['quantity'] : null;

    if (empty($cart_item_key) || $qty === null) {
      wp_send_json_error(['message' => 'Missing data']);
    }

    if (!WC()->cart) {
      wc_load_cart();
    }

    WC()->cart->set_quantity($cart_item_key, max(0, $qty), true);
    WC()->cart->calculate_totals();

    $cart_item = WC()->cart->get_cart_item($cart_item_key);
    $line_total_html = '';
    if ($cart_item && isset($cart_item['data'])) {
      $line_total_html = WC()->cart->get_product_subtotal($cart_item['data'], $cart_item['quantity']);
    }

    wp_send_json_success([
      'totals_html'     => mz_cart_render_totals_html(),
      'notices_html'    => mz_cart_render_notices_html(),
      'line_total_html' => $line_total_html,
      'cart_count'      => WC()->cart->get_cart_contents_count(),
    ]);
  }
}

/** AJAX: Apply coupon */
if (!function_exists('mz_apply_coupon')) {
  add_action('wp_ajax_mz_apply_coupon', 'mz_apply_coupon');
  add_action('wp_ajax_nopriv_mz_apply_coupon', 'mz_apply_coupon');

  function mz_apply_coupon() {
    check_ajax_referer('mz_cart_nonce', 'nonce');
    if (!WC()->cart) wc_load_cart();

    $code = isset($_POST['coupon_code']) ? wc_format_coupon_code(wp_unslash($_POST['coupon_code'])) : '';
    $code = trim($code);

    wc_clear_notices();

    if ($code === '') {
      wc_add_notice(__('Please enter a coupon code.', 'woocommerce'), 'error');
      wp_send_json_success([
        'totals_html'  => mz_cart_render_totals_html(),
        'notices_html' => mz_cart_render_notices_html(),
      ]);
    }

    $applied = WC()->cart->apply_coupon($code);
    WC()->cart->calculate_totals();

    if (!$applied) {
      if (!wc_notice_count('error')) {
        wc_add_notice(__('Coupon could not be applied.', 'woocommerce'), 'error');
      }
    } else {
      if (!wc_notice_count('success')) {
        wc_add_notice(__('Coupon applied successfully.', 'woocommerce'), 'success');
      }
    }

    wp_send_json_success([
      'totals_html'  => mz_cart_render_totals_html(),
      'notices_html' => mz_cart_render_notices_html(),
    ]);
  }
}

/** AJAX: Remove coupon */
if (!function_exists('mz_remove_coupon')) {
  add_action('wp_ajax_mz_remove_coupon', 'mz_remove_coupon');
  add_action('wp_ajax_nopriv_mz_remove_coupon', 'mz_remove_coupon');

  function mz_remove_coupon() {
    check_ajax_referer('mz_cart_nonce', 'nonce');
    if (!WC()->cart) wc_load_cart();

    $code = isset($_POST['coupon_code']) ? wc_format_coupon_code(wp_unslash($_POST['coupon_code'])) : '';
    $code = trim($code);

    wc_clear_notices();

    if ($code === '') {
      wc_add_notice(__('Invalid coupon.', 'woocommerce'), 'error');
    } else {
      WC()->cart->remove_coupon($code);
      WC()->cart->calculate_totals();
      wc_add_notice(__('Coupon removed.', 'woocommerce'), 'success');
    }

    wp_send_json_success([
      'totals_html'  => mz_cart_render_totals_html(),
      'notices_html' => mz_cart_render_notices_html(),
    ]);
  }
}

/** AJAX: Remove cart item (used on checkout too) */
if (!function_exists('mz_remove_cart_item')) {
  add_action('wp_ajax_mz_remove_cart_item', 'mz_remove_cart_item');
  add_action('wp_ajax_nopriv_mz_remove_cart_item', 'mz_remove_cart_item');

  function mz_remove_cart_item() {
    check_ajax_referer('mz_cart_nonce', 'nonce');

    $cart_item_key = isset($_POST['cart_item_key']) ? wc_clean(wp_unslash($_POST['cart_item_key'])) : '';
    if (empty($cart_item_key)) {
      wp_send_json_error(['message' => 'Missing cart_item_key']);
    }

    if (!WC()->cart) wc_load_cart();

    WC()->cart->remove_cart_item($cart_item_key);
    WC()->cart->calculate_totals();

    wp_send_json_success([
      'cart_count' => WC()->cart->get_cart_contents_count(),
    ]);
  }
}


/**
 * Checkout UI (qty + remove button) - KEEP
 */
add_filter('woocommerce_checkout_cart_item_quantity', function ($qty_html, $cart_item, $cart_item_key) {
  if (!is_checkout() || is_order_received_page()) return $qty_html;

  $qty = isset($cart_item['quantity']) ? (int)$cart_item['quantity'] : 1;

  return '
    <div class="mz-ck-qty mz-inline-flex mz-items-center mz-gap-2" data-mz-ck-qty-wrap data-key="' . esc_attr($cart_item_key) . '">
      <button type="button" class="mz-ck-qty-btn" data-mz-ck-qty="minus" aria-label="Decrease">
        <svg class="w-6 h-6 text-gray-800" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24">
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14"></path>
        </svg>
      </button>
      <input type="number" min="1" step="1" class="mz-ck-qty-input" value="' . esc_attr($qty) . '" />
      <button type="button" class="mz-ck-qty-btn" data-mz-ck-qty="plus" aria-label="Increase">
        <svg class="w-6 h-6 text-gray-800" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24">
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-7 7V5"></path>
        </svg>
      </button>
    </div>
  ';
}, 10, 3);

add_filter('woocommerce_cart_item_name', function ($name, $cart_item, $cart_item_key) {
  if (!is_checkout() || is_order_received_page()) return $name;

  $remove_btn = sprintf(
    '<button type="button" class="mz-ck-remove" data-mz-ck-remove="%s" aria-label="Remove item">&times;</button>',
    esc_attr($cart_item_key)
  );

  return '<div class="mz-ck-line">' . $name . $remove_btn . '</div>';
}, 10, 3);


/**
 * Cart page inline JS (KEEP - your existing)
 */
add_action('wp_footer', function () {
  if (!function_exists('is_cart') || !is_cart()) return;
  ?>
  <script>
    (function(){
      const ajaxUrl = "<?php echo esc_js(admin_url('admin-ajax.php')); ?>";
      const nonce   = "<?php echo esc_js(wp_create_nonce('mz_cart_nonce')); ?>";

      function setLoading(el, loading){
        if(!el) return;
        el.style.opacity = loading ? '0.6' : '';
        el.style.pointerEvents = loading ? 'none' : '';
      }

      function updateUI(data){
        if(!data) return;

        const totalsWrap = document.querySelector('#mz-cart-totals');
        if (totalsWrap && data.totals_html) totalsWrap.innerHTML = data.totals_html;

        const noticesWrap = document.querySelector('#mz-cart-notices');
        if (noticesWrap && data.notices_html !== undefined) noticesWrap.innerHTML = data.notices_html;

        const badge = document.querySelector('[data-mz-cart-count]');
        if (badge && data.cart_count !== undefined) {
          badge.textContent = data.cart_count;
          if (parseInt(data.cart_count, 10) > 0) {
            badge.classList.remove('mz-hidden');
            badge.setAttribute('aria-hidden', 'false');
          } else {
            badge.classList.add('mz-hidden');
            badge.setAttribute('aria-hidden', 'true');
          }
        }
      }

      function post(action, payload){
        const fd = new FormData();
        fd.append('action', action);
        fd.append('nonce', nonce);
        Object.keys(payload || {}).forEach(k => fd.append(k, payload[k]));
        return fetch(ajaxUrl, { method:'POST', body: fd }).then(r => r.json());
      }

      function postQty(cartItemKey, qty, rowEl){
        setLoading(rowEl, true);

        post('mz_update_cart_qty', { cart_item_key: cartItemKey, quantity: qty })
          .then(res => {
            if(!res || !res.success) return;

            updateUI(res.data);

            const lineTotal = rowEl.querySelector('[data-mz-line-total]');
            if (lineTotal && res.data.line_total_html) lineTotal.innerHTML = res.data.line_total_html;
          })
          .finally(() => setLoading(rowEl, false));
      }

      document.addEventListener('click', function(e){
        const btn = e.target.closest('[data-mz-qty-btn]');
        if(!btn) return;

        const row = btn.closest('[data-mz-cart-row]');
        const input = row.querySelector('input[data-mz-qty-input]');
        const key = row.getAttribute('data-mz-cart-row');

        let val = parseInt(input.value || '0', 10);
        if (isNaN(val)) val = 0;

        if(btn.dataset.mzQtyBtn === 'plus') val += 1;
        if(btn.dataset.mzQtyBtn === 'minus') val = Math.max(0, val - 1);

        input.value = val;
        postQty(key, val, row);
      });

      document.addEventListener('change', function(e){
        const input = e.target.closest('input[data-mz-qty-input]');
        if(!input) return;

        const row = input.closest('[data-mz-cart-row]');
        const key = row.getAttribute('data-mz-cart-row');

        let val = parseInt(input.value || '0', 10);
        if (isNaN(val) || val < 0) val = 0;

        input.value = val;
        postQty(key, val, row);
      });

      document.addEventListener('click', function(e){
        const btn = e.target.closest('[data-mz-apply-coupon]');
        if(!btn) return;

        const input = document.querySelector('#mz-coupon-code');
        const code = input ? input.value.trim() : '';

        setLoading(btn, true);

        post('mz_apply_coupon', { coupon_code: code })
          .then(res => {
            if(!res || !res.success) return;
            updateUI(res.data);
          })
          .finally(() => setLoading(btn, false));
      });

      document.addEventListener('click', function(e){
        const link = e.target.closest('#mz-cart-totals a.woocommerce-remove-coupon');
        if(!link) return;

        e.preventDefault();

        const url = new URL(link.href, window.location.origin);
        const code = url.searchParams.get('remove_coupon');

        setLoading(link, true);

        post('mz_remove_coupon', { coupon_code: code || '' })
          .then(res => {
            if(!res || !res.success) return;
            updateUI(res.data);
          })
          .finally(() => setLoading(link, false));
      });
    })();
  </script>
  <?php
}, 100);


/**
 * checkout page
 */
add_filter('body_class', function ($classes) {
  if (function_exists('is_checkout') && is_checkout()) $classes[] = 'mz-checkout';
  return $classes;
});

add_filter('woocommerce_checkout_fields', function ($fields) {
  foreach ($fields as $section_key => $section) {
    foreach ($section as $key => $field) {
      $fields[$section_key][$key]['input_class'][] = 'mz-w-full';
      $fields[$section_key][$key]['class'][] = 'mz-mb-4';
    }
  }
  return $fields;
});

/**
 * Customers pages
 */
add_filter('body_class', function ($classes) {
  if (function_exists('is_account_page') && is_account_page()) {
    $classes[] = 'mz-account';
  }
  return $classes;
});


/**
 * Custom footer include
 */
add_action('astra_footer_before', function () {
  if (is_admin()) return;

  $file = get_stylesheet_directory() . '/template-parts/meziva-footer.php';
  if (file_exists($file)) include $file;
}, 1);


/**
 * =====================================
 * IMPORTANT PERFORMANCE FIX:
 * Remove global "no-cache headers" (it kills speed)
 * Keep only on logged-in/admin OR when you explicitly need.
 * (Same functionality, better performance)
 * =====================================
 */
add_action('send_headers', function () {
  // Allow caching for normal visitors (best performance)
  if (!is_user_logged_in()) return;

  header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
  header("Pragma: no-cache");
  header("Expires: 0");
});


/**
 * Footer ACF (SAFE) - cached in global once
 */
add_action('wp', function () {
  if (!function_exists('get_field')) return;

  $home_id = (int) get_option('page_on_front');
  if (!$home_id) return;

  $newsletter_text = get_field('ft_newsletter_text', $home_id) ?: 'Get the latest news, events & more delivered to your inbox.';
  $placeholder     = get_field('ft_newsletter_placeholder', $home_id) ?: 'Email address...';

  $bg         = get_field('ft_bg_color', $home_id) ?: '#FFFFFF';
  $text_color = get_field('ft_text_color', $home_id) ?: '#6B7280';
  $social_bg  = get_field('ft_social_bg', $home_id) ?: '#2E2E2E';
  $social_col = get_field('ft_social_color', $home_id) ?: '#FFFFFF';

  $GLOBALS['mz_footer_acf'] = compact('newsletter_text','placeholder','bg','text_color','social_bg','social_col');
});


/**
 * ===============================
 * Product page: redirect Woo "View cart" notice to Checkout
 * (You had it twice — keeping single version)
 * ===============================
 */
add_action('wp_footer', function () {
  if (is_admin()) return;
  if (!function_exists('wc_get_checkout_url')) return;

  $checkout = wc_get_checkout_url();
  ?>
  <script>
    (function () {
      const checkoutUrl = <?php echo json_encode($checkout); ?>;

      document.addEventListener('click', function(e){
        const a = e.target.closest('a.wc-forward');
        if(!a) return;

        const href = (a.getAttribute('href') || '').toLowerCase();
        if (href.includes('/cart') || href.includes('cart=')
          || href.includes('cart')) {
          e.preventDefault();
          window.location.href = checkoutUrl;
        }
      }, true);

      document.querySelectorAll('a.wc-forward').forEach(function(a){
        const href = (a.getAttribute('href') || '').toLowerCase();
        if (href.includes('cart')) a.setAttribute('href', checkoutUrl);
      });
    })();
  </script>
  <?php
}, 999);


/**
 * ===============================
 * PDP: Hide "Added to cart" notices (only on product page)
 * ===============================
 */
add_action('wp', function () {
  if (function_exists('is_product') && is_product()) {
    remove_action('woocommerce_before_single_product', 'woocommerce_output_all_notices', 10);
  }
});


/**
 * ===============================
 * Buy Now: if mz_buy_now=1 then redirect to Checkout after add-to-cart
 * (Keep single version only)
 * ===============================
 */
add_filter('woocommerce_add_to_cart_redirect', function ($url) {
  if (!empty($_REQUEST['mz_buy_now']) && $_REQUEST['mz_buy_now'] == '1') {
    return function_exists('wc_get_checkout_url') ? wc_get_checkout_url() : $url;
  }
  return $url;
}, 99);


/**
 * ===============================
 * Optional: Disable Cart page (redirect cart -> checkout)
 * ===============================
 */
add_action('template_redirect', function () {
  $disable_cart = true; // future me cart chahiye ho to false kar dena
  if ($disable_cart && function_exists('is_cart') && is_cart() && function_exists('wc_get_checkout_url')) {
    wp_safe_redirect(wc_get_checkout_url());
    exit;
  }
});


/**
 * Checkout: hide success notice type
 */
add_filter('woocommerce_notice_types', function ($types) {
  if (function_exists('is_checkout') && is_checkout() && !is_order_received_page()) {
    $types = array_diff($types, ['success']);
  }
  return $types;
});


/**
 * Checkout page JS (qty + remove) - KEEP
 */
add_action('wp_footer', function () {
  if (!is_checkout() || is_order_received_page()) return;
  ?>
  <style>
    .mz-ck-line{display:flex;align-items:flex-start;justify-content:space-between;gap:10px}
    .mz-ck-remove{border:0;background:transparent;font-size:22px;line-height:1;opacity:.65;cursor:pointer}
    .mz-ck-remove:hover{opacity:1}
    .mz-ck-qty-btn{width:32px;height:32px;border:1px solid #e5e7eb;border-radius:10px;background:#fff;cursor:pointer}
    .mz-ck-qty-input{width:54px;height:32px;text-align:center;border:1px solid #e5e7eb;border-radius:10px}
  </style>

  <script>
    (function(){
      const ajaxUrl = "<?php echo esc_js(admin_url('admin-ajax.php')); ?>";
      const nonce   = "<?php echo esc_js(wp_create_nonce('mz_cart_nonce')); ?>";

      function post(action, payload){
        const fd = new FormData();
        fd.append('action', action);
        fd.append('nonce', nonce);
        Object.keys(payload || {}).forEach(k => fd.append(k, payload[k]));
        return fetch(ajaxUrl, { method:'POST', body: fd }).then(r => r.json());
      }

      function refreshCheckout(){
        if (window.jQuery) window.jQuery('body').trigger('update_checkout');
      }

      function updateBadge(count){
        const badge = document.querySelector('[data-mz-cart-count]');
        if (!badge || count === undefined) return;
        badge.textContent = count;
        if (parseInt(count, 10) > 0) {
          badge.classList.remove('mz-hidden');
          badge.setAttribute('aria-hidden','false');
        } else {
          badge.classList.add('mz-hidden');
          badge.setAttribute('aria-hidden','true');
        }
      }

      document.addEventListener('click', function(e){
        const btn = e.target.closest('[data-mz-ck-qty]');
        if(!btn) return;

        const wrap = btn.closest('[data-mz-ck-qty-wrap]');
        if(!wrap) return;

        const input = wrap.querySelector('.mz-ck-qty-input');
        const key   = wrap.getAttribute('data-key');

        let val = parseInt(input.value || '1', 10);
        if (isNaN(val) || val < 1) val = 1;

        if (btn.dataset.mzCkQty === 'plus')  val += 1;
        if (btn.dataset.mzCkQty === 'minus') val = Math.max(1, val - 1);

        input.value = val;

        post('mz_update_cart_qty', { cart_item_key: key, quantity: val })
          .then(res => {
            if(!res || !res.success) return;
            updateBadge(res.data && res.data.cart_count);
            refreshCheckout();
          });
      });

      document.addEventListener('change', function(e){
        const input = e.target.closest('.mz-ck-qty-input');
        if(!input) return;

        const wrap = input.closest('[data-mz-ck-qty-wrap]');
        const key  = wrap ? wrap.getAttribute('data-key') : '';

        let val = parseInt(input.value || '1', 10);
        if (isNaN(val) || val < 1) val = 1;
        input.value = val;

        post('mz_update_cart_qty', { cart_item_key: key, quantity: val })
          .then(res => {
            if(!res || !res.success) return;
            updateBadge(res.data && res.data.cart_count);
            refreshCheckout();
          });
      });

      document.addEventListener('click', function(e){
        const btn = e.target.closest('[data-mz-ck-remove]');
        if(!btn) return;

        const key = btn.getAttribute('data-mz-ck-remove');

        post('mz_remove_cart_item', { cart_item_key: key })
          .then(res => {
            if(!res || !res.success) return;
            updateBadge(res.data && res.data.cart_count);
            refreshCheckout();
          });
      });
    })();
  </script>
  <?php
}, 999);


/**
 * Hide add-to-cart message HTML on PDP/Checkout (single version)
 */
add_filter('wc_add_to_cart_message_html', function ($message, $products = []) {
  if (function_exists('is_product') && is_product()) return '';
  if (function_exists('is_checkout') && is_checkout()) return '';
  return $message;
}, 999, 2);


/**
 * Checkout empty => redirect
 * (kept; no duplicate "defined" blocks)
 */
if (!function_exists('mz_checkout_empty_redirect_url')) {
  function mz_checkout_empty_redirect_url() {
    // Option B: Home
    return home_url('/');
  }
}

add_action('template_redirect', function () {
  if (is_admin() || wp_doing_ajax()) return;
  if (!function_exists('is_checkout') || !is_checkout()) return;
  if (function_exists('is_order_received_page') && is_order_received_page()) return;

  if (function_exists('WC') && WC()->cart && WC()->cart->is_empty()) {
    wc_clear_notices();
    wp_safe_redirect(mz_checkout_empty_redirect_url());
    exit;
  }
}, 1);


defined('ABSPATH') || exit;

/**
 * CONTACT FORM: Turnstile verify + send email
 */

// 1) Load Turnstile JS (only when keys exist)
add_action('wp_enqueue_scripts', function () {
  if (defined('MZ_TURNSTILE_SITE_KEY') && MZ_TURNSTILE_SITE_KEY) {
    wp_enqueue_script(
      'cf-turnstile',
      'https://challenges.cloudflare.com/turnstile/v0/api.js',
      [],
      null,
      true
    );
  }
}, 20);



// 2) (Optional but recommended) Create CPT to store messages in WP Admin
add_action('init', function () {
  register_post_type('mz_contact_msg', [
    'labels' => [
      'name' => 'Contact Messages',
      'singular_name' => 'Contact Message',
    ],
    'public' => false,
    'show_ui' => true,
    'menu_icon' => 'dashicons-email-alt2',
    'supports' => ['title'],
  ]);
});


// 3) Handle form submit
add_action('admin_post_nopriv_mz_contact_submit', 'mz_handle_contact_submit');
add_action('admin_post_mz_contact_submit', 'mz_handle_contact_submit');

function mz_handle_contact_submit() {

  // Where to redirect back
  $redirect = wp_get_referer() ? wp_get_referer() : home_url('/contact/');

  // Nonce check
  if (!isset($_POST['mz_contact_nonce']) || !wp_verify_nonce($_POST['mz_contact_nonce'], 'mz_contact_submit')) {
    wp_safe_redirect(add_query_arg('mz_contact', 'fail', $redirect));
    exit;
  }

  // Basic sanitize
  $name    = isset($_POST['full_name']) ? sanitize_text_field($_POST['full_name']) : '';
  $email   = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
  $phone   = isset($_POST['phone']) ? preg_replace('/\D+/', '', $_POST['phone']) : '';
  $message = isset($_POST['message']) ? wp_kses_post($_POST['message']) : '';

  if (!$name || !$email || !$phone || !$message) {
    wp_safe_redirect(add_query_arg('mz_contact', 'fail', $redirect));
    exit;
  }

  // 4) Turnstile verify
  if (!defined('MZ_TURNSTILE_SECRET_KEY') || !MZ_TURNSTILE_SECRET_KEY) {
    wp_safe_redirect(add_query_arg('mz_contact', 'fail', $redirect));
    exit;
  }

  $token = isset($_POST['cf-turnstile-response']) ? sanitize_text_field($_POST['cf-turnstile-response']) : '';
  if (!$token) {
    wp_safe_redirect(add_query_arg('mz_contact', 'fail', $redirect));
    exit;
  }

  $verify = wp_remote_post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
    'timeout' => 15,
    'body' => [
      'secret'   => MZ_TURNSTILE_SECRET_KEY,
      'response' => $token,
      'remoteip' => $_SERVER['REMOTE_ADDR'] ?? '',
    ],
  ]);

  $ok = false;
  if (!is_wp_error($verify)) {
    $body = json_decode(wp_remote_retrieve_body($verify), true);
    $ok = !empty($body['success']);
  }

  if (!$ok) {
    wp_safe_redirect(add_query_arg('mz_contact', 'fail', $redirect));
    exit;
  }

  // 5) Store in DB (CPT)
  $post_id = wp_insert_post([
    'post_type'   => 'mz_contact_msg',
    'post_status' => 'publish',
    'post_title'  => 'Message from ' . $name . ' (' . current_time('Y-m-d H:i') . ')',
  ]);

  if ($post_id && !is_wp_error($post_id)) {
    update_post_meta($post_id, 'mz_name', $name);
    update_post_meta($post_id, 'mz_email', $email);
    update_post_meta($post_id, 'mz_phone', $phone);
    update_post_meta($post_id, 'mz_message', $message);
  }

  // 6) Send email
  $to = get_option('admin_email'); // or set support email here
  $subject = 'New Contact Query - Meziva';
  $body_email = "Name: $name\nEmail: $email\nPhone: $phone\n\nMessage:\n$message\n";
  $headers = ['Content-Type: text/plain; charset=UTF-8', 'Reply-To: ' . $name . ' <' . $email . '>'];

  wp_mail($to, $subject, $body_email, $headers);

  wp_safe_redirect(add_query_arg('mz_contact', 'sent', $redirect));
  exit;
}


// Show columns in Contact Messages admin list
add_filter('manage_mz_contact_msg_posts_columns', function ($columns) {
  $new = [];
  $new['cb'] = $columns['cb'];
  $new['title'] = 'Title';
  $new['mz_name'] = 'Name';
  $new['mz_email'] = 'Email';
  $new['mz_phone'] = 'Phone';
  $new['mz_message'] = 'Message';
  $new['date'] = 'Date';
  return $new;
});

add_action('manage_mz_contact_msg_posts_custom_column', function ($column, $post_id) {
  if ($column === 'mz_name') {
    echo esc_html(get_post_meta($post_id, 'mz_name', true));
  }
  if ($column === 'mz_email') {
    $email = get_post_meta($post_id, 'mz_email', true);
    echo $email ? '<a href="mailto:' . esc_attr($email) . '">' . esc_html($email) . '</a>' : '';
  }
  if ($column === 'mz_phone') {
    echo esc_html(get_post_meta($post_id, 'mz_phone', true));
  }
  if ($column === 'mz_message') {
    $msg = wp_strip_all_tags(get_post_meta($post_id, 'mz_message', true));
    echo esc_html(mb_strimwidth($msg, 0, 60, '...'));
  }
}, 10, 2);


/**
 * Meziva Coming Soon — Tailwind (mz-) + Countdown + 2 Products + Leads + Admin Page
 */

/** ====== A) Toggle (7 Feb ko FALSE kar dena) ====== */
define('MZ_COMING_SOON_LOCK', true);
define('MZ_COMING_SOON_SLUG', 'coming-soon');

/**
 * IMPORTANT:
 * Countdown date YAHAN set hai (YYYY-MM-DD HH:MM:SS) — IST timezone WP settings se aayega.
 * Example: 2026-02-07 10:00:00
 */
define('MZ_LAUNCH_DATETIME', '2026-02-09 06:00:00');

/** ====== B) Create Leads Table ====== */
function mz_create_leads_table() {
  global $wpdb;
  $table = $wpdb->prefix . 'mz_leads';
  $charset = $wpdb->get_charset_collate();

  require_once ABSPATH . 'wp-admin/includes/upgrade.php';
  $sql = "CREATE TABLE IF NOT EXISTS $table (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    email VARCHAR(190) NOT NULL,
    phone VARCHAR(30) NULL,
    source VARCHAR(50) NULL,
    created_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY email_unique (email)
  ) $charset;";
  dbDelta($sql);
}
add_action('after_switch_theme', 'mz_create_leads_table');

/** ====== C) AJAX save lead ====== */
function mz_save_lead_ajax() {
  check_ajax_referer('mz_lead_nonce', 'nonce');

  $email  = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';
  $phone  = isset($_POST['phone']) ? preg_replace('/[^0-9+]/', '', wp_unslash($_POST['phone'])) : '';
  $source = isset($_POST['source']) ? sanitize_text_field(wp_unslash($_POST['source'])) : 'coming-soon';

  if (empty($email) || !is_email($email)) {
    wp_send_json_error(['message' => 'Please enter a valid email.']);
  }

  global $wpdb;
  $table = $wpdb->prefix . 'mz_leads';

  // Ensure table exists (safety)
  $wpdb->query("CREATE TABLE IF NOT EXISTS $table (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    email VARCHAR(190) NOT NULL,
    phone VARCHAR(30) NULL,
    source VARCHAR(50) NULL,
    created_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY email_unique (email)
  ) {$wpdb->get_charset_collate()};");

  $ok = $wpdb->replace(
    $table,
    [
      'email' => $email,
      'phone' => $phone ?: null,
      'source' => $source,
      'created_at' => current_time('mysql'),
    ],
    ['%s','%s','%s','%s']
  );

  if ($ok === false) {
    wp_send_json_error(['message' => 'Something went wrong. Please try again.']);
  }

  wp_send_json_success(['message' => 'Done! We’ll notify you on launch day.']);
}
add_action('wp_ajax_mz_save_lead', 'mz_save_lead_ajax');
add_action('wp_ajax_nopriv_mz_save_lead', 'mz_save_lead_ajax');

/** ====== D) Shortcode: [mz_coming_soon] ====== */
function mz_coming_soon_shortcode($atts) {
  $atts = shortcode_atts([
    'brand_title'  => 'Something Beautiful Is Coming',
    'hint'         => 'Hydrating tinted lip balms, crafted for everyday comfort.',
    'amazon_text'  => 'Available soon on Amazon & Meziva.in',
    'ig_url'       => 'https://www.instagram.com/officialsmezivabeauty',
    'fb_url'       => '#',
    'whatsapp_no'  => '',  // e.g. 919816975832 (no +)
    'logo_url'     => '',

    // Product titles
    'p1_title'     => 'Hydrating Berry Blast Lip Balm',
    'p1_desc'      => 'Soft tint • Ultra hydration • Everyday comfort',
    'p2_title'     => 'Hydrating Cherry Blast Lip Balm',
    'p2_desc'      => 'Fresh tint • Smooth finish • Daily lip care',

    // Optional product images (URL)
    'p1_img'       => '',
    'p2_img'       => '',
  ], $atts);

  // Use WP custom logo if not provided
  if (empty($atts['logo_url'])) {
    $custom_logo_id = get_theme_mod('custom_logo');
    if ($custom_logo_id) {
      $logo = wp_get_attachment_image_src($custom_logo_id, 'full');
      if ($logo) $atts['logo_url'] = $logo[0];
    }
  }

  $ajax_url = admin_url('admin-ajax.php');
  $nonce    = wp_create_nonce('mz_lead_nonce');

  // Countdown target (timestamp)
  $tz = wp_timezone();
  try {
    $target = new DateTime(MZ_LAUNCH_DATETIME, $tz);
  } catch (Exception $e) {
    $target = new DateTime('2026-02-09 06:00:00', $tz);
  }
  $target_ts = $target->getTimestamp();

  ob_start(); ?>

  <div class="mz-min-h-[100vh] mz-items-center mz-flex mz-w-full mz-bg-[radial-gradient(1200px_circle_at_20%_10%,rgba(197,139,170,.20),transparent_55%),radial-gradient(900px_circle_at_80%_20%,rgba(155,74,106,.18),transparent_55%),linear-gradient(180deg,#fff,rgba(246,239,234,.65))] mz-px-4 mz-py-10">
    <div class="mz-mx-auto mz-w-full mz-max-w-6xl">
 
      <!-- Top Card -->
      <div class="mz-rounded-3xl mz-border mz-border-[rgba(155,74,106,.22)] mz-bg-white/85 mz-backdrop-blur mz-shadow-[0_18px_70px_rgba(0,0,0,.08)] mz-overflow-hidden">
        <div class="mz-grid lg:mz-grid-cols-2">

          <!-- Left: Brand -->
          <div class="mz-p-7 md:mz-p-10">
            <div class="mz-flex mz-items-center mz-gap-3">
              <?php if (!empty($atts['logo_url'])): ?>
                <img class="mz-h-[50px] mz-w-auto" src="<?php echo esc_url($atts['logo_url']); ?>" alt="Meziva Beauty">
              <?php else: ?>
                <div class="mz-font-extrabold mz-tracking-[0.2em] mz-text-[#9B4A6A]">
                   <span class="mz-font-bold mz-tracking-wide logo-svg-wrapper">
                    <svg id="Layer_1" xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 626.59 236.11"><defs> <style> .st0 { fill: url(#linear-gradient2); } .st1 { fill: url(#linear-gradient1); } .st2 { fill: url(#linear-gradient4); } .st3 { fill: url(#linear-gradient5); } .st4 { fill: #8b384e; } .st5 { fill: url(#linear-gradient3); } .st6 { fill: #cb9853; } .st7 { fill: url(#linear-gradient); } </style> <linearGradient id="linear-gradient" x1="68.36" y1="161.01" x2="68.36" y2="123.83" gradientUnits="userSpaceOnUse"> <stop offset="0" stop-color="#a82c49"/> <stop offset="1" stop-color="#742138"/> </linearGradient> <linearGradient id="linear-gradient1" x1="84.64" y1="135.21" x2="8.98" y2="59.55" gradientUnits="userSpaceOnUse"> <stop offset=".19" stop-color="#b17231"/> <stop offset=".43" stop-color="#cca656"/> <stop offset=".7" stop-color="#b9813c"/> <stop offset=".84" stop-color="#b37534"/> <stop offset="1" stop-color="#cca656"/> </linearGradient> <linearGradient id="linear-gradient2" x1="28.88" y1="75.09" x2="99.02" y2="75.09" gradientUnits="userSpaceOnUse"> <stop offset="0" stop-color="#823046"/> <stop offset=".5" stop-color="#ba6976"/> <stop offset="1" stop-color="#711d36"/> </linearGradient> <linearGradient id="linear-gradient3" x1="94.12" y1="54.79" x2="30.07" y2="17.81" gradientUnits="userSpaceOnUse"> <stop offset="0" stop-color="#b9853c"/> <stop offset=".3" stop-color="#c39646"/> <stop offset=".61" stop-color="#cfa64f"/> <stop offset="1" stop-color="#b9853c"/> </linearGradient> <linearGradient id="linear-gradient4" x1="78.85" y1="52.66" x2="52.1" y2="25.91" gradientUnits="userSpaceOnUse"> <stop offset="0" stop-color="#c08937"/> <stop offset=".58" stop-color="#eac36a"/> </linearGradient> <linearGradient id="linear-gradient5" x1="86.55" y1="33.99" x2="125.06" y2="33.99" gradientUnits="userSpaceOnUse"> <stop offset=".58" stop-color="#731e3b"/> <stop offset="1" stop-color="#e6959b"/> </linearGradient> </defs> <g id="logo-shape"> <path class="st7" d="M39.01,123.83c1.07.54,2.61,1.28,4.49,2.12,2.06.92,4.01,1.78,5.94,2.43,2.03.68,5.08,1.45,9.09,1.61,8.23.52,14.6,2.57,18.72,4.25,6.17,2.52,11.5,4.69,15.57,10.2,4.67,6.33,4.96,13.38,4.88,16.58-1.4-1.94-3.39-4.16-6.2-6.04-1.6-1.08-4.54-2.75-14.48-4.88-5.41-1.16-6.01-.98-9.11-1.66-4.86-1.07-17.09-3.77-24.2-13.68-1.44-2.01-3.57-5.56-4.7-10.92Z"/> <path class="st1" d="M24.42,44.11c-.43,3.32-.77,9.55,1.94,16.45,2.4,6.11,6.03,9.81,9.24,13.09,3,3.05,8.06,7.49,15.59,11.15,4.98,1.93,11.91,6.21,19.57,12.52,9.55,7.87,18.64,18.98,16.78,31.89,0,0,0-19.83-31.91-31.19,0,0-25.09-7.97-29.84-21.44,0,0-1.65,28.46,31.81,37.13,0,0-20.45-12.5-25.72-24.17,0,0,9.81,9.71,21.17,12.81,0,0,29.12,8.26,32.84,31.6-1.43-1.06-3.62-2.49-6.51-3.69-2.26-.94-4.05-1.36-7.17-2.09-2.06-.48-4.87-1.06-8.27-1.55-5.88-.51-15.56-2.22-25.32-8.49-3.6-2.31-9.6-6.25-14.43-13.81-6-9.37-6.64-18.72-6.66-23.03-.22-3.93-.17-8.44.39-13.4,1.12-9.88,3.91-17.94,6.51-23.78Z"/> <path class="st0" d="M36.74,20.77c-.61,3.91-.83,7.18-.91,9.55-.11,3.45.07,4.93.16,5.55.45,3.16,1.48,5.64,2.3,7.25,2.04,4.05,4.94,7.19,5.5,7.77,5.07,5.28,10.48,8.54,17.74,12.84,7.66,4.54,13.46,7.95,16.27,9.6,3.46,1.87,9.29,5.66,13.94,12.55,2.39,3.54,3.83,6.94,4.72,9.66,1.2,3.06,2.65,7.95,2.57,14.04-.14,9.68-4.1,16.75-6.09,19.83.24-3.09.24-7.33-.83-12.19-1.54-7.02-4.58-12.16-6.71-15.18-2.69-3.02-6.41-6.69-11.2-10.23-1.52-1.12-5.72-4.13-11.62-7.02-.21-.1-.34-.16-.43-.21-3.65-1.79-22.08-11.34-30.03-28.5-2.63-5.68-3.1-10.95-3.1-10.95-.64-7.24,1.53-12.87,2.48-15.26,1.64-4.13,3.74-7.18,5.27-9.11Z"/> <path class="st5" d="M36.12,7.34s19.67.15,32.53,12.55c0,0,19.21,14.56,12.7,41.52-.03-4.29-.68-18.9-11.68-31.75-7.62-8.91-16.58-12.73-21.03-14.28.07,4.57.7,8.31,1.31,10.97.76,3.35,2.61,11.1,8.47,18.8,2.55,3.35,4.94,5.48,6.93,7.23,6.36,5.6,10.57,6.89,16.21,12.91,1.8,1.93,3.09,3.63,3.82,4.65,0,0-23.08-9.76-30.98-19.05-1.77-1.76-3.69-3.99-5.46-6.76-4.72-7.37-5.96-14.69-6.31-19.11.26-1.93.58-6.34-1.65-11.15-1.48-3.19-3.51-5.31-4.85-6.51Z"/> <path class="st2" d="M54.86,23.14c1.75.89,4.36,2.38,7.13,4.72,7.64,6.48,10.52,14.4,11.61,17.52.82,2.35,1.82,5.85,2.24,10.28-1.46-.7-3.54-1.86-5.7-3.72-1.73-1.48-2.91-2.89-4.04-4.36-3.83-4.97-7.64-9.91-9.85-17.49-.47-1.61-1.06-3.99-1.39-6.97Z"/> <path class="st3" d="M120.6,9.61s-1.24,3.92-13.63,11.15c0,0-26.36,10.33-19.17,37.59,0,0,5.78-8.26,15.78-16.73,0,0,6.08-4.75,8.76-17.56,0,0,3.3,8.47-5.78,18.8l-14.25,13.84s30.98-11.77,32.22-26.85c0,0,2.69-13.63-3.92-20.24Z"/> <polygon class="st6" points="96.64 6.28 97.4 8.62 99.85 8.62 97.86 10.06 98.62 12.4 96.64 10.96 94.65 12.4 95.41 10.06 93.42 8.62 95.88 8.62 96.64 6.28"/> <polygon class="st6" points="135.57 19.76 136.33 22.1 138.79 22.1 136.8 23.54 137.56 25.88 135.57 24.43 133.58 25.88 134.34 23.54 132.35 22.1 134.81 22.1 135.57 19.76"/> <polygon class="st6" points="20.63 27.87 21.38 30.2 23.84 30.2 21.85 31.65 22.61 33.99 20.63 32.54 18.64 33.99 19.4 31.65 17.41 30.2 19.87 30.2 20.63 27.87"/> <polygon class="st6" points="10.14 43.4 10.66 45.02 12.37 45.02 10.99 46.03 11.52 47.66 10.14 46.65 8.75 47.66 9.28 46.03 7.9 45.02 9.61 45.02 10.14 43.4"/> <polygon class="st6" points="15.17 105.86 15.7 107.49 17.41 107.49 16.02 108.5 16.55 110.12 15.17 109.12 13.78 110.12 14.31 108.5 12.93 107.49 14.64 107.49 15.17 105.86"/> <polygon class="st6" points="23.84 118.38 25.2 122.55 29.58 122.55 26.03 125.12 27.39 129.29 23.84 126.71 20.3 129.29 21.65 125.12 18.11 122.55 22.49 122.55 23.84 118.38"/> </g> <g id="logo"> <path class="st4" d="M116.54,60.16h14.21l26.76,64.11,27.97-64.11h14.21l16.93,100.85h-19.96l-9.07-62.14-27.06,62.14h-6.65l-25.85-62.14-10.28,62.14h-19.96l18.75-100.85Z"/> <path class="st4" d="M288.4,77.1h-36.29v22.53h34.78v16.93h-34.78v27.52h36.29v16.93h-55.94V60.16h55.94v16.93Z"/> <path class="st6" d="M421.14,60.16v100.85h-19.66V60.16h19.66Z"/> <path class="st6" d="M336.72,144.08h46.57v16.93h-79.23l48.99-83.92h-41.28v-16.93h73.94l-48.99,83.92Z"/> <path class="st6" d="M458.63,60.16l26.91,67.74,26.91-67.74h21.47l-42.64,100.85h-11.79l-42.34-100.85h21.47Z"/> <polygon class="st6" points="571.26 60.16 555.84 60.16 512.44 161.01 533.76 161.01 548.88 123.81 563.25 87.53 577.61 123.81 592.28 161.01 613.45 161.01 571.26 60.16"/> </g> <g> <path class="st6" d="M304.75,185.09h14.21c7.83,0,11.47,4.12,11.47,9.25,0,4.3-2.47,7.1-5.31,8.07,2.58.82,6.53,3.43,6.53,8.87,0,6.98-5.32,10.84-12.34,10.84h-14.55v-37.02ZM318.01,200.48c5.36,0,7.45-2.11,7.45-5.72,0-3.28-2.35-5.62-6.69-5.62h-9.21v11.34h8.45ZM309.56,218.05h9.14c4.65,0,7.88-2.18,7.88-6.8,0-4.02-2.58-6.72-8.82-6.72h-8.2v13.52Z"/> <path class="st6" d="M384.37,204.8h-18.41v13.1h20.22l-.63,4.21h-24.4v-37.02h24.11v4.21h-19.29v11.3h18.41v4.21Z"/> <path class="st6" d="M420.84,211l-3.98,11.11h-4.89l13.18-37.02h6.04l13.75,37.02h-5.23l-4.11-11.11h-14.77ZM434.35,206.8c-3.53-9.73-5.47-14.9-6.27-17.72h-.05c-.91,3.14-3.07,9.4-5.92,17.72h12.24Z"/> <path class="st6" d="M476.08,185.09v22.28c0,8.75,4.79,11.11,9.54,11.11,5.55,0,9.46-2.55,9.46-11.11v-22.28h4.94v22.02c0,12.01-6.64,15.46-14.51,15.46s-14.41-3.73-14.41-15.11v-22.37h4.98Z"/> <path class="st6" d="M539.42,189.29h-11.96v-4.21h28.87v4.21h-11.97v32.81h-4.94v-32.81Z"/> <path class="st6" d="M594.84,222.11v-13.58c0-.36-.09-.73-.25-.98l-13.04-22.46h5.61c3.37,6.01,8.73,15.56,10.42,18.81,1.6-3.2,7.12-12.83,10.62-18.81h5.25l-13.45,22.54c-.13.23-.22.43-.22.95v13.53h-4.94Z"/> </g> </svg>
                  </span>
                </div>
              <?php endif; ?>
              <span class="mz-text-xs mz-font-extrabold mz-rounded-full mz-bg-[#F6EFEA] mz-text-[#9B4A6A] mz-border mz-border-[rgba(155,74,106,.18)] mz-px-3 mz-py-1">
                Coming Soon
              </span>
            </div>

            <h1 class="mz-mt-5 xl:mz-mt-8 mz-text-3xl md:mz-text-5xl mz-font-extrabold mz-leading-[1.05] mz-text-[#1c1116]">
              <?php echo esc_html($atts['brand_title']); ?>
            </h1>

            <p class="mz-mt-4 mz-text-[15px] md:mz-text-base mz-leading-7 mz-text-[#6a4b57]">
              <?php echo esc_html($atts['hint']); ?>
            </p>

            <!-- Countdown -->
            <div class="mz-mt-6 xl:mz-mt-10">
              <div class="mz-text-xs mz-font-extrabold mz-uppercase mz-tracking-wide mz-text-[#9B4A6A]">
                Launching in
              </div>

              <div class="mz-mt-2 mz-grid mz-grid-cols-4 mz-gap-2 md:mz-gap-3 mz-max-w-[420px]" id="mzCountdown" data-target="<?php echo esc_attr($target_ts); ?>">
                <?php
                  $box = function($id, $label){
                    ?>
                    <div class="mz-rounded-2xl mz-border mz-border-[rgba(155,74,106,.18)] mz-bg-[#f5f5f5] mz-shadow-[0_8px_22px_rgba(155,74,106,.10)] mz-p-3 md:mz-p-4 mz-text-center">
                      <div class="mz-text-2xl md:mz-text-3xl mz-font-extrabold mz-text-[#1c1116]" id="<?php echo esc_attr($id); ?>">00</div>
                      <div class="mz-mt-1 mz-text-[10px] md:mz-text-xs mz-font-bold mz-text-[#6a4b57]"><?php echo esc_html($label); ?></div>
                    </div>
                    <?php
                  };
                  $box('mzCdDays','Days');
                  $box('mzCdHours','Hours');
                  $box('mzCdMins','Minutes');
                  $box('mzCdSecs','Seconds');
                ?>
              </div>

              <div class="mz-mt-3 mz-text-xs mz-font-bold mz-text-[#6a4b57]">
                Available soon on <span class="mz-font-extrabold mz-text-[#1c1116]">Amazon</span> & <span class="mz-font-extrabold mz-text-[#1c1116]">Meziva.in</span>
              </div>
            </div>

            <!-- Social -->
            <div class="mz-mt-6 xl:mz-mt-[70px] mz-flex mz-flex-wrap mz-gap-2">
              <a class="mz-inline-flex mz-items-center mz-justify-center mz-rounded-full mz-border mz-border-[rgba(155,74,106,.22)] mz-bg-white mz-px-3 mz-py-3 mz-text-xs mz-font-extrabold mz-text-[#9B4A6A] hover:mz-bg-[#F6EFEA] mz-w-[40px] mz-h-[40px]"
                 href="https://www.instagram.com/officialsmezivabeauty" target="_blank" rel="noopener">
                 <svg width="24px" height="24px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"> <path fill-rule="evenodd" clip-rule="evenodd" d="M12 18C15.3137 18 18 15.3137 18 12C18 8.68629 15.3137 6 12 6C8.68629 6 6 8.68629 6 12C6 15.3137 8.68629 18 12 18ZM12 16C14.2091 16 16 14.2091 16 12C16 9.79086 14.2091 8 12 8C9.79086 8 8 9.79086 8 12C8 14.2091 9.79086 16 12 16Z" fill="#0F0F0F"/> <path d="M18 5C17.4477 5 17 5.44772 17 6C17 6.55228 17.4477 7 18 7C18.5523 7 19 6.55228 19 6C19 5.44772 18.5523 5 18 5Z" fill="#0F0F0F"/> <path fill-rule="evenodd" clip-rule="evenodd" d="M1.65396 4.27606C1 5.55953 1 7.23969 1 10.6V13.4C1 16.7603 1 18.4405 1.65396 19.7239C2.2292 20.8529 3.14708 21.7708 4.27606 22.346C5.55953 23 7.23969 23 10.6 23H13.4C16.7603 23 18.4405 23 19.7239 22.346C20.8529 21.7708 21.7708 20.8529 22.346 19.7239C23 18.4405 23 16.7603 23 13.4V10.6C23 7.23969 23 5.55953 22.346 4.27606C21.7708 3.14708 20.8529 2.2292 19.7239 1.65396C18.4405 1 16.7603 1 13.4 1H10.6C7.23969 1 5.55953 1 4.27606 1.65396C3.14708 2.2292 2.2292 3.14708 1.65396 4.27606ZM13.4 3H10.6C8.88684 3 7.72225 3.00156 6.82208 3.0751C5.94524 3.14674 5.49684 3.27659 5.18404 3.43597C4.43139 3.81947 3.81947 4.43139 3.43597 5.18404C3.27659 5.49684 3.14674 5.94524 3.0751 6.82208C3.00156 7.72225 3 8.88684 3 10.6V13.4C3 15.1132 3.00156 16.2777 3.0751 17.1779C3.14674 18.0548 3.27659 18.5032 3.43597 18.816C3.81947 19.5686 4.43139 20.1805 5.18404 20.564C5.49684 20.7234 5.94524 20.8533 6.82208 20.9249C7.72225 20.9984 8.88684 21 10.6 21H13.4C15.1132 21 16.2777 20.9984 17.1779 20.9249C18.0548 20.8533 18.5032 20.7234 18.816 20.564C19.5686 20.1805 20.1805 19.5686 20.564 18.816C20.7234 18.5032 20.8533 18.0548 20.9249 17.1779C20.9984 16.2777 21 15.1132 21 13.4V10.6C21 8.88684 20.9984 7.72225 20.9249 6.82208C20.8533 5.94524 20.7234 5.49684 20.564 5.18404C20.1805 4.43139 19.5686 3.81947 18.816 3.43597C18.5032 3.27659 18.0548 3.14674 17.1779 3.0751C16.2777 3.00156 15.1132 3 13.4 3Z" fill="#0F0F0F"/> </svg>
                </a>

              <a class="mz-inline-flex mz-items-center mz-justify-center mz-rounded-full mz-border mz-border-[rgba(155,74,106,.22)] mz-bg-white mz-px-3 mz-py-3 mz-text-xs mz-font-extrabold mz-text-[#9B4A6A] hover:mz-bg-[#F6EFEA] mz-w-[40px] mz-h-[40px]"
                 href="https://www.facebook.com/people/Meziva-Beauty/61585775721470/" target="_blank" rel="noopener">
                    <svg width="24px" height="24px" viewBox="-5 0 20 20" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"> <title>facebook [#176]</title> <desc>Created with Sketch.</desc> <defs> </defs> <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"> <g id="Dribbble-Light-Preview" transform="translate(-385.000000, -7399.000000)" fill="#000000"> <g id="icons" transform="translate(56.000000, 160.000000)"> <path d="M335.821282,7259 L335.821282,7250 L338.553693,7250 L339,7246 L335.821282,7246 L335.821282,7244.052 C335.821282,7243.022 335.847593,7242 337.286884,7242 L338.744689,7242 L338.744689,7239.14 C338.744689,7239.097 337.492497,7239 336.225687,7239 C333.580004,7239 331.923407,7240.657 331.923407,7243.7 L331.923407,7246 L329,7246 L329,7250 L331.923407,7250 L331.923407,7259 L335.821282,7259 Z" id="facebook-[#176]"> </path> </g> </g> </g> </svg>
                </a>

                 <a class="mz-inline-flex mz-items-center mz-justify-center mz-rounded-full mz-border mz-border-[rgba(155,74,106,.22)] mz-bg-white mz-px-3 mz-py-3 mz-text-xs mz-font-extrabold mz-text-[#9B4A6A] hover:mz-bg-[#F6EFEA] mz-w-[40px] mz-h-[40px]"
                  href="mailto:support@meziva.in" target="_blank" rel="noopener">
                      <svg width="24px" height="24px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"> <path d="M4 9.00005L10.2 13.65C11.2667 14.45 12.7333 14.45 13.8 13.65L20 9" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/> <path d="M3 9.17681C3 8.45047 3.39378 7.78123 4.02871 7.42849L11.0287 3.5396C11.6328 3.20402 12.3672 3.20402 12.9713 3.5396L19.9713 7.42849C20.6062 7.78123 21 8.45047 21 9.17681V17C21 18.1046 20.1046 19 19 19H5C3.89543 19 3 18.1046 3 17V9.17681Z" stroke="#000000" stroke-width="2" stroke-linecap="round"/> </svg>
                  </a>

              <?php if (!empty($atts['whatsapp_no'])): ?>
                <a class="mz-inline-flex mz-items-center mz-justify-center mz-rounded-xl mz-border mz-border-[rgba(34,197,94,.35)] mz-bg-white mz-px-4 mz-py-2 mz-text-xs mz-font-extrabold mz-text-[#16a34a] hover:mz-bg-[rgba(34,197,94,.08)]"
                   href="<?php echo esc_url('https://wa.me/' . $atts['whatsapp_no']); ?>" target="_blank" rel="noopener">WhatsApp</a>
              <?php endif; ?>

              
            </div>
           
          </div>

          <!-- Right: Products + Form -->
          <div class="mz-p-4 md:mz-p-8 mz-bg-[linear-gradient(180deg,rgba(246,239,234,.75),rgba(255,255,255,.9))] mz-border-t lg:mz-border-t-0 lg:mz-border-l mz-border-[rgba(155,74,106,.14)]">

            <div class="mz-text-sm mz-font-extrabold mz-text-[#1c1116]">Our first launches</div>
            <div class="mz-mt-4 mz-grid md:mz-grid-cols-2 mz-gap-3">

              <!-- Product Card 1 -->
              <div class="mz-rounded-2xl mz-border mz-border-[rgba(155,74,106,.18)] mz-bg-white mz-p-4 mz-shadow-[0_10px_26px_rgba(0,0,0,.06)]">
                <div class="mz-flex mz-items-center mz-gap-3">
                  <div class="mz-w-[40px] m mz-rounded-xl mz-bg-[rgba(155,74,106,.12)] mz-flex mz-items-center mz-justify-center mz-font-extrabold mz-text-[#9B4A6A]">
                    <img src="https://meziva.in/wp-content/uploads/2026/01/berry-blast-front-removebg-preview-1.png" alt="">
                  </div>
                  <div class="mz-flex-1">
                    <div class="mz-font-extrabold mz-text-sm mz-text-[#1c1116]">Hydrating Berry Blast Lip Balm</div>
                    <div class="mz-text-xs mz-font-bold mz-text-[#6a4b57]"><?php echo esc_html($atts['p1_desc']); ?></div>
                       <div class="mz-mt-3 mz-flex mz-gap-2 mz-flex-wrap">
                  <span class="mz-text-[10px] mz-font-extrabold mz-rounded-full mz-bg-[#F6EFEA] mz-text-[#9B4A6A] mz-px-3 mz-py-1">Hydrating</span>
                  <span class="mz-text-[10px] mz-font-extrabold mz-rounded-full mz-bg-[#F6EFEA] mz-text-[#9B4A6A] mz-px-3 mz-py-1">Tinted</span>
                  <span class="mz-text-[10px] mz-font-extrabold mz-rounded-full mz-bg-[#F6EFEA] mz-text-[#9B4A6A] mz-px-3 mz-py-1">Everyday</span>
                </div>
                  </div>
                </div>

                <?php if (!empty($atts['p1_img'])): ?>
                  <img class="mz-mt-3 mz-w-full mz-rounded-xl mz-border mz-border-[rgba(155,74,106,.10)]" src="<?php echo esc_url($atts['p1_img']); ?>" alt="<?php echo esc_attr($atts['p1_title']); ?>">
                <?php endif; ?>

             
              </div>

              <!-- Product Card 2 -->
              <div class="mz-rounded-2xl mz-border mz-border-[rgba(155,74,106,.18)] mz-bg-white mz-p-4 mz-shadow-[0_10px_26px_rgba(0,0,0,.06)]">
                <div class="mz-flex mz-items-center mz-gap-3">
                    <div class="mz-w-[40px] m mz-rounded-xl mz-bg-[rgba(155,74,106,.12)] mz-flex mz-items-center mz-justify-center mz-font-extrabold mz-text-[#9B4A6A]">
                    <img src="https://meziva.in/wp-content/uploads/2026/01/cherry-blast-front-removebg-preview-1.png" alt="">
                  </div>
                  <div class="mz-flex-1">
                    <div class="mz-font-extrabold mz-text-sm mz-text-[#1c1116]">Hydrating Cherry Blast Lip Balm</div>
                    <div class="mz-text-xs mz-font-bold mz-text-[#6a4b57]"><?php echo esc_html($atts['p2_desc']); ?></div>
                      <div class="mz-mt-3 mz-flex mz-gap-2 mz-flex-wrap">
                  <span class="mz-text-[10px] mz-font-extrabold mz-rounded-full mz-bg-[#F6EFEA] mz-text-[#9B4A6A] mz-px-3 mz-py-1">Hydrating</span>
                  <span class="mz-text-[10px] mz-font-extrabold mz-rounded-full mz-bg-[#F6EFEA] mz-text-[#9B4A6A] mz-px-3 mz-py-1">Tinted</span>
                  <span class="mz-text-[10px] mz-font-extrabold mz-rounded-full mz-bg-[#F6EFEA] mz-text-[#9B4A6A] mz-px-3 mz-py-1">Everyday</span>
                </div>
                  </div>
                </div>

           

              
              </div>

            </div>

            <!-- Lead Form -->
            <div class="mz-mt-6 mz-rounded-2xl mz-border mz-border-[rgba(155,74,106,.18)] mz-bg-white mz-p-5 mz-shadow-[0_10px_26px_rgba(0,0,0,.06)]">
              <div class="mz-flex mz-items-center mz-justify-between mz-gap-2 mz-flex-wrap">
                <div>
                  <div class="mz-text-sm mz-font-extrabold mz-text-[#1c1116]">Get launch updates</div>
                  <div class="mz-text-xs mz-font-bold mz-text-[#6a4b57]">Join early access list for Meziva Beauty.</div>
                </div>
                <span class="mz-text-[10px] mz-font-extrabold mz-rounded-full mz-bg-[#F6EFEA] mz-text-[#9B4A6A] mz-border mz-border-[rgba(155,74,106,.12)] mz-px-3 mz-py-1">
                  Early list
                </span>
              </div>

              <form id="mzLeadForm" class="mz-mt-4 mz-space-y-3">
                <input type="hidden" name="nonce" value="<?php echo esc_attr($nonce); ?>">
                <input type="hidden" name="source" value="coming-soon">

                <input
                  class="mz-w-full mz-rounded-xl mz-border mz-border-[rgba(155,74,106,.25)] mz-bg-white mz-px-4 mz-py-3 mz-text-sm mz-outline-none focus:mz-border-[#9B4A6A] focus:mz-ring-4 focus:mz-ring-[rgba(155,74,106,.12)]"
                  type="email" name="email" placeholder="Enter your email" required
                >
                <input
                  class="mz-w-full mz-rounded-xl mz-border mz-border-[rgba(155,74,106,.25)] mz-bg-white mz-px-4 mz-py-3 mz-text-sm mz-outline-none focus:mz-border-[#9B4A6A] focus:mz-ring-4 focus:mz-ring-[rgba(155,74,106,.12)]"
                  type="tel" maxlength="10" name="phone" placeholder="WhatsApp number (optional)"
                >

                <button
                  class="mz-w-full mz-rounded-xl mz-bg-[#9B4A6A] mz-py-3 mz-text-sm mz-font-extrabold mz-text-white hover:mz-brightness-95"
                  type="submit"
                >
                  Notify me at launch
                </button>

                <div id="mzMsg" class="mz-text-center mz-text-xs mz-font-extrabold mz-text-[#006400] mz-min-h-[18px]" aria-live="polite"></div>

                <div class="mz-text-center mz-text-[11px] mz-font-bold mz-text-[#6a4b57]">
                  By submitting, you agree to receive updates from Meziva Beauty. You can opt out anytime.
                </div>
              </form>
            </div>

           
          </div>
        </div>
      </div>

      <!-- Footer tiny -->
      <div class="mz-mt-6 mz-text-center mz-text-xs mz-font-bold mz-text-[#6a4b57]">
        © <?php echo esc_html(date('Y')); ?> Meziva Beauty • www.meziva.in
      </div>

    </div>
  </div>

  <script>
    (function(){
      // ====== Lead submit ======
      const form = document.getElementById('mzLeadForm');
      const msg  = document.getElementById('mzMsg');

      if(form){
        form.addEventListener('submit', async function(e){
          e.preventDefault();
          if(msg) msg.textContent = 'Saving...';

          const fd = new FormData(form);
          fd.append('action', 'mz_save_lead');

          try{
            const res = await fetch('<?php echo esc_js($ajax_url); ?>', { method:'POST', body: fd });
            const data = await res.json();
            if(data && data.success){
              if(msg) msg.textContent = data.data.message || 'Saved!';
              form.reset();
            }else{
              if(msg) msg.textContent = (data && data.data && data.data.message) ? data.data.message : 'Please try again.';
            }
          }catch(err){
            if(msg) msg.textContent = 'Network error. Please try again.';
          }
        });
      }

      // ====== Countdown ======
      const wrap = document.getElementById('mzCountdown');
      if(!wrap) return;

      const target = parseInt(wrap.getAttribute('data-target') || '0', 10) * 1000;

      const dEl = document.getElementById('mzCdDays');
      const hEl = document.getElementById('mzCdHours');
      const mEl = document.getElementById('mzCdMins');
      const sEl = document.getElementById('mzCdSecs');

      function pad(n){ return String(n).padStart(2,'0'); }

      function tick(){
        const now = Date.now();
        let diff = Math.max(0, target - now);

        const days = Math.floor(diff / (1000*60*60*24));
        diff -= days * (1000*60*60*24);

        const hours = Math.floor(diff / (1000*60*60));
        diff -= hours * (1000*60*60);

        const mins = Math.floor(diff / (1000*60));
        diff -= mins * (1000*60);

        const secs = Math.floor(diff / 1000);

        if(dEl) dEl.textContent = String(days);
        if(hEl) hEl.textContent = pad(hours);
        if(mEl) mEl.textContent = pad(mins);
        if(sEl) sEl.textContent = pad(secs);
      }

      tick();
      setInterval(tick, 1000);
    })();
  </script>

  <?php
  return ob_get_clean();
}
add_shortcode('mz_coming_soon', 'mz_coming_soon_shortcode');

/** ====== E) Site Lock (Coming Soon Mode) ====== */
function mz_coming_soon_lock() {
  if (!MZ_COMING_SOON_LOCK) return;

  // Admin/logged-in can browse
  if (is_user_logged_in()) return;
  if (is_admin()) return;
  if (defined('DOING_AJAX') && DOING_AJAX) return;

  // Allow coming soon page + login
  if (is_page(MZ_COMING_SOON_SLUG)) return;
  if (strpos($_SERVER['REQUEST_URI'], 'wp-login.php') !== false) return;

  wp_safe_redirect(home_url('/' . MZ_COMING_SOON_SLUG . '/'));
  exit;
}
add_action('template_redirect', 'mz_coming_soon_lock');

/** ====== F) Admin: Tools -> Meziva Leads ====== */
add_action('admin_menu', function () {
  add_management_page(
    'Meziva Leads',
    'Meziva Leads',
    'manage_options',
    'mz-leads',
    function () {
      if (!current_user_can('manage_options')) return;

      global $wpdb;
      $table = $wpdb->prefix . 'mz_leads';
      $rows = $wpdb->get_results("SELECT * FROM $table ORDER BY created_at DESC LIMIT 1000");

      echo '<div class="wrap"><h1>Meziva Leads</h1>';
      echo '<p>Latest 1000 leads (Email + Phone).</p>';
      echo '<table class="widefat striped"><thead><tr>
              <th>ID</th><th>Email</th><th>Phone</th><th>Source</th><th>Date</th>
            </tr></thead><tbody>';

      if ($rows) {
        foreach ($rows as $r) {
          echo '<tr>
                  <td>'.esc_html($r->id).'</td>
                  <td>'.esc_html($r->email).'</td>
                  <td>'.esc_html($r->phone).'</td>
                  <td>'.esc_html($r->source).'</td>
                  <td>'.esc_html($r->created_at).'</td>
                </tr>';
        }
      } else {
        echo '<tr><td colspan="5">No leads yet.</td></tr>';
      }

      echo '</tbody></table></div>';
    }
  );
});
 
