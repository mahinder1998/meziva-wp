<?php
/**
 * SECTION: Meziva Hero Banner (No Crop + Mobile 100% width + Auto height)
 *
 * Requirements:
 * - Desktop banner: 1800x600 (3:1) → keep visual ratio on desktop
 * - Mobile banner: 800x549 → width 100%, height auto (natural) → NO crop
 * - No min-height on mobile
 * - Use wp srcset/sizes when attachment ID available
 * - LCP: preload + fetchpriority
 */

if (!function_exists('get_field')) return;

$type = get_field('banner_type') ?: 'option_b';

/**
 * Safely get image data from ACF field (Array or ID)
 */
if (!function_exists('mz_get_img_data')) {
  function mz_get_img_data($img) {
    $data = [
      'id' => 0,
      'url' => '',
      'alt' => '',
      'w' => 0,
      'h' => 0,
      'srcset' => '',
      'sizes' => '',
    ];

    if (is_array($img)) {
      $data['id']  = !empty($img['ID']) ? (int)$img['ID'] : 0;
      $data['url'] = !empty($img['url']) ? esc_url($img['url']) : '';
      $data['alt'] = !empty($img['alt']) ? esc_attr($img['alt']) : '';
      $data['w']   = !empty($img['width']) ? (int)$img['width'] : 0;
      $data['h']   = !empty($img['height']) ? (int)$img['height'] : 0;
    } elseif (is_numeric($img)) {
      $data['id']  = (int)$img;
      $data['url'] = esc_url(wp_get_attachment_image_url($data['id'], 'full'));
      $data['alt'] = esc_attr(get_post_meta($data['id'], '_wp_attachment_image_alt', true));
      $meta = wp_get_attachment_metadata($data['id']);
      if (!empty($meta['width']))  $data['w'] = (int)$meta['width'];
      if (!empty($meta['height'])) $data['h'] = (int)$meta['height'];
    }

    // Srcset/sizes
    if ($data['id']) {
      $data['srcset'] = wp_get_attachment_image_srcset($data['id'], 'full') ?: '';
      // desktop max 1800 as per banner
      $data['sizes']  = '(max-width: 767px) 100vw, 1800px';
    }

    return $data;
  }
}

/**
 * Preload helper (runs in <head>)
 */
if (!function_exists('mz_preload_image')) {
  function mz_preload_image($url) {
    if (!$url) return;
    add_action('wp_head', function() use ($url) {
      echo '<link rel="preload" as="image" href="' . esc_url($url) . '" fetchpriority="high">' . "\n";
    }, 1);
  }
}

?>

<section class="mz-w-full mz-relative mz-overflow-hidden">

<?php if ($type === 'option_a') : 
  $d = mz_get_img_data(get_field('banner_image_desktop'));
  $m = mz_get_img_data(get_field('banner_image_mobile'));
  $url = esc_url(get_field('banner_link'));

  // Preload: prefer mobile for small screens, else desktop
  if (wp_is_mobile() && !empty($m['url'])) mz_preload_image($m['url']);
  else if (!empty($d['url'])) mz_preload_image($d['url']);
?>

  <a href="<?php echo $url ? $url : '#'; ?>" class="mz-block mz-w-full" aria-label="Hero banner link">
    <picture class="mz-block mz-w-full">

      <?php if (!empty($m['url'])): ?>
        <!-- Mobile: 100% width, natural height (no crop) -->
        <source
          media="(max-width: 767px)"
          srcset="<?php echo esc_attr($m['srcset'] ?: $m['url']); ?>"
          sizes="100vw"
        >
      <?php endif; ?>

      <?php if (!empty($d['url'])): ?>
        <!-- Desktop: keep 1800x600 behavior; Mobile will still be natural height -->
        <img
          src="<?php echo esc_url($d['url']); ?>"
          <?php if (!empty($d['srcset'])): ?>srcset="<?php echo esc_attr($d['srcset']); ?>"<?php endif; ?>
          sizes="(max-width: 767px) 100vw, 1800px"
          alt="<?php echo $d['alt'] ?: 'Banner'; ?>"
          width="1800"
          height="600"
          class="mz-w-full mz-h-auto mz-block"
          loading="eager"
          decoding="async"
          fetchpriority="high"
        >
      <?php endif; ?>

    </picture>
  </a>

<?php else : 
  $bg_d    = mz_get_img_data(get_field('bg_desktop'));
  $bg_m    = mz_get_img_data(get_field('bg_mobile'));
  $product = mz_get_img_data(get_field('product_image'));

  // Preload background (LCP)
  if (wp_is_mobile() && !empty($bg_m['url'])) mz_preload_image($bg_m['url']);
  else if (!empty($bg_d['url'])) mz_preload_image($bg_d['url']);
?>

  <!-- On mobile: height auto via image
       On desktop: we keep a 3:1 ratio canvas using aspect on the container -->
  <div class="mz-relative mz-w-full md:mz-aspect-[3/1]">

    <!-- Background -->
    <picture class="mz-block md:mz-absolute md:mz-inset-0 mz--z-10">
      <?php if (!empty($bg_m['url'])): ?>
        <source
          media="(max-width: 767px)"
          srcset="<?php echo esc_attr($bg_m['srcset'] ?: $bg_m['url']); ?>"
          sizes="100vw"
        >
      <?php endif; ?>

      <?php if (!empty($bg_d['url'])): ?>
        <img
          src="<?php echo esc_url($bg_d['url']); ?>"
          <?php if (!empty($bg_d['srcset'])): ?>srcset="<?php echo esc_attr($bg_d['srcset']); ?>"<?php endif; ?>
          sizes="(max-width: 767px) 100vw, 1800px"
          alt=""
          width="1800"
          height="600"
          class="mz-w-full mz-h-auto md:mz-h-full md:mz-w-full md:mz-object-contain mz-block"
          loading="eager"
          decoding="async"
          fetchpriority="high"
        >
      <?php endif; ?>
    </picture>

    <!-- Content layer -->
    <div class="mz-relative mz-max-w-[1290px] mz-mx-auto mz-px-4 mz-py-10 md:mz-py-0 md:mz-h-full md:mz-flex md:mz-items-center xl:mz-px-0">
      <div class="mz-w-full mz-grid md:mz-grid-cols-2 mz-gap-10 mz-items-center">

        <!-- Product -->
        <?php if (!empty($product['url'])): ?>
          <div class="mz-flex mz-justify-center mz-order-2 md:mz-order-2">
            <img
              src="<?php echo esc_url($product['url']); ?>"
              <?php if (!empty($product['srcset'])): ?>srcset="<?php echo esc_attr($product['srcset']); ?>"<?php endif; ?>
              sizes="(max-width: 767px) 70vw, 520px"
              alt="<?php echo $product['alt'] ?: 'Product'; ?>"
              width="<?php echo $product['w'] ?: 800; ?>"
              height="<?php echo $product['h'] ?: 800; ?>"
              class="mz-max-h-[420px] xl:mz-max-h-[580px] mz-w-auto mz-block"
              loading="eager"
              decoding="async"
              fetchpriority="high"
              style="aspect-ratio: 1 / 1;"
            >
          </div>
        <?php endif; ?>

        <!-- Content -->
        <div class="mz-text-white mz-order-1 md:mz-order-1 mz-text-center md:mz-text-left">
          <?php if ($k = get_field('banner_kicker')): ?> 
            <p class="mz-uppercase mz-font-heading mz-tracking-widest mz-text-sm mz-mb-3"><?php echo esc_html($k); ?></p>
          <?php endif; ?>

          <?php if ($h = get_field('banner_heading')): ?>
            <h1 class="mz-text-[48px] mz-max-w-[200px] md:mz-max-w-full mz-mx-auto mz-leading-[45px] md:mz-text-5xl mz-font-bold mz-mb-6 mz-text-white md:mz-text-[60px] xl:mz-text-[100px] xl:mz-px-0">
              <?php echo esc_html($h); ?>
            </h1>
          <?php endif; ?>

          <?php if ($cta = get_field('cta_text')): ?>
            <a href="<?php echo esc_url(get_field('cta_link')); ?>"
              class="mz-inline-block mz-bg-primary mz-bg-brand-accent mz-text-white mz-px-5 mz-py-3 mz-rounded-lg hover:mz-bg-opacity-90 mz-transition mz-text-sm mz-font-bold hover:mz-bg-brand-primary hover:mz-text-white md:mz-min-w-[140px] md:mz-py-4 md:mz-text-center xl:mz-min-w-[150px] xl:mz-py-[18px] xl:mz-text-center xl:mz-text-[15px] xl:mz-rounded-xl">
              <?php echo esc_html($cta); ?>
            </a>
          <?php endif; ?>
        </div>

      </div>
    </div>

  </div>
<?php endif; ?>

</section>
