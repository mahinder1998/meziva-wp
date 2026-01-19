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

// =========================
// ANNOUNCEMENT (ACF on Home)
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
// =========================
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

/**
 * ✅ IMPORTANT: Force "menu-link" class on ALL WP menu anchors
 * (So your CSS selectors work, and active class works cleanly)
 */
$mz_menu_link_filter = function($atts, $item, $args, $depth){
  $cls = isset($atts['class']) ? $atts['class'] : '';
  if (strpos(' '.$cls.' ', ' menu-link ') === false) {
    $cls = trim($cls . ' menu-link');
  }
  $atts['class'] = $cls;
  return $atts;
};
add_filter('nav_menu_link_attributes', $mz_menu_link_filter, 10, 4);
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

      /* ✅ Dynamic scroll offset variable (JS updates it) */
      --mz-scroll-offset: 96px;
    }

    /* ✅ Fix #2: section cut issue (apply on HTML not BODY) */
    html{
      scroll-behavior: smooth;
      scroll-padding-top: var(--mz-scroll-offset);
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

    /* Smooth sticky on scroll */
    [data-meziva-header].is-scrolled{
      box-shadow: 0 10px 30px rgba(0,0,0,.08);
      background-color: var(--mz-header-bg);
    }

    /* Optional mini shrink feel (premium) */
    [data-meziva-header].is-scrolled .mz-h-16{ height: 56px; }
    @media (min-width: 768px){
      [data-meziva-header].is-scrolled .md\:mz-h-20{ height: 68px; }
    }

    /* Desktop + Mobile nav links */
    .meziva-desktop-menu a.menu-link,
    .meziva-mobile-menu a.menu-link {
      color: var(--mz-nav-color) !important;
      transition: color .25s ease;
      font-size: 14px;
    }
    .meziva-desktop-menu a.menu-link:hover,
    .meziva-mobile-menu a.menu-link:hover {
      color: var(--mz-nav-hover) !important;
    }

    /* ✅ Fix #3: Active nav (scroll spy) */
    .meziva-desktop-menu a.menu-link.is-active,
    .meziva-mobile-menu a.menu-link.is-active{
      color: var(--mz-nav-hover) !important;
    }

    /* Right icons */
    .header-right-col a,
    .header-right-col button {
      color: var(--mz-nav-color);
      transition: color .25s ease, background-color .25s ease;
    }
    .header-right-col a:hover,
    .header-right-col button:hover {
      color: var(--mz-nav-hover);
    }

    @media (min-width: 768px) {
      .meziva-desktop-menu a.menu-link,
      .meziva-mobile-menu a.menu-link {
        font-size: 16px;
        font-weight: normal;
      }
    }

    /* Drawer open state */
    body.mz-menu-open { overflow: hidden; touch-action: none; }
    [data-meziva-overlay].is-open { display: block !important; }
    [data-meziva-drawer].is-open { transform: translateX(0) !important; }
  </style>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

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

<header
  data-meziva-header
  class="mz-backdrop-blur"
>
  <div class="mz-max-w-[1290px] mz-mx-auto mz-px-4 xl:mz-px-0">
    <div class="mz-h-16 md:mz-h-20 mz-grid mz-grid-cols-12 mz-items-center mz-transition-all mz-duration-300">

      <!-- LOGO -->
      <div class="mz-col-span-6 md:mz-col-span-3 mz-flex mz-items-center">
        <a href="<?php echo esc_url($home); ?>" class="mz-flex mz-items-center mz-gap-2" aria-label="Meziva Home">
          <?php if ( $logo_url ) : ?>
            <img
              src="<?php echo esc_url($logo_url); ?>"
              <?php if ($logo_srcset): ?> srcset="<?php echo esc_attr($logo_srcset); ?>" <?php endif; ?>
              sizes="<?php echo esc_attr($logo_sizes); ?>"
              alt="<?php echo esc_attr($logo_alt); ?>"
              width="160"
              height="48"
              class="mz-h-[34px] md:mz-h-[40px] mz-w-auto"
              loading="eager"
              decoding="async"
              fetchpriority="high"
            />
          <?php else : ?>
            <span class="mz-font-bold mz-tracking-wide">
              <svg id="Layer_1" data-name="Layer 1" width="140px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 308.66 63.4"> <defs> <style> .cls-1 { fill: #9c4a64; } .cls-2 { fill: #c9a26b; } </style> </defs> <g id="Layer_1-2" data-name="Layer 1"> <path class="cls-1" d="M11.79.05h8.94l16.8,40.29L55.1.05h8.94l10.63,63.35h-12.53l-5.7-39.04-17.01,39.04h-4.19L19,24.36l-6.47,39.04H0L11.79.05Z"/> <path class="cls-1" d="M117.03,10.71h-22.8v14.16h21.85v10.63h-21.85v17.28h22.8v10.63h-35.15V.05h35.15v10.66Z"/> <path class="cls-2" d="M144.93,52.77h29.24v10.63h-49.79l30.79-52.72h-25.95V.05h46.46l-30.79,52.72h.03Z"/> <path class="cls-2" d="M195.28.05v63.35h-12.35V.05h12.35Z"/> <path class="cls-2" d="M216.18.05l16.92,42.54L250.02.05h13.48l-26.78,63.35h-7.42L202.7.05s13.48,0,13.48,0Z"/> <path class="cls-2" d="M295.18,63.35l-16.92-42.54-16.92,42.54h-13.48L274.64,0h7.42l26.6,63.35h-13.48Z"/> </g> </svg>
            </span>
          <?php endif; ?>
        </a>
      </div>

      <!-- DESKTOP MENU -->
      <nav class="mz-hidden md:mz-flex md:mz-col-span-6 mz-justify-center" aria-label="Primary navigation">
        <?php
          wp_nav_menu([
            'theme_location' => 'meziva_primary',
            'container'      => false,
            'fallback_cb'    => '__return_empty_string',
            'menu_class'     => 'meziva-desktop-menu mz-flex mz-items-center mz-gap-6 xl:mz-gap-10 mz-text-[15px] mz-font-medium',
            'depth'          => 2,
          ]);
        ?>
      </nav>

      <!-- RIGHT -->
      <div class="mz-col-span-6 md:mz-col-span-3 mz-flex mz-items-center mz-justify-end mz-gap-2 xl:mz-gap-3 header-right-col">
        <!-- Cart -->
        <a href="<?php echo esc_url($cart_url); ?>"
          class="mz-relative mz-h-9 mz-w-9 mz-rounded-full mz-flex mz-items-center mz-justify-center hover:mz-bg-black/5 mz-transition"
          aria-label="Cart">
          <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 4h1.5L9 16m0 0h8m-8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm-8.5-3h9.25L19 7H7.312"/>
          </svg>

          <span
            data-mz-cart-count
            class="mz-absolute -mz-top-1 -mz-right-1 mz-min-w-[18px] mz-h-[18px] mz-rounded-full mz-bg-brand-accent mz-text-white mz-text-[11px] mz-leading-[18px] mz-text-center <?php echo ($cart_count > 0) ? '' : 'mz-hidden'; ?>"
            aria-hidden="<?php echo ($cart_count > 0) ? 'false' : 'true'; ?>"
          ><?php echo esc_html($cart_count); ?></span>
        </a>

        <!-- Account -->
        <a href="<?php echo esc_url($account_url); ?>"
          class="mz-h-9 mz-w-9 mz-rounded-full mz-flex mz-items-center mz-justify-center hover:mz-bg-black/5 mz-transition"
          aria-label="Account">
          <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Zm0 0a8.949 8.949 0 0 0 4.951-1.488A3.987 3.987 0 0 0 13 16h-2a3.987 3.987 0 0 0-3.951 3.512A8.948 8.948 0 0 0 12 21Zm3-11a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
          </svg>
        </a>

        <!-- MOBILE OPEN -->
        <button
          type="button"
          data-meziva-menu-open
          class="md:mz-hidden mz-h-9 mz-w-9 mz-rounded-full mz-flex mz-items-center mz-justify-center hover:mz-bg-black/5 mz-transition"
          aria-label="Open menu"
          aria-expanded="false"
          aria-controls="meziva-mobile-drawer"
        >
          <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24">
            <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M5 7h14M5 12h14M5 17h14"/>
          </svg>
        </button>
      </div>

    </div>
  </div>

  <!-- Overlay -->
  <div data-meziva-overlay class="mz-hidden mz-fixed mz-inset-0 mz-bg-black/60 mz-z-[998]"></div>

  <!-- Drawer -->
  <aside
    id="meziva-mobile-drawer"
    data-meziva-drawer
    class="mz-fixed mz-top-0 mz-right-0 mz-h-screen mz-w-[88%] mz-max-w-[360px]
           mz-bg-white mz-text-text-heading mz-z-[999]
           mz-translate-x-full mz-transition-transform mz-duration-300 mz-ease-out"
    aria-hidden="true"
  >
    <div class="mz-h-14 mz-px-4 mz-flex mz-items-center mz-justify-end mz-border-b mz-border-black/5">
      <button
        type="button"
        data-meziva-menu-close
        class="mz-h-9 mz-w-9 mz-rounded-full hover:mz-bg-black/5 mz-transition mz-flex mz-items-center mz-justify-center"
        aria-label="Close menu"
      >
        <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24">
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M18 18 6 6"/>
        </svg>
      </button>
    </div>

    <div class="mz-px-5 mz-py-4 mz-overflow-y-auto mz-h-[calc(100vh-56px)]">
      <nav class="mz-font-body" aria-label="Mobile navigation">
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

<script>
  (function(){
    const header  = document.querySelector('[data-meziva-header]');
    const annBar  = document.querySelector('[data-meziva-announcement]');
    const openBtn = document.querySelector('[data-meziva-menu-open]');
    const closeBtn= document.querySelector('[data-meziva-menu-close]');
    const overlay = document.querySelector('[data-meziva-overlay]');
    const drawer  = document.querySelector('[data-meziva-drawer]');

    // ✅ Fix #2: Dynamic offset so section never cuts (header + announcement)
    function updateScrollOffset(){
      const h = header ? header.getBoundingClientRect().height : 0;
      const a = annBar ? annBar.getBoundingClientRect().height : 0;
      const offset = Math.ceil(h + a + 12); // +12px extra breathing space
      document.documentElement.style.setProperty('--mz-scroll-offset', offset + 'px');
    }

    // Sticky smooth shadow on scroll
    function onScroll(){
      if(!header) return;
      if(window.scrollY > 10) header.classList.add('is-scrolled');
      else header.classList.remove('is-scrolled');
      updateScrollOffset(); // because header shrinks
    }

    updateScrollOffset();
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });
    window.addEventListener('resize', updateScrollOffset);

    // Drawer open/close
    if(!openBtn || !closeBtn || !overlay || !drawer) return;

    function openMenu(){
      document.body.classList.add('mz-menu-open');
      overlay.classList.remove('mz-hidden');
      overlay.classList.add('is-open');
      drawer.classList.add('is-open');
      drawer.setAttribute('aria-hidden','false');
      openBtn.setAttribute('aria-expanded','true');
    }

    function closeMenu(){
      document.body.classList.remove('mz-menu-open');
      overlay.classList.remove('is-open');
      overlay.classList.add('mz-hidden');
      drawer.classList.remove('is-open');
      drawer.setAttribute('aria-hidden','true');
      openBtn.setAttribute('aria-expanded','false');
    }

    openBtn.addEventListener('click', openMenu);
    closeBtn.addEventListener('click', closeMenu);
    overlay.addEventListener('click', closeMenu);

    document.addEventListener('keydown', function(e){
      if(e.key === 'Escape' && drawer.classList.contains('is-open')) closeMenu();
    });

    // ✅ Fix #1: Mobile menu link click => close drawer
    drawer.addEventListener('click', function(e){
      const a = e.target.closest('a');
      if(!a) return;
      const href = (a.getAttribute('href') || '').trim();
      if(!href || href === '#') return;
      closeMenu();
    });

    // ✅ Fix #3: Scroll spy active nav (works with full URLs + hash)
    const navLinks = document.querySelectorAll('.meziva-desktop-menu a.menu-link, .meziva-mobile-menu a.menu-link');
    if(!navLinks.length) return;

    // map: sectionId -> [links]
    const map = {};
    const sections = [];

    navLinks.forEach(link=>{
      const href = link.getAttribute('href') || '';
      let hash = '';

      // supports "#id" AND "https://site/#id"
      try{
        const u = new URL(href, window.location.href);
        hash = u.hash || '';
      } catch(err){
        // fallback
        if(href.indexOf('#') !== -1) hash = '#' + href.split('#').pop();
      }

      if(!hash || hash === '#') return;

      const id = hash.replace('#','').trim();
      if(!id) return;

      const section = document.getElementById(id);
      if(!section) return;

      if(!map[id]) map[id] = [];
      map[id].push(link);

      if(!sections.includes(section)) sections.push(section);
    });

    if(!sections.length) return;

    function setActive(id){
      // remove all
      navLinks.forEach(l => l.classList.remove('is-active'));
      // add only current
      if(map[id]){
        map[id].forEach(l => l.classList.add('is-active'));
      }
    }

    // set active on load if hash exists
    if(window.location.hash){
      const id = window.location.hash.replace('#','');
      if(id) setActive(id);
    }

    const io = new IntersectionObserver((entries)=>{
      // pick most visible intersecting section
      let best = null;
      entries.forEach(entry=>{
        if(entry.isIntersecting){
          if(!best || entry.intersectionRatio > best.intersectionRatio) best = entry;
        }
      });
      if(best && best.target && best.target.id){
        setActive(best.target.id);
      }
    },{
      root: null,
      // center zone detection (best for long sections)
      rootMargin: '-45% 0px -45% 0px',
      threshold: [0, 0.2, 0.35, 0.5, 0.65, 0.8, 1]
    });

    sections.forEach(sec => io.observe(sec));

    // if user clicks a nav link, highlight immediately
    navLinks.forEach(link=>{
      link.addEventListener('click', ()=>{
        const href = link.getAttribute('href') || '';
        try{
          const u = new URL(href, window.location.href);
          if(u.hash){
            const id = u.hash.replace('#','');
            if(id) setActive(id);
          }
        } catch(err){}
      });
    });

    window.addEventListener('hashchange', ()=>{
      const id = (window.location.hash || '').replace('#','');
      if(id) setActive(id);
    });

  })();
</script>

<?php
// ✅ Clean up filter so it won't affect other templates unexpectedly
remove_filter('nav_menu_link_attributes', $mz_menu_link_filter, 10);
?>
      