<?php
defined('ABSPATH') || exit;

global $product;

do_action('woocommerce_before_single_product');

if (post_password_required()) {
  echo get_the_password_form();
  return;
}

$product_id = get_the_ID();

// ACF fields (support both names)
$mz_how_to_use         = mz_get_acf('mz_how_to_use', $product_id);
$mz_ingredients        = mz_get_acf('ingredients', $product_id); // ✅ your ACF name
if (!$mz_ingredients) $mz_ingredients = mz_get_acf('mz_ingredients', $product_id); // fallback
$mz_additional_details = mz_get_acf('mz_additional_details', $product_id);

$additional_rows = mz_parse_label_value_lines($mz_additional_details);

// Images
$main_id = $product->get_image_id();
$gallery_ids = $product->get_gallery_image_ids();
$image_ids = [];
if ($main_id) $image_ids[] = $main_id;
if (!empty($gallery_ids)) $image_ids = array_merge($image_ids, $gallery_ids);
$image_ids = array_values(array_unique($image_ids));

// Reviews
$review_count = $product->get_review_count();
$average = $product->get_average_rating();

// Short description -> Features
$short = $product ? $product->get_short_description() : '';

?>

<div id="product-<?php the_ID(); ?>" <?php wc_product_class('mz-w-full', $product); ?>>

  <!-- Breadcrumb -->
  <div class="mz-text-sm mz-text-gray-500 mz-mb-4">
    <?php woocommerce_breadcrumb(); ?>
  </div>

  <!-- Top Grid -->
  <div class="mz-grid mz-grid-cols-1 lg:mz-grid-cols-2 mz-gap-8 lg:mz-gap-12">

    <!-- LEFT: Custom Gallery -->
    <div class="mz-bg-white mz-border mz-border-gray-200 mz-rounded-2xl mz-p-4 md:mz-p-5 lg:mz-sticky lg:mz-top-24 mz-h-fit">

      <?php if (!empty($image_ids)) : ?>
        <?php
          $first_id = $image_ids[0];
          $first_full  = wp_get_attachment_image_url($first_id, 'full');
          $first_large = wp_get_attachment_image_url($first_id, 'large');
          $first_alt   = get_post_meta($first_id, '_wp_attachment_image_alt', true);
        ?>

        <div class="mz-relative mz-rounded-2xl mz-overflow-hidden mz-bg-gray-50 mz-border mz-border-gray-200">

          <!-- Main clickable (zoom) -->
          <a
            href="<?php echo esc_url($first_full); ?>"
            class="mz-block"
            data-fancybox="mz-product"
            data-mz-main-link
          >
            <img
              src="<?php echo esc_url($first_large); ?>"
              alt="<?php echo esc_attr($first_alt); ?>"
              class="mz-w-full mz-h-[520px] md:mz-h-[620px] mz-object-contain"
              data-mz-main-img
              loading="eager"
            />
          </a>

          <!-- Zoom icon -->
          <button
            type="button"
            class="mz-absolute mz-top-4 mz-right-4 mz-w-10 mz-h-10 mz-rounded-full mz-bg-white/90 mz-shadow-md mz-border mz-border-gray-200 mz-flex mz-items-center mz-justify-center hover:mz-scale-105 mz-transition"
            data-mz-zoom
            aria-label="Zoom image"
          >
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 3h6v6M14 10l7-7M9 21H3v-6M10 14l-7 7" />
            </svg>
          </button>

          <!-- Sale badge (optional) -->
          <?php if ($product->is_on_sale()) : ?>
            <span class="mz-absolute mz-top-4 mz-left-4 mz-bg-white mz-text-gray-900 mz-text-xs mz-font-semibold mz-px-3 mz-py-1.5 mz-rounded-full mz-shadow">
              Sale!
            </span>
          <?php endif; ?>
        </div>

        <!-- Hidden fancybox items (all images) -->
        <div class="mz-hidden">
          <?php foreach ($image_ids as $img_id) :
            $full = wp_get_attachment_image_url($img_id, 'full');
            $alt = get_post_meta($img_id, '_wp_attachment_image_alt', true);
          ?>
            <a href="<?php echo esc_url($full); ?>" data-fancybox="mz-product" data-caption="<?php echo esc_attr($alt); ?>"></a>
          <?php endforeach; ?>
        </div>

        <!-- Thumbnails slider -->
        <div class="mz-mt-4">
          <div class="mz-keen-thumbs keen-slider" data-mz-thumbs>
            <?php foreach ($image_ids as $i => $img_id) :
              $thumb = wp_get_attachment_image_url($img_id, 'woocommerce_thumbnail');
              $large = wp_get_attachment_image_url($img_id, 'large');
              $full  = wp_get_attachment_image_url($img_id, 'full');
              $alt   = get_post_meta($img_id, '_wp_attachment_image_alt', true);
            ?>
              <button
                type="button"
                class="keen-slider__slide mz-p-2"
                data-mz-thumb
                data-large="<?php echo esc_url($large); ?>"
                data-full="<?php echo esc_url($full); ?>"
                aria-label="Select image <?php echo (int)($i + 1); ?>"
              >
                <span class="mz-block mz-rounded-xl mz-border mz-border-gray-200 mz-bg-gray-50 mz-overflow-hidden mz-h-20 mz-flex mz-items-center mz-justify-center">
                  <img src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr($alt); ?>" class="mz-max-h-full mz-max-w-full mz-object-contain" loading="lazy" />
                </span>
              </button>
            <?php endforeach; ?>
          </div>
        </div>

      <?php else : ?>
        <div class="mz-text-gray-500">No product images found.</div>
      <?php endif; ?>
    </div>

    <!-- RIGHT: Summary -->
    <div class="mz-bg-white mz-border mz-border-gray-200 mz-rounded-2xl mz-p-5 md:mz-p-6">

      <div class="mz-flex mz-flex-col mz-gap-3">
        <h1 class="mz-text-2xl md:mz-text-3xl mz-font-semibold mz-text-gray-900 mz-leading-tight">
          <?php the_title(); ?>
        </h1>

        <!-- Price + Rating + Reviews scroll -->
        <div class="mz-flex mz-items-center mz-gap-4 mz-flex-wrap">
          <div class="mz-text-xl md:mz-text-2xl mz-font-semibold mz-text-gray-900">
            <?php woocommerce_template_single_price(); ?>
          </div>

          <?php if (wc_review_ratings_enabled()) : ?>
            <div class="mz-flex mz-items-center mz-gap-2">
              <?php echo wc_get_rating_html($average); ?>
              <a href="#mz-reviews" class="mz-text-sm mz-text-gray-900 mz-font-semibold hover:mz-underline">
                Customer reviews<?php echo $review_count ? ' (' . (int)$review_count . ')' : ''; ?>
              </a>
            </div>
          <?php endif; ?>
        </div>

        <div class="mz-text-gray-600 mz-leading-relaxed">
          <?php woocommerce_template_single_excerpt(); ?>
        </div>
      </div>

      <!-- Trust pills -->
      <div class="mz-mt-6 mz-grid mz-grid-cols-2 mz-gap-3">
        <?php foreach (['Fast Shipping','Easy Returns','COD Available','Secure Payments'] as $t): ?>
          <div class="mz-flex mz-items-center mz-gap-2 mz-text-sm mz-text-gray-700 mz-bg-gray-50 mz-border mz-border-gray-200 mz-rounded-xl mz-px-3 mz-py-2">
            <span class="mz-inline-block mz-w-2 mz-h-2 mz-rounded-full mz-bg-gray-900"></span>
            <span><?php echo esc_html($t); ?></span>
          </div>
        <?php endforeach; ?>
      </div>

      <!-- Add to cart -->
      <div class="mz-mt-6 mz-mz-pdp-cart">
        <?php woocommerce_template_single_add_to_cart(); ?>
      </div>

      <!-- Meta -->
      <div class="mz-mt-6 mz-text-sm mz-text-gray-500 mz-border-t mz-border-gray-200 mz-pt-4">
        <?php woocommerce_template_single_meta(); ?>
      </div>

    </div>
  </div>

  <!-- Accordion Info -->
  <div class="mz-mt-10 mz-bg-white mz-border mz-border-gray-200 mz-rounded-2xl mz-overflow-hidden">
    <?php
      $sections = [];

      if (!empty($short)) {
        $sections[] = [
          'title' => 'Features',
          'content_html' => '<div class="mz-prose mz-max-w-none mz-text-gray-700">' . wp_kses_post($short) . '</div>',
        ];
      }

      if (!empty($mz_how_to_use)) {
        $sections[] = [
          'title' => 'How to use',
          'content_html' => '<div class="mz-prose mz-max-w-none mz-text-gray-700">' . wp_kses_post($mz_how_to_use) . '</div>',
        ];
      }

      // ✅ Ingredients (now reads correct ACF field)
      if (!empty($mz_ingredients)) {
        $ing_html = $mz_ingredients;
        if ($ing_html === strip_tags($ing_html)) {
          $ing_html = nl2br(esc_html($ing_html));
        }
        $sections[] = [
          'title' => 'Ingredients',
          'content_html' => '<div class="mz-prose mz-max-w-none mz-text-gray-700">' . wp_kses_post($ing_html) . '</div>',
        ];
      }

      if (!empty($additional_rows)) {
        $rows_html = '<div class="mz-space-y-3">';
        foreach ($additional_rows as $r) {
          $label = $r['label']; $value = $r['value'];
          if ($label) {
            $rows_html .= '<div class="mz-flex mz-flex-col sm:mz-flex-row sm:mz-gap-3">';
            $rows_html .= '<div class="mz-text-gray-900 mz-font-semibold sm:mz-w-[260px]">' . esc_html($label) . '</div>';
            $rows_html .= '<div class="mz-text-gray-700">' . esc_html($value) . '</div>';
            $rows_html .= '</div>';
          } else {
            $rows_html .= '<div class="mz-text-gray-700">' . esc_html($value) . '</div>';
          }
        }
        $rows_html .= '</div>';

        $sections[] = [
          'title' => 'Additional details',
          'content_html' => $rows_html,
        ];
      }
    ?>

    <?php foreach ($sections as $index => $sec): ?>
      <div class="mz-border-b mz-border-gray-200 last:mz-border-b-0">
        <button
          type="button"
          class="mz-w-full mz-flex mz-items-center mz-justify-between mz-gap-4 mz-px-5 md:mz-px-6 mz-py-5 md:mz-py-6 mz-text-left"
          data-mz-acc-trigger
          aria-expanded="<?php echo $index === 0 ? 'true' : 'false'; ?>"
        >
          <span class="mz-tracking-widest mz-text-xs mz-font-semibold mz-text-gray-900 mz-uppercase">
            <?php echo esc_html($sec['title']); ?>
          </span>

          <span class="mz-inline-flex mz-items-center mz-justify-center mz-w-8 mz-h-8 mz-rounded-full mz-border mz-border-gray-300 mz-text-gray-700">
            <span class="mz-text-lg mz-leading-none" data-mz-acc-icon>
              <?php echo $index === 0 ? '−' : '+'; ?>
            </span>
          </span>
        </button>

       <div class="mz-px-5 md:mz-px-6" data-mz-acc-panel>
          <?php echo $sec['content_html']; ?>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <!-- Reviews section (separate, not inside tabs) -->
  <div id="mz-reviews" class="mz-mt-10 mz-bg-white mz-border mz-border-gray-200 mz-rounded-2xl mz-p-6">
    <div class="mz-flex mz-items-center mz-justify-between mz-gap-4 mz-mb-4">
      <h2 class="mz-text-xl mz-font-semibold mz-text-gray-900">Customer Reviews</h2>
      <a href="#review_form" class="mz-text-sm mz-font-semibold mz-text-gray-900 hover:mz-underline">
        Write a review
      </a>
    </div>

    <?php
      // Woo reviews template loader works via comments_template
      if (comments_open()) {
        comments_template();
      } else {
        echo '<div class="mz-text-gray-600">Reviews are disabled for this product.</div>';
      }
    ?>
  </div>

  <!-- Related products -->
  <div class="mz-mt-10">
    <?php woocommerce_output_related_products(); ?>
  </div>

</div>

<?php do_action('woocommerce_after_single_product'); ?>
 