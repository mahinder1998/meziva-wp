<?php
defined('ABSPATH') || exit;

do_action('woocommerce_before_cart'); ?>

<div class="mz-max-w-[1100px] mz-mx-auto mz-px-4 md:mz-px-6 mz-py-10">
  <h1 class="mz-text-center mz-tracking-[0.25em] mz-uppercase mz-text-[22px] md:mz-text-[28px] mz-font-semibold mz-mb-10">
    CART
  </h1>
     <div id="mz-cart-notices" class="mz-mb-5">
            <?php wc_print_notices(); ?>
            </div>


  <form class="woocommerce-cart-form" action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post">
    <?php do_action('woocommerce_before_cart_table'); ?>

    

    <div class="mz-border mz-border-black/10 mz-rounded-2xl mz-overflow-hidden mz-bg-white">
      <!-- Header row desktop -->
      <div class="mz-hidden md:mz-grid mz-grid-cols-[1fr_220px_140px] mz-gap-4 mz-px-6 mz-py-4 mz-text-[12px] mz-uppercase mz-tracking-[0.18em] mz-text-black/60 mz-border-b mz-border-black/10">
        <div>PRODUCT</div>
        <div class="mz-text-center">QUANTITY</div>
        <div class="mz-text-right">TOTAL</div>
      </div>

     

      <?php
      foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
        $_product   = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
        $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);

        if (!$_product || !$_product->exists() || $cart_item['quantity'] <= 0) continue;
        if (!apply_filters('woocommerce_cart_item_visible', true, $cart_item, $cart_item_key)) continue;

        $product_permalink = apply_filters(
          'woocommerce_cart_item_permalink',
          $_product->is_visible() ? $_product->get_permalink($cart_item) : '',
          $cart_item,
          $cart_item_key
        );

        $thumbnail     = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image('woocommerce_thumbnail'), $cart_item, $cart_item_key);
        $product_name  = apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key);
        $line_subtotal = WC()->cart->get_product_subtotal($_product, $cart_item['quantity']);

        // Price logic (regular/sale + SAVE)
        $regular = $_product->get_regular_price();
        $sale    = $_product->get_sale_price();
        $has_sale = ($sale && $regular && floatval($regular) > floatval($sale));
        $save_amt = $has_sale ? (floatval($regular) - floatval($sale)) : 0;
        ?>

        <div
          class="mz-grid md:mz-grid-cols-[1fr_220px_140px] mz-gap-4 mz-p-4 md:mz-px-6 md:mz-py-6 mz-border-b mz-border-black/10 last:mz-border-b-0"
          data-mz-cart-row="<?php echo esc_attr($cart_item_key); ?>"
        >
          <!-- PRODUCT -->
          <div class="mz-flex mz-gap-4">
            <div class="mz-w-[86px] mz-h-[86px] mz-bg-black/5 mz-rounded-xl mz-overflow-hidden mz-flex mz-items-center mz-justify-center">
              <?php
              if ($product_permalink) {
                echo '<a class="mz-block" href="'.esc_url($product_permalink).'">'.$thumbnail.'</a>';
              } else {
                echo $thumbnail;
              }
              ?>
            </div>

            <div class="mz-flex-1">
              <div class="mz-flex mz-items-start mz-justify-between mz-gap-3">
                <div>
                  <div class="mz-text-[13px] mz-uppercase mz-tracking-[0.14em] mz-font-semibold mz-leading-5">
                    <?php
                    if ($product_permalink) {
                      echo '<a class="mz-text-black hover:mz-opacity-80" href="'.esc_url($product_permalink).'">'.wp_kses_post($product_name).'</a>';
                    } else {
                      echo wp_kses_post($product_name);
                    }
                    ?>
                  </div>

                  <!-- Variations -->
                  <div class="mz-mt-2 mz-text-[12px] mz-uppercase mz-tracking-[0.14em] mz-text-black/50">
                    <?php echo wp_kses_post(wc_get_formatted_cart_item_data($cart_item)); ?>
                  </div>

                  <!-- Price (sale style) -->
                  <?php if ($has_sale): ?>
                    <div class="mz-mt-2 mz-text-[13px] mz-text-black/70">
                      <span class="mz-line-through mz-text-black/35 mz-mr-2"><?php echo wc_price($regular); ?></span>
                      <span class="mz-font-semibold"><?php echo wc_price($sale); ?></span>
                    </div>
                    <div class="mz-mt-2">
                      <span class="mz-inline-flex mz-items-center mz-border mz-border-black/20 mz-rounded-md mz-px-2 mz-py-1 mz-text-[12px] mz-text-black/70">
                        SAVE <?php echo wc_price($save_amt); ?>
                      </span>
                    </div>
                  <?php else: ?>
                    <div class="mz-mt-2 mz-text-[13px] mz-text-black/70">
                      <?php echo wp_kses_post(WC()->cart->get_product_price($_product)); ?>
                    </div>
                  <?php endif; ?>
                </div>
              </div>

              <!-- Mobile remove link -->
              <div class="md:mz-hidden mz-mt-3">
                <?php
                echo apply_filters(
                  'woocommerce_cart_item_remove_link',
                  sprintf(
                    '<a href="%s" class="mz-inline-block mz-text-[13px] mz-underline mz-underline-offset-4 mz-text-black/70 hover:mz-text-black" aria-label="%s" data-product_id="%s" data-product_sku="%s">Remove</a>',
                    esc_url(wc_get_cart_remove_url($cart_item_key)),
                    esc_attr__('Remove this item', 'woocommerce'),
                    esc_attr($product_id),
                    esc_attr($_product->get_sku())
                  ),
                  $cart_item_key
                );
                ?>
              </div>
            </div>
          </div>

          <!-- QUANTITY -->
          <div class="mz-flex mz-items-center md:mz-justify-center mz-gap-4">
            <div class="mz-inline-flex mz-items-center mz-border mz-border-black/15 mz-rounded-lg mz-overflow-hidden">
              <button type="button" data-mz-qty-btn="minus" class="mz-w-10 mz-h-10 mz-flex mz-items-center mz-justify-center mz-text-[18px] hover:mz-bg-black/5">−</button>

              <input
                data-mz-qty-input
                type="number"
                class="mz-w-14 mz-h-10 mz-text-center mz-outline-none mz-border-x mz-border-black/15 mz-text-[14px]"
                value="<?php echo esc_attr($cart_item['quantity']); ?>"
                min="0"
                step="1"
                inputmode="numeric"
              />

              <button type="button" data-mz-qty-btn="plus" class="mz-w-10 mz-h-10 mz-flex mz-items-center mz-justify-center mz-text-[18px] hover:mz-bg-black/5">+</button>
            </div>

            <!-- Desktop remove link -->
            <div class="mz-hidden md:mz-block">
              <?php
              echo apply_filters(
                'woocommerce_cart_item_remove_link',
                sprintf(
                  '<a href="%s" class="mz-text-[13px] mz-underline mz-underline-offset-4 mz-text-black/70 hover:mz-text-black">Remove</a>',
                  esc_url(wc_get_cart_remove_url($cart_item_key))
                ),
                $cart_item_key
              );
              ?>
            </div>
          </div>

          <!-- TOTAL -->
          <div class="mz-flex mz-items-center md:mz-justify-end">
            <div class="mz-text-[14px] mz-font-semibold" data-mz-line-total>
              <?php echo wp_kses_post($line_subtotal); ?>
            </div>
          </div>
        </div>
      <?php } ?>
    </div>

    <?php do_action('woocommerce_cart_contents'); ?>
    <?php wp_nonce_field('woocommerce-cart', 'woocommerce-cart-nonce'); ?>

    <!-- Bottom row like your screenshot -->
    <div class="mz-flex mz-items-center mz-justify-between mz-mt-6">
      <a class="mz-text-[13px] mz-underline mz-underline-offset-4 mz-text-black/70 hover:mz-text-black" href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>">
        Continue shopping
      </a>

      <!-- Update cart removed (we are doing AJAX) -->
    </div>

    <?php do_action('woocommerce_after_cart_table'); ?>
  </form>

  <!-- Coupon left, totals right -->
  <div class="mz-grid md:mz-grid-cols-2 mz-gap-8 mz-mt-10 mz-items-start">
    <!-- Coupon -->
    <div class="mz-order-2 md:mz-order-1">
    <?php if (wc_coupons_enabled()): ?>
  <div class="mz-mt-2">
    <div class="mz-text-[13px] mz-text-black/60 mz-mb-3">Add coupons</div>

    <div class="mz-flex mz-gap-3">
      <input
        id="mz-coupon-code"
        type="text"
        class="mz-flex-1 mz-border mz-border-black/15 mz-rounded-lg mz-px-4 mz-py-3 mz-outline-none focus:mz-border-black/30"
        placeholder="Enter code"
        value=""
      />

      <button
        type="button"
        data-mz-apply-coupon
        class="mz-min-w-[140px] mz-rounded-lg mz-bg-black mz-text-white mz-px-6 mz-py-3 hover:mz-opacity-90"
      >
        Apply
      </button>
    </div>
  </div>
<?php endif; ?>

    </div>

    <!-- Totals -->
    <div class="mz-order-1 md:mz-order-2">
      <div id="mz-cart-totals">
        <?php do_action('woocommerce_cart_collaterals'); ?>
      </div>
    </div>
  </div>
</div>

<?php do_action('woocommerce_after_cart'); ?>
