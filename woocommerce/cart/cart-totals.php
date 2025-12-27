<?php
defined('ABSPATH') || exit;
?>

<div class="mz-border mz-border-black/10 mz-rounded-2xl mz-p-6 mz-bg-white">
  <h2 class="mz-text-[13px] mz-uppercase mz-tracking-[0.18em] mz-font-semibold mz-mb-5">
    CART TOTALS
  </h2>

  <div class="mz-space-y-3 mz-text-[14px]">
    <div class="mz-flex mz-justify-between">
      <span class="mz-text-black/60"><?php esc_html_e('Subtotal', 'woocommerce'); ?></span>
      <span class="mz-font-semibold"><?php wc_cart_totals_subtotal_html(); ?></span>
    </div>

    <?php foreach (WC()->cart->get_coupons() as $code => $coupon): ?>
      <div class="mz-flex mz-justify-between">
        <span class="mz-text-black/60"><?php wc_cart_totals_coupon_label($coupon); ?></span>
        <span class="mz-font-semibold"><?php wc_cart_totals_coupon_html($coupon); ?></span>
      </div>
    <?php endforeach; ?>

    <?php if (WC()->cart->needs_shipping() && WC()->cart->show_shipping()): ?>
      <div class="mz-pt-2 mz-border-t mz-border-black/10">
        <?php wc_cart_totals_shipping_html(); ?>
      </div>
    <?php endif; ?>

    <?php foreach (WC()->cart->get_fees() as $fee): ?>
      <div class="mz-flex mz-justify-between">
        <span class="mz-text-black/60"><?php echo esc_html($fee->name); ?></span>
        <span class="mz-font-semibold"><?php wc_cart_totals_fee_html($fee); ?></span>
      </div>
    <?php endforeach; ?>

    <?php if (wc_tax_enabled() && !WC()->cart->display_prices_including_tax()): ?>
      <?php if ('itemized' === get_option('woocommerce_tax_total_display')): ?>
        <?php foreach (WC()->cart->get_tax_totals() as $code => $tax): ?>
          <div class="mz-flex mz-justify-between">
            <span class="mz-text-black/60"><?php echo esc_html($tax->label); ?></span>
            <span class="mz-font-semibold"><?php echo wp_kses_post($tax->formatted_amount); ?></span>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="mz-flex mz-justify-between">
          <span class="mz-text-black/60"><?php echo esc_html(WC()->countries->tax_or_vat()); ?></span>
          <span class="mz-font-semibold"><?php wc_cart_totals_taxes_total_html(); ?></span>
        </div>
      <?php endif; ?>
    <?php endif; ?>

    <div class="mz-pt-4 mz-border-t mz-border-black/10 mz-flex mz-justify-between mz-items-center">
      <span class="mz-text-black/60"><?php esc_html_e('Total', 'woocommerce'); ?></span>
      <span class="mz-text-[16px] mz-font-bold"><?php wc_cart_totals_order_total_html(); ?></span>
    </div>
  </div>

  <p class="mz-mt-3 mz-text-[12px] mz-text-black/50">
    <?php esc_html_e('Tax included and shipping calculated at checkout', 'woocommerce'); ?>
  </p>

  <div class="mz-mt-6">
    <a href="<?php echo esc_url(wc_get_checkout_url()); ?>"
       class="mz-w-full mz-block mz-text-center mz-bg-black mz-text-white mz-py-4 mz-rounded-xl mz-uppercase mz-tracking-[0.20em] mz-text-[13px] hover:mz-opacity-90">
      <?php esc_html_e('Checkout', 'woocommerce'); ?>
    </a>
  </div>
</div>
