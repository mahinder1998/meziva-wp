<?php
/**
 * SECTION: Select Product (ACF Free + Tailwind mz-)
 * ✅ Card-1 style EXACT same for Card-2
 * ✅ Sale + MRP + Discount dynamic for BOTH cards
 * ✅ Section BG + Text color from ACF: bg_color, text_color
 */

/** Context */
if ( function_exists('acf_add_options_page') ) {
  $ctx = 'option';
} else {
  $ctx = (int) get_option('page_on_front');
  if ( ! $ctx ) { $ctx = get_the_ID(); }
}

/** Enable */
$enable = get_field('choose_shade_enable', $ctx);
if ($enable === null) { $enable = true; }
if ( ! $enable ) return;

/** Helpers */
function mz_num($v){
  if ($v === null || $v === '') return 0;
  $v = is_string($v) ? preg_replace('/[^\d.]/', '', $v) : $v;
  return (float)$v;
}
function mz_money($n){
  $n = (float)$n;
  if ($n <= 0) return '';
  return '₹' . number_format($n, 0);
}
function mz_discount_percent($mrp, $sale){
  $mrp = (float)$mrp; $sale = (float)$sale;
  if ($mrp <= 0 || $sale <= 0 || $sale >= $mrp) return 0;
  return (int) round((($mrp - $sale) / $mrp) * 100);
}
function mz_render_img($img, $alt_fallback = '') {
  if ( empty($img) ) return '';
  $id = is_array($img) ? ($img['ID'] ?? 0) : (int)$img;
  if (!$id) return '';
  return wp_get_attachment_image(
    $id,
    'large',
    false,
    [
      'class' => 'mz-w-full mz-h-full mz-object-cover',
      'loading' => 'lazy',
      'decoding' => 'async',
      'alt' => esc_attr( get_post_meta($id, '_wp_attachment_image_alt', true) ?: $alt_fallback )
    ]
  );
}

/** ✅ Section BG + Text color (ACF names as per your screenshot) */
$bg_color   = get_field('bg_color', $ctx);
$text_color = get_field('text_color', $ctx);

// Fallbacks (Meziva theme friendly)
$bg_color   = !empty($bg_color) ? $bg_color : '#FFF7F9';   // soft pink bg
$text_color = !empty($text_color) ? $text_color : '#111827'; // near-black

/** Section text */
$heading = get_field('choose_shade_heading', $ctx) ?: "Select Product";
$subhead = get_field('choose_shade_subheading', $ctx) ?: "description";

/** Prices from ACF */
$p1_sale = mz_num(get_field('choose_shade_p1_sale_price', $ctx));
$p1_mrp  = mz_num(get_field('choose_shade_p1_mrp_price',  $ctx));

$p2_sale = mz_num(get_field('choose_shade_p2_sale_price', $ctx));
$p2_mrp  = mz_num(get_field('choose_shade_p2_mrp_price',  $ctx));

/** Demo fallback prices (remove later if you want) */
if ($p1_sale <= 0) $p1_sale = 299;
if ($p1_mrp  <= 0) $p1_mrp  = 349;

if ($p2_sale <= 0) $p2_sale = 299;
if ($p2_mrp  <= 0) $p2_mrp  = 349;

$p1_off = mz_discount_percent($p1_mrp, $p1_sale);
$p2_off = mz_discount_percent($p2_mrp, $p2_sale);

/** Products */
$p1 = [
  'title' => get_field('choose_shade_p1_title', $ctx) ?: 'Berry Blast',
  'shade' => get_field('choose_shade_p1_shade', $ctx) ?: '',
  'desc'  => get_field('choose_shade_p1_desc',  $ctx) ?: 'A soft natural tint for everyday wear with comfy hydration.',
  'img'   => get_field('choose_shade_p1_img',   $ctx),
  'sale'  => $p1_sale,
  'mrp'   => $p1_mrp,
  'off'   => $p1_off,
  'btn_l' => get_field('choose_shade_p1_btn_label', $ctx) ?: 'Shop Berry Blast',
  'btn_u' => get_field('choose_shade_p1_btn_url',   $ctx) ?: '#',
  'b1'    => get_field('choose_shade_p1_b1', $ctx) ?: 'Natural pink tint',
  'b2'    => get_field('choose_shade_p1_b2', $ctx) ?: 'Office & daily wear',
  'b3'    => get_field('choose_shade_p1_b3', $ctx) ?: 'Soft hydrated finish',
];

$p2 = [
  'title' => get_field('choose_shade_p2_title', $ctx) ?: 'Cherry Blast',
  'shade' => get_field('choose_shade_p2_shade', $ctx) ?: '',
  'desc'  => get_field('choose_shade_p2_desc',  $ctx) ?: 'A richer tint with buildable colour payoff — perfect for evenings & photos.',
  'img'   => get_field('choose_shade_p2_img',   $ctx),
  'sale'  => $p2_sale,
  'mrp'   => $p2_mrp,
  'off'   => $p2_off,
  'btn_l' => get_field('choose_shade_p2_btn_label', $ctx) ?: 'Shop Cherry Blast',
  'btn_u' => get_field('choose_shade_p2_btn_url',   $ctx) ?: '#',
  'b1'    => get_field('choose_shade_p2_b1', $ctx) ?: 'Rich rosy-red tint',
  'b2'    => get_field('choose_shade_p2_b2', $ctx) ?: 'Buildable colour',
  'b3'    => get_field('choose_shade_p2_b3', $ctx) ?: 'Party / glam ready',
];

$cards = [$p1, $p2];
?>

<section class="mz-w-full mz-relative" style="background-color: <?php echo esc_attr($bg_color); ?>;"
id="products"
>
  <div class="mz-max-w-[1290px] mz-mx-auto mz-px-4 mz-py-10 md:mz-py-20 xl:mz-px-0">

    <!-- Heading -->
    <div class="mz-text-center mz-max-w-[720px] mz-mx-auto">
      <h2 class="mz-text-[36px] xl:mz-text-[50px] mz-leading-[1.05] mz-font-extrabold mz-tracking-tight"
           style="color: <?php echo esc_attr($text_color); ?>;">
        <?php echo esc_html($heading); ?>
      </h2>

      <p class="mz-mt-4 mz-text-[18px] md:mz-text-[18px] mz-font-semibold"
         style="color: <?php echo esc_attr($text_color); ?>;">
        <?php echo esc_html($subhead); ?>
      </p>
    </div>

    <!-- Cards -->
    <div class="mz-mt-10 md:mz-mt-12 mz-grid mz-grid-cols-1 md:mz-grid-cols-2 mz-gap-6 md:mz-gap-8">

      <?php foreach ($cards as $p): ?>
        <div class="mz-transition mz-duration-200 mz-rounded-2xl mz-border mz-border-black/5 mz-bg-white mz-overflow-hidden mz-shadow-sm hover:mz-shadow-md hover:mz--translate-y-[2px]">
          <div class="mz-grid mz-grid-cols-1 sm:mz-grid-cols-5 mz-gap-0">

            <!-- Image -->
            <div class="sm:mz-col-span-2 mz-bg-white">
              <div class="mz-bg-[#FFF1F5] mz-flex mz-items-center mz-justify-center mz-h-full">
                <div class="mz-w-full mz-h-full mz-overflow-hidden">
                  <?php
                    $img = mz_render_img($p['img'], $p['title']);
                    echo $img ? $img : '<div class="mz-text-[12px] mz-opacity-70 mz-text-[#9B4A6A] mz-h-full mz-flex mz-items-center mz-justify-center">Add image in ACF</div>';
                  ?>
                </div>
              </div>
            </div>

            <!-- Content -->
            <div class="sm:mz-col-span-3 mz-p-5 xl:mz-pt-3 xl:mz-px-5 xl:mz-pb-5">

              <div class="mz-text-2xl mz-font-heading md:mz-text-2xl mz-font-semibold mz-leading-snug mz-mb-2 mz-text-text-heading">
                <?php echo esc_html($p['title']); ?>
              </div>

              <!-- Price UI -->
              <div class="mz-flex mz-items-center mz-flex-wrap mz-gap-2 mz-mb-3">
                <?php if (!empty($p['sale'])): ?>
                  <span class="mz-text-[18px] mz-font-extrabold mz-text-[#9B4A6A]">
                    <?php echo esc_html(mz_money($p['sale'])); ?>
                  </span>
                <?php endif; ?>

                <?php if (!empty($p['mrp']) && $p['mrp'] > $p['sale']): ?>
                  <span class="mz-text-[18px] mz-text-text-body mz-line-through mz-opacity-70">
                    <?php echo esc_html(mz_money($p['mrp'])); ?>
                  </span>
                <?php endif; ?>

                <?php if (!empty($p['off'])): ?>
                  <span class="mz-text-[12px] mz-font-bold mz-px-2.5 mz-py-1 mz-rounded-full mz-bg-[#9B4A6A] mz-text-white">
                    <?php echo esc_html($p['off']); ?>% OFF
                  </span>
                <?php endif; ?>
              </div>

              <p class="mz-text-[14px] md:mz-text-[16px] mz-leading-7 mz-text-text-body">
                <?php echo esc_html($p['desc']); ?>
              </p>

              <ul class="mz-mt-4 mz-space-y-2">
                <?php foreach (['b1','b2','b3'] as $k): if(!empty($p[$k])): ?>
                  <li class="mz-flex mz-gap-2 mz-items-center mz-text-[14px] md:mz-text-[16px] mz-leading-7 mz-text-text-body">
                    <span class="mz-w-[18px] mz-h-[18px] mz-rounded-full mz-flex mz-items-center mz-justify-center mz-bg-[#F6EFEA] mz-text-[#9B4A6A]">✓</span>
                    <span><?php echo esc_html($p[$k]); ?></span>
                  </li>
                <?php endif; endforeach; ?>
              </ul>

              <div class="mz-mt-5">
                <a href="<?php echo esc_url($p['btn_u']); ?>"
                  class="mz-inline-block mz-bg-brand-accent mz-text-white mz-px-5 mz-py-3 mz-rounded-lg hover:mz-bg-opacity-90 mz-transition
                        mz-text-sm mz-font-bold hover:mz-bg-brand-primary hover:mz-text-white
                        md:mz-min-w-[140px] md:mz-py-4 md:mz-text-center
                        xl:mz-min-w-[150px] xl:mz-py-[18px] xl:mz-text-center xl:mz-text-[15px] xl:mz-rounded-xl">
                  <?php echo esc_html($p['btn_l']); ?>
                </a>
              </div>

            </div>

          </div>
        </div>
      <?php endforeach; ?>

    </div>
  </div>
</section>
  