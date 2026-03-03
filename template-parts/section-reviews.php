<?php
/**
 * ACF (Free) fields to create (Options page OR Home page):
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
 * - reviews_button_text (text)  // optional
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

if (!function_exists('mz_int_range')) {
  function mz_int_range($v, $min = 1, $max = 5, $fallback = 5){
    $n = (int)$v;
    if ($n < $min || $n > $max) return (int)$fallback;
    return $n;
  }
}

if (!function_exists('mz_avatar_img')) {
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
}

$heading = get_field('reviews_heading', $ctx) ?: 'Customer Reviews';
$btn_text = get_field('reviews_button_text', $ctx) ?: 'View All Reviews';

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

// Mobile = first 2 only
$mobile_reviews = array_slice($reviews, 0, 2);
// Desktop = first 4
$desktop_reviews = array_slice($reviews, 0, 4);
// Modal = all
$all_reviews = $reviews;
?>

<style>
  #reviews h2{ font-weight: 700 !important; }
</style>

<section class="mz-w-full mz-relative" id="reviews" style="background-color: <?php echo esc_attr($bg); ?>;">
  <div class="mz-max-w-[1000px] mz-mx-auto mz-px-4 mz-py-10 md:mz-py-14 xl:mz-py-16">

    <div class="mz-text-center mz-mb-6 md:mz-mb-8">
      <h2 class="mz-text-[22px] sm:mz-text-[24px] md:mz-text-[30px] xl:mz-text-[30px] mz-leading-[1.08] mz-font-extrabold mz-tracking-tight"
          style="color: <?php echo esc_attr($txt); ?>;">
        <?php echo esc_html($heading); ?>
      </h2>
    </div>

    <!-- MOBILE: ONLY 2 REVIEWS -->
    <div class="mz-block md:mz-hidden">
      <div class="mz-grid mz-grid-cols-1 mz-place-items-center mz-gap-6 mz-px-2">
        <?php foreach ($mobile_reviews as $r): ?>
          <div class="mz-flex mz-flex-col mz-justify-center mz-gap-4 mz-items-center">
            <div class="mz-shrink-0">
              <div class="mz-w-16 mz-h-16 mz-rounded-full mz-overflow-hidden mz-bg-white mz-ring-2 mz-ring-white mz-shadow-sm">
                <?php
                  $img = mz_avatar_img($r['img'], $r['name']);
                  echo $img ? $img : '<div class="mz-w-full mz-h-full mz-flex mz-items-center mz-justify-center mz-text-[12px] mz-font-bold mz-text-[#9B4A6A] mz-bg-[#F6EFEA]">'
                    . esc_html(mb_substr($r['name'], 0, 1)) .
                    '</div>';
                ?>
              </div>
            </div>

            <div class="mz-min-w-0">
              <h3 class="mz-text-[16px] mz-font-extrabold mz-leading-snug mz-text-center"
                  style="color: <?php echo esc_attr($txt); ?>;">
                <?php echo esc_html($r['name']); ?>
              </h3>

              <div class="mz-mt-1 mz-flex mz-items-center mz-justify-center mz-gap-0" aria-label="<?php echo esc_attr($r['stars']); ?> star rating">
                <?php for ($s = 1; $s <= 5; $s++): ?>
                  <svg class="mz-w-4 mz-h-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M12 17.3l-5.3 3 1.4-6.1L3 9.7l6.2-.5L12 3.5l2.8 5.7 6.2.5-5.1 4.5 1.4 6.1-5.3-3Z"
                          fill="<?php echo ($s <= (int)$r['stars']) ? '#F59E0B' : '#E5E7EB'; ?>"/>
                  </svg>
                <?php endfor; ?>
              </div>

              <p class="mz-mt-2 mz-text-[13px] sm:mz-text-[14px] mz-leading-6 mz-text-center mz-text-[#374151]">
                <?php echo esc_html($r['text']); ?>
              </p>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- DESKTOP: SHOW 4 REVIEWS -->
    <div class="mz-hidden md:mz-block">
      <div class="mz-relative">
        <!-- Optional center divider (works for 2-column layout even with 4 items) -->
        <span class="mz-absolute mz-left-1/2 mz-top-0 mz-bottom-0 mz-w-px"
              style="background-color: <?php echo esc_attr($divc); ?>;"></span>

        <div class="mz-grid md:mz-grid-cols-2 mz-gap-10 lg:mz-gap-x-[100px] mz-px-5">
          <?php foreach ($desktop_reviews as $r): ?>
            <div class="mz-flex mz-flex-col mz-justify-center mz-gap-5 mz-items-center">
              <div class="mz-shrink-0">
                <div class="mz-w-[72px] mz-h-[72px] mz-rounded-full mz-overflow-hidden mz-bg-white mz-ring-2 mz-ring-white mz-shadow-sm">
                  <?php
                    $img = mz_avatar_img($r['img'], $r['name']);
                    echo $img ? $img : '<div class="mz-w-full mz-h-full mz-flex mz-items-center mz-justify-center mz-text-[12px] mz-font-bold mz-text-[#9B4A6A] mz-bg-[#F6EFEA]">'
                      . esc_html(mb_substr($r['name'], 0, 1)) .
                      '</div>';
                  ?>
                </div>
              </div>

              <div class="mz-min-w-0">
                <h3 class="mz-text-[18px] mz-font-extrabold mz-leading-snug mz-text-center"
                    style="color: <?php echo esc_attr($txt); ?>;">
                  <?php echo esc_html($r['name']); ?>
                </h3>

                <div class="mz-mt-1 mz-flex mz-items-center mz-justify-center mz-gap-0" aria-label="<?php echo esc_attr($r['stars']); ?> star rating">
                  <?php for ($s = 1; $s <= 5; $s++): ?>
                    <svg class="mz-w-4 mz-h-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                      <path d="M12 17.3l-5.3 3 1.4-6.1L3 9.7l6.2-.5L12 3.5l2.8 5.7 6.2.5-5.1 4.5 1.4 6.1-5.3-3Z"
                            fill="<?php echo ($s <= (int)$r['stars']) ? '#F59E0B' : '#E5E7EB'; ?>"/>
                    </svg>
                  <?php endfor; ?>
                </div>

                <p class="mz-mt-2 mz-text-[14px] mz-leading-7 mz-text-center mz-text-[#374151]">
                  <?php echo esc_html($r['text']); ?>
                </p>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <!-- View all button (KEEP for both) -->
    <div class="mz-mt-8 mz-flex mz-justify-cente  lg:mz-hidden">
      <button
        type="button"
        class="mz-inline-flex mz-items-center mz-justify-center mz-gap-2 mz-bg-brand-accent mz-text-white mz-font-extrabold mz-rounded-xl mz-px-4 mz-py-3 md:mz-py-3.5 mz-text-[13px] sm:mz-text-[14px] md:mz-text-[15px] hover:mz-bg-brand-primary hover:mz-text-white mz-transition"
        data-mz-open-reviews
        aria-haspopup="dialog"
        aria-controls="mz-reviews-modal"
      >
        <?php echo esc_html($btn_text); ?>
      </button>
    </div>

  </div>

  <!-- MODAL: ALL REVIEWS -->
  <div
    id="mz-reviews-modal"
    class="mz-fixed mz-inset-0 mz-z-[9999] mz-hidden"
    aria-hidden="true"
    role="dialog"
    aria-modal="true"
  >
    <div class="mz-absolute mz-inset-0 mz-bg-black/50" data-mz-close-reviews></div>

    <div class="mz-relative mz-flex mz-min-h-full mz-items-center mz-justify-center mz-p-3 md:mz-p-6">
      <div class="mz-w-full md:mz-max-w-[900px] mz-rounded-2xl mz-bg-white mz-shadow-xl mz-overflow-hidden">

        <div class="mz-flex mz-items-center mz-justify-between mz-gap-3 mz-px-4 md:mz-px-6 mz-py-4 mz-border-b mz-border-gray-200">
          <div class="mz-text-[16px] md:mz-text-[18px] mz-font-extrabold mz-text-gray-900">
            <?php echo esc_html($heading); ?>
          </div>

          <button
            type="button"
            class="mz-inline-flex mz-h-10 mz-w-10 mz-items-center mz-justify-center mz-rounded-xl mz-border mz-border-gray-200 mz-bg-white mz-text-gray-700"
            data-mz-close-reviews
            aria-label="Close reviews"
          >
            <svg class="mz-h-5 mz-w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
          </button>
        </div>

        <div class="mz-max-h-[70vh] md:mz-max-h-[75vh] mz-overflow-y-auto mz-px-4 md:mz-px-6 mz-py-5">
          <div class="mz-grid mz-grid-cols-1 md:mz-grid-cols-2 mz-gap-6 md:mz-gap-10">
            <?php foreach ($all_reviews as $r): ?>
              <div class="mz-flex mz-gap-4 mz-items-start">
                <div class="mz-shrink-0">
                  <div class="mz-w-12 mz-h-12 md:mz-w-14 md:mz-h-14 mz-rounded-full mz-overflow-hidden mz-bg-white mz-ring-1 mz-ring-gray-200">
                    <?php
                      $img = mz_avatar_img($r['img'], $r['name']);
                      echo $img ? $img : '<div class="mz-w-full mz-h-full mz-flex mz-items-center mz-justify-center mz-text-[12px] mz-font-bold mz-text-[#9B4A6A] mz-bg-[#F6EFEA]">'
                        . esc_html(mb_substr($r['name'], 0, 1)) .
                        '</div>';
                    ?>
                  </div>
                </div>

                <div class="mz-min-w-0">
                  <div class="mz-font-extrabold mz-text-[15px] md:mz-text-[16px] mz-text-gray-900">
                    <?php echo esc_html($r['name']); ?>
                  </div>

                  <div class="mz-mt-1 mz-flex mz-items-center mz-gap-0" aria-label="<?php echo esc_attr($r['stars']); ?> star rating">
                    <?php for ($s = 1; $s <= 5; $s++): ?>
                      <svg class="mz-w-4 mz-h-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M12 17.3l-5.3 3 1.4-6.1L3 9.7l6.2-.5L12 3.5l2.8 5.7 6.2.5-5.1 4.5 1.4 6.1-5.3-3Z"
                              fill="<?php echo ($s <= (int)$r['stars']) ? '#F59E0B' : '#E5E7EB'; ?>"/>
                      </svg>
                    <?php endfor; ?>
                  </div>

                  <p class="mz-mt-2 mz-text-[13px] md:mz-text-[14px] mz-leading-6 mz-text-gray-700">
                    <?php echo esc_html($r['text']); ?>
                  </p>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

      </div>
    </div>
  </div>

</section>

<script>
(function () {
  const modal = document.getElementById('mz-reviews-modal');
  if (!modal) return;

  const openBtn = document.querySelector('[data-mz-open-reviews]');
  const closeBtns = modal.querySelectorAll('[data-mz-close-reviews]');
  let lastActiveEl = null;

  function openModal() {
    lastActiveEl = document.activeElement;
    modal.classList.remove('mz-hidden');
    modal.setAttribute('aria-hidden', 'false');
    document.documentElement.classList.add('mz-overflow-hidden');
    document.body.classList.add('mz-overflow-hidden');

    const closeBtn = modal.querySelector('[data-mz-close-reviews]');
    if (closeBtn) closeBtn.focus();
  }

  function closeModal() {
    modal.classList.add('mz-hidden');
    modal.setAttribute('aria-hidden', 'true');
    document.documentElement.classList.remove('mz-overflow-hidden');
    document.body.classList.remove('mz-overflow-hidden');

    if (lastActiveEl && typeof lastActiveEl.focus === 'function') {
      lastActiveEl.focus();
    }
  }

  if (openBtn) openBtn.addEventListener('click', openModal);
  closeBtns.forEach(btn => btn.addEventListener('click', closeModal));

  document.addEventListener('keydown', function (e) {
    if (modal.classList.contains('mz-hidden')) return;
    if (e.key === 'Escape') closeModal();
  });
})();
</script>   