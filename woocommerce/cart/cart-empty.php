<?php
defined('ABSPATH') || exit;

do_action('woocommerce_cart_is_empty');

if (wc_get_page_id('shop') > 0): ?>
  <div class="mz-max-w-[1100px] mz-mx-auto mz-px-4 md:mz-px-6 mz-py-14">
    <h1 class="mz-text-center mz-tracking-[0.25em] mz-uppercase mz-text-[22px] md:mz-text-[28px] mz-font-semibold mz-mb-6">
      CART
    </h1>

    <div class="mz-border mz-border-black/10 mz-rounded-2xl mz-p-10 mz-text-center mz-bg-white">
      <p class="mz-text-black/60 mz-mb-6"><?php esc_html_e('Your cart is currently empty.', 'woocommerce'); ?></p>

      <a class="mz-inline-block mz-bg-black mz-text-white mz-px-8 mz-py-3 mz-rounded-xl mz-uppercase mz-tracking-[0.20em] mz-text-[13px] hover:mz-opacity-90"
         href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>">
        <?php esc_html_e('Return to shop', 'woocommerce'); ?>
      </a>
    </div>
  </div>
<?php endif; ?>
