<?php
/**
 * Astra Child Theme functions and definitions
 */

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
    class="mz-w-full mz-text-center mz-text-sm md:mz-text-base mz-font-body"
    style="background: <?php echo esc_attr($bg); ?>; color: <?php echo esc_attr($textColor); ?>;"
  >
    <div class="mz-max-w-[1240px] mz-mx-auto mz-px-3 mz-py-2 mz-flex mz-items-center mz-justify-center mz-gap-2 mz-flex-wrap">
      <span class="mz-tracking-wide">
        <?php echo esc_html($text); ?>
        <?php if ($code): ?>
          <span class="mz-font-semibold"> <?php echo esc_html($code); ?></span>
        <?php endif; ?>
      </span>

      <?php if (!empty($linkUrl)): ?>
        <a
          href="<?php echo esc_url($linkUrl); ?>"
          class="mz-font-semibold mz-underline mz-underline-offset-4 hover:mz-opacity-90 mz-transition"
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
 * Custom footer include
 */
add_action('astra_footer_before', function () {
  if (is_admin()) return;

  $file = get_stylesheet_directory() . '/template-parts/meziva-footer.php';
  if (file_exists($file)) include $file;
}, 1);
