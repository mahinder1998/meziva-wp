<?php
if ( ! defined('ABSPATH') ) exit;

$home     = home_url('/');
$front_id = (int) get_option('page_on_front');

// LOGO (ACF on Home)
$logo_url = '';
$logo_alt = 'Meziva Beauty';
$logo_id  = 0;

if ( $front_id && function_exists('get_field') ) {
  $logo = get_field('logo_url', $front_id);

  if ( is_array($logo) && !empty($logo['url']) ) {
    $logo_url = $logo['url'];
    $logo_alt = !empty($logo['alt']) ? $logo['alt'] : $logo_alt;
    $logo_id  = !empty($logo['ID']) ? (int)$logo['ID'] : 0;
  } elseif ( is_numeric($logo) ) {
    $logo_id  = (int)$logo;
    $logo_url = wp_get_attachment_image_url($logo_id, 'full') ?: '';
  }
}

$logo_srcset = $logo_id ? wp_get_attachment_image_srcset($logo_id, 'full') : '';
$logo_sizes  = '(max-width: 768px) 120px, 160px';

// ANNOUNCEMENT (ACF on Home)
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

// HEADER COLORS (ACF on Home)
$header_bg = mz_get_acf('header_bg_color', $front_id, '');
if (!$header_bg) $header_bg = mz_get_acf('header_bg', $front_id, '#ffffff');

$nav_color = mz_get_acf('nav_link_color', $front_id, '');
if (!$nav_color) $nav_color = mz_get_acf('nav_color', $front_id, '#2B1C23');

$nav_hover_color = mz_get_acf('nav_hover_color', $front_id, '');
if (!$nav_hover_color) $nav_hover_color = mz_get_acf('nav_link_hover_color', $front_id, '#9B4A6A');

// Woo URLs
$cart_url    = function_exists('wc_get_cart_url') ? wc_get_cart_url() : home_url('/cart');
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
      <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-TQMFKQ2K');</script>
    <!-- End Google Tag Manager -->

  <style>
    :root{
      --mz-header-bg: <?php echo esc_attr($header_bg); ?>;
      --mz-nav-color: <?php echo esc_attr($nav_color); ?>;
      --mz-nav-hover: <?php echo esc_attr($nav_hover_color); ?>;
      --mz-scroll-offset: 96px;
    }

    html{
      scroll-behavior: smooth;
      scroll-padding-top: var(--mz-scroll-offset);
    }

    header button:hover{
      background:transparent !important;
      border-color: transparent !important;
    }

    /* Header base */
    [data-meziva-header]{
      background-color: var(--mz-header-bg);
      position: sticky;
      top: 0;
      z-index: 999;
      will-change: transform;
      transition: box-shadow .25s ease, background-color .25s ease, transform .25s ease;
      border-bottom: 1px solid rgba(0,0,0,.06);
    }
    [data-meziva-header].is-scrolled{
      box-shadow: 0 10px 30px rgba(0,0,0,.08);
      background-color: var(--mz-header-bg);
    }

    /* Links */
    .meziva-desktop-menu a.menu-link,
    .meziva-mobile-menu a.menu-link{
      color: var(--mz-nav-color) !important;
      transition: color .25s ease;
    }
    .meziva-desktop-menu a.menu-link:hover,
    .meziva-mobile-menu a.menu-link:hover{
      color: var(--mz-nav-hover) !important;
    }

    /* Drawer open state */
    body.mz-menu-open { overflow: hidden; touch-action: none; }
    [data-meziva-overlay].is-open { display: block !important; }
    [data-meziva-drawer].is-open { transform: translateX(0) !important; }

    /* =========================
       DESKTOP dropdown panels
       ========================= */
    .meziva-desktop-menu > li{ position: relative; }

    .mz-dropdown-panel{
      position:absolute;
      left:50%;
      top:100%;
      transform:translateX(-50%);
      margin-top:8px;
      min-width:220px;
      background:#fff;
      border-radius:14px;
      box-shadow:0 24px 60px rgba(0,0,0,.12);
      opacity:0;
      visibility:hidden;
      pointer-events:none;
      transition: opacity .18s ease, transform .18s ease, visibility .18s ease;
      padding:8px;
      z-index:1000;
    }
    .mz-dropdown-panel::before,
    .mz-mega-panel::before{
      content:"";
      position:absolute;
      left:0;
      right:0;
      top:-14px;
      height:14px;
    }

    .mz-mega-panel{
      position:absolute;
      left:0%;
      top:100%;
      /* transform:translateX(-50%); */
      margin-top:29px;
      width:min(790px, calc(100vw - 40px));
      background:#fff;
      border-radius:16px;
      box-shadow:0 30px 80px rgba(0,0,0,.14);
      opacity:0;
      visibility:hidden;
      pointer-events:none;
      transition: opacity .18s ease, transform .18s ease, visibility .18s ease;
      padding:18px;
      z-index:1000;
    }

    .mz-dd-open > .mz-dropdown-panel,
    .mz-dd-open > .mz-mega-panel{
      opacity:1;
      visibility:visible;
      pointer-events:auto;
    }    

    div#ast-scroll-top svg {
        top: 15px;
    }

  
    /* =========================
       ✅ MOBILE ONLY styles (SCOPED)
       ========================= */
    #meziva-mobile-drawer .meziva-mobile-menu{ width:100%; }
    #meziva-mobile-drawer .meziva-mobile-menu > li{ padding: 6px 0; }

    /* parent anchor => icon right */
    #meziva-mobile-drawer .meziva-mobile-menu li.menu-item-has-children > a{
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:12px;
      width:100%;
    }

    #meziva-mobile-drawer .meziva-mobile-menu li.menu-item-has-children > a [data-mz-plus]{
      display:inline-flex;
      align-items:center;
      justify-content:center;
      width:28px;
      height:28px;
      border-radius:9999px;
      background: rgba(0,0,0,.06);
      flex: 0 0 auto;
      font-weight:700;
      line-height:1;
      user-select:none;
    }

    #meziva-mobile-drawer .mz-mobile-subwrap{
      max-height: 0;
      overflow: hidden;
      transition: max-height .30s ease;
      will-change: max-height;
    }

    #meziva-mobile-drawer .mz-mobile-submenu{
      padding-left: 14px;
      margin-top: 8px;
      display:flex;
      flex-direction:column;
      gap:8px;
    }

    #meziva-mobile-drawer .mz-mobile-submenu a{
      display:flex;
      align-items:center;
      gap:10px;
      padding:8px 10px;
      border-radius:10px;
    }

    #meziva-mobile-drawer .mz-mobile-submenu a:hover{
      background: rgba(0,0,0,.05);
    }
    #meziva-mobile-drawer  [aria-expanded="true"] + .mz-mobile-subwrap {
        max-height: 100%;
    }
  </style>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TQMFKQ2K"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

<?php if ( $ann_enabled && ( $ann_text || $ann_code || ($ann_link_text && $ann_link_url) ) ) : ?>
  <div data-meziva-announcement class="mz-w-full mz-text-center mz-text-sm mz-font-medium mz-py-2" style="<?php echo esc_attr($ann_style); ?> min-height:28px;">
    
  <div class="mz-max-w-[1290px] mz-mx-auto mz-px-4 xl:mz-px-0 mz-flex mz-flex-wrap mz-items-center mz-justify-center mz-gap-x-2 mz-gap-y-1">
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

<header data-meziva-header class="mz-backdrop-blur">
  <div class="mz-max-w-[1290px] mz-mx-auto mz-px-4 xl:mz-px-0">
    <div class="mz-grid mz-grid-cols-12 mz-items-center mz-transition-all mz-duration-300">

      <!-- MOBILE OPEN -->
      <div class="mz-col-span-3 lg:mz-hidden mz-flex mz-items-center">
        <button type="button" data-meziva-menu-open class="lg:mz-hidden mz-h-9 mz-w-9 mz-rounded-full mz-flex mz-items-center mz-justify-center mz-transition" aria-label="Open menu" aria-expanded="false" aria-controls="meziva-mobile-drawer">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18">
            <path d="M0,11.5H24v1H0v-1ZM0,4v1H24v-1H0ZM0,20H24v-1H0v1Z"/>
          </svg>
        </button>
      </div>

      <!-- LOGO -->
      <div class="mz-col-span-5 lg:mz-col-span-3 mz-flex mz-items-center md:mz-justify-center lg:mz-justify-start">
        <a href="<?php echo esc_url($home); ?>" class="mz-flex mz-items-center mz-gap-2" aria-label="Meziva Home">
          <?php if ( $logo_url ) : ?>
            <img
              src="<?php echo esc_url($logo_url); ?>"
              <?php if ($logo_srcset): ?> srcset="<?php echo esc_attr($logo_srcset); ?>" <?php endif; ?>
              sizes="<?php echo esc_attr($logo_sizes); ?>"
              alt="<?php echo esc_attr($logo_alt); ?>"
              width="160" height="48"
              class="mz-h-[34px] md:mz-h-[40px] mz-w-auto"
              loading="eager" decoding="async" fetchpriority="high"
            />
          <?php else : ?> 
             <span class="mz-font-bold mz-tracking-wide mz-w-[140px] xl:mz-w-[160px] logo-svg-wrapper">
              <!-- (your inline svg logo unchanged) -->
              <svg id="Layer_1" xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 626.59 236.11"><defs> <style> .st0 { fill: url(#linear-gradient2); } .st1 { fill: url(#linear-gradient1); } .st2 { fill: url(#linear-gradient4); } .st3 { fill: url(#linear-gradient5); } .st4 { fill: #8b384e; } .st5 { fill: url(#linear-gradient3); } .st6 { fill: #cb9853; } .st7 { fill: url(#linear-gradient); } </style> <linearGradient id="linear-gradient" x1="68.36" y1="161.01" x2="68.36" y2="123.83" gradientUnits="userSpaceOnUse"> <stop offset="0" stop-color="#a82c49"/> <stop offset="1" stop-color="#742138"/> </linearGradient> <linearGradient id="linear-gradient1" x1="84.64" y1="135.21" x2="8.98" y2="59.55" gradientUnits="userSpaceOnUse"> <stop offset=".19" stop-color="#b17231"/> <stop offset=".43" stop-color="#cca656"/> <stop offset=".7" stop-color="#b9813c"/> <stop offset=".84" stop-color="#b37534"/> <stop offset="1" stop-color="#cca656"/> </linearGradient> <linearGradient id="linear-gradient2" x1="28.88" y1="75.09" x2="99.02" y2="75.09" gradientUnits="userSpaceOnUse"> <stop offset="0" stop-color="#823046"/> <stop offset=".5" stop-color="#ba6976"/> <stop offset="1" stop-color="#711d36"/> </linearGradient> <linearGradient id="linear-gradient3" x1="94.12" y1="54.79" x2="30.07" y2="17.81" gradientUnits="userSpaceOnUse"> <stop offset="0" stop-color="#b9853c"/> <stop offset=".3" stop-color="#c39646"/> <stop offset=".61" stop-color="#cfa64f"/> <stop offset="1" stop-color="#b9853c"/> </linearGradient> <linearGradient id="linear-gradient4" x1="78.85" y1="52.66" x2="52.1" y2="25.91" gradientUnits="userSpaceOnUse"> <stop offset="0" stop-color="#c08937"/> <stop offset=".58" stop-color="#eac36a"/> </linearGradient> <linearGradient id="linear-gradient5" x1="86.55" y1="33.99" x2="125.06" y2="33.99" gradientUnits="userSpaceOnUse"> <stop offset=".58" stop-color="#731e3b"/> <stop offset="1" stop-color="#e6959b"/> </linearGradient> </defs> <g id="logo-shape"> <path class="st7" d="M39.01,123.83c1.07.54,2.61,1.28,4.49,2.12,2.06.92,4.01,1.78,5.94,2.43,2.03.68,5.08,1.45,9.09,1.61,8.23.52,14.6,2.57,18.72,4.25,6.17,2.52,11.5,4.69,15.57,10.2,4.67,6.33,4.96,13.38,4.88,16.58-1.4-1.94-3.39-4.16-6.2-6.04-1.6-1.08-4.54-2.75-14.48-4.88-5.41-1.16-6.01-.98-9.11-1.66-4.86-1.07-17.09-3.77-24.2-13.68-1.44-2.01-3.57-5.56-4.7-10.92Z"/> <path class="st1" d="M24.42,44.11c-.43,3.32-.77,9.55,1.94,16.45,2.4,6.11,6.03,9.81,9.24,13.09,3,3.05,8.06,7.49,15.59,11.15,4.98,1.93,11.91,6.21,19.57,12.52,9.55,7.87,18.64,18.98,16.78,31.89,0,0,0-19.83-31.91-31.19,0,0-25.09-7.97-29.84-21.44,0,0-1.65,28.46,31.81,37.13,0,0-20.45-12.5-25.72-24.17,0,0,9.81,9.71,21.17,12.81,0,0,29.12,8.26,32.84,31.6-1.43-1.06-3.62-2.49-6.51-3.69-2.26-.94-4.05-1.36-7.17-2.09-2.06-.48-4.87-1.06-8.27-1.55-5.88-.51-15.56-2.22-25.32-8.49-3.6-2.31-9.6-6.25-14.43-13.81-6-9.37-6.64-18.72-6.66-23.03-.22-3.93-.17-8.44.39-13.4,1.12-9.88,3.91-17.94,6.51-23.78Z"/> <path class="st0" d="M36.74,20.77c-.61,3.91-.83,7.18-.91,9.55-.11,3.45.07,4.93.16,5.55.45,3.16,1.48,5.64,2.3,7.25,2.04,4.05,4.94,7.19,5.5,7.77,5.07,5.28,10.48,8.54,17.74,12.84,7.66,4.54,13.46,7.95,16.27,9.6,3.46,1.87,9.29,5.66,13.94,12.55,2.39,3.54,3.83,6.94,4.72,9.66,1.2,3.06,2.65,7.95,2.57,14.04-.14,9.68-4.1,16.75-6.09,19.83.24-3.09.24-7.33-.83-12.19-1.54-7.02-4.58-12.16-6.71-15.18-2.69-3.02-6.41-6.69-11.2-10.23-1.52-1.12-5.72-4.13-11.62-7.02-.21-.1-.34-.16-.43-.21-3.65-1.79-22.08-11.34-30.03-28.5-2.63-5.68-3.1-10.95-3.1-10.95-.64-7.24,1.53-12.87,2.48-15.26,1.64-4.13,3.74-7.18,5.27-9.11Z"/> <path class="st5" d="M36.12,7.34s19.67.15,32.53,12.55c0,0,19.21,14.56,12.7,41.52-.03-4.29-.68-18.9-11.68-31.75-7.62-8.91-16.58-12.73-21.03-14.28.07,4.57.7,8.31,1.31,10.97.76,3.35,2.61,11.1,8.47,18.8,2.55,3.35,4.94,5.48,6.93,7.23,6.36,5.6,10.57,6.89,16.21,12.91,1.8,1.93,3.09,3.63,3.82,4.65,0,0-23.08-9.76-30.98-19.05-1.77-1.76-3.69-3.99-5.46-6.76-4.72-7.37-5.96-14.69-6.31-19.11.26-1.93.58-6.34-1.65-11.15-1.48-3.19-3.51-5.31-4.85-6.51Z"/> <path class="st2" d="M54.86,23.14c1.75.89,4.36,2.38,7.13,4.72,7.64,6.48,10.52,14.4,11.61,17.52.82,2.35,1.82,5.85,2.24,10.28-1.46-.7-3.54-1.86-5.7-3.72-1.73-1.48-2.91-2.89-4.04-4.36-3.83-4.97-7.64-9.91-9.85-17.49-.47-1.61-1.06-3.99-1.39-6.97Z"/> <path class="st3" d="M120.6,9.61s-1.24,3.92-13.63,11.15c0,0-26.36,10.33-19.17,37.59,0,0,5.78-8.26,15.78-16.73,0,0,6.08-4.75,8.76-17.56,0,0,3.3,8.47-5.78,18.8l-14.25,13.84s30.98-11.77,32.22-26.85c0,0,2.69-13.63-3.92-20.24Z"/> <polygon class="st6" points="96.64 6.28 97.4 8.62 99.85 8.62 97.86 10.06 98.62 12.4 96.64 10.96 94.65 12.4 95.41 10.06 93.42 8.62 95.88 8.62 96.64 6.28"/> <polygon class="st6" points="135.57 19.76 136.33 22.1 138.79 22.1 136.8 23.54 137.56 25.88 135.57 24.43 133.58 25.88 134.34 23.54 132.35 22.1 134.81 22.1 135.57 19.76"/> <polygon class="st6" points="20.63 27.87 21.38 30.2 23.84 30.2 21.85 31.65 22.61 33.99 20.63 32.54 18.64 33.99 19.4 31.65 17.41 30.2 19.87 30.2 20.63 27.87"/> <polygon class="st6" points="10.14 43.4 10.66 45.02 12.37 45.02 10.99 46.03 11.52 47.66 10.14 46.65 8.75 47.66 9.28 46.03 7.9 45.02 9.61 45.02 10.14 43.4"/> <polygon class="st6" points="15.17 105.86 15.7 107.49 17.41 107.49 16.02 108.5 16.55 110.12 15.17 109.12 13.78 110.12 14.31 108.5 12.93 107.49 14.64 107.49 15.17 105.86"/> <polygon class="st6" points="23.84 118.38 25.2 122.55 29.58 122.55 26.03 125.12 27.39 129.29 23.84 126.71 20.3 129.29 21.65 125.12 18.11 122.55 22.49 122.55 23.84 118.38"/> </g> <g id="logo"> <path class="st4" d="M116.54,60.16h14.21l26.76,64.11,27.97-64.11h14.21l16.93,100.85h-19.96l-9.07-62.14-27.06,62.14h-6.65l-25.85-62.14-10.28,62.14h-19.96l18.75-100.85Z"/> <path class="st4" d="M288.4,77.1h-36.29v22.53h34.78v16.93h-34.78v27.52h36.29v16.93h-55.94V60.16h55.94v16.93Z"/> <path class="st6" d="M421.14,60.16v100.85h-19.66V60.16h19.66Z"/> <path class="st6" d="M336.72,144.08h46.57v16.93h-79.23l48.99-83.92h-41.28v-16.93h73.94l-48.99,83.92Z"/> <path class="st6" d="M458.63,60.16l26.91,67.74,26.91-67.74h21.47l-42.64,100.85h-11.79l-42.34-100.85h21.47Z"/> <polygon class="st6" points="571.26 60.16 555.84 60.16 512.44 161.01 533.76 161.01 548.88 123.81 563.25 87.53 577.61 123.81 592.28 161.01 613.45 161.01 571.26 60.16"/> </g> <g> <path class="st6" d="M304.75,185.09h14.21c7.83,0,11.47,4.12,11.47,9.25,0,4.3-2.47,7.1-5.31,8.07,2.58.82,6.53,3.43,6.53,8.87,0,6.98-5.32,10.84-12.34,10.84h-14.55v-37.02ZM318.01,200.48c5.36,0,7.45-2.11,7.45-5.72,0-3.28-2.35-5.62-6.69-5.62h-9.21v11.34h8.45ZM309.56,218.05h9.14c4.65,0,7.88-2.18,7.88-6.8,0-4.02-2.58-6.72-8.82-6.72h-8.2v13.52Z"/> <path class="st6" d="M384.37,204.8h-18.41v13.1h20.22l-.63,4.21h-24.4v-37.02h24.11v4.21h-19.29v11.3h18.41v4.21Z"/> <path class="st6" d="M420.84,211l-3.98,11.11h-4.89l13.18-37.02h6.04l13.75,37.02h-5.23l-4.11-11.11h-14.77ZM434.35,206.8c-3.53-9.73-5.47-14.9-6.27-17.72h-.05c-.91,3.14-3.07,9.4-5.92,17.72h12.24Z"/> <path class="st6" d="M476.08,185.09v22.28c0,8.75,4.79,11.11,9.54,11.11,5.55,0,9.46-2.55,9.46-11.11v-22.28h4.94v22.02c0,12.01-6.64,15.46-14.51,15.46s-14.41-3.73-14.41-15.11v-22.37h4.98Z"/> <path class="st6" d="M539.42,189.29h-11.96v-4.21h28.87v4.21h-11.97v32.81h-4.94v-32.81Z"/> <path class="st6" d="M594.84,222.11v-13.58c0-.36-.09-.73-.25-.98l-13.04-22.46h5.61c3.37,6.01,8.73,15.56,10.42,18.81,1.6-3.2,7.12-12.83,10.62-18.81h5.25l-13.45,22.54c-.13.23-.22.43-.22.95v13.53h-4.94Z"/> </g> </svg>
            </span>  
          <?php endif; ?>
        </a>
      </div>

      <!-- DESKTOP MENU -->
      <nav class="mz-hidden lg:mz-py-7 lg:mz-flex md:mz-col-span-6 mz-justify-center" aria-label="Primary navigation" data-mz-desktop-nav>
        <?php
          if (function_exists('mz_render_primary_menu_html')) {
            mz_render_primary_menu_html('meziva_primary', false);
          } else {
            wp_nav_menu([
              'theme_location' => 'meziva_primary',
              'container'      => false,
              'fallback_cb'    => '__return_empty_string',
            ]);
          }
        ?>
      </nav>

      <!-- RIGHT -->
      <div class="mz-col-span-4 lg:mz-col-span-3 mz-flex mz-items-center mz-justify-end mz-gap-2 xl:mz-gap-3 header-right-col">
        <a href="<?php echo esc_url($account_url); ?>" class="mz-h-9 mz-w-9 mz-transition mz-flex mz-items-center mz-justify-center lg:mz-flex-col" aria-label="Account">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18">
            <path d="M12,12c3.309,0,6-2.691,6-6S15.309,0,12,0,6,2.691,6,6s2.691,6,6,6Zm0-11c2.757,0,5,2.243,5,5s-2.243,5-5,5-5-2.243-5-5S9.243,1,12,1Zm9,18v5h-1v-5c0-2.206-1.794-4-4-4H8c-2.206,0-4,1.794-4,4v5h-1v-5c0-2.757,2.243-5,5-5h8c2.757,0,5,2.243,5,5Z"/>
          </svg>
          <span class="mz-hidden lg:mz-block mz-text-xs mz-font-semibold mz-pt-1">Profile</span>
        </a>

        <a href="<?php echo esc_url($cart_url); ?>" class="mz-relative mz-h-9 mz-w-9 mz-rounded-full mz-flex mz-items-center mz-justify-center lg:mz-flex-col" aria-label="Cart">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18">
            <path d="m23.918,4H4.49l-.256-1.843c-.17-1.229-1.234-2.157-2.476-2.157H0v1h1.759c.745,0,1.383.556,1.485,1.294l2.021,14.549c.17,1.229,1.234,2.157,2.476,2.157h12.259v-1H7.741c-.745,0-1.383-.556-1.485-1.294l-.237-1.706h15.699l2.2-11ZM5.88,14l-1.25-9h18.068l-1.8,9H5.88Zm1.12,6c-1.103,0-2,.897-2,2s.897,2,2,2,2-.897,2-2-.897-2-2-2Zm0,3c-.552,0-1-.449-1-1s.448-1,1-1,1,.449,1,1-.448,1-1,1Zm10-3c-1.103,0-2,.897-2,2s.897,2,2,2,2-.897,2-2-.897-2-2-2Zm0,3c-.552,0-1-.449-1-1s.448-1,1-1,1,.449,1,1-.448,1-1,1Z"/>
          </svg>
          <span class="mz-hidden lg:mz-block mz-text-xs mz-font-semibold mz-pt-1">Bag</span>
          <span data-mz-cart-count class="mz-absolute -mz-top-1 -mz-right-1 mz-min-w-[18px] mz-h-[18px] mz-rounded-full mz-bg-brand-accent mz-text-white mz-text-[11px] mz-leading-[18px] mz-text-center <?php echo ($cart_count > 0) ? '' : 'mz-hidden'; ?>"
            aria-hidden="<?php echo ($cart_count > 0) ? 'false' : 'true'; ?>"><?php echo esc_html($cart_count); ?></span>
        </a>
      </div>

    </div>
  </div>

  <!-- Overlay -->
  <div data-meziva-overlay class="mz-hidden mz-fixed mz-inset-0 mz-bg-black/60 mz-z-[998]"></div>

  <!-- Drawer -->
  <aside id="meziva-mobile-drawer" data-meziva-drawer
    class="mz-fixed mz-top-0 mz-right-0 mz-h-screen mz-w-[88%] mz-max-w-[360px] mz-bg-white mz-text-text-heading mz-z-[999] mz-translate-x-full mz-transition-transform mz-duration-300 mz-ease-out"
    aria-hidden="true">
    <div class="mz-h-14 mz-px-4 mz-flex mz-items-center mz-justify-end mz-border-b mz-border-black/5">
      <button type="button" data-meziva-menu-close class="mz-h-9 mz-w-9 mz-rounded-full hover:mz-bg-black/5 mz-transition mz-flex mz-items-center mz-justify-center" aria-label="Close menu">
        <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24">
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M18 18 6 6"/>
        </svg>
      </button>
    </div>

    <div class="mz-px-5 mz-py-4 mz-overflow-y-auto mz-h-[calc(100vh-56px)]">
      <nav class="mz-font-body" aria-label="Mobile navigation" data-mz-mobile-nav>
        <?php
          if (function_exists('mz_render_primary_menu_html')) {
            mz_render_primary_menu_html('meziva_primary', true);
          } else {
            wp_nav_menu([
              'theme_location' => 'meziva_primary',
              'container'      => false,
              'fallback_cb'    => '__return_empty_string',
            ]);
          }
        ?>
      </nav>
    </div>
  </aside>
</header>

<script>
(function(){
  const header  = document.querySelector('[data-meziva-header]');
  const annBar  = document.querySelector('[data-meziva-announcement]');
  const openBtn = document.querySelector('[data-meziva-menu-open]');
  const closeBtn= document.querySelector('[data-meziva-menu-close]');
  const overlay = document.querySelector('[data-meziva-overlay]');
  const drawer  = document.querySelector('[data-meziva-drawer]');

  function updateScrollOffset(){
    const h = header ? header.getBoundingClientRect().height : 0;
    const a = annBar ? annBar.getBoundingClientRect().height : 0;
    const offset = Math.ceil(h + a + 12);
    document.documentElement.style.setProperty('--mz-scroll-offset', offset + 'px');
  }
  function onScroll(){
    if(!header) return;
    header.classList.toggle('is-scrolled', window.scrollY > 10);
    updateScrollOffset();
  }
  updateScrollOffset();
  onScroll();
  window.addEventListener('scroll', onScroll, { passive: true });
  window.addEventListener('resize', updateScrollOffset);

  function openMenu(){
    document.body.classList.add('mz-menu-open');
    overlay.classList.remove('mz-hidden');
    overlay.classList.add('is-open');
    drawer.classList.add('is-open');
    drawer.setAttribute('aria-hidden','false');
    openBtn && openBtn.setAttribute('aria-expanded','true');
  }
  function closeMenu(){
    document.body.classList.remove('mz-menu-open');
    overlay.classList.remove('is-open');
    overlay.classList.add('mz-hidden');
    drawer.classList.remove('is-open');
    drawer.setAttribute('aria-hidden','true');
    openBtn && openBtn.setAttribute('aria-expanded','false');
  }

  if(openBtn && closeBtn && overlay && drawer){
    openBtn.addEventListener('click', openMenu);
    closeBtn.addEventListener('click', closeMenu);
    overlay.addEventListener('click', closeMenu);
    document.addEventListener('keydown', (e)=>{ if(e.key === 'Escape' && drawer.classList.contains('is-open')) closeMenu(); });
  }

  // =========================
  // DESKTOP hover dropdowns
  // =========================
  const desktopNav = document.querySelector('[data-mz-desktop-nav]');
  if (desktopNav) {
    const items = desktopNav.querySelectorAll('li.menu-item-has-children');
    items.forEach(li => {
      let t = null;
      const open = () => { if (t) clearTimeout(t); li.classList.add('mz-dd-open'); };
      const close = () => { if (t) clearTimeout(t); t = setTimeout(() => li.classList.remove('mz-dd-open'), 140); };
      li.addEventListener('mouseenter', open);
      li.addEventListener('mouseleave', close);

      const panel = li.querySelector('.mz-mega-panel, .mz-dropdown-panel');
      if (panel) {
        panel.addEventListener('mouseenter', open);
        panel.addEventListener('mouseleave', close);
      }
    });
  }

  // =========================
  // Mega menu category hover
  // =========================
  const megaPanels = document.querySelectorAll('.mz-mega-panel');
  megaPanels.forEach(panel => {
    const catBtns = panel.querySelectorAll('[data-mz-cat]');
    const prodPanels = panel.querySelectorAll('[data-mz-products]');
    if (!catBtns.length || !prodPanels.length) return;

    const setActive = (catId) => {
      catBtns.forEach(b => b.classList.toggle('is-active', b.getAttribute('data-mz-cat') === String(catId)));
      prodPanels.forEach(p => p.classList.toggle('mz-hidden', p.getAttribute('data-mz-products') !== String(catId)));
    };

    const first = catBtns[0].getAttribute('data-mz-cat');
    if (first) setActive(first);

    catBtns.forEach(btn => {
      btn.addEventListener('mouseenter', () => setActive(btn.getAttribute('data-mz-cat')));
      btn.addEventListener('click', () => setActive(btn.getAttribute('data-mz-cat')));
    });
  });

  // =========================
  // ✅ MOBILE accordion (scoped)
  // =========================
  // =========================
// ✅ MOBILE accordion (NO HEIGHT CALC) - STABLE
// =========================
const mobileNav = document.querySelector('[data-mz-mobile-nav]');

function getTopA(li){ return li ? li.querySelector(':scope > a') : null; }
function getPlus(a){ return a ? a.querySelector('[data-mz-plus]') : null; }

function closeSiblings(li){
  const parent = li.parentElement;
  if (!parent) return;

  Array.from(parent.children).forEach(sib => {
    if (sib === li) return;
    if (!sib.classList?.contains('menu-item-has-children')) return;
    if (!sib.classList.contains('is-open')) return;

    sib.classList.remove('is-open');

    const a = getTopA(sib);
    if (a) a.setAttribute('aria-expanded','false');

    const plus = getPlus(a);
    if (plus) plus.textContent = '+';
  });
}

if (mobileNav) {
  mobileNav.addEventListener('click', function(e){
    const a = e.target.closest('a');
    if (!a) return;

    const li = a.closest('li.menu-item-has-children');
    if (!li) return;

    const topA = getTopA(li);
    if (!topA) return;

    // ✅ only top link toggles
    if (a !== topA) return;

    // toggle, don't navigate
    e.preventDefault();

    const willOpen = !li.classList.contains('is-open');

    // close siblings
    closeSiblings(li);

    // toggle current
    li.classList.toggle('is-open', willOpen);
    topA.setAttribute('aria-expanded', willOpen ? 'true' : 'false');

    const plus = getPlus(topA);
    if (plus) plus.textContent = willOpen ? '−' : '+';
  });

  // init: ensure closed icons are +
  mobileNav.querySelectorAll('li.menu-item-has-children').forEach(li => {
    const a = getTopA(li);
    const plus = getPlus(a);
    if (plus && !li.classList.contains('is-open')) plus.textContent = '+';
  });
} 

  // Close drawer only on real links (not toggle parents)
  if (drawer) {
    drawer.addEventListener('click', function(e){
      const a = e.target.closest('a');
      if(!a) return;

      const li = a.closest('li.menu-item-has-children');
      if (li) {
        const topA = getTopA(li);
        const wrap = getWrap(li);
        if (wrap && topA && a === topA) return; // toggle click => don't close
      }

      const href = (a.getAttribute('href') || '').trim();
      if(!href || href === '#') return;

      // close on navigation
      if (drawer.classList.contains('is-open')) closeMenu();
    });
  }
})();
</script>

<?php wp_footer(); ?>
</body>
</html>  