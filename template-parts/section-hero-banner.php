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

    <div class="mz-max-w-[1290px] mz-mx-auto mz-px-4 mz-py-10 md:mz-py-24 mz-grid md:mz-grid-cols-2 mz-gap-10 mz-items-center
    xl:mz-px-0
    ">

      <!-- Product -->
      <?php if ($product): ?>
        <div class="mz-flex mz-justify-center mz-order-2">
          <img src="<?= $product ?>" alt="Product"
               class="mz-max-h-[420px] xl:mz-max-h-[580px] mz-w-auto">
        </div>
      <?php endif; ?>

      <!-- Content -->
      <div class="mz-text-white mz-order-1 mz-text-center md:mz-text-left md:mz-order-2">
        <?php if ($k = get_field('banner_kicker')): ?> 
          <p class="mz-uppercase mz-font-heading   mz-tracking-widest mz-text-sm mz-mb-3"><?= esc_html($k) ?></p>
        <?php endif; ?>

        <?php if ($h = get_field('banner_heading')): ?>
          <h1 class="mz-text-[48px] mz-max-w-[200px] md:mz-max-w-full mz-mx-auto mz-leading-[45px] md:mz-text-5xl  mz-font-bold mz-mb-6
          mz-text-white md:mz-text-[60px] xl:mz-text-[100px] xl:mz-px-0

          ">
            <?= esc_html($h) ?>
          </h1>
        <?php endif; ?>

        <?php if ($cta = get_field('cta_text')): ?>
          <a href="<?= esc_url(get_field('cta_link')) ?>"
             class="mz-inline-block mz-bg-primary mz-bg-brand-accent mz-text-white mz-px-5 mz-py-3 mz-rounded-lg hover:mz-bg-opacity-90 mz-transition
             mz-text-sm mz-font-bold hover:mz-bg-brand-primary hover:mz-text-white
            md:mz-min-w-[140px] md:mz-py-4 md:mz-text-center xl:mz-min-w-[150px] xl:mz-py-[18px]  xl:mz-text-center xl:mz-text-[15px] xl:mz-rounded-xl
             ">
            <?= esc_html($cta) ?>
          </a>
        <?php endif; ?>
      </div>

    </div>
  </div>
<?php endif; ?>

</section>
