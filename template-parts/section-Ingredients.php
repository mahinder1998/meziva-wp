<?php
/**
 * Section: Ingredients / Benefits
 * ACF FREE (Fixed 4 Features)
 * Tailwind Prefix: mz-
 * UPDATE: Feature icons now use ACF Image Upload (no SVG echo)
 */

if ( ! defined('ABSPATH') ) exit;
if ( ! function_exists('get_field') ) return;

$section_bg         = get_field('section_bg');          // Color Picker
$section_text_color = get_field('section_text_color');  // Color Picker

$heading     = get_field('heading');
$sub_heading = get_field('sub_heading');
$right_image = get_field('right_image'); // ACF image array (recommended: return array)

// Features (ICON fields should be ACF Image fields now)
$f1_icon  = get_field('feature_1_icon');
$f1_title = get_field('feature_1_title');
$f1_desc  = get_field('feature_1_description');

$f2_icon  = get_field('feature_2_icon');
$f2_title = get_field('feature_2_title');
$f2_desc  = get_field('feature_2_description');

$f3_icon  = get_field('feature_3_icon');
$f3_title = get_field('feature_3_title');
$f3_desc  = get_field('feature_3_description');

$f4_icon  = get_field('feature_4_icon');
$f4_title = get_field('feature_4_title');
$f4_desc  = get_field('feature_4_description');

$features = [
  ['icon' => $f1_icon, 'title' => $f1_title, 'desc' => $f1_desc],
  ['icon' => $f2_icon, 'title' => $f2_title, 'desc' => $f2_desc],
  ['icon' => $f3_icon, 'title' => $f3_title, 'desc' => $f3_desc],
  ['icon' => $f4_icon, 'title' => $f4_title, 'desc' => $f4_desc],
];

// Fallbacks
$bg_color   = !empty($section_bg) ? $section_bg : '#2f6f44';
$text_color = !empty($section_text_color) ? $section_text_color : '#ffffff';

// ✅ Right image optimization (no layout change)
$ri_src    = '';
$ri_srcset = '';
$ri_sizes  = '(max-width: 767px) 90vw, 460px';
$ri_alt    = '';
$ri_w      = '';
$ri_h      = '';

if (!empty($right_image) && is_array($right_image)) {
  $ri_src    = $right_image['sizes']['large'] ?? $right_image['url'];
  $ri_alt    = $right_image['alt'] ?? '';
  $ri_w      = $right_image['sizes']['large-width']  ?? '';
  $ri_h      = $right_image['sizes']['large-height'] ?? '';

  if (!empty($right_image['ID'])) {
    $ri_srcset = wp_get_attachment_image_srcset((int)$right_image['ID'], 'large');
  }
}

/**
 * ✅ Helper: render ACF image icon safely
 * Supports: ACF Image Array, Attachment ID, or direct URL string (fallback).
 */
function mz_render_feature_icon($acf_icon, $classes = 'mz-w-full mz-h-full', $size = 'thumbnail') {
  // ACF Image Array
  if (is_array($acf_icon)) {
    $id  = !empty($acf_icon['ID']) ? (int)$acf_icon['ID'] : 0;
    $alt = !empty($acf_icon['alt']) ? $acf_icon['alt'] : 'Feature icon';

    if ($id) {
      return wp_get_attachment_image(
        $id,
        $size,
        false,
        [
          'class'    => $classes,
          'alt'      => $alt,
          'loading'  => 'lazy',
          'decoding' => 'async',
        ]
      );
    }

    // if no ID but URL exists
    if (!empty($acf_icon['url'])) {
      $url = esc_url($acf_icon['url']);
      return '<img src="'.$url.'" alt="'.esc_attr($alt).'" class="'.esc_attr($classes).'" loading="lazy" decoding="async" />';
    }
  }

  // Attachment ID
  if (is_numeric($acf_icon) && (int)$acf_icon > 0) {
    return wp_get_attachment_image(
      (int)$acf_icon,
      $size,
      false,
      [
        'class'    => $classes,
        'alt'      => 'Feature icon',
        'loading'  => 'lazy',
        'decoding' => 'async',
      ]
    );
  }

  // Direct URL string
  if (is_string($acf_icon) && trim($acf_icon) !== '') {
    $url = esc_url(trim($acf_icon));
    return '<img src="'.$url.'" alt="Feature icon" class="'.esc_attr($classes).'" loading="lazy" decoding="async" />';
  }

  return '';
}
?>

<section
  id="ingredients"
  class="mz-w-full mz-relative"
  style="background-color: <?php echo esc_attr($bg_color); ?>; color: <?php echo esc_attr($text_color); ?>;"
>
  <div class="mz-max-w-[1290px] mz-mx-auto mz-px-4 md:mz-px-6 mz-py-[60px] md:mz-py-20 xl:mz-px-0">

    <div class="mz-grid mz-grid-cols-1 lg:mz-grid-cols-2 mz-gap-12 lg:mz-gap-20 mz-items-start xl:mz-flex">

      <!-- LEFT : FEATURES -->
      <div class="mz-order-2 lg:mz-order-2 lg:mz-col-span-2 xl:mz-order-1 xl:mz-w-[66%]">
        <div class="mz-grid mz-grid-cols-1 sm:mz-grid-cols-2 mz-gap-10 md:mz-gap-12 xl:mz-gap-12">

          <?php foreach ($features as $item): ?>
            <?php
              $title = trim((string)($item['title'] ?? ''));
              $desc  = $item['desc'] ?? '';
              $icon  = $item['icon'] ?? null;

              if ($title === '' && empty($desc) && empty($icon)) continue;

              $icon_html = mz_render_feature_icon(
                $icon,
                'mz-w-full mz-h-full mz-object-contain',
                'thumbnail' // change to 'medium' if your icons are bigger
              );
            ?>
            <div class="mz-text-center mz-grid mz-gap-3 xl:mz-gap-3 md:mz-text-left">

              <!-- ICON (ACF Image Upload) -->
              <?php if (!empty($icon_html)): ?>
                <div class="mz-w-[100px] mz-h-[60px] mz-mx-auto md:mz-ml-0">
                  <?php echo $icon_html; ?>
                </div>
              <?php else: ?>
                <!-- Optional fallback (remove if you want blank) -->
                <div class="mz-w-[100px] mz-h-[60px] mz-mx-auto md:mz-ml-0 mz-opacity-50">
                  <div class="mz-w-full mz-h-full mz-rounded-full mz-bg-white/20"></div>
                </div>
              <?php endif; ?>

              <!-- TITLE -->
              <?php if ($title !== ''): ?>
                <h3
                  class="mz-text-2xl mz-font-heading md:mz-text-2xl mz-font-semibold mz-leading-snug xl:mz-pt-3"
                  style="color: <?php echo esc_attr($text_color); ?>;"
                >
                  <?php echo esc_html($title); ?>
                </h3>
              <?php endif; ?>

              <!-- DESCRIPTION -->
              <?php if (!empty($desc)): ?>
                <div class="mz-text-base md:mz-text-[15px] xl:mz-text-[16px] mz-leading-relaxed mz-px-4 md:mz-px-0">
                  <?php echo wp_kses_post($desc); ?>
                </div>
              <?php endif; ?>

            </div>
          <?php endforeach; ?>

        </div>
      </div>

      <!-- RIGHT : CONTENT -->
      <div class="section-header md:mz-col-span-2 mz-order-1 lg:mz-order-1 md:mz-text-center xl:md:mz-col-span-1 xl:mz-text-left xl:mz-w-1/3 xl:mz-mt-0">
        <div class="lg:mz-order-2 mz-text-center xl:mz-text-left mz-relative">

          <?php if (!empty($heading)): ?>
            <h2
              class="mz-text-[36px] xl:mz-text-[50px] mz-leading-[1.05] mz-font-extrabold mz-tracking-tight mz-mb-8 xl:mz-mb-5"
              style="color: <?php echo esc_attr($text_color); ?>;"
            >
              <?php echo wp_kses($heading, ['br' => []]); ?>
            </h2>
          <?php endif; ?>

          <?php if (!empty($sub_heading)): ?>
            <p
              class="mz-mt-4 mz-text-[18px] md:mz-text-[18px] mz-font-semibold"
              style="color: <?php echo esc_attr($text_color); ?>;"
            >
              <?php echo esc_html($sub_heading); ?>
            </p>
          <?php endif; ?>

          <?php if ($ri_src): ?>
            <div class="mz-hidden mz-relative mz-mt-4 mz-w-full mz-max-w-[420px] lg:mz-max-w-[460px] md:mz-hidden xl:mz-block mz-mx-auto xl:mz-absolute xl:mz-top-[180px]">
              <img
                src="<?php echo esc_url($ri_src); ?>"
                <?php if (!empty($ri_srcset)): ?>srcset="<?php echo esc_attr($ri_srcset); ?>"<?php endif; ?>
                sizes="<?php echo esc_attr($ri_sizes); ?>"
                alt="<?php echo esc_attr($ri_alt); ?>"
                <?php if ($ri_w && $ri_h): ?>width="<?php echo esc_attr($ri_w); ?>" height="<?php echo esc_attr($ri_h); ?>"<?php endif; ?>
                class="mz-w-full mz-h-auto mz-rounded-xl mz-shadow-lg"
                loading="lazy"
                decoding="async"
              />
            </div>
          <?php endif; ?>

        </div>
      </div>

    </div>
  </div>
</section> 