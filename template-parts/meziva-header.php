<?php
$home = home_url('/');

$front_id = (int) get_option('page_on_front');
$logo_url = '';
$logo_alt = 'Meziva Beauty';

if ($front_id && function_exists('get_field')) {
  $logo = get_field('meziva_logo', $front_id);
  if (is_array($logo) && !empty($logo['url'])) {
    $logo_url = $logo['url'];
    $logo_alt = !empty($logo['alt']) ? $logo['alt'] : $logo_alt;
  } elseif (is_numeric($logo)) {
    $logo_url = wp_get_attachment_image_url((int)$logo, 'full') ?: '';
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
          <?php if (!empty($logo_url)): ?>
            <img
              src="<?php echo esc_url($logo_url); ?>"
              alt="<?php echo esc_attr($logo_alt); ?>"
              class="mz-h-8 md:mz-h-10 mz-w-auto mz-object-contain"
              loading="eager"
            />
          <?php else: ?>
            <span class="mz-font-heading mz-text-text-heading mz-text-lg md:mz-text-2xl mz-tracking-tight">
              Meziva Beauty
            </span>
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
            'menu_class'     => 'meziva-desktop-menu mz-flex mz-items-center mz-gap-8 mz-font-body mz-text-text-heading mz-text-[15px]',
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

        <a href="<?php echo esc_url(home_url('/?s=')); ?>"
           class="mz-h-10 mz-w-10 mz-rounded-full mz-flex mz-items-center mz-justify-center hover:mz-bg-black/5 mz-transition"
           aria-label="Search">🔍</a>

        <a href="<?php echo esc_url($cart_url); ?>"
           class="mz-relative mz-h-10 mz-w-10 mz-rounded-full mz-flex mz-items-center mz-justify-center hover:mz-bg-black/5 mz-transition"
           aria-label="Cart">🛒
          <?php if ($cart_count > 0): ?>
            <span class="mz-absolute -mz-top-1 -mz-right-1 mz-min-w-[18px] mz-h-[18px] mz-rounded-full mz-bg-brand-accent mz-text-white mz-text-[11px] mz-leading-[18px] mz-text-center mz-font-body">
              <?php echo esc_html($cart_count); ?>
            </span>
          <?php endif; ?>
        </a>

        <button type="button" data-meziva-menu-open
          class="md:mz-hidden mz-h-10 mz-w-10 mz-rounded-full mz-flex mz-items-center mz-justify-center hover:mz-bg-black/5 mz-transition"
          aria-label="Open menu">☰</button>
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
