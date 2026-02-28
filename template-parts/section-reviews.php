<?php
/**
 * ACF (Free) fields to create (on Options page OR Home page):
 * - reviews_enable (true/false)
 * - reviews_heading (text)
 *
 * Review 1:
 * - r1_name (text)
 * - r1_text (textarea)
 * - r1_stars (number 1-5) [optional]
 * - r1_avatar (image)
 *
 * Review 2:
 * - r2_name, r2_text, r2_stars, r2_avatar
 * Review 3:
 * - r3_name, r3_text, r3_stars, r3_avatar
 * Review 4:
 * - r4_name, r4_text, r4_stars, r4_avatar
 *
 * Optional styling fields:
 * - reviews_bg_color (color)
 * - reviews_text_color (color)
 * - reviews_divider_color (color)
 */

if ( function_exists('acf_add_options_page') ) {
  $ctx = 'option';
} else {
  $ctx = (int) get_option('page_on_front');
  if ( ! $ctx ) { $ctx = get_the_ID(); }
}

$enable = get_field('reviews_enable', $ctx);
if ($enable === null) { $enable = true; }
if ( ! $enable ) return;

function mz_int_range($v, $min = 1, $max = 5, $fallback = 5){
  $n = (int)$v;
  if ($n < $min || $n > $max) return (int)$fallback;
  return $n;
}

function mz_avatar_img($img, $alt = ''){
  if (empty($img)) return '';
  $id = is_array($img) ? ($img['ID'] ?? 0) : (int)$img;
  if (!$id) return '';
  return wp_get_attachment_image(
    $id,
    'thumbnail',
    false,
    [
      'class' => 'mz-w-full mz-h-full mz-object-cover',
      'loading' => 'lazy',
      'decoding' => 'async',
      'alt' => esc_attr( get_post_meta($id, '_wp_attachment_image_alt', true) ?: $alt )
    ]
  );
}

$heading = get_field('reviews_heading', $ctx) ?: 'Customer Reviews';

$bg   = get_field('reviews_bg_color', $ctx) ?: '#fff';
$txt  = get_field('reviews_text_color', $ctx) ?: '#111827';
$divc = get_field('reviews_divider_color', $ctx) ?: '#E5E7EB';

$reviews = [
  [
    'name'  => get_field('r1_name', $ctx) ?: 'Anoushka Pandey',
    'text'  => get_field('r1_text', $ctx) ?: 'My lips stay hydrated for hours, and the subtle tint is just perfect. Will buy again!',
    'stars' => mz_int_range(get_field('r1_stars', $ctx), 1, 5, 5),
    'img'   => get_field('r1_avatar', $ctx),
  ],
  [
    'name'  => get_field('r2_name', $ctx) ?: 'Priya Mehta',
    'text'  => get_field('r2_text', $ctx) ?: 'Love this lip balm! It adds a natural color while protecting my lips from the sun.',
    'stars' => mz_int_range(get_field('r2_stars', $ctx), 1, 5, 5),
    'img'   => get_field('r2_avatar', $ctx),
  ],
  [
    'name'  => get_field('r3_name', $ctx) ?: 'Riya Saini',
    'text'  => get_field('r3_text', $ctx) ?: 'Amazing product! Keeps my lips soft and hydrated! 👍👍',
    'stars' => mz_int_range(get_field('r3_stars', $ctx), 1, 5, 5),
    'img'   => get_field('r3_avatar', $ctx),
  ],
  [
    'name'  => get_field('r4_name', $ctx) ?: 'Lifsu Chia',
    'text'  => get_field('r4_text', $ctx) ?: 'No animal testing, 100% vegan & safe.',
    'stars' => mz_int_range(get_field('r4_stars', $ctx), 1, 5, 5),
    'img'   => get_field('r4_avatar', $ctx),
  ],
];
?>

<style>
    #reviews h2{
        font-weight:700 !important;
    }
</style>

<section class="mz-w-full mz-relative" id="reviews" style="background-color: <?php echo esc_attr($bg); ?>;">
  <div class="mz-max-w-[1000px] mz-mx-auto mz-px-4 mz-py-12 md:mz-py-16 xl:mz-py-20">

    <div class="mz-text-center mz-mb-8 md:mz-mb-10">
      <h2 class="mz-text-[22px] sm:mz-text-[24px] md:mz-text-[30px] xl:mz-text-[30px] mz-leading-[1.08] mz-font-extrabold mz-tracking-tight "
          style="color: <?php echo esc_attr($txt); ?>;">
        <?php echo esc_html($heading); ?>
      </h2>
    </div>

    <div class="mz-relative">
      <!-- Center divider only on md+ -->
      <span class="mz-hidden md:mz-block mz-absolute mz-left-1/2 mz-top-0 mz-bottom-0 mz-w-px"
            style="background-color: <?php echo esc_attr($divc); ?>;"></span>

      <div class="mz-grid mz-grid-cols-1 mz-place-items-center md:mz-grid-cols-2 mz-gap-6 md:mz-gap-12
      mz-px-5 lg:mz-gap-x-[100px]
      ">
        <?php foreach ($reviews as $i => $r): ?>
          <div class="mz-flex mz-flex-col mz-justify-center mz-gap-4 md:mz-gap-5 mz-items-center">
            <!-- Avatar -->
            <div class="mz-shrink-0">
              <div class="mz-w-16 mz-h-16 md:mz-w-[72px] md:mz-h-[72px] mz-rounded-full mz-overflow-hidden mz-bg-white mz-ring-2 mz-ring-white mz-shadow-sm">
                <?php
                  $img = mz_avatar_img($r['img'], $r['name']);
                  echo $img ? $img : '<div class="mz-w-full mz-h-full mz-flex mz-items-center mz-justify-center mz-text-[12px] mz-font-bold mz-text-[#9B4A6A] mz-bg-[#F6EFEA]">'
                    . esc_html(mb_substr($r['name'], 0, 1)) .
                    '</div>';
                ?> 
              </div>
            </div>

            <!-- Content -->
            <div class="mz-min-w-0">
              <div class="mz-flex mz-items-center mz-gap-2 mz-flex-wrap mz-justify-center">
                <h3 class="mz-text-[16px] md:mz-text-[18px] mz-font-extrabold mz-leading-snug"
                    style="color: <?php echo esc_attr($txt); ?>;">
                  <?php echo esc_html($r['name']); ?>
                </h3>
              </div>

              <!-- Stars -->
              <div class="mz-mt-1 mz-flex mz-items-center mz-justify-center mz-gap-[0px]" aria-label="<?php echo esc_attr($r['stars']); ?> star rating">
                <?php for ($s = 1; $s <= 5; $s++): ?>
                  <svg class="mz-w-4 mz-h-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M12 17.3l-5.3 3 1.4-6.1L3 9.7l6.2-.5L12 3.5l2.8 5.7 6.2.5-5.1 4.5 1.4 6.1-5.3-3Z"
                          fill="<?php echo ($s <= (int)$r['stars']) ? '#F59E0B' : '#E5E7EB'; ?>"/>
                  </svg>
                <?php endfor; ?>
              </div>

              <p class="mz-mt-2 mz-text-[13px] sm:mz-text-[14px] mz-text-center  md:mz-text-[15px] mz-leading-6 md:mz-leading-7 mz-text-[#374151]">
                <?php echo esc_html($r['text']); ?>
              </p>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

  </div>
</section>