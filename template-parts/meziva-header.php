<?php
$home = home_url('/');

$front_id = (int) get_option('page_on_front');

$logo_url = '';
$logo_alt = 'Meziva Beauty';

if ($front_id && function_exists('get_field')) {
    $logo = get_field('logo_url', $front_id);

    if (is_array($logo) && !empty($logo['url'])) {
        $logo_url = $logo['url'];
        $logo_alt = !empty($logo['alt']) ? $logo['alt'] : $logo_alt;
    } elseif (is_numeric($logo)) {
        $logo_url = wp_get_attachment_image_url((int) $logo, 'full');
    }
}


$cart_url = function_exists('wc_get_cart_url') ? wc_get_cart_url() : home_url('/cart');
$account_url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('myaccount') : home_url('/my-account');

$cart_count = 0;
if (function_exists('WC') && WC() && isset(WC()->cart) && WC()->cart) {
  $cart_count = (int) WC()->cart->get_cart_contents_count();
}
?>

<header data-meziva-header class="meziva-header mz-sticky mz-top-0 mz-z-[999] mz-bg-brand-secondary/95 mz-backdrop-blur mz-border-b mz-border-black/5 mz-transition-shadow">
  <div class="mz-max-w-[1240px] mz-mx-auto mz-px-4">
    <div class="meziva-header-inner mz-h-16 md:mz-h-20 mz-grid mz-grid-cols-12 mz-items-center mz-transition-all mz-duration-300">

      <!-- LEFT: LOGO -->
      <div class="mz-col-span-6 md:mz-col-span-3 mz-flex mz-items-center mz-justify-start">
        <a href="<?php echo esc_url($home); ?>" class="mz-flex mz-items-center mz-gap-2">
          <?php if ($logo_url): ?>
  <a href="<?php echo esc_url($home); ?>">
    <img src="<?php echo esc_url($logo_url); ?>"
         alt="<?php echo esc_attr($logo_alt); ?>"  
         class="mz-h-10 md:mz-h-12 mz-w-auto">
  </a>
<?php else: ?>
  <span class="mz-text-xl mz-font-semibold">Meziva Beauty</span>
<?php endif; ?>

        </a>
      </div>

      <!-- CENTER: MENU -->
      <nav class="mz-hidden md:mz-flex md:mz-col-span-6 mz-justify-center">
        <?php
          wp_nav_menu([
            'theme_location' => 'meziva_primary',
            'container'      => false,
            'fallback_cb'    => '__return_empty_string',
            'menu_class'     => 'meziva-desktop-menu mz-flex mz-items-center mz-gap-8 mz-font-body mz-text-text-heading mz-text-[15px] mz-font-semibold',
            'depth'          => 2,
          ]);
        ?>
      </nav>

      <!-- RIGHT: ICONS -->
      <div class="mz-col-span-6 md:mz-col-span-3 mz-flex mz-items-center mz-justify-end mz-gap-1 md:mz-gap-2">
        <a href="<?php echo esc_url($account_url); ?>"
           class="mz-hidden md:mz-inline-flex mz-font-body mz-text-[14px] mz-text-text-heading hover:mz-opacity-80 mz-transition mz-px-2">
          Login / Register
        </a>

        <!--<a href="<?php echo esc_url(home_url('/?s=')); ?>"
           class="mz-h-10 mz-w-10 mz-rounded-full mz-flex mz-items-center mz-justify-center hover:mz-bg-black/5 mz-transition"
           aria-label="Search">🔍</a>-->

        <a href="<?php echo esc_url($cart_url); ?>"
           class="mz-relative mz-h-10 mz-w-10 mz-rounded-full mz-flex mz-items-center mz-justify-center hover:mz-bg-black/5 mz-transition"
           aria-label="Cart">
            <svg xmlns="http://www.w3.org/2000/svg" fill="#383838" width="22" height="22" viewBox="0 0 32 32" id="cart">
              <g transform="translate(36 -244)">
                <path fill-rule="evenodd" d="m -31.371094,255.00586 c -1.625564,0 -2.864805,1.51618 -2.589843,3.10351 l 2.27539,13.13477 c 0.27851,1.60776 1.701467,2.76172 3.314453,2.76172 h 16.767578 c 1.6146248,0 3.0129936,-1.16108 3.3105472,-2.74805 a 1.0001,1.0001 0 0 0 0.00195,-0.0137 l 2.2734375,-13.12109 v -0.002 c 0.2984384,-1.59859 -0.9594241,-3.11523 -2.5859375,-3.11523 z m 0,2 h 22.7675784 c 0.4158021,0 0.6977222,0.33936 0.6210937,0.74805 a 1.0001,1.0001 0 0 0 -0.00195,0.0137 l -2.2753911,13.12695 c -0.124292,0.65116 -0.680335,1.11133 -1.34375,1.11133 h -16.767578 c -0.666611,0 -1.23316,-0.46316 -1.34375,-1.10156 l -2.27539,-13.13672 c -0.07374,-0.42569 0.201707,-0.76172 0.61914,-0.76172 z" color="#000" style="-inkscape-stroke:none"/>
                <path fill-rule="evenodd" d="M-24.695312 246.07227a1 1 0 00-.556641.52734l-4 9a1 1 0 00.507812 1.32031 1 1 0 001.320313-.50781l4-9a1 1 0 00-.507813-1.32031 1 1 0 00-.763671-.0195zM-15.279297 246.07227a1 1 0 00-.763672.0195 1 1 0 00-.507812 1.32031l4 9a1 1 0 001.318359.50781 1 1 0 00.507813-1.32031l-4-9a1 1 0 00-.554688-.52734zM-20 259.00586a1 1 0 00-1 1v8a1 1 0 001 1 1 1 0 001-1v-8a1 1 0 00-1-1zM-24 259.00586a1 1 0 00-1 1v8a1 1 0 001 1 1 1 0 001-1v-8a1 1 0 00-1-1zM-16 259.00586a1 1 0 00-1 1v8a1 1 0 001 1 1 1 0 001-1v-8a1 1 0 00-1-1z" color="#000" style="-inkscape-stroke:none"/>
              </g>
            </svg>

           
          <?php if ($cart_count > 0): ?>
            <span class="mz-absolute -mz-top-1 -mz-right-1 mz-min-w-[18px] mz-h-[18px] mz-rounded-full mz-bg-brand-accent mz-text-white mz-text-[11px] mz-leading-[18px] mz-text-center mz-font-body">
              <?php echo esc_html($cart_count); ?>
            </span>
          <?php endif; ?>
        </a>

        <button type="button" data-meziva-menu-open
          class="md:mz-hidden mz-h-10 mz-w-10 mz-rounded-full mz-flex mz-items-center mz-justify-center hover:mz-bg-black/5 mz-transition"
          aria-label="Open menu">
            <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
              <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M5 7h14M5 12h14M5 17h14"/>
            </svg>
        </button>
      </div>

    </div>
  </div>

  <!-- Overlay -->
  <div data-meziva-overlay class="mz-hidden mz-fixed mz-inset-0 mz-bg-black/60 mz-z-[998]"></div>

  <!-- Mobile Drawer -->
  <aside data-meziva-drawer
    class="mz-fixed mz-top-0 mz-right-0 mz-h-full mz-w-[88%] mz-max-w-[360px] mz-bg-black mz-text-white mz-z-[999]
           mz-translate-x-full mz-transition-transform mz-duration-300 mz-ease-out"
    aria-hidden="true"
  >
    <div class="mz-h-16 mz-px-5 mz-flex mz-items-center mz-justify-between mz-border-b mz-border-white/10">
      <span class="mz-font-heading mz-text-base mz-tracking-wide">Menu</span>
      <button type="button" data-meziva-menu-close
        class="mz-h-10 mz-w-10 mz-rounded-full hover:mz-bg-white/10 mz-transition"
        aria-label="Close menu">✕</button>
    </div>

    <div class="mz-px-5 mz-py-4 mz-overflow-y-auto mz-h-[calc(100%-64px)]">
      <nav class="mz-font-body">
        <?php
          wp_nav_menu([
            'theme_location' => 'meziva_primary',
            'container'      => false,
            'fallback_cb'    => '__return_empty_string',
            'menu_class'     => 'meziva-mobile-menu',
            'depth'          => 3,
          ]);
        ?>
      </nav>

      <a href="<?php echo esc_url(function_exists('wc_get_checkout_url') ? wc_get_checkout_url() : home_url('/checkout')); ?>"
         class="mz-mt-6 mz-w-full mz-inline-flex mz-items-center mz-justify-center mz-px-5 mz-py-3 mz-rounded-full mz-bg-brand-accent mz-text-white mz-font-body mz-text-sm hover:mz-opacity-95 mz-transition">
        Checkout
      </a>
    </div>
  </aside>
</header>
