<style>
  @media (min-width: 1024px) {
  .single-product .site,
  .single-product .site-main,
  .single-product .content-area,
  .single-product .ast-container,
  .single-product .woocommerce,
  .single-product .woocommerce-page {
    overflow: visible !important;
  }
  [data-mz-acc-panel] {
    will-change: height;
  }
}
</style>
<?php
defined('ABSPATH') || exit; 

global $product;

do_action('woocommerce_before_single_product');

if (post_password_required()) {
  echo get_the_password_form();
  return;
}

$product_id = get_the_ID();

// -----------------------------
// ACF fields
// -----------------------------
$mz_how_to_use         = mz_get_acf('mz_how_to_use', $product_id);
$mz_ingredients        = mz_get_acf('ingredients', $product_id);
if (!$mz_ingredients) $mz_ingredients = mz_get_acf('mz_ingredients', $product_id);
$mz_additional_details = mz_get_acf('mz_additional_details', $product_id);

// New dynamic product marketing fields
$mz_promo_line_1 = mz_get_acf('mz_promo_line_1', $product_id);
$mz_promo_line_2 = mz_get_acf('mz_promo_line_2', $product_id);
$mz_usp_list     = mz_get_acf('mz_usp_list', $product_id);
$mz_fomo_text_1  = mz_get_acf('mz_fomo_text_1', $product_id);
$mz_fomo_text_2  = mz_get_acf('mz_fomo_text_2', $product_id);

// Fallback defaults
if (!$mz_promo_line_1) $mz_promo_line_1 = 'Buy 1 Get 1 FREE Today';
if (!$mz_promo_line_2) $mz_promo_line_2 = 'Free Shipping | COD Available';
if (!$mz_usp_list) {
  $mz_usp_list = "Dermatologically Tested\nSPF 30 Protection\nCruelty Free\nMade in India";
}
if (!$mz_fomo_text_1) $mz_fomo_text_1 = '24 people viewing this product';
if (!$mz_fomo_text_2) $mz_fomo_text_2 = 'Limited stock available';

$additional_rows = mz_parse_label_value_lines($mz_additional_details);
$usp_rows = array_filter(array_map('trim', preg_split("/\r\n|\n|\r/", (string) $mz_usp_list)));

// -----------------------------
// Images
// -----------------------------
$main_id = $product->get_image_id();
$gallery_ids = $product->get_gallery_image_ids();
$image_ids = [];
if ($main_id) $image_ids[] = $main_id;
if (!empty($gallery_ids)) $image_ids = array_merge($image_ids, $gallery_ids);
$image_ids = array_values(array_unique($image_ids));

// -----------------------------
// Reviews
// -----------------------------
$review_count = $product ? (int) $product->get_review_count() : 0;
$average      = $product ? (float) $product->get_average_rating() : 0;

// -----------------------------
// Short description
// -----------------------------
$short = $product ? $product->get_short_description() : '';
?>

<div id="product-<?php the_ID(); ?>" <?php wc_product_class('mz-w-full', $product); ?>>

  <!-- Breadcrumb -->
  <div class="mz-text-sm mz-text-gray-500 mz-mb-4">
    <?php woocommerce_breadcrumb(); ?>
  </div>

  <!-- Top Grid -->
  <div class="mz-grid mz-relative mz-grid-cols-1 lg:mz-grid-cols-2 mz-gap-6 lg:mz-gap-[50px] lg:mz-items-start">

    <!-- MOBILE TOP SUMMARY -->
    <div class="mz-order-1 lg:mz-hidden mz-text-left">
      <div class="mz-flex mz-flex-col mz-gap-2">
        <h1 class="mz-text-[20px] mz-leading-[1.15] mz-font-extrabold mz-tracking-tight mz-text-gray-900">
          <?php the_title(); ?>
        </h1>

        <?php if (wc_review_ratings_enabled()) : ?>
          <div class="mz-flex mz-items-center mz-gap-2 mz-flex-wrap">
            <div class="mz-flex mz-items-center mz-gap-1">
              <?php echo wc_get_rating_html($average); ?>
            </div>
            <a href="#mz-reviews" class="mz-text-[14px] mz-text-gray-800 mz-font-semibold">
              <?php echo number_format((float) $average, 1); ?> Rating
              <?php echo $review_count ? ' | ' . (int) $review_count . '+ Reviews' : ''; ?>
            </a>
          </div>
        <?php endif; ?>

        <!-- Mobile Promo -->
        <div class="mz-mt-1 mz-space-y-2">
          <div class="mz-inline-flex mz-items-center mz-gap-2 mz-rounded-full mz-bg-[#fff4f7] mz-px-2 mz-py-2 mz-text-[12px] mz-font-semibold mz-text-brand-accent">
            <span>🔥</span>
            <span><?php echo esc_html($mz_promo_line_1); ?></span>
          </div>
        </div>
      </div>
    </div>

    <!-- LEFT: Custom Gallery -->
    <div class="mz-order-2 lg:mz-order-1 mz-bg-white lg:mz-sticky lg:mz-top-[96px] lg:mz-self-start mz-h-fit">

      <?php if (!empty($image_ids)) : ?>
        <?php
          $first_id    = $image_ids[0];
          $first_full  = wp_get_attachment_image_url($first_id, 'full');
          $first_large = wp_get_attachment_image_url($first_id, 'large');
          $first_alt   = get_post_meta($first_id, '_wp_attachment_image_alt', true);
        ?>

        <div class="mz-relative mz-rounded-2xl mz-overflow-hidden mz-bg-gray-50 mz-border mz-border-gray-200">

          <!-- MAIN SLIDER -->
          <div class="keen-slider" data-mz-main-slider>
            <?php foreach ($image_ids as $i => $img_id) :
              $large = wp_get_attachment_image_url($img_id, 'large');
              $full  = wp_get_attachment_image_url($img_id, 'full');
              $alt   = get_post_meta($img_id, '_wp_attachment_image_alt', true);
            ?>
              <div
                class="keen-slider__slide"
                data-mz-slide
                data-full="<?php echo esc_url($full); ?>"
                data-large="<?php echo esc_url($large); ?>"
                data-index="<?php echo (int) $i; ?>"
              >
                <a
                  href="<?php echo esc_url($full); ?>"
                  class="mz-block"
                  data-fancybox="mz-product"
                  data-mz-main-link
                  data-index="<?php echo (int) $i; ?>"
                >
                  <img
                    src="<?php echo esc_url($large); ?>"
                    alt="<?php echo esc_attr($alt); ?>"
                    class="mz-w-full mz-h-[420px] sm:mz-h-[500px] md:mz-h-[620px] mz-object-contain"
                    loading="<?php echo $i === 0 ? 'eager' : 'lazy'; ?>"
                    data-mz-main-img
                  />
                </a>
              </div>
            <?php endforeach; ?>
          </div>

          <!-- ZOOM -->
          <button
            type="button"
            class="mz-absolute mz-top-4 mz-right-4 mz-z-10 mz-w-10 mz-h-10 mz-rounded-full mz-bg-white/90 mz-shadow-md mz-border mz-border-gray-200 mz-flex mz-items-center mz-justify-center hover:mz-scale-105 mz-transition"
            data-mz-zoom
            aria-label="Zoom image"
          >
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 3h6v6M14 10l7-7M9 21H3v-6M10 14l-7 7" />
            </svg>
          </button>

          <?php if ($product->is_on_sale()) : ?>
            <span class="mz-absolute mz-top-4 mz-left-4 mz-z-10 mz-bg-brand-accent mz-text-white mz-text-xs mz-font-semibold mz-px-3 mz-py-1.5 mz-rounded-full mz-shadow">
              Sale!
            </span>
          <?php endif; ?>

          <!-- DOTS -->
          <div class="mz-absolute mz-bottom-4 mz-left-1/2 -mz-translate-x-1/2 mz-z-10 mz-flex mz-items-center mz-gap-2 lg:mz-hidden" data-mz-slider-dots></div>
        </div>

        <!-- HIDDEN EXTRA FANCYBOX LINKS -->
        <div class="mz-hidden">
          <?php foreach ($image_ids as $img_id) :
            $full = wp_get_attachment_image_url($img_id, 'full');
            $alt  = get_post_meta($img_id, '_wp_attachment_image_alt', true);
          ?>
            <a href="<?php echo esc_url($full); ?>" data-fancybox="mz-product" data-caption="<?php echo esc_attr($alt); ?>"></a>
          <?php endforeach; ?>
        </div>

        <!-- THUMBS -->
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
                class="keen-slider__slide"
                data-mz-thumb
                data-large="<?php echo esc_url($large); ?>"
                data-full="<?php echo esc_url($full); ?>"
                data-index="<?php echo (int) $i; ?>"
                aria-label="Select image <?php echo (int) ($i + 1); ?>"
              >
                <span class="mz-rounded-xl mz-overflow-hidden mz-h-20 mz-p-1 mz-flex mz-items-center mz-justify-center">
                  <img
                    src="<?php echo esc_url($thumb); ?>"
                    alt="<?php echo esc_attr($alt); ?>"
                    class="mz-max-h-full mz-rounded-lg mz-max-w-full mz-object-contain"
                    loading="lazy"
                  />
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
    <div class="mz-order-3 lg:mz-order-2 mz-text-center lg:mz-text-left xl:mz-ml-12">

      <!-- DESKTOP ONLY TITLE / RATING / PROMO / PRICE / SHORT DESC -->
      <div class="mz-hidden lg:mz-flex lg:mz-flex-col lg:mz-gap-4">
        <h1 class="mz-text-[28px] xl:mz-text-[32px] mz-leading-[1.08] mz-font-extrabold mz-tracking-tight mz-text-gray-900">
          <?php the_title(); ?>
        </h1>

        <?php if (wc_review_ratings_enabled()) : ?>
          <div class="mz-flex mz-items-center mz-gap-3 mz-flex-wrap">
            <div class="mz-flex mz-items-center mz-gap-1">
              <?php echo wc_get_rating_html($average); ?>
            </div>
            <a href="#mz-reviews" class="mz-text-[15px] mz-text-gray-800 mz-font-semibold">
              <?php echo number_format((float) $average, 1); ?> Rating
              <?php echo $review_count ? ' | ' . (int) $review_count . '+ Reviews' : ''; ?>
            </a>
          </div>
        <?php endif; ?>

        <div class="mz-flex mz-flex-col mz-gap-2 lg:mz-flex-row">
          <div class="mz-inline-flex mz-items-center mz-gap-2 mz-self-start mz-rounded-full mz-bg-[#fff4f7] mz-px-4 mz-py-2.5 mz-text-[14px] mz-font-semibold mz-text-brand-accent">
            <span>🔥</span>
            <span><?php echo esc_html($mz_promo_line_1); ?></span>
          </div>

          <div class="mz-flex mz-flex-wrap mz-items-center mz-gap-2 mz-text-[14px] mz-font-medium mz-text-gray-700">
            <span class="mz-inline-flex mz-items-center mz-gap-2 mz-rounded-full mz-bg-gray-100 mz-px-4 mz-py-2">
              🚚 <span><?php echo esc_html($mz_promo_line_2); ?></span>
            </span>
          </div>
        </div>

        <?php if (!$product->is_type('variable')) : ?>
          <div class="mz-text-2xl md:mz-text-3xl mz-font-semibold mz-text-brand-accent mz-price-wrap" data-mz-top-price>
            <?php echo $product->get_price_html(); ?>
          </div>
        <?php endif; ?>

        <div class="mz-text-gray-600 mz-leading-relaxed">
          <?php
            $short_html = $product ? $product->get_short_description() : '';
            if ($short_html) {
              echo '<div class="mz-prose mz-max-w-none mz-text-text-body">';
              echo wp_kses_post(wpautop(do_shortcode($short_html)));
              echo '</div>';
            }
          ?>
        </div>
      </div>

      <!-- MOBILE PRICE -->
      <div class="lg:mz-hidden">
        <?php if (!$product->is_type('variable')) : ?>
          <div class="mz-text-left mz-text-[24px] mz-font-bold mz-text-brand-accent mz-price-wrap" data-mz-top-price>
            <?php echo $product->get_price_html(); ?>
          </div>
        <?php endif; ?>
      </div>

      <!-- Add to cart -->
      <div id="mz-pdp-cart-form" class="mz-mt-6 mz-mz-pdp-cart">
        <?php woocommerce_template_single_add_to_cart(); ?>
      </div>

      <!-- USP BOX -->
      <?php if (!empty($usp_rows)) : ?>
        <div class="mz-mt-5 mz-rounded-2xl mz-border mz-border-[#ead7df] mz-bg-[#fff8fb] mz-p-4 lg:mz-p-5">
          <div class="mz-grid mz-grid-cols-1 sm:mz-grid-cols-2 mz-gap-3">
            <?php foreach ($usp_rows as $usp) : ?>
              <div class="mz-flex mz-items-start mz-gap-2 mz-text-left">
                <span class="mz-inline-flex mz-items-center mz-justify-center mz-w-5 mz-h-5 mz-rounded-full mz-bg-[#dba0b8] mz-text-white mz-text-[11px] mz-font-bold mz-shrink-0 mz-p-1">
                  <svg fill="#fff" width="18px" height="18px" viewBox="0 0 1920 1920" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M1827.701 303.065 698.835 1431.801 92.299 825.266 0 917.564 698.835 1616.4 1919.869 395.234z" fill-rule="evenodd"/>
                  </svg>
                </span>
                <span class="mz-text-[14px] lg:mz-text-[15px] mz-font-medium mz-text-gray-800"><?php echo esc_html($usp); ?></span>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>

      <!-- FOMO BOX -->
      <div class="mz-mt-4 mz-flex mz-flex-col mz-gap-2">
        <div class="mz-flex mz-items-center mz-gap-2 mz-rounded-xl mz-bg-[#fff4f7] mz-px-4 mz-py-3 mz-text-left">
          <span class="mz-text-[18px]">🔥</span>
          <span class="mz-text-[14px] lg:mz-text-[15px] mz-font-semibold mz-text-[#7a2a4f]"><?php echo esc_html($mz_fomo_text_1); ?></span>
        </div>
        <div class="mz-flex mz-items-center mz-gap-2 mz-rounded-xl mz-bg-[#fff8e7] mz-px-4 mz-py-3 mz-text-left">
          <span class="mz-text-[18px]">⚡</span>
          <span class="mz-text-[14px] lg:mz-text-[15px] mz-font-semibold mz-text-[#7a2a4f]"><?php echo esc_html($mz_fomo_text_2); ?></span>
        </div>
      </div>

      <!-- Accordion Info -->
      <div class="mz-mt-10 mz-text-left">
        <?php
          $sections = [];
          $desc  = $product ? $product->get_description() : '';

          if (!empty($desc)) {
            $sections[] = [
              'title' => 'Features',
              'content_html' => '<div class="mz-prose mz-max-w-none mz-text-text-body">' . wp_kses_post(wpautop($desc)) . '</div>',
            ];
          }

          if (!empty($mz_how_to_use)) {
            $sections[] = [
              'title' => 'How to use',
              'content_html' => '<div class="mz-prose mz-max-w-none mz-text-text-body">' . wp_kses_post($mz_how_to_use) . '</div>',
            ];
          }

          if (!empty($mz_ingredients)) {
            $ing_html = $mz_ingredients;
            if ($ing_html === strip_tags($ing_html)) {
              $ing_html = nl2br(esc_html($ing_html));
            }
            $sections[] = [
              'title' => 'Ingredients',
              'content_html' => '<div class="mz-prose mz-max-w-none mz-text-text-body">' . wp_kses_post($ing_html) . '</div>',
            ];
          }

          if (!empty($additional_rows)) {
            $rows_html = '<div class="mz-space-y-3">';
            foreach ($additional_rows as $r) {
              $label = $r['label'];
              $value = $r['value'];

              if ($label) {
                $rows_html .= '<div class="mz-flex mz-flex-col sm:mz-flex-row sm:mz-gap-3 lg:mz-grid lg:mz-grid-cols-[200px,1fr]">';
                $rows_html .= '<div class="mz-text-gray-body mz-font-medium mz-font-text-sm sm:mz-w-[260px] lg:mz-w-[200px]">' . esc_html($label) . '</div>';
                $rows_html .= '<div class="mz-text-text-body">' . esc_html($value) . '</div>';
                $rows_html .= '</div>';
              } else {
                $rows_html .= '<div class="mz-text-text-body">' . esc_html($value) . '</div>';
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
          <div class="mz-border-b mz-border-[#dfdfdf] pdp-accordion mz-py-4" data-mz-acc-item>
            <button
              type="button"
              class="mz-w-full mz-flex mz-items-center mz-justify-between mz-text-base mz-bg-transparent mz-text-text-heading mz-border-none mz-outline-none mz-shadow-none hover:mz-bg-transparent"
              data-mz-acc-trigger
              aria-expanded="<?php echo $index === 0 ? 'true' : 'false'; ?>"
            >
              <span class="mz-text-[15px] lg:mz-text-[16px] mz-tracking-normal mz-font-medium mz-text-gray-900 mz-uppercase">
                <?php echo esc_html($sec['title']); ?>
              </span>

              <span class="mz-inline-flex mz-items-center mz-justify-center mz-w-8 mz-h-8 mz-rounded-full mz-text-text-body">
                <span class="mz-text-lg mz-leading-none" data-mz-acc-icon>
                  <?php echo $index === 0 ? '−' : '+'; ?>
                </span>
              </span>
            </button>

            <div
              class="mz-overflow-hidden mz-transition-[height] mz-duration-300"
              data-mz-acc-panel
            >
              <div class="mz-py-5">
                <?php echo $sec['content_html']; ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

    </div>
  </div>

  <!-- Reviews section -->
  <div id="mz-reviews" class="mz-mt-[30px] lg:mz-mt-[40px]">
    <?php
      global $product;

      if (!$product || !is_a($product, 'WC_Product')) {
        $product = wc_get_product(get_the_ID());
      }

      $avg    = $product ? (float) $product->get_average_rating() : 0;
      $total  = $product ? (int) $product->get_review_count() : 0;
      $counts = $product ? (array) $product->get_rating_counts() : [];

      for ($i = 1; $i <= 5; $i++) {
        if (!isset($counts[$i])) $counts[$i] = 0;
      }

      if (!function_exists('mz_star_svg')) {
        function mz_star_svg($type = 'full') {
          $full = '<svg class="mz-w-5 mz-h-5" viewBox="0 0 20 20" fill="#fda256" aria-hidden="true"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.955a1 1 0 00.95.69h4.156c.969 0 1.371 1.24.588 1.81l-3.363 2.443a1 1 0 00-.364 1.118l1.285 3.955c.3.921-.755 1.688-1.538 1.118l-3.363-2.443a1 1 0 00-1.176 0l-3.363 2.443c-.783.57-1.838-.197-1.538-1.118l1.285-3.955a1 1 0 00-.364-1.118L2.07 9.382c-.783-.57-.38-1.81.588-1.81h4.156a1 1 0 00.95-.69l1.286-3.955z"/></svg>';

          $empty = '<svg class="mz-w-5 mz-h-5" viewBox="0 0 20 20" fill="none" stroke="#fda256" stroke-width="1.5" aria-hidden="true"><path d="M10 1.9l2.35 4.76 5.26.76-3.8 3.7.9 5.24L10 13.95 5.29 16.36l.9-5.24-3.8-3.7 5.26-.76L10 1.9z"/></svg>';

          $half = '<svg class="mz-w-5 mz-h-5" viewBox="0 0 20 20" aria-hidden="true">
            <defs>
              <linearGradient id="mz-half-grad" x1="0" x2="1">
                <stop offset="50%" stop-color="#fda256" />
                <stop offset="50%" stop-color="transparent" />
              </linearGradient>
            </defs>
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.955a1 1 0 00.95.69h4.156c.969 0 1.371 1.24.588 1.81l-3.363 2.443a1 1 0 00-.364 1.118l1.285 3.955c.3.921-.755 1.688-1.538 1.118l-3.363-2.443a1 1 0 00-1.176 0l-3.363 2.443c-.783.57-1.838-.197-1.538-1.118l1.285-3.955a1 1 0 00-.364-1.118L2.07 9.382c-.783-.57-.38-1.81.588-1.81h4.156a1 1 0 00.95-.69l1.286-3.955z" fill="url(#mz-half-grad)" stroke="#fda256" stroke-width="1.2"/>
          </svg>';

          if ($type === 'full') return $full;
          if ($type === 'half') return $half;
          return $empty;
        }
      }

      $filled = (int) floor($avg);
      $half   = ($avg - $filled) >= 0.5 ? 1 : 0;
      $empty  = 5 - $filled - $half;
    ?>

    <div class="mz-mb-6">
      <div class="mz-text-center">
        <h2 class="mz-text-[22px] sm:mz-text-[24px] md:mz-text-[30px] xl:mz-text-[30px] mz-leading-[1.08] mz-font-extrabold mz-tracking-tight">
          Customer Reviews
        </h2>

        <div class="mz-flex mz-justify-center mz-items-center mz-gap-2 mz-mt-3 mz-text-gray-900">
          <div class="mz-flex mz-items-center">
            <?php
              for ($i = 0; $i < $filled; $i++) echo mz_star_svg('full');
              if ($half) echo mz_star_svg('half');
              for ($i = 0; $i < $empty; $i++) echo mz_star_svg('empty');
            ?>
          </div>
          <div class="mz-text-lg">
            <span class="mz-font-semibold"><?php echo number_format($avg, 2); ?></span> out of 5
          </div>
        </div>

        <div class="mz-text-base mz-text-gray-700 mz-mt-1">
          Based on <?php echo (int) $total; ?> reviews
        </div>

        <div class="mz-mt-7 mz-max-w-[520px] mz-mx-auto mz-space-y-3">
          <?php for ($star = 5; $star >= 1; $star--) :
            $c = (int) $counts[$star];
            $pct = $total > 0 ? ($c / $total) * 100 : 0;
          ?>
            <div class="mz-flex mz-items-center mz-gap-4">
              <div class="mz-w-[120px] mz-flex mz-justify-start mz-items-center mz-text-text-body">
                <?php
                  for ($s = 1; $s <= 5; $s++) {
                    echo mz_star_svg($s <= $star ? 'full' : 'empty');
                  }
                ?>
              </div>

              <div class="mz-flex-1 mz-h-2 mz-bg-gray-200 mz-rounded-sm mz-overflow-hidden">
                <div class="mz-h-full mz-bg-brand-primary" style="width: <?php echo $pct; ?>%"></div>
              </div>

              <div class="mz-w-[44px] mz-text-right mz-text-sm mz-text-gray-500">
                <?php echo $c; ?>
              </div>
            </div>
          <?php endfor; ?>
        </div>
      </div>

      <div class="mz-text-center mz-mt-5">
        <a
          href="#review_form"
          class="mz-inline-block mz-bg-primary mz-bg-brand-accent mz-text-white mz-px-5 mz-py-3 mz-rounded-lg hover:mz-bg-opacity-90 mz-transition mz-text-sm mz-font-bold hover:mz-bg-brand-primary hover:mz-text-white md:mz-min-w-[140px] md:mz-py-4 md:mz-text-center xl:mz-min-w-[150px] xl:mz-py-[14px] xl:mz-text-center xl:mz-text-[15px] xl:mz-rounded-xl"
        >
          Write a review
        </a>
      </div>
    </div>

    <?php
      if (comments_open()) {
        comments_template();
      } else {
        echo '<div class="mz-text-gray-600">Reviews are disabled for this product.</div>';
      }
    ?>
  </div>

</div>

<!-- MOBILE STICKY CTA -->
<div class="lg:mz-hidden mz-fixed mz-bottom-0 mz-left-0 mz-right-0  mz-bg-white mz-border-t mz-border-gray-200 mz-shadow-[0_-4px_20px_rgba(0,0,0,0.08)] mz-z-[999999]">
  <div class="mz-px-4 mz-py-3 mz-flex mz-items-center mz-gap-3 mz-relative mz-z-[999]">
    <div class="mz-flex-1 mz-min-w-0"> 
      <?php if (!$product->is_type('variable')) : ?>
        <div class="mz-text-[18px] mz-font-extrabold mz-leading-tight mz-text-gray-900">
          <?php echo wp_kses_post($product->get_price_html()); ?>
        </div>
      <?php else : ?>
        <div class="mz-text-[13px] mz-text-gray-500 mz-leading-none">Select options</div>
        <div class="mz-text-[18px] mz-font-bold mz-leading-tight mz-text-gray-900">
          Choose Variant
        </div>
      <?php endif; ?>
    </div>

   <button
  type="button"
  data-mz-sticky-buy-now
  class="mz-inline-flex mz-items-center mz-justify-center mz-h-[50px] mz-min-w-[170px] mz-px-6 mz-rounded-xl mz-bg-brand-accent mz-text-white mz-font-bold mz-text-[16px] hover:mz-opacity-90 mz-transition"
>
  Buy Now
</button>
  </div>
</div>

<?php do_action('woocommerce_after_single_product'); ?> 


<script>
document.addEventListener('DOMContentLoaded', function () {
  const stickyBtn = document.querySelector('[data-mz-sticky-buy-now]');
  if (!stickyBtn) return;

  stickyBtn.addEventListener('click', function (e) {
    e.preventDefault();

    const cartForm = document.querySelector('#mz-pdp-cart-form form.cart');
    if (!cartForm) return;

    const realBuyNowBtn = cartForm.querySelector('[data-mz-real-buy-now]');
    if (!realBuyNowBtn) {
      cartForm.scrollIntoView({ behavior: 'smooth', block: 'center' });
      return;
    }

    // pehle form tak smooth scroll
    cartForm.scrollIntoView({ behavior: 'smooth', block: 'center' });

    // thoda delay deke actual buy now trigger karo
    setTimeout(function () {
      if (realBuyNowBtn.disabled) return;

      if (typeof cartForm.requestSubmit === 'function') {
        cartForm.requestSubmit(realBuyNowBtn);
      } else {
        realBuyNowBtn.click();
      }
    }, 250);
  });
});
</script>  