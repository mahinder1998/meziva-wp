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
  <div class="mz-grid mz-grid-cols-1 lg:mz-grid-cols-2 mz-gap-[50px] lg:mz-gap-12">

    <!-- LEFT: Custom Gallery -->
    <div class="mz-bg-white lg:mz-sticky lg:mz-top-24 mz-h-fit">

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
            <span class="mz-absolute mz-top-4 mz-left-4 mz-bg-brand-accent mz-text-white mz-text-xs mz-font-semibold mz-px-3 mz-py-1.5 mz-rounded-full mz-shadow">
              Sale!
            </span>
          <?php endif; ?>
        </div>

        <!-- Hidden fancybox items (all images) -->
        <div class="mz-hidden">
          <?php foreach ($image_ids as $img_id) :
            if ((int)$img_id === (int)$first_id) continue; // ✅ skip first (already main)
            $full = wp_get_attachment_image_url($img_id, 'full');
            $alt  = get_post_meta($img_id, '_wp_attachment_image_alt', true);
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
                class="keen-slider__slide"
                data-mz-thumb
                data-large="<?php echo esc_url($large); ?>"
                data-full="<?php echo esc_url($full); ?>"
                aria-label="Select image <?php echo (int)($i + 1); ?>"
              >
                <span class="mz-rounded-xl  mz-overflow-hidden mz-h-20 mz-p-1  mz-flex mz-items-center mz-justify-center">
                  <img src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr($alt); ?>" class="mz-max-h-full mz-rounded-lg mz-max-w-full mz-object-contain" loading="lazy" />
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
    <div class="mz-text-center lg:mz-text-left xl:mz-ml-12">
      <div class="mz-flex mz-flex-col mz-gap-3">
        <h1 class="mz-text-[22px] sm:mz-text-[24px] md:mz-text-[30px] xl:mz-text-[30px] mz-leading-[1.08] mz-font-extrabold mz-tracking-tight">
          <?php the_title(); ?>
        </h1>

        <!-- Price + Rating + Reviews scroll -->
        <div class="mz-flex  mz-gap-4 mz-flex-col">
          <?php if (wc_review_ratings_enabled()) : ?>
            <div class="mz-flex mz-gap-1 mz-flex-row-reverse mz-justify-center lg:mz-justify-end">
              <a href="#mz-reviews" class="mz-text-sm mz-text-gray-900 mz-font-semibold">
                <?php echo $review_count ? ' (' . (int)$review_count . ')' : ''; ?>
              </a>
              <?php echo wc_get_rating_html($average); ?>
              
            </div>
          <?php endif; ?>

          <?php if ( ! $product->is_type('variable') ) : ?>
          <div class="mz-text-xl md:mz-text-2xl mz-font-semibold mz-text-brand-accent mz-price-wrap" data-mz-top-price>
              <?php echo $product->get_price_html(); ?>
          </div>
          <?php endif; ?> 

        </div>

       <div class="mz-text-gray-600 mz-leading-relaxed mz-px-4 lg:mz-px-0">
        <?php
          $short_html = $product ? $product->get_short_description() : '';
          if ($short_html) {
            echo '<div class="mz-prose mz-max-w-none mz-text-text-body">';
            echo wp_kses_post( wpautop( do_shortcode($short_html) ) );
            echo '</div>';
          }
        ?>
      </div> 
      </div>

      <!-- Add to cart -->
      <div class="mz-mt-6 mz-mz-pdp-cart">
        <?php woocommerce_template_single_add_to_cart(); ?>
      </div>

      <!-- Meta -->
      <!-- <div class="mz-mt-6 mz-text-sm mz-text-gray-500 mz-border-t mz-border-gray-200 mz-pt-4">
        <?php woocommerce_template_single_meta(); ?>
      </div> -->


        <!-- Accordion Info -->
         
  <div class="mz-mt-14 mz-text-left">
    
    <?php
      $sections = [];

      $short = $product ? $product->get_short_description() : '';
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

      // ✅ Ingredients (now reads correct ACF field)
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
          $label = $r['label']; $value = $r['value'];
          if ($label) {
            $rows_html .= '<div class="mz-flex mz-flex-col sm:mz-flex-row sm:mz-gap-3 lg:mz-grid lg:mz-grid-cols-[200px,1fr]">';
            $rows_html .= '<div class="mz-text-gray-body mz-font-medium mz-font-text-sm sm:mz-w-[260px]
            lg:mz-w-[200px]
            ">' . esc_html($label) . '</div>';
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
      <div class="mz-border-b mz-border-[#dfdfdf] pdp-accordion mz-py-4 " data-mz-acc-item>
        <button
          type="button"
          class="mz-w-full mz-flex mz-items-center mz-justify-between mz-text-base
          mz-bg-transparent mz-text-text-heading mz-border-none mz-outline-none mz-shadow-none hover:mz-bg-transparent
          "
          data-mz-acc-trigger
          aria-expanded="<?php echo $index === 0 ? 'true' : 'false'; ?>"
        >
          <span class="mz-text-[15px] lg:mz-text-[16px] mz-tracking-normal  mz-font-medium mz-text-gray-900 mz-uppercase">
            <?php echo esc_html($sec['title']); ?>
          </span>

          <span class="mz-inline-flex mz-items-center mz-justify-center mz-w-8 mz-h-8 mz-rounded-full  mz-text-text-body">
            <span class="mz-text-lg mz-leading-none" data-mz-acc-icon>
              <?php echo $index === 0 ? '−' : '+'; ?>
            </span>
          </span>
        </button>

        <div
          class=" mz-overflow-hidden mz-transition-[height] mz-duration-300"
          data-mz-acc-panel
          style="height: <?php echo $index === 0 ? 'auto' : '0px'; ?>;"
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


   


  <!-- Reviews section (separate, not inside tabs) -->
  <div id="mz-reviews" class="mz-mt-[100px]">
  <!-- your top title row optional (remove if you want exact screenshot look) -->

<?php
global $product;

if ( ! $product || ! is_a($product, 'WC_Product') ) {
  $product = wc_get_product(get_the_ID());
}

$avg    = $product ? (float) $product->get_average_rating() : 0;
$total  = $product ? (int) $product->get_review_count() : 0;
$counts = $product ? (array) $product->get_rating_counts() : [];

for ($i=1; $i<=5; $i++) {
  if (!isset($counts[$i])) $counts[$i] = 0;
}

// build stars (filled/half/empty) similar to screenshot
$filled = (int) floor($avg);
$half   = ($avg - $filled) >= 0.5 ? 1 : 0;
$empty  = 5 - $filled - $half;

function mz_star_svg($type = 'full') {
  // type: full | half | empty
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
?>

<!-- Screenshot-style summary -->
<div class="mz-mb-6">
  <div class="mz-text-center">
    <h2 class="mz-text-[22px] sm:mz-text-[24px] md:mz-text-[30px] xl:mz-text-[30px] mz-leading-[1.08] mz-font-extrabold mz-tracking-tight">Customer Reviews</h2>

    <div class="mz-flex mz-justify-center mz-items-center mz-gap-2 mz-mt-3 mz-text-gray-900">
      <div class="mz-flex mz-items-center">
        <?php
          for ($i=0; $i<$filled; $i++) echo mz_star_svg('full');
          if ($half) echo mz_star_svg('half');
          for ($i=0; $i<$empty; $i++) echo mz_star_svg('empty');
        ?>
      </div>
      <div class="mz-text-lg">
        <span class="mz-font-semibold"><?php echo number_format($avg, 2); ?></span> out of 5
      </div>
    </div>

    <div class="mz-text-base mz-text-gray-700 mz-mt-1">
      Based on <?php echo (int) $total; ?> reviews
    </div>

    <!-- breakdown -->
    <div class="mz-mt-7 mz-max-w-[520px] mz-mx-auto mz-space-y-3">
      <?php for ($star=5; $star>=1; $star--):
        $c = (int) $counts[$star];
        $pct = $total > 0 ? ($c / $total) * 100 : 0;
      ?>
        <div class="mz-flex mz-items-center mz-gap-4">
          <!-- left stars row -->
          <div class="mz-w-[120px] mz-flex mz-justify-start mz-items-center mz-text-text-body">
            <?php
              for ($s=1; $s<=5; $s++) {
                echo mz_star_svg($s <= $star ? 'full' : 'empty');
              }
            ?>
          </div>

          <!-- bar -->
          <div class="mz-flex-1 mz-h-2 mz-bg-gray-200 mz-rounded-sm mz-overflow-hidden">
            <div class="mz-h-full mz-bg-brand-primary" style="width: <?php echo $pct; ?>%"></div>
          </div>

          <!-- count right -->
          <div class="mz-w-[44px] mz-text-right mz-text-sm mz-text-gray-500">
            <?php echo $c; ?>
          </div>
        </div>
      <?php endfor; ?>
    </div>
  </div>

  <!-- black bar button like screenshot -->
   <div class="mz-text-center mz-mt-5">
     <a href="#review_form"
      class="mz-inline-block mz-bg-primary mz-bg-brand-accent mz-text-white mz-px-5 mz-py-3 mz-rounded-lg hover:mz-bg-opacity-90 mz-transition
                  mz-text-sm mz-font-bold hover:mz-bg-brand-primary hover:mz-text-white
                    md:mz-min-w-[140px] md:mz-py-4 md:mz-text-center xl:mz-min-w-[150px] xl:mz-py-[14px]  xl:mz-text-center xl:mz-text-[15px] xl:mz-rounded-xl
                    ">
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


  <!-- Related products -->
  <!-- <div class="mz-mt-10">
    <?php woocommerce_output_related_products(); ?>
  </div> -->

</div>

<?php do_action('woocommerce_after_single_product'); ?>
    