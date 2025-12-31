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


/**
 * CMS pages debug (keep; but avoid noisy logs on production if needed)
 */
add_action('acf/init', function () {
  // error_log('ACF Loaded');
});
