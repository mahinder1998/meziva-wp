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
 * =========================
 * Meziva Mega Menu Helpers
 * =========================
 */

if (!function_exists('mz_get_menu_tree')) {
  function mz_get_menu_tree($location = 'meziva_primary') {
    $locations = get_nav_menu_locations();
    if (empty($locations[$location])) return [];

    $menu_id = (int) $locations[$location];
    $items = wp_get_nav_menu_items($menu_id);
    if (empty($items)) return [];

    // Normalize
    $map = [];
    foreach ($items as $it) {
      $id = (int) $it->ID;
      $map[$id] = [
        'ID' => $id,
        'title' => $it->title,
        'url' => $it->url,
        'type' => $it->type,
        'object' => $it->object,
        'object_id' => (int) $it->object_id,
        'menu_item_parent' => (int) $it->menu_item_parent,
        'classes' => is_array($it->classes) ? $it->classes : [],
        'children' => [],
      ];
    }

    // Build tree
    $tree = [];
    foreach ($map as $id => &$node) {
      $parent = $node['menu_item_parent'];
      if ($parent && isset($map[$parent])) {
        $map[$parent]['children'][] = &$node;
      } else {
        $tree[] = &$node;
      }
    }
    unset($node);

    return $tree;
  }
}

if (!function_exists('mz_menu_item_image_url')) {
  /**
   * Returns menu item image url:
   * 1) ACF field on menu item: menu_thumb (Image field)
   * 2) If object is product: featured image
   */
  function mz_menu_item_image_url($menuItemNode) {
    $menu_item_id = (int) ($menuItemNode['ID'] ?? 0);

    // 1) ACF menu_thumb on menu item
    if ($menu_item_id && function_exists('get_field')) {
      $img = get_field('menu_thumb', $menu_item_id);
      if (is_array($img) && !empty($img['url'])) return $img['url'];
      if (is_numeric($img)) {
        $u = wp_get_attachment_image_url((int)$img, 'medium');
        if ($u) return $u;
      }
    }

    // 2) Product featured image
    $object = $menuItemNode['object'] ?? '';
    $object_id = (int) ($menuItemNode['object_id'] ?? 0);

    if ($object === 'product' && $object_id) {
      $thumb_id = get_post_thumbnail_id($object_id);
      if ($thumb_id) {
        $u = wp_get_attachment_image_url($thumb_id, 'woocommerce_thumbnail');
        if ($u) return $u;
      }
    }

    return '';
  }
}


/** 
 *  Mobile menu & desktop menu
 * **/
if (!function_exists('mz_render_primary_menu_html')) {
  function mz_render_primary_menu_html($location = 'meziva_primary', $is_mobile = false) {
    $tree = mz_get_menu_tree($location);
    if (empty($tree)) return;

    $ulClass = $is_mobile
      ? 'meziva-mobile-menu mz-flex mz-flex-col mz-gap-3 mz-text-[16px] mz-font-medium'
      : 'meziva-desktop-menu mz-flex mz-items-center mz-gap-6 xl:mz-gap-10 mz-text-[15px] mz-font-medium';

    echo '<ul class="' . esc_attr($ulClass) . '">';

    foreach ($tree as $top) {
      $has_children = !empty($top['children']);
      $title = $top['title'];
      $url   = $top['url'];

      $is_shop = (strtolower(trim($title)) === 'shop');

      $li_classes = [];
      if ($has_children) $li_classes[] = 'menu-item-has-children';
      if ($is_shop) $li_classes[] = 'mz-has-mega';

      echo '<li class="mz-relative ' . esc_attr(implode(' ', $li_classes)) . '" data-mz-menu-item="' . esc_attr($top['ID']) . '">';

      // =========================
      // TOP LINK
      // =========================
      if ($is_mobile) {
        // ✅ Mobile: show + icon on top level if it has children
        echo '<a href="' . esc_url($url) . '" class="menu-link mz-flex mz-items-center mz-justify-between mz-w-full">';
        echo '<span>' . esc_html($title) . '</span>';
        if ($has_children) echo '<span class="mz-text-xl mz-font-light" data-mz-plus>+</span>';
        echo '</a>';
      } else {
        // Desktop
        echo '<a href="' . esc_url($url) . '" class="menu-link mz-inline-flex mz-items-center mz-gap-2">';
        echo esc_html($title);
        if ($has_children) echo '<span class="mz-text-[12px] mz-opacity-70">
          <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24"> <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7"/> </svg>
        </span>';
        echo '</a>';
      }

      // =========================
      // DESKTOP dropdown / mega
      // =========================
      if (!$is_mobile && $has_children) {
        if ($is_shop) {
          echo '<div class="mz-mega-panel" data-mz-mega-panel>';
          echo '  <div class="mz-grid mz-grid-cols-12 mz-gap-6">';

          // Left: categories
          echo '    <div class="mz-col-span-4">';
          echo '      <div class="mz-text-xs mz-uppercase mz-tracking-wider mz-opacity-60 mz-mb-3">Category</div>';
          echo '      <ul class="mz-flex mz-flex-col mz-gap-1">';

          $firstCatId = 0;
          foreach ($top['children'] as $cat) {
            if (!$firstCatId) $firstCatId = (int)$cat['ID'];
            echo '<li>';
            echo '  <button type="button" class="mz-mega-cat-btn mz-w-full mz-border-0  mz-text-left  mz-py-2 mz-rounded-lg" data-mz-cat="' . esc_attr($cat['ID']) . '">';
            echo    esc_html($cat['title']);
            echo '  </button>';
            echo '</li>';
          }
          echo '      </ul>';
          echo '    </div>';

          // Right: products
          echo '    <div class="mz-col-span-12">';
          echo '      <div class="mz-text-xs mz-uppercase mz-tracking-wider mz-opacity-60 mz-mb-3">Products</div>';

          foreach ($top['children'] as $cat) {
            $catId = (int)$cat['ID'];
            $activeClass = ($catId === $firstCatId) ? '' : 'mz-hidden';

            echo '<div class="mz-mega-products-panel ' . esc_attr($activeClass) . '" data-mz-products="' . esc_attr($catId) . '">';

            if (!empty($cat['children'])) {
              echo '<div class="mz-grid mz-grid-cols-2 lg:mz-grid-cols-2 mz-gap-3">';
              foreach ($cat['children'] as $prod) {
                $img = mz_menu_item_image_url($prod);
                $purl = $prod['url'];
                $ptitle = $prod['title'];

                echo '<a href="' . esc_url($purl) . '" class="mz-group mz-grid  mz-gap-3 mz-rounded-xl mz-border mz-border-black/5 mz-bg-white hover:mz-shadow-lg mz-transition mz-p-3">';
                if ($img) {
                  echo '<img src="' . esc_url($img) . '" alt="' . esc_attr($ptitle) . '" class="mz-w-full mz-h-auto mz-rounded-lg mz-object-cover mz-bg-black/5" loading="lazy" decoding="async" />';
                } else {
                  echo '<div class="mz-w-12 mz-h-12 mz-rounded-lg mz-bg-black/5 mz-flex mz-items-center mz-justify-center mz-text-[10px] mz-opacity-60">No image</div>';
                }
                echo '<div class="mz-min-w-0">';
                echo '  <div class="mz-text-sm mz-font-medium mz-truncate group-hover:mz-text-[var(--mz-nav-hover)] mz-transition">' . esc_html($ptitle) . '</div>';
                echo '  ';
                echo '</div>';
                echo '</a>';
              }
              echo '</div>';
            } else {
              echo '<div class="mz-text-sm mz-opacity-70">No products added under this category.</div>';
            }

            echo '</div>';
          }

          echo '    </div>';
          echo '  </div>';
          echo '</div>';
        } else {
          echo '<ul class="mz-dropdown-panel">';
          foreach ($top['children'] as $child) {
            echo '<li><a class="menu-link" href="' . esc_url($child['url']) . '">' . esc_html($child['title']) . '</a></li>';
          }
          echo '</ul>';
        }
      }

      // =========================
      // MOBILE submenu (level 2 + 3)
      // =========================
      if ($is_mobile && $has_children) {
        echo '<div class="mz-mobile-subwrap" data-mz-subwrap>';
        echo '<ul class="mz-mobile-submenu">';

        foreach ($top['children'] as $child) {
          $child_has = !empty($child['children']);
          $child_li_cls = $child_has ? 'menu-item-has-children' : '';
          echo '<li class="' . esc_attr($child_li_cls) . '">';

          // level 2 link
          echo '<a href="' . esc_url($child['url']) . '" class="menu-link mz-flex mz-items-center mz-justify-between mz-w-full">';
          echo '<span>' . esc_html($child['title']) . '</span>';
          if ($child_has) echo '<span class="mz-text-xl mz-font-light" data-mz-plus>+</span>';
          echo '</a>';

          // level 3
          if ($child_has) {
            echo '<div class="mz-mobile-subwrap" data-mz-subwrap>';
            echo '<ul class="mz-mobile-submenu">';

            foreach ($child['children'] as $grand) {
              $img = mz_menu_item_image_url($grand);

              echo '<li>';
              echo '<a href="' . esc_url($grand['url']) . '" class="menu-link mz-flex mz-items-center mz-gap-3 mz-py-2">';

              if ($img) {
                echo '<img src="' . esc_url($img) . '" alt="' . esc_attr($grand['title']) . '" class="mz-w-10 mz-h-10 mz-rounded-lg mz-object-cover mz-bg-black/5" loading="lazy" decoding="async" />';
              } else {
                echo '<div class="mz-w-10 mz-h-10 mz-rounded-lg mz-bg-black/5 mz-flex mz-items-center mz-justify-center mz-text-[10px] mz-opacity-60">No</div>';
              }

              echo '<span class="mz-text-sm mz-font-medium">' . esc_html($grand['title']) . '</span>';
              echo '</a>';
              echo '</li>';
            }

            echo '</ul>';
            echo '</div>';
          }

          echo '</li>';
        }

        echo '</ul>';
        echo '</div>';
      }

      echo '</li>';
    }

    echo '</ul>';
  }
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

  // ✅ Prevent duplicate binding
  if (window.__mzPdpQtyBound) return;
  window.__mzPdpQtyBound = true;

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

    e.preventDefault();
    e.stopPropagation();

    const form = btn.closest('form.cart');
    if(!form) return;

    const input = findQtyInput(form);
    if(!input) return;

    const type = btn.dataset.type;
    const step = parseFloat(input.getAttribute('step')) || 1;
    const min  = input.getAttribute('min') !== null ? parseFloat(input.getAttribute('min')) : 1;
    const max  = input.getAttribute('max') !== null ? parseFloat(input.getAttribute('max')) : Infinity;

    let val = parseFloat(input.value);
    if (isNaN(val)) val = min;

    if(type === 'plus')  val += step;
    if(type === 'minus') val -= step;

    if(val < min) val = min;
    if(val > max) val = max;

    input.value = val;
    triggerAll(input);

  }, true); // capture mode prevents double bubbling

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
    '<button type="button" class="mz-ck-remove mz-bg-white mz-w-2 mz-h-2" data-mz-ck-remove="%s" aria-label="Remove item">&times;</button>',
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
  if (!function_exists('is_checkout') || !is_checkout() || is_order_received_page()) return;
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
      // ✅ IMPORTANT: bind only once (fix +2/-2 issue)
      if (window.__mzCheckoutQtyBound) return;
      window.__mzCheckoutQtyBound = true;

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

      // ✅ Click (Plus/Minus)
      document.addEventListener('click', function(e){
        const btn = e.target.closest('[data-mz-ck-qty]');
        if(!btn) return;

        // stop any other duplicate handlers
        e.preventDefault();
        e.stopPropagation();

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
      }, true); // capture=true helps prevent double bubbling issues

      // ✅ Manual input change
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
      }, true);

      // ✅ Remove item
      document.addEventListener('click', function(e){
        const btn = e.target.closest('[data-mz-ck-remove]');
        if(!btn) return;

        e.preventDefault();
        e.stopPropagation();

        const key = btn.getAttribute('data-mz-ck-remove');

        post('mz_remove_cart_item', { cart_item_key: key })
          .then(res => {
            if(!res || !res.success) return;
            updateBadge(res.data && res.data.cart_count);
            refreshCheckout();
          });
      }, true);
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



// checkout tracking for GTM / Meta
add_action('wp_footer', function () {
    if (!function_exists('is_checkout') || !is_checkout() || is_order_received_page()) return;
    if (!function_exists('WC') || !WC()->cart) return;

    $currency = get_woocommerce_currency();
    $total    = (float) WC()->cart->get_total('edit');
    $items    = [];

    foreach (WC()->cart->get_cart() as $cart_item) {
        $product = $cart_item['data'];
        if (!$product) continue;

        $items[] = [
            'item_id'   => (string) $product->get_id(),
            'item_name' => (string) $product->get_name(),
            'price'     => (float) wc_get_price_to_display($product),
            'quantity'  => (int) $cart_item['quantity'],
        ];
    }
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
      window.dataLayer = window.dataLayer || [];

      const checkoutCurrency = <?php echo wp_json_encode($currency); ?>;
      const checkoutValue    = <?php echo wp_json_encode($total); ?>;
      const checkoutItems    = <?php echo wp_json_encode($items); ?>;

      // prevent duplicate begin_checkout on refresh fragments
      const checkoutKey = 'mz_begin_checkout_fired';
      if (!sessionStorage.getItem(checkoutKey)) {
        window.dataLayer.push({ ecommerce: null });

        window.dataLayer.push({
          event: 'begin_checkout',
          ecommerce: {
            currency: checkoutCurrency,
            value: checkoutValue,
            items: checkoutItems
          }
        });

        sessionStorage.setItem(checkoutKey, '1');
        console.log('begin_checkout fired');
      }

      // Shipping method select
      document.addEventListener('change', function (e) {
        if (e.target.matches('input[name^="shipping_method"]')) {
          window.dataLayer.push({ ecommerce: null });
          window.dataLayer.push({
            event: 'add_shipping_info',
            ecommerce: {
              currency: checkoutCurrency,
              value: checkoutValue,
              shipping_tier: e.target.value || '',
              items: checkoutItems
            }
          });
          console.log('add_shipping_info fired');
        }

        // Payment method select
        if (e.target.matches('input[name="payment_method"]')) {
          window.dataLayer.push({ ecommerce: null });
          window.dataLayer.push({
            event: 'add_payment_info',
            ecommerce: {
              currency: checkoutCurrency,
              value: checkoutValue,
              payment_type: e.target.value || '',
              items: checkoutItems
            }
          });
          console.log('add_payment_info fired');
        }
      });
    });
    </script>
    <?php
}, 99);   




if (!defined('ABSPATH')) exit;
/**
 * Meziva: Sticky WhatsApp Floating Button
 * - Shows on entire site (you can restrict to homepage)
 * - Opens WhatsApp chat with prefilled message
 */

add_action('wp_footer', function () {
  // CHANGE THIS NUMBER (with country code, no +, no spaces)
  $phone = '919217912201';

  // Prefilled message
  $msg = 'Hi Meziva Beauty 👋
I want to order your Hydrating Lip Balm (SPF 30).
Please share shades, price & delivery details.';

  // WhatsApp URL
  $wa_url = 'https://wa.me/' . $phone . '?text=' . rawurlencode($msg);

  // If you want ONLY home page, uncomment below:
  // if (!is_front_page()) return;
  ?>
  
  <a
    href="<?php echo esc_url($wa_url); ?>"
    class="mz-fixed mz-right-[24px] mz-bottom-[70px] md:mz-right-6 md:mz-bottom-[70px] mz-z-[9999] mz-flex mz-items-center mz-justify-center mz-w-10 mz-h-10 md:mz-w-12 md:mz-h-12 mz-rounded-full mz-bg-[#25D366] mz-shadow-lg hover:mz-opacity-95 mz-transition"
    target="_blank"
    rel="noopener noreferrer"
    aria-label="Chat on WhatsApp"
    title="Chat on WhatsApp"
  >
    <!-- WhatsApp SVG Icon -->
  <!-- Uploaded to: SVG Repo, www.svgrepo.com, Generator: SVG Repo Mixer Tools --> <svg fill="#ffffff" width="24px" height="24px" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg"><path d="M11.42 9.49c-.19-.09-1.1-.54-1.27-.61s-.29-.09-.42.1-.48.6-.59.73-.21.14-.4 0a5.13 5.13 0 0 1-1.49-.92 5.25 5.25 0 0 1-1-1.29c-.11-.18 0-.28.08-.38s.18-.21.28-.32a1.39 1.39 0 0 0 .18-.31.38.38 0 0 0 0-.33c0-.09-.42-1-.58-1.37s-.3-.32-.41-.32h-.4a.72.72 0 0 0-.5.23 2.1 2.1 0 0 0-.65 1.55A3.59 3.59 0 0 0 5 8.2 8.32 8.32 0 0 0 8.19 11c.44.19.78.3 1.05.39a2.53 2.53 0 0 0 1.17.07 1.93 1.93 0 0 0 1.26-.88 1.67 1.67 0 0 0 .11-.88c-.05-.07-.17-.12-.36-.21z"/><path d="M13.29 2.68A7.36 7.36 0 0 0 8 .5a7.44 7.44 0 0 0-6.41 11.15l-1 3.85 3.94-1a7.4 7.4 0 0 0 3.55.9H8a7.44 7.44 0 0 0 5.29-12.72zM8 14.12a6.12 6.12 0 0 1-3.15-.87l-.22-.13-2.34.61.62-2.28-.14-.23a6.18 6.18 0 0 1 9.6-7.65 6.12 6.12 0 0 1 1.81 4.37A6.19 6.19 0 0 1 8 14.12z"/></svg>
  </a>

  <?php
}, 100);


// 
add_filter( 'woocommerce_product_review_list_args', 'mz_reviews_latest_first' );
function mz_reviews_latest_first( $args ) {
    $args['reverse_top_level'] = true;
    return $args;
}


add_action('wp_enqueue_scripts', 'mz_force_cart_fragments_script', 20);
function mz_force_cart_fragments_script() {
    if (class_exists('WooCommerce')) {
        wp_enqueue_script('wc-cart-fragments');
    }
}

add_filter('woocommerce_add_to_cart_fragments', 'mz_refresh_cart_count_fragment');
function mz_refresh_cart_count_fragment($fragments) {
    if (!function_exists('WC') || !WC()->cart) {
        return $fragments;
    }

    $cart_count = WC()->cart->get_cart_contents_count();

    ob_start();
    ?>
    <span data-mz-cart-count
        class="mz-absolute -mz-top-1 -mz-right-1 mz-min-w-[18px] mz-h-[18px] mz-rounded-full mz-bg-brand-accent mz-text-white mz-text-[11px] mz-leading-[18px] mz-text-center <?php echo ($cart_count > 0) ? '' : 'mz-hidden'; ?>"
        aria-hidden="<?php echo ($cart_count > 0) ? 'false' : 'true'; ?>">
        <?php echo esc_html($cart_count); ?>
    </span>
    <?php

    $fragments['span[data-mz-cart-count]'] = ob_get_clean();

    return $fragments;
}



add_action('wp_footer', 'mz_update_cart_count_after_ajax', 999);
function mz_update_cart_count_after_ajax() {
    if (is_admin()) return;
    ?>
    <script>
    jQuery(function($){
        $(document.body).on('added_to_cart', function(event, fragments){
            if (fragments && fragments['span[data-mz-cart-count]']) {
                $('span[data-mz-cart-count]').replaceWith(fragments['span[data-mz-cart-count]']);
            } else {
                $.get('<?php echo esc_url(admin_url('admin-ajax.php?action=mz_get_cart_count')); ?>', function(response){
                    if (response && typeof response.count !== 'undefined') {
                        var $count = $('span[data-mz-cart-count]');
                        $count.text(response.count);

                        if (parseInt(response.count, 10) > 0) {
                            $count.removeClass('mz-hidden').attr('aria-hidden', 'false');
                        } else {
                            $count.addClass('mz-hidden').attr('aria-hidden', 'true');
                        }
                    }
                });
            }
        });
    });
    </script>
    <?php
}


add_action('wp_ajax_mz_get_cart_count', 'mz_get_cart_count');
add_action('wp_ajax_nopriv_mz_get_cart_count', 'mz_get_cart_count');

function mz_get_cart_count() {
    wp_send_json(array(
        'count' => (function_exists('WC') && WC()->cart) ? WC()->cart->get_cart_contents_count() : 0,
    ));
}