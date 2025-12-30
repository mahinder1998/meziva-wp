<?php
defined('ABSPATH') || exit;
global $product;
?>
<div class="woocommerce-variation-add-to-cart variations_button mz-w-full">

  <?php do_action('woocommerce_before_add_to_cart_button'); ?>

  <div class="mz-grid mz-place-items-center mz-items-center mz-gap-4 lg:mz-flex lg:mz-gap-5 lg:mz-flex-nowrap">

    <?php do_action('woocommerce_before_add_to_cart_quantity'); ?>

    <div class="mz-flex mz-max-w-[250px] lg:mz-max-w-full mz-mx-auto mz-border mz-rounded-xl mz-border-gray-200 mz-overflow-hidden">
      <button type="button" class="mz-qty-btn mz-w-12 mz-h-12 mz-flex mz-items-center mz-justify-center mz-text-lg mz-select-none mz-border-none" data-type="minus">
        <svg class="w-6 h-6 text-gray-800" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24">
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14"/>
        </svg>
      </button>

      <div class="mz-w-16 mz-h-12 mz-flex mz-items-center mz-justify-center">
        <?php
          woocommerce_quantity_input([
            'min_value'   => apply_filters('woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product),
            'max_value'   => apply_filters('woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product),
            'input_value' => isset($_POST['quantity']) ? wc_stock_amount(wp_unslash($_POST['quantity'])) : $product->get_min_purchase_quantity(),
            'classes'     => ['qty','mz-qty-input','mz-w-14','mz-text-center','mz-border-0','mz-outline-none','mz-shadow-none','mz-p-0'],
          ]);
        ?>
      </div>

      <button type="button" class="mz-qty-btn mz-w-12 mz-h-12 mz-flex mz-items-center mz-justify-center mz-text-lg mz-select-none mz-border-none" data-type="plus">
        <svg class="w-6 h-6 text-gray-800" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24">
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-7 7V5"/>
        </svg>
      </button>
    </div>

    <?php do_action('woocommerce_after_add_to_cart_quantity'); ?>

    <!-- (Optional) Normal Add to cart button -->
    <button type="submit"
      class="single_add_to_cart_button button alt mz-h-12 mz-px-10 lg:mz-mt-0 mz-flex mz-max-w-[250px] mz-w-full mz-justify-center mz-bg-green-500 hover:mz-bg-green-600 mz-text-white mz-font-semibold mz-rounded-md">
      <?php echo esc_html($product->single_add_to_cart_text()); ?>
    </button>

    <!-- ✅ Buy Now -->
    <button type="submit"
      name="mz_buy_now"
      value="1"
      class="button button-buy-now  alt mz-h-12 mz-px-10 lg:mz-mt-0 mz-flex mz-max-w-[250px] mz-w-full mz-justify-center mz-bg-brand-accent hover:mz-bg-brand-primary mz-text-white mz-font-bold mz-rounded-md">
      Buy now
    </button>

    <input type="hidden" name="add-to-cart" value="<?php echo absint($product->get_id()); ?>" />
    <input type="hidden" name="product_id" value="<?php echo absint($product->get_id()); ?>" />
    <input type="hidden" name="variation_id" class="variation_id" value="0" />

  </div>

  <?php do_action('woocommerce_after_add_to_cart_button'); ?>

</div>
