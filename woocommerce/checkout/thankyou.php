<?php
/**
 * Custom Thank You Page (WooCommerce)
 * Path: your-theme/woocommerce/checkout/thankyou.php
 */
defined('ABSPATH') || exit;

$order_id = absint( get_query_var('order-received') );
$order    = wc_get_order( $order_id );

if ( ! $order ) {
  echo '<div class="mz-max-w-5xl mz-mx-auto mz-px-4 mz-py-10"><p class="mz-text-center mz-text-gray-600">Order not found.</p></div>';
  return;
}

$is_failed = $order->has_status('failed');

// Helpful links (update these slugs if needed)
$shop_url   = home_url('/shop/');
$home_url   = home_url('/');
$orders_url = function_exists('wc_get_account_endpoint_url') ? wc_get_account_endpoint_url('orders') : home_url('/my-account/orders/');

// WhatsApp (change number)
$wa_number  = "91XXXXXXXXXX";
$wa_text    = rawurlencode("Hi Meziva Team, I need help with my order #" . $order->get_order_number());
$wa_link    = "https://wa.me/{$wa_number}?text={$wa_text}";

// Optional: coupon (change if you want)
$coupon_code = "SAVE10";
?>

<div class=" mz-min-h-[70vh]">
  <div class="mz-max-w-6xl mz-mx-auto mz-px-4">

    <!-- Hero -->
    <div class="mz-rounded-3xl mz-overflow-hidden mz-shadow-sm mz-border mz-border-[#F4D6E3] mz-bg-white">
      <div class="mz-bg-gradient-to-r mz-from-[#FCE7F3] mz-via-[#FFF7FB] mz-to-[#FFE4EF] mz-px-6 md:mz-px-10 mz-py-8 md:mz-py-10">
        <div class="mz-flex mz-flex-col md:mz-flex-row md:mz-items-center md:mz-justify-between mz-gap-6">

          <div>
            <?php if ( $is_failed ) : ?>
              <h1 class="mz-text-2xl md:mz-text-4xl mz-font-extrabold mz-text-red-600">Payment failed 😕</h1>
              <p class="mz-mt-2 mz-text-gray-700">Please try again or choose a different payment method.</p>
            <?php else : ?>
              <h1 class="mz-text-2xl md:mz-text-4xl mz-font-extrabold mz-text-gray-900">
                Thank you for your order <span aria-hidden="true">💖</span>
              </h1>
              <p class="mz-mt-2 mz-text-gray-700">Your Meziva glow is on the way! We’re processing your order now.</p>
            <?php endif; ?>
          </div>

          <!-- Quick badge -->
          <div class="mz-rounded-2xl mz-bg-white mz-border mz-border-[#F4D6E3] mz-shadow-sm mz-p-4 md:mz-p-5 mz-min-w-[240px]">
            <div class="mz-text-xs mz-uppercase mz-tracking-widest mz-text-gray-500">Order Number</div>
            <div class="mz-text-xl mz-font-bold mz-text-gray-900">#<?php echo esc_html( $order->get_order_number() ); ?></div>
            <div class="mz-mt-2 mz-text-sm mz-text-gray-600">
              <span class="mz-font-semibold">Total:</span> <?php echo wp_kses_post( $order->get_formatted_order_total() ); ?>
            </div>
          </div>

        </div>
      </div>

      <!-- Content -->
      <div class="mz-px-4 md:mz-px-10 mz-py-8 md:mz-py-10">

        <!-- Summary Cards -->
        <div class="mz-grid mz-grid-cols-1 sm:mz-grid-cols-2 lg:mz-grid-cols-4 mz-gap-4 md:mz-gap-5">
          <div class="mz-rounded-2xl mz-border mz-border-gray-200 mz-bg-white mz-p-5">
            <div class="mz-text-xs mz-uppercase mz-tracking-widest mz-text-gray-500">Date</div>
            <div class="mz-mt-1 mz-text-lg mz-font-bold mz-text-gray-900">
              <?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?>
            </div>
          </div>

          <div class="mz-rounded-2xl mz-border mz-border-gray-200 mz-bg-white mz-p-5">
            <div class="mz-text-xs mz-uppercase mz-tracking-widest mz-text-gray-500">Payment</div>
            <div class="mz-mt-1 mz-text-lg mz-font-bold mz-text-gray-900">
              <?php echo esc_html( $order->get_payment_method_title() ); ?>
            </div>
          </div>

          <div class="mz-rounded-2xl mz-border mz-border-gray-200 mz-bg-white mz-p-5">
            <div class="mz-text-xs mz-uppercase mz-tracking-widest mz-text-gray-500">Status</div>
            <div class="mz-mt-1 mz-text-lg mz-font-bold mz-text-gray-900">
              <?php echo esc_html( wc_get_order_status_name( $order->get_status() ) ); ?>
            </div>
          </div>

          <div class="mz-rounded-2xl mz-border mz-border-gray-200 mz-bg-white mz-p-5">
            <div class="mz-text-xs mz-uppercase mz-tracking-widest mz-text-gray-500">Email</div>
            <div class="mz-mt-1 mz-text-sm mz-font-semibold mz-text-gray-900 mz-break-words">
              <?php echo esc_html( $order->get_billing_email() ? $order->get_billing_email() : '-' ); ?>
            </div>
          </div>
        </div>

        <div class="mz-mt-6 md:mz-mt-8 mz-grid mz-grid-cols-1 lg:mz-grid-cols-3 mz-gap-5 md:mz-gap-6">

          <!-- Order Details -->
          <div class="lg:mz-col-span-2 mz-rounded-3xl mz-border mz-border-gray-200 mz-bg-white mz-p-6 md:mz-p-8">
            <div class="mz-flex mz-items-center mz-justify-between mz-gap-4">
              <h2 class="mz-text-lg md:mz-text-xl mz-font-extrabold mz-text-gray-900">Order Details</h2>
              <span class="mz-text-sm mz-text-gray-500"><?php echo esc_html( $order->get_item_count() ); ?> item(s)</span>
            </div>

            <div class="mz-mt-5 mz-space-y-4">
              <?php foreach ( $order->get_items() as $item_id => $item ) :
                $product  = $item->get_product();
                $qty      = $item->get_quantity();
                $subtotal = $order->get_formatted_line_subtotal( $item );
                $img_url  = '';

                if ( $product && $product->get_image_id() ) {
                  $img_url = wp_get_attachment_image_url( $product->get_image_id(), 'thumbnail' );
                }
              ?>
                <div class="mz-flex mz-items-start mz-gap-4 mz-rounded-2xl mz-border mz-border-gray-100 mz-p-4">
                  <div class="mz-w-14 mz-h-14 mz-rounded-xl mz-bg-[#FFF7FB] mz-border mz-border-[#F4D6E3] mz-overflow-hidden mz-flex mz-items-center mz-justify-center">
                    <?php if ( $img_url ) : ?>
                      <img src="<?php echo esc_url($img_url); ?>" alt="" class="mz-w-full mz-h-full mz-object-cover" />
                    <?php else : ?>
                      <span class="mz-text-xl">💄</span>
                    <?php endif; ?>
                  </div>

                  <div class="mz-flex-1">
                    <div class="mz-font-bold mz-text-gray-900"><?php echo esc_html( $item->get_name() ); ?></div>
                    <div class="mz-mt-1 mz-text-sm mz-text-gray-600">Qty: <span class="mz-font-semibold"><?php echo esc_html( $qty ); ?></span></div>
                    <?php if ( $item->get_meta_data() ) : ?>
                      <div class="mz-mt-2 mz-text-xs mz-text-gray-500">
                        <?php
                          // show variation/meta nicely (small)
                          foreach ( $item->get_meta_data() as $meta ) {
                            $key = wc_attribute_label( $meta->key );
                            $val = is_scalar($meta->value) ? $meta->value : '';
                            if ( $key && $val ) {
                              echo '<div>' . esc_html($key) . ': <span class="mz-font-semibold">' . esc_html($val) . '</span></div>';
                            }
                          }
                        ?>
                      </div>
                    <?php endif; ?>
                  </div>

                  <div class="mz-text-right">
                    <div class="mz-font-extrabold mz-text-gray-900"><?php echo wp_kses_post( $subtotal ); ?></div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>

            <!-- Totals -->
            <div class="mz-mt-6 mz-border-t mz-border-gray-100 mz-pt-5">
              <div class="mz-space-y-2 mz-text-sm">
                <div class="mz-flex mz-justify-between">
                  <span class="mz-text-gray-600">Subtotal</span>
                  <span class="mz-font-semibold mz-text-gray-900"><?php echo wp_kses_post( wc_price( $order->get_subtotal() ) ); ?></span>
                </div>

                <?php if ( $order->get_shipping_total() > 0 ) : ?>
                  <div class="mz-flex mz-justify-between">
                    <span class="mz-text-gray-600">Shipping</span>
                    <span class="mz-font-semibold mz-text-gray-900"><?php echo wp_kses_post( wc_price( $order->get_shipping_total() ) ); ?></span>
                  </div>
                <?php endif; ?>

                <?php if ( $order->get_discount_total() > 0 ) : ?>
                  <div class="mz-flex mz-justify-between">
                    <span class="mz-text-gray-600">Discount</span>
                    <span class="mz-font-semibold mz-text-gray-900">- <?php echo wp_kses_post( wc_price( $order->get_discount_total() ) ); ?></span>
                  </div>
                <?php endif; ?>

                <div class="mz-flex mz-justify-between mz-text-base md:mz-text-lg mz-pt-2">
                  <span class="mz-font-extrabold mz-text-gray-900">Total</span>
                  <span class="mz-font-extrabold mz-text-gray-900"><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></span>
                </div>
              </div>
            </div>
          </div>

          <!-- Side Panel -->
          <div class="mz-space-y-5">

            <!-- Address -->
            <div class="mz-rounded-3xl mz-border mz-border-gray-200 mz-bg-white mz-p-6 md:mz-p-7">
              <h3 class="mz-text-base md:mz-text-lg mz-font-extrabold mz-text-gray-900">Delivery Details</h3>
              <div class="mz-mt-4 mz-text-sm mz-text-gray-700 mz-space-y-3">
                <div>
                  <div class="mz-text-xs mz-uppercase mz-tracking-widest mz-text-gray-500">Shipping Address</div>
                  <div class="mz-mt-1 mz-font-semibold mz-text-gray-900">
                    <?php echo wp_kses_post( nl2br( $order->get_formatted_shipping_address() ?: $order->get_formatted_billing_address() ) ); ?>
                  </div>
                </div>

                <?php if ( $order->get_billing_phone() ) : ?>
                  <div>
                    <div class="mz-text-xs mz-uppercase mz-tracking-widest mz-text-gray-500">Phone</div>
                    <div class="mz-mt-1 mz-font-semibold mz-text-gray-900"><?php echo esc_html( $order->get_billing_phone() ); ?></div>
                  </div>
                <?php endif; ?>
              </div>
            </div>

           

          </div>
        </div>

        <!-- CTA Buttons -->
        <div class="mz-mt-8 md:mz-mt-10 mz-flex mz-flex-col md:mz-flex-row mz-gap-3 md:mz-gap-4 md:mz-justify-center">
          <a href="<?php echo esc_url( $shop_url ); ?>"
             class="mz-inline-flex mz-items-center mz-justify-center mz-rounded-2xl mz-bg-black mz-text-white mz-px-6 mz-py-3 mz-font-extrabold hover:mz-opacity-90">
            Continue Shopping
          </a>

          <?php if ( is_user_logged_in() ) : ?>
            <a href="<?php echo esc_url( $orders_url ); ?>"
               class="mz-inline-flex mz-items-center mz-justify-center mz-rounded-2xl mz-border mz-border-gray-300 mz-bg-white mz-px-6 mz-py-3 mz-font-extrabold hover:mz-bg-gray-50">
              View My Orders
            </a>
          <?php endif; ?>

          <a href="https://wa.me/9217912201"
             target="_blank" rel="noopener"
             class="mz-inline-flex mz-items-center mz-justify-center mz-rounded-2xl mz-border mz-border-green-600 mz-text-green-700 mz-bg-white mz-px-6 mz-py-3 mz-font-extrabold hover:mz-bg-green-50">
            WhatsApp Support
          </a>
        </div>
     
      </div>
    </div>

  </div>
</div>  