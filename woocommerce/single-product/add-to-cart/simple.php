<?php
defined('ABSPATH') || exit;

global $product;

if (!$product || !$product->is_purchasable()) return;

echo wc_get_stock_html($product);

if ($product->is_in_stock()) : ?>
  <?php do_action('woocommerce_before_add_to_cart_form'); ?>

  <form class="cart mz-w-full"
        action="<?php echo esc_url(apply_filters('woocommerce_add_to_cart_form_action', $product->get_permalink())); ?>"
        method="post"
        enctype='multipart/form-data'>

    <?php do_action('woocommerce_before_add_to_cart_button'); ?>

    <div class="mz-grid mz-place-items-center mz-items-center mz-gap-4 lg:mz-flex lg:mz-gap-5 lg:mz-flex-nowrap">

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

      <!-- ✅ Required for Woo add-to-cart -->
      <input type="hidden" name="add-to-cart" value="<?php echo esc_attr($product->get_id()); ?>" />

      <!-- ✅ Buy Now (adds to cart + redirects to checkout via filter) -->
      <button type="submit"
              name="mz_buy_now"
              value="1"
              class="button button-buy-now alt mz-h-12 mz-px-10 lg:mz-mt-0 mz-flex mz-max-w-[250px] mz-w-full mz-justify-center mz-bg-brand-accent hover:mz-bg-brand-primary mz-text-white mz-font-semibold mz-rounded-md">
        Buy now
      </button>

    </div>

    <?php do_action('woocommerce_after_add_to_cart_button'); ?>
    <?php do_action('woocommerce_after_add_to_cart_form'); ?>
  </form>

<?php endif; ?>



<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.querySelector('form.cart');
  if (!form) return;

  let alreadySubmitted = false;

  form.addEventListener('submit', function (e) {
    const submitter = e.submitter;
    const isBuyNow = submitter && submitter.name === 'mz_buy_now' && submitter.value === '1';
    if (!isBuyNow) return;

    if (alreadySubmitted) return;

    e.preventDefault();

    const qtyEl = form.querySelector('input.qty');
    const qty = qtyEl ? (parseInt(qtyEl.value, 10) || 1) : 1;

    const price = <?php echo json_encode((float) $product->get_price()); ?>;
    const productId = <?php echo json_encode((string) $product->get_id()); ?>;
    const productName = <?php echo json_encode((string) $product->get_name()); ?>;

    window.dataLayer = window.dataLayer || [];

    window.dataLayer.push({ ecommerce: null });

    function doRealSubmit(){
      if (alreadySubmitted) return;
      alreadySubmitted = true;

      // ✅ IMPORTANT: submit with the same button so mz_buy_now=1 is sent
      if (form.requestSubmit) {
        form.requestSubmit(submitter);
      } else {
        // fallback: add hidden field then normal submit
        let hidden = form.querySelector('input[name="mz_buy_now"]');
        if (!hidden) {
          hidden = document.createElement('input');
          hidden.type = 'hidden';
          hidden.name = 'mz_buy_now';
          form.appendChild(hidden);
        }
        hidden.value = '1';
        form.submit();
      }
    }

    window.dataLayer.push({
      event: 'add_to_cart',
      ecommerce: {
        currency: 'INR',
        value: (price * qty),
        items: [{
          item_id: productId,
          item_name: productName,
          price: price,
          quantity: qty
        }]
      },
      eventCallback: doRealSubmit,
      eventTimeout: 1500
    });

    setTimeout(doRealSubmit, 800);

  }, true);
});
</script>