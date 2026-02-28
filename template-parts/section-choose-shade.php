<?php
/**
 * SECTION: Select Product (ACF Free + Tailwind mz-)
 * ✅ Hide bullet list
 * ✅ Desktop cards smaller (reduced media height + container width)
 * ✅ CTA color kept as before (uses theme classes mz-bg-brand-accent / hover mz-bg-brand-primary)
 * ✅ Mobile 2 columns in 1 row
 * ✅ NEW: Image + Title (heading) are clickable (same URL as button)
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

/** Colors */
$bg_color   = get_field('bg_color', $ctx);
$text_color = get_field('text_color', $ctx);
$bg_color   = !empty($bg_color) ? $bg_color : '#FFF7F9';
$text_color = !empty($text_color) ? $text_color : '#111827';

/** Text */
$heading = get_field('choose_shade_heading', $ctx) ?: "Tinted Lip Balm with SPF 30";
$subhead = get_field('choose_shade_subheading', $ctx) ?: "Berry & Cherry Shades";

/** Prices */
$p1_sale = mz_num(get_field('choose_shade_p1_sale_price', $ctx));
$p1_mrp  = mz_num(get_field('choose_shade_p1_mrp_price',  $ctx));
$p2_sale = mz_num(get_field('choose_shade_p2_sale_price', $ctx));
$p2_mrp  = mz_num(get_field('choose_shade_p2_mrp_price',  $ctx));

if ($p1_sale <= 0) $p1_sale = 299;
if ($p1_mrp  <= 0) $p1_mrp  = 349;
if ($p2_sale <= 0) $p2_sale = 299;
if ($p2_mrp  <= 0) $p2_mrp  = 349;

$p1_off = mz_discount_percent($p1_mrp, $p1_sale);
$p2_off = mz_discount_percent($p2_mrp, $p2_sale);

/** Products */
$p1 = [
  'title' => get_field('choose_shade_p1_title', $ctx) ?: 'Berry Blast',
  'desc'  => get_field('choose_shade_p1_desc',  $ctx) ?: 'A soft natural tint for everyday wear with comfy hydration.',
  'img'   => get_field('choose_shade_p1_img',   $ctx),
  'sale'  => $p1_sale,
  'mrp'   => $p1_mrp,
  'off'   => $p1_off,
  'btn_l' => get_field('choose_shade_p1_btn_label', $ctx) ?: 'Shop Berry',
  'btn_u' => get_field('choose_shade_p1_btn_url',   $ctx) ?: '#',
  'tag'   => get_field('choose_shade_p1_tag', $ctx) ?: 'Best Seller',
];

$p2 = [
  'title' => get_field('choose_shade_p2_title', $ctx) ?: 'Cherry Blast',
  'desc'  => get_field('choose_shade_p2_desc',  $ctx) ?: 'A richer tint with buildable colour payoff — perfect for evenings & photos.',
  'img'   => get_field('choose_shade_p2_img',   $ctx),
  'sale'  => $p2_sale,
  'mrp'   => $p2_mrp,
  'off'   => $p2_off,
  'btn_l' => get_field('choose_shade_p2_btn_label', $ctx) ?: 'Shop Now',
  'btn_u' => get_field('choose_shade_p2_btn_url',   $ctx) ?: '#',
  'tag'   => get_field('choose_shade_p2_tag', $ctx) ?: 'New',
];

$cards = [$p1, $p2];
?>

<section class="mz-w-full mz-relative" id="products" style="background-color: <?php echo esc_attr($bg_color); ?>;">
  <div class="mz-max-w-[860px] mz-mx-auto mz-px-4 mz-py-10 md:mz-py-14 xl:mz-py-16">

    <!-- Heading -->
    <div class="mz-text-center mz-max-w-[460px] mz-mx-auto">
      <h2 class="mz-text-[22px] sm:mz-text-[24px] md:mz-text-[30px] xl:mz-text-[30px] mz-leading-[1.08] mz-font-extrabold mz-tracking-tight"
          style="color: <?php echo esc_attr($text_color); ?>;">
        <?php echo esc_html($heading); ?>
      </h2>

      <p class="mz-mt-3 mz-text-[14px] sm:mz-text-[16px] mz-font-semibold mz-opacity-90"
         style="color: <?php echo esc_attr($text_color); ?>;">
        <?php echo esc_html($subhead); ?>
      </p>
    </div>

    <!-- Cards -->
    <div class="mz-mt-8 md:mz-mt-9 mz-grid mz-grid-cols-2 md:mz-grid-cols-2 mz-gap-3 sm:mz-gap-4 md:mz-gap-6">

      <?php foreach ($cards as $p): 
        $card_url = !empty($p['btn_u']) ? $p['btn_u'] : '#';
      ?>
        <article class="mz-group mz-rounded-2xl mz-bg-white mz-border mz-border-black/5 mz-overflow-hidden mz-shadow-sm hover:mz-shadow-md mz-transition mz-duration-200">

          <!-- Media -->
          <div class="mz-relative">
            <!-- ✅ IMAGE CLICKABLE -->
            <a href="<?php echo esc_url($card_url); ?>" class="mz-block mz-focus:outline-none" aria-label="<?php echo esc_attr($p['title']); ?>">
              <div class="mz-aspect-[4/5] sm:mz-aspect-[5/4] md:mz-aspect-auto mz-bg-[#F7F2F4] mz-overflow-hidden">
                <?php
                  $img = mz_render_img($p['img'], $p['title']);
                  echo $img ? $img : '<div class="mz-text-[12px] mz-opacity-70 mz-text-[#9B4A6A] mz-h-full mz-flex mz-items-center mz-justify-center">Add image in ACF</div>';
                ?>
              </div>
            </a>

            <!-- Pills -->
            <div class="mz-absolute mz-left-3 mz-bottom-0 mz-flex mz-gap-2">
              <?php if (!empty($p['tag'])): ?>
                <span class="mz-text-[10px] sm:mz-text-[11px] mz-font-bold mz-uppercase mz-tracking-wide mz-px-2.5 mz-py-1 mz-rounded-full mz-bg-[#EAF7F2] mz-text-[#0F6A4F] mz-opacity-90">
                  <?php echo esc_html($p['tag']); ?>
                </span>
              <?php endif; ?>

              <?php if (!empty($p['off'])): ?>
                <span class="mz-text-[10px] sm:mz-text-[11px] mz-font-bold mz-px-2.5 mz-py-1 mz-rounded-full mz-bg-[#9B4A6A] mz-text-white">
                  <?php echo esc_html($p['off']); ?>% OFF
                </span>
              <?php endif; ?>
            </div>
          </div>

          <!-- Content -->
          <div class="mz-p-3 sm:mz-p-4 md:mz-p-4 xl:mz-p-5 mz-text-center">

            <!-- ✅ TITLE CLICKABLE -->
            <h3 class="mz-text-[15px] sm:mz-text-[18px] md:mz-text-[19px] mz-font-extrabold mz-leading-tight mz-text-[#111827]">
              <a href="<?php echo esc_url($card_url); ?>" class="mz-inline-block hover:mz-opacity-80 mz-transition">
                <?php echo esc_html($p['title']); ?>
              </a>
            </h3>

            <!-- Rating row -->
            <div class="mz-mt-2 mz-flex mz-items-center mz-gap-2 mz-text-[12px] mz-text-text-body mz-justify-center">
              <span class="mz-inline-flex mz-items-center mz-gap-1">
                <span class="mz-text-[#F59E0B]">★</span><span>4.7</span>
              </span>
              <span class="mz-inline-flex mz-items-center mz-gap-1">
                <span>Reviews</span>
              </span>
            </div>

            <!-- Price row -->
            <div class="mz-mt-2 mz-flex mz-items-baseline mz-gap-2 mz-flex-wrap mz-justify-center">
              <?php if (!empty($p['sale'])): ?>
                <span class="mz-text-[16px] sm:mz-text-[18px] md:mz-text-[19px] mz-font-extrabold mz-text-text-heading">
                  <?php echo esc_html(mz_money($p['sale'])); ?>
                </span>
              <?php endif; ?>

              <?php if (!empty($p['mrp']) && $p['mrp'] > $p['sale']): ?>
                <span class="mz-text-[12px] sm:mz-text-[13px] md:mz-text-[14px] mz-line-through mz-text-[#9CA3AF]">
                  <?php echo esc_html(mz_money($p['mrp'])); ?>
                </span>
              <?php endif; ?>
            </div>

            <p class="mz-mt-2 mz-text-[12px] sm:mz-text-[13px] md:mz-text-[14px] mz-leading-5 md:mz-leading-6 mz-text-[#6B7280] mz-px-4 lg:mz-px-8">
              <?php echo esc_html($p['desc']); ?>
            </p>

            <!-- CTA -->
            <div class="mz-mt-4">
              <a href="<?php echo esc_url($card_url); ?>"
                 class="mz-w-full mz-inline-flex mz-items-center mz-justify-center mz-gap-2
                        mz-bg-brand-accent mz-text-white mz-font-extrabold
                        mz-rounded-xl mz-px-4 mz-py-3 md:mz-py-3.5
                        mz-text-[13px] sm:mz-text-[14px] md:mz-text-[15px]
                        hover:mz-bg-brand-primary hover:mz-text-white mz-transition">
                <?php echo esc_html($p['btn_l']); ?>
                <svg class="mz-h-4 mz-w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                  <path d="M5 12h12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                  <path d="M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </a>
            </div>

          </div>
        </article>
      <?php endforeach; ?>

    </div>
  </div>
</section>  