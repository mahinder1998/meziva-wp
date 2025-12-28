    <?php
/**
 * Variable product add to cart
 * Override: yourtheme/woocommerce/single-product/add-to-cart/variable.php
 */
defined('ABSPATH') || exit;

global $product;

$attribute_keys  = array_keys($attributes);
$variations_json = wp_json_encode($available_variations);
$variations_attr = function_exists('wc_esc_json') ? wc_esc_json($variations_json) : _wp_specialchars($variations_json, ENT_QUOTES, 'UTF-8', true);

do_action('woocommerce_before_add_to_cart_form'); ?>

<form class="variations_form cart mz-variations-form"
      action="<?php echo esc_url(apply_filters('woocommerce_add_to_cart_form_action', $product->get_permalink())); ?>"
      method="post"
      enctype='multipart/form-data'
      data-product_id="<?php echo absint($product->get_id()); ?>"
      data-product_variations="<?php echo $variations_attr; ?>">

  <?php do_action('woocommerce_before_variations_form'); ?>

  <?php if (empty($available_variations) && false !== $available_variations) : ?>
    <p class="stock out-of-stock mz-text-sm mz-text-red-600">
      <?php echo esc_html(apply_filters('woocommerce_out_of_stock_message', __('This product is currently out of stock and unavailable.', 'woocommerce'))); ?>
    </p>
  <?php else : ?>

    <div class="mz-space-y-5 mz-mb-5" data-mz-variant-ui>
      <?php foreach ($attributes as $attribute_name => $options) : ?>
        <?php
          // Detect "color" attribute (taxonomy label/slug contains color/colour)
          $is_color = false;
          $attr_slug = wc_attribute_taxonomy_slug($attribute_name);
          $name_lower = strtolower(is_string($attribute_name) ? $attribute_name : '');
          $slug_lower = strtolower(is_string($attr_slug) ? $attr_slug : '');
          if (strpos($name_lower, 'color') !== false || strpos($name_lower, 'colour') !== false || strpos($slug_lower, 'color') !== false || strpos($slug_lower, 'colour') !== false) {
            $is_color = true;
          }

          // Detect "size" attribute
          $is_size = false;
          if (strpos($name_lower, 'size') !== false || strpos($slug_lower, 'size') !== false) {
            $is_size = true;
          }

          $label = wc_attribute_label($attribute_name);
          $select_id = 'mz_' . sanitize_title($attribute_name);
        ?>

        <div class="mz-variant-block" data-mz-attr="<?php echo esc_attr($attribute_name); ?>" data-mz-type="<?php echo $is_color ? 'color' : ($is_size ? 'size' : 'text'); ?>">
          <div class="mz-flex mz-items-center mz-justify-between mz-mb-2">
            <div class="mz-text-sm mz-font-semibold mz-text-gray-900">
              <?php echo esc_html($label); ?>
              <span class="mz-text-gray-600 mz-font-normal" data-mz-selected-label></span>
            </div>

            <?php if (end($attribute_keys) === $attribute_name) : ?>
              <a class="reset_variations mz-text-sm mz-font-semibold mz-text-gray-700 hover:mz-underline" href="#" style="visibility:hidden" data-mz-reset>
                <?php esc_html_e('Clear', 'woocommerce'); ?>
              </a>
            <?php endif; ?>
          </div>

          <!-- Custom UI rendered by JS here -->
          <div class="mz-flex mz-flex-wrap mz-gap-2" data-mz-options></div>

          <!-- Keep Woo dropdown hidden for compatibility -->
          <div class="mz-hidden">
            <?php
              wc_dropdown_variation_attribute_options([
                'options'   => $options,
                'attribute' => $attribute_name,
                'product'   => $product,
                'id'        => $select_id,
                'class'     => 'mz-variation-select',
              ]);
            ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <?php do_action('woocommerce_before_single_variation'); ?>

    <div class="single_variation_wrap">
      <?php
        do_action('woocommerce_single_variation');
      ?>
    </div>

    <?php do_action('woocommerce_after_single_variation'); ?>

  <?php endif; ?>

  <?php do_action('woocommerce_after_variations_form'); ?>
</form>

<?php do_action('woocommerce_after_add_to_cart_form'); ?>
