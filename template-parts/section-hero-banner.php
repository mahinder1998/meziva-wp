<?php
/**
 * SECTION: Meziva Hero Banner
 * Tailwind Prefix: mz-
 * ACF Required
 */

if (!function_exists('get_field')) return;

$type = get_field('banner_type') ?: 'option_b';

/* Helper */
function mz_img($img) {
  return is_array($img) && !empty($img['url']) ? esc_url($img['url']) : '';
}
?>

<section class="mz-w-full mz-relative mz-overflow-hidden">

<?php if ($type === 'option_a') : 
  $d = mz_img(get_field('banner_image_desktop'));
  $m = mz_img(get_field('banner_image_mobile'));
  $url = esc_url(get_field('banner_link'));
?>
  <a href="<?= $url ?>" class="mz-block mz-w-full">
    <picture>
      <?php if ($m): ?>
        <source media="(max-width: 767px)" srcset="<?= $m ?>">
      <?php endif; ?>
      <?php if ($d): ?>
        <img src="<?= $d ?>" alt="Banner" class="mz-w-full mz-h-auto">
      <?php endif; ?>
    </picture>
  </a>

<?php else : 
  $bg_d = mz_img(get_field('bg_desktop'));
  $bg_m = mz_img(get_field('bg_mobile'));
  $product = mz_img(get_field('product_image'));
?>
  <div class="mz-relative mz-w-full">
    <picture class="mz-absolute mz-inset-0 mz--z-10">
      <?php if ($bg_m): ?>
        <source media="(max-width: 767px)" srcset="<?= $bg_m ?>">
      <?php endif; ?>
      <?php if ($bg_d): ?>
        <img src="<?= $bg_d ?>" alt="" class="mz-w-full mz-h-full mz-object-cover">
      <?php endif; ?>
    </picture>

    <div class="mz-max-w-[1200px] mz-mx-auto mz-px-4 mz-py-16 md:mz-py-24 mz-grid md:mz-grid-cols-2 mz-gap-10 mz-items-center">

      <!-- Product -->
      <?php if ($product): ?>
        <div class="mz-flex mz-justify-center">
          <img src="<?= $product ?>" alt="Product"
               class="mz-max-h-[420px] mz-w-auto">
        </div>
      <?php endif; ?>

      <!-- Content -->
      <div class="mz-text-white">
        <?php if ($k = get_field('banner_kicker')): ?>
          <p class="mz-uppercase mz-tracking-widest mz-text-sm mz-mb-3"><?= esc_html($k) ?></p>
        <?php endif; ?>

        <?php if ($h = get_field('banner_heading')): ?>
          <h1 class="mz-text-4xl md:mz-text-5xl mz-font-bold mz-leading-tight mz-mb-6">
            <?= esc_html($h) ?>
          </h1>
        <?php endif; ?>

        <?php if ($cta = get_field('cta_text')): ?>
          <a href="<?= esc_url(get_field('cta_link')) ?>"
             class="mz-inline-block mz-bg-primary mz-text-white mz-px-8 mz-py-3 mz-rounded-lg hover:mz-bg-opacity-90 mz-transition">
            <?= esc_html($cta) ?>
          </a>
        <?php endif; ?>
      </div>

    </div>
  </div>
<?php endif; ?>

</section>
