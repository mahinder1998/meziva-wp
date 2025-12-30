<?php
if ( ! defined('ABSPATH') ) exit;

$home     = home_url('/');
$front_id = (int) get_option('page_on_front');



// =========================
// LOGO (ACF on Home)
// field name: logo_url
// =========================
$logo_url = '';
$logo_alt = 'Meziva Beauty';

if ( $front_id && function_exists('get_field') ) {
  $logo = get_field('logo_url', $front_id);

  if ( is_array($logo) && !empty($logo['url']) ) {
    $logo_url = $logo['url'];
    $logo_alt = !empty($logo['alt']) ? $logo['alt'] : $logo_alt;
  } elseif ( is_numeric($logo) ) {
    $logo_url = wp_get_attachment_image_url((int)$logo, 'full') ?: '';
  }
} 

// =========================
// ANNOUNCEMENT (ACF on Home)
// (same as your previous)
// =========================
$ann_enabled   = (bool) mz_get_acf('announcement_enable', $front_id, false);
$ann_text      = (string) mz_get_acf('announcement_text', $front_id, '');
$ann_code      = (string) mz_get_acf('announcement_code', $front_id, '');
$ann_link_text = (string) mz_get_acf('announcement_link_text', $front_id, '');
$ann_link_url  = (string) mz_get_acf('announcement_link_url', $front_id, '');
$ann_bg        = (string) mz_get_acf('announcement_bg', $front_id, '');
$ann_text_col  = (string) mz_get_acf('announcement_text_color', $front_id, '');

$ann_style = '';
if ( $ann_bg )       $ann_style .= 'background-color:' . esc_attr($ann_bg) . ';';
if ( $ann_text_col ) $ann_style .= 'color:' . esc_attr($ann_text_col) . ';';

// =========================
// HEADER COLORS (ACF on Home)
// IMPORTANT: field names MUST match ACF "Field Name"
// Try these (because tumhare group me kabhi kabhi name mismatch hota hai):
// header_bg_color OR header_bg
// nav_link_color OR nav_color
// nav_hover_color OR nav_link_hover_color
// =========================
$header_bg       = mz_get_acf('header_bg_color', $front_id, '');
if (!$header_bg) $header_bg = mz_get_acf('header_bg', $front_id, '#ffffff');

$nav_color       = mz_get_acf('nav_link_color', $front_id, '');
if (!$nav_color) $nav_color = mz_get_acf('nav_color', $front_id, '#2B1C23');

$nav_hover_color = mz_get_acf('nav_hover_color', $front_id, '');
if (!$nav_hover_color) $nav_hover_color = mz_get_acf('nav_link_hover_color', $front_id, '#9B4A6A');

// Woo URLs
$cart_url = function_exists('wc_get_checkout_url') ? wc_get_checkout_url() : home_url('/checkout');

// if (function_exists('is_product') && is_product() && function_exists('wc_get_checkout_url')) {
//   $cart_url = wc_get_checkout_url();
// }


$account_url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('myaccount') : home_url('/my-account');

$cart_count = 0;
if ( function_exists('WC') && WC() && isset(WC()->cart) && WC()->cart ) {
  $cart_count = (int) WC()->cart->get_cart_contents_count();
}
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php wp_head(); ?>

  <style>
    :root{
      --mz-header-bg: <?php echo esc_attr($header_bg); ?>;
      --mz-nav-color: <?php echo esc_attr($nav_color); ?>;
      --mz-nav-hover: <?php echo esc_attr($nav_hover_color); ?>;
    }

    /* Force header background from ACF */
    [data-meziva-header] { background-color: var(--mz-header-bg); }

    /* Desktop + Mobile nav links (force because Tailwind classes can override) */
    .meziva-desktop-menu a.menu-link,
    .meziva-mobile-menu a.menu-link 
    {
      color: var(--mz-nav-color) !important;
      transition: color .25s ease;
      font-size:14px;
    }
    .meziva-desktop-menu a.menu-link:hover,
    .meziva-mobile-menu a.menu-link:hover {
      color: var(--mz-nav-hover) !important;
    }
    .header-right-col a:hover {
      color: var(--mz-nav-hover) !important;
    }
    @media (min-width: 768px) {
      .meziva-desktop-menu a.menu-link,
      .meziva-mobile-menu a.menu-link 
      {
        color: var(--mz-nav-color) !important;
        transition: color .25s ease;
        font-size:16px;
        font-weight:normal;
      }
    }

  </style>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<?php if ( $ann_enabled && ( $ann_text || $ann_code || ($ann_link_text && $ann_link_url) ) ) : ?>
  <div class="mz-w-full mz-text-center mz-text-sm mz-font-medium mz-py-2" style="<?php echo esc_attr($ann_style); ?>">
    <div class="mz-max-w-[1290px] mz-mx-auto mz-px-4 xl:mz-px-0 mz-flex mz-flex-wrap mz-items-center mz-justify-center mz-gap-x-2 mz-gap-y-1 lg:mz-py-[3px]">
      <?php if ( $ann_text ) : ?><span><?php echo esc_html($ann_text); ?></span><?php endif; ?>
      <?php if ( $ann_code ) : ?><span class="mz-font-semibold"><?php echo esc_html($ann_code); ?></span><?php endif; ?>
      <?php if ( $ann_link_text && $ann_link_url ) : ?>
        <a href="<?php echo esc_url($ann_link_url); ?>" class="mz-underline hover:mz-opacity-90 mz-transition">
          <?php echo esc_html($ann_link_text); ?>
        </a>
      <?php endif; ?>
    </div>
  </div>
<?php endif; ?>

<header
  data-meziva-header
  class="mz-sticky mz-top-0 mz-z-[999] mz-backdrop-blur mz-border-b mz-border-black/5"
>
  <div class="mz-max-w-[1290px] mz-mx-auto mz-px-4 xl:mz-px-0">
    <div class="mz-h-16 md:mz-h-20 mz-grid mz-grid-cols-12 mz-items-center">

      <!-- LOGO -->
      <div class="mz-col-span-6 md:mz-col-span-3 mz-flex mz-items-center logo-cols">
        <a href="<?php echo esc_url($home); ?>" class="mz-flex mz-items-center mz-gap-2">
          <?php if ( $logo_url ) : ?>
            <img src="<?php echo esc_url($logo_url); ?>" alt="<?php echo esc_attr($logo_alt); ?>"
                 class="mz-h-10 md:mz-h-12 mz-w-auto" loading="eager" />
          <?php else : ?>
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="713" zoomAndPan="magnify" viewBox="2.850001096725464 17.849998474121094 106.94999432563782 28.950000762939453" height="193" preserveAspectRatio="xMidYMid meet" version="1.0" style=""><defs><g></g><clipPath id="c25e23f134"><path d="M 0.164062 0 L 111.839844 0 L 111.839844 67.003906 L 0.164062 67.003906 Z M 0.164062 0 " clip-rule="nonzero"></path></clipPath><clipPath id="0c6afc8512"><path d="M 0.261719 1 L 27 1 L 27 27 L 0.261719 27 Z M 0.261719 1 " clip-rule="nonzero"></path></clipPath><clipPath id="e451b9eda5"><rect x="0" width="92" y="0" height="33"></rect></clipPath><clipPath id="0848eca801"><path d="M 1 6 L 23.515625 6 L 23.515625 31 L 1 31 Z M 1 6 " clip-rule="nonzero"></path></clipPath><clipPath id="53e929f4c1"><rect x="0" width="24" y="0" height="33"></rect></clipPath></defs><g clip-path="url(#c25e23f134)"><path fill="#ffffff" d="M 0.164062 0 L 111.839844 0 L 111.839844 67.003906 L 0.164062 67.003906 Z M 0.164062 0 " fill-opacity="1" fill-rule="nonzero"></path><path fill="#ffffff" d="M 0.164062 0 L 111.839844 0 L 111.839844 67.003906 L 0.164062 67.003906 Z M 0.164062 0 " fill-opacity="1" fill-rule="nonzero"></path></g><g transform="matrix(1, 0, 0, 1, 3, 18)"><g clip-path="url(#e451b9eda5)"><g clip-path="url(#0c6afc8512)"><g fill="#9b4a6a" fill-opacity="1"><g transform="translate(0.310776, 26.073352)"><g><path d="M 7.78125 -24.875 L 13.75 -8.90625 L 19.484375 -24.875 L 22.890625 -24.875 L 26.515625 0 L 21.765625 0 L 19.828125 -15.046875 L 19.765625 -15.046875 L 14.265625 0.328125 L 12.625 0.328125 L 7.328125 -15.046875 L 7.265625 -15.046875 L 5.109375 0 L 0.359375 0 L 4.4375 -24.875 Z M 7.78125 -24.875 "></path></g></g></g></g><g fill="#9b4a6a" fill-opacity="1"><g transform="translate(27.174684, 26.073352)"><g><path d="M 14.265625 -24.234375 L 14.265625 -20.125 L 6.453125 -20.125 L 6.453125 -14.78125 L 13.984375 -14.78125 L 13.984375 -10.671875 L 6.453125 -10.671875 L 6.453125 -4.109375 L 14.265625 -4.109375 L 14.265625 0 L 1.734375 0 L 1.734375 -24.234375 Z M 14.265625 -24.234375 "></path></g></g></g><g fill="#9b4a6a" fill-opacity="1"><g transform="translate(42.856004, 26.073352)"><g><path d="M 18.3125 -24.234375 L 7.6875 -4.109375 L 17.734375 -4.109375 L 17.734375 0 L 0.15625 0 L 10.828125 -20.125 L 1.796875 -20.125 L 1.796875 -24.234375 Z M 18.3125 -24.234375 "></path></g></g></g><g fill="#9b4a6a" fill-opacity="1"><g transform="translate(61.33297, 26.073352)"><g><path d="M 6.453125 -24.234375 L 6.453125 0 L 1.734375 0 L 1.734375 -24.234375 Z M 6.453125 -24.234375 "></path></g></g></g><g fill="#9b4a6a" fill-opacity="1"><g transform="translate(69.494977, 26.073352)"><g><path d="M 4.625 -24.234375 L 10.453125 -7.90625 L 10.515625 -7.90625 L 15.90625 -24.234375 L 20.984375 -24.234375 L 11.578125 0.640625 L 8.8125 0.640625 L -0.453125 -24.234375 Z M 4.625 -24.234375 "></path></g></g></g></g></g><g transform="matrix(1, 0, 0, 1, 86, 14)"><g clip-path="url(#53e929f4c1)"><g clip-path="url(#0848eca801)"><g fill="#9b4a6a" fill-opacity="1"><g transform="translate(22.68412, 6.692118)"><g><path d="M -4.625 24.234375 L -10.453125 7.90625 L -10.515625 7.90625 L -15.90625 24.234375 L -20.984375 24.234375 L -11.578125 -0.640625 L -8.8125 -0.640625 L 0.453125 24.234375 Z M -4.625 24.234375 "></path></g></g></g></g></g></g></svg>
          <?php endif; ?>
        </a>
      </div>

      <!-- DESKTOP MENU -->
      <nav class="mz-hidden md:mz-flex md:mz-col-span-6 mz-justify-center">
        <?php
          wp_nav_menu([
            'theme_location' => 'meziva_primary',
            'container'      => false,
            'fallback_cb'    => '__return_empty_string',
            // IMPORTANT: removed mz-text-text-heading so ACF colors can apply
            'menu_class'     => 'meziva-desktop-menu mz-flex mz-items-center mz-gap-6 xl:mz-gap-10 mz-text-[15px] mz-font-medium',
            'depth'          => 2,
          ]);
        ?>
      </nav>

      <!-- RIGHT -->
      <div class="mz-col-span-6 md:mz-col-span-3 mz-flex mz-items-center mz-justify-end mz-gap-3 xl:mz-gap-4
      header-right-col
      ">
        <a href="<?php echo esc_url($cart_url); ?>"
          class="mz-relative mz-h-8 mz-w-8 mz-rounded-full mz-flex mz-items-center mz-justify-center  mz-transition"
          aria-label="Cart">
          <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 4h1.5L9 16m0 0h8m-8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm-8.5-3h9.25L19 7H7.312"/>
          </svg>

          <!-- IMPORTANT: always render badge so JS can update -->
          <span
            data-mz-cart-count
            class="mz-absolute -mz-top-1 -mz-right-1 mz-min-w-[18px] mz-h-[18px] mz-rounded-full mz-bg-brand-accent mz-text-white mz-text-[11px] mz-leading-[18px] mz-text-center <?php echo ($cart_count > 0) ? '' : 'mz-hidden'; ?>"
            aria-hidden="<?php echo ($cart_count > 0) ? 'false' : 'true'; ?>"
          >
            <?php echo esc_html($cart_count); ?>
          </span>
        </a>

        <a href="<?php echo esc_url($account_url); ?>"
            class="mz-relative mz-h-8 mz-w-8 mz-rounded-full mz-flex mz-items-center mz-justify-center mz-transition"
           aria-label="login">
          <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Zm0 0a8.949 8.949 0 0 0 4.951-1.488A3.987 3.987 0 0 0 13 16h-2a3.987 3.987 0 0 0-3.951 3.512A8.948 8.948 0 0 0 12 21Zm3-11a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
        </svg>

        </a>

        <!-- MOBILE OPEN -->
        <button type="button" data-meziva-menu-open
          class="md:mz-hidden mz-h-8 mz-w-8 mz-rounded-full mz-flex mz-items-center mz-justify-center mz-transition"
          aria-label="Open menu">
          <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
            <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M5 7h14M5 12h14M5 17h14"/>
          </svg>
        </button>
      </div>

    </div>
  </div>

  <!-- Overlay -->
  <div data-meziva-overlay class="mz-hidden mz-fixed mz-h-screen mz-inset-0 mz-bg-black/60 mz-z-[998]"></div>

  <!-- Drawer -->
  <aside data-meziva-drawer
    class="mz-fixed mz-top-0 mz-right-0 mz-h-screen mz-w-[88%] mz-max-w-[360px]
           mz-bg-white mz-text-text-heading mz-z-[999]
           mz-translate-x-full mz-transition-transform mz-duration-300 mz-ease-out"
    aria-hidden="true">
    <div class="mz-h-4 mz-px-4 mz-flex mz-items-center mz-absolute mz-top-3 mz-right-1 mz-left-auto mz-justify-between">
      <button type="button" data-meziva-menu-close
        class="mz-h-8 mz-w-8 mz-rounded-full hover:mz-bg-black/5 mz-transition mz-flex mz-items-center mz-justify-center"
        aria-label="Close menu">
            <svg class="w-3 h-3 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24">
              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6"/>
            </svg>
      </button>
    </div>

    <div class="mz-px-5 mz-py-4 mz-overflow-y-auto mz-h-screen">
      <nav class="mz-font-body">
        <?php
          wp_nav_menu([
            'theme_location' => 'meziva_primary',
            'container'      => false,
            'fallback_cb'    => '__return_empty_string',
            'menu_class'     => 'meziva-mobile-menu mz-flex mz-flex-col mz-gap-3 mz-text-[16px] mz-font-medium',
            'depth'          => 3,
          ]);
        ?>
      </nav>
    </div>
  </aside>
</header>
 