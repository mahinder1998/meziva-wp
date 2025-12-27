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
 * Safe ACF getter
 */
function mz_get_acf($key, $post_id = null) {
  if (function_exists('get_field')) return get_field($key, $post_id);
  $post_id = $post_id ?: get_the_ID();
  return get_post_meta($post_id, $key, true);
}

/**
 * Custom footer include
 */
add_action('astra_footer_before', function () {
  if (is_admin()) return;

  $file = get_stylesheet_directory() . '/template-parts/meziva-footer.php';
  if (file_exists($file)) include $file;
}, 1);
