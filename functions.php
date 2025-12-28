<?php
/**
 * Astra Child Theme functions and definitions
 */

defined('ABSPATH') || exit;

define('CHILD_THEME_ASTRA_CHILD_VERSION', '1.0.0');

/**
 * Enqueue child theme CSS
 */
add_action('wp_enqueue_scripts', function () {
  wp_enqueue_style(
    'astra-child-theme-css',
    get_stylesheet_directory_uri() . '/style.css',
    ['astra-theme-css'],
    CHILD_THEME_ASTRA_CHILD_VERSION
  );
}, 15);

/**
 * Enqueue Tailwind CSS output
 */
add_action('wp_enqueue_scripts', function () {
  $css_path = get_stylesheet_directory() . '/assets/tailwind.css';
  if (file_exists($css_path)) {
    wp_enqueue_style(
      'astra-child-tailwind',
      get_stylesheet_directory_uri() . '/assets/tailwind.css',
      [],
      filemtime($css_path)
    );
  }
}, 20);

/**
 * Enqueue header JS
 */
add_action('wp_enqueue_scripts', function () {
  $js_path = get_stylesheet_directory() . '/assets/js/header.js';
  if (file_exists($js_path)) {
    wp_enqueue_script(
      'meziva-header',
      get_stylesheet_directory_uri() . '/assets/js/header.js',
      [],
      filemtime($js_path),
      true
    );
  }
}, 25);

/**
 * Register menu
 */
add_action('after_setup_theme', function () {
  register_nav_menus([
    'meziva_primary' => __('Meziva Primary Menu', 'meziva'),
  ]);
});

/**
 * Disable Astra default header + footer
 */
add_filter('astra_primary_header_display', '__return_false');
add_filter('astra_footer_display', '__return_false');

/**
 * Helper: Front page ID
 */
function meziva_front_page_id() {
  $id = (int) get_option('page_on_front');
  return $id > 0 ? $id : null;
}

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
 * Announcement bar (ACF FREE) - sitewide (from Front Page fields)
 */
add_action('astra_header_before', function () {
  if (is_admin()) return;
  if (!function_exists('get_field')) return;

  $front_id = meziva_front_page_id();
  if (!$front_id) return;

  $enabled = get_field('announcement_enable', $front_id);
  if (!$enabled) return;

  $text      = get_field('announcement_text', $front_id);
  $code      = get_field('announcement_code', $front_id);
  $linkText  = get_field('announcement_link_text', $front_id) ?: 'Shop Now';
  $linkUrl   = get_field('announcement_link_url', $front_id);
  $bg        = get_field('announcement_bg', $front_id) ?: '#9B4A6A';
  $textColor = get_field('announcement_text_color', $front_id) ?: '#ffffff';

  if (!$text) return;
  ?>
  <div
    class="mz-w-full mz-text-center mz-text-sm md:mz-text-[14px] mz-font-body"
    style="background: <?php echo esc_attr($bg); ?>; color: <?php echo esc_attr($textColor); ?>;"
  >
    <div class="mz-max-w-[1240px] mz-mx-auto mz-px-4 mz-py-2 mz-inline-block mz-items-center mz-justify-center mz-gap-2 mz-flex-wrap">
      <span class="mz-tracking-wide">
        <?php echo esc_html($text); ?>
        <?php if ($code): ?>
          <span class="mz-font-semibold"> <?php echo esc_html($code); ?></span>
        <?php endif; ?>
      </span>

      <?php if (!empty($linkUrl)): ?>
        <a
          href="<?php echo esc_url($linkUrl); ?>"
          class="mz-font-semibold mz-underline-offset-4 hover:mz-opacity-90 mz-transition"
        >
          <?php echo esc_html($linkText); ?>
        </a>
      <?php endif; ?>
    </div>
  </div>
  <?php
}, 1);

/**
 * Custom header include
 */
add_action('astra_header_before', function () {
  if (is_admin()) return;

  $file = get_stylesheet_directory() . '/template-parts/meziva-header.php';
  if (file_exists($file)) include $file;
}, 2);

/**
 * Customer Reviews include (Keen slider - your existing)
 */
function mz_success_stories_assets() {
  wp_enqueue_style(
    'keen-slider',
    'https://cdn.jsdelivr.net/npm/keen-slider@6.8.6/keen-slider.min.css'
  );

  wp_enqueue_script(
    'keen-slider',
    'https://cdn.jsdelivr.net/npm/keen-slider@6.8.6/keen-slider.min.js',
    [],
    null,
    true
  );

  wp_enqueue_script(
    'mz-success-stories',
    get_stylesheet_directory_uri() . '/assets/js/success-stories.js',
    ['keen-slider'],
    null,
    true
  );
}
add_action('wp_enqueue_scripts', 'mz_success_stories_assets');

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
 * WooCommerce PDP setup: we render summary in our template
 */
add_action('after_setup_theme', function () {
  remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_title', 5);
  remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10);
  remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);
  remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20);
  remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
  remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);

  // We'll not use default tabs
  remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10);

  // We will call related manually in template
  remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);
}, 20);


add_action('wp_enqueue_scripts', function () {
  if (!function_exists('is_product') || !is_product()) return;

  // Fancybox v5
  wp_enqueue_style('mz-fancybox', 'https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css', [], null);
  wp_enqueue_script('mz-fancybox', 'https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js', [], null, true);

  // Keen slider (thumbs)
  wp_enqueue_style('mz-keen', 'https://cdn.jsdelivr.net/npm/keen-slider@6.8.6/keen-slider.min.css', [], null);
  wp_enqueue_script('mz-keen', 'https://cdn.jsdelivr.net/npm/keen-slider@6.8.6/keen-slider.min.js', [], null, true);

  // Our gallery js
  $gpath = get_stylesheet_directory() . '/assets/js/mz-gallery.js';
  if (file_exists($gpath)) {
    wp_enqueue_script('mz-gallery', get_stylesheet_directory_uri() . '/assets/js/mz-gallery.js', ['mz-keen','mz-fancybox'], filemtime($gpath), true);
  }

  // Our accordion js (your working one)
  $apath = get_stylesheet_directory() . '/assets/js/mz-pdp.js';
  if (file_exists($apath)) {
    wp_enqueue_script('mz-pdp', get_stylesheet_directory_uri() . '/assets/js/mz-pdp.js', [], filemtime($apath), true);
  }
}, 50);
  

/**
 * Helper: Parse "Label: Value" lines from a textarea
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
 * PDP page
 */
add_action('wp_enqueue_scripts', function () {
  if (is_product()) {
    wp_enqueue_script('wc-add-to-cart-variation');

    wp_enqueue_script(
      'mz-variants',
      get_stylesheet_directory_uri() . '/assets/js/mz-variants.js',
      ['jquery', 'wc-add-to-cart-variation'],
      '1.0.2',
      true
    );

    wp_enqueue_script(
      'mz-pdp',
      get_stylesheet_directory_uri() . '/assets/js/mz-pdp.js',
      [],
      '1.0.2',
      true
    );
  }
}, 20);



/***
 * PDP add to cart
 * 
 */
add_action('wp_footer', function () {
  if (!is_product()) return;
  ?>
  <script>
    (function(){
      function findQtyInput(scope){
        return scope.querySelector('input.qty')
          || scope.querySelector('input[name="quantity"]')
          || scope.querySelector('input[type="number"]');
      }

      function triggerAll(input){
        // native + jQuery triggers for max compatibility (ajax cart plugins)
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
});



/**
 * Safe ACF getter
 */
function mz_get_acf($key, $post_id = null) {
  if (function_exists('get_field')) return get_field($key, $post_id);
  $post_id = $post_id ?: get_the_ID();
  return get_post_meta($post_id, $key, true);
}

add_action('wp_head', function () {
  if (!is_cart()) return;
  echo '<style>button[name="update_cart"]{display:none!important;}</style>';
});

/**
 * AJAX: Update cart quantity (no reload)
 */
// ===============================
// 1) Hide Update Cart button
// ===============================
add_action('wp_head', function () {
  if (!is_cart()) return;
  echo '<style>button[name="update_cart"]{display:none!important;}</style>';
});

// ===============================
// 2) Helpers: render totals + notices
// =============================== 
function mz_cart_render_totals_html() {
  ob_start();
  wc_get_template('cart/cart-totals.php');
  return ob_get_clean();
}

function mz_cart_render_notices_html() {
  ob_start();
  wc_print_notices();
  return ob_get_clean();
}

// ===============================
// 3) AJAX: Update qty
// ===============================
add_action('wp_ajax_mz_update_cart_qty', 'mz_update_cart_qty');
add_action('wp_ajax_nopriv_mz_update_cart_qty', 'mz_update_cart_qty');

function mz_update_cart_qty() {
  check_ajax_referer('mz_cart_nonce', 'nonce');

  if (!isset($_POST['cart_item_key'], $_POST['quantity'])) {
    wp_send_json_error(['message' => 'Missing data']);
  }

  $cart_item_key = wc_clean(wp_unslash($_POST['cart_item_key']));
  $qty = max(0, intval($_POST['quantity']));

  if (!WC()->cart) wc_load_cart();

  WC()->cart->set_quantity($cart_item_key, $qty, true);
  WC()->cart->calculate_totals();

  $cart_item = WC()->cart->get_cart_item($cart_item_key);
  $line_total_html = '';
  if ($cart_item && isset($cart_item['data'])) {
    $line_total_html = WC()->cart->get_product_subtotal($cart_item['data'], $cart_item['quantity']);
  }

  wp_send_json_success([
    'totals_html' => mz_cart_render_totals_html(),
    'notices_html' => mz_cart_render_notices_html(),
    'line_total_html' => $line_total_html,
    'cart_count' => WC()->cart->get_cart_contents_count(),
  ]);
}

// ===============================
// 4) AJAX: Apply coupon
// ===============================
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
      'totals_html' => mz_cart_render_totals_html(),
      'notices_html' => mz_cart_render_notices_html(),
    ]);
  }

  $applied = WC()->cart->apply_coupon($code);
  WC()->cart->calculate_totals();

  // apply_coupon already adds notices sometimes, but ensure some feedback
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
    'totals_html' => mz_cart_render_totals_html(),
    'notices_html' => mz_cart_render_notices_html(),
  ]);
}

// ===============================
// 5) AJAX: Remove coupon
// ===============================
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
    'totals_html' => mz_cart_render_totals_html(),
    'notices_html' => mz_cart_render_notices_html(),
  ]);
}

// ===============================
// 6) Cart page JS: qty + coupon apply/remove (AJAX)
// ===============================
add_action('wp_footer', function () {
  if (!is_cart()) return;
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

        // totals
        const totalsWrap = document.querySelector('#mz-cart-totals');
        if (totalsWrap && data.totals_html) totalsWrap.innerHTML = data.totals_html;

        // notices
        const noticesWrap = document.querySelector('#mz-cart-notices');
        if (noticesWrap && data.notices_html !== undefined) noticesWrap.innerHTML = data.notices_html;

        // cart count (optional)
        const badge = document.querySelector('[data-mz-cart-count]');
        if (badge && data.cart_count !== undefined) badge.textContent = data.cart_count;
      }

      function post(action, payload){
        const fd = new FormData();
        fd.append('action', action);
        fd.append('nonce', nonce);
        Object.keys(payload || {}).forEach(k => fd.append(k, payload[k]));

        return fetch(ajaxUrl, { method:'POST', body: fd }).then(r => r.json());
      }

      // ---------------------
      // Qty AJAX
      // ---------------------
      function postQty(cartItemKey, qty, rowEl){
        setLoading(rowEl, true);

        post('mz_update_cart_qty', { cart_item_key: cartItemKey, quantity: qty })
          .then(res => {
            if(!res || !res.success) return;

            updateUI(res.data);

            // line total
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

      // ---------------------
      // Coupon APPLY AJAX
      // ---------------------
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

            // clear input on success (optional)
            // input.value = '';
          })
          .finally(() => setLoading(btn, false));
      });

      // ---------------------
      // Coupon REMOVE AJAX (from totals remove link)
      // Woo outputs link like ?remove_coupon=welcome&_wpnonce=...
      // We'll intercept click and call our AJAX.
      // ---------------------
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
});


/**
 * checkout page
 */
add_filter('body_class', function($classes){
  if (is_checkout()) $classes[] = 'mz-checkout';
  return $classes;
});

add_filter('woocommerce_checkout_fields', function($fields){
  // optional: field classes for better spacing
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
  if (is_account_page()) {
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
