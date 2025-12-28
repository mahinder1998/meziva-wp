<?php
defined('ABSPATH') || exit;

do_action('woocommerce_cart_is_empty');

if (wc_get_page_id('shop') > 0): ?>
  <div class="mz-max-w-[980px] mz-mx-auto mz-px-4 md:mz-px-6 xl:mz-px-0 mz-min-h-full mz-my-[200px]">
    <h1 class="mz-text-center mz-text-3xl md:mz-text-3xl mz-text-brand-accent  mz-font-semibold mz-leading-tight">
      CART
    </h1>

    <div class="mz-text-center mz-bg-white">
      <p class="mz-text-text-body mz-py-6  mz-flex mz-justify-center mz-items-center mz-gap-3">
        <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="none" viewBox="0 0 24 24">
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 4h1.5L9 16m0 0h8m-8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm-8.5-3h9.25L19 7H7.312"/>
        </svg>
        <span>
          <?php esc_html_e('Your cart is currently empty.', 'woocommerce'); ?>
        </span>
      </p>

      <a class="mz-inline-block mz-bg-primary mz-bg-brand-accent mz-text-white mz-px-5 mz-py-3 mz-rounded-lg hover:mz-bg-opacity-90 mz-transition
                mz-text-sm mz-font-bold hover:mz-bg-brand-primary hover:mz-text-white
                  md:mz-min-w-[140px] md:mz-py-4 md:mz-text-center xl:mz-py-[14px] xl:mz-min-w-[200px] xl:mz-text-center xl:mz-text-[15px] xl:mz-rounded-xl
                "
         href="<?php echo esc_url(wc_get_page_permalink('/')); ?>">
        <?php esc_html_e('Return to shop', 'woocommerce'); ?>
      </a>
    </div>
  </div>
<?php endif; ?>
