<?php
if ( ! defined('ABSPATH') ) exit;

$page_id  = get_queried_object_id();
$front_id = (int) get_option('page_on_front');
$pid      = $page_id ?: $front_id;

if ( ! function_exists('get_field') ) return;

// --- Read ACF once ---
$title    = (string) get_field('about_title', $pid);
$subtitle = (string) get_field('about_subtitle', $pid);
$content  = get_field('about_content', $pid);
$btn_text = (string) get_field('about_btn_text', $pid);
$btn_url  = (string) get_field('about_btn_url', $pid);

$image    = get_field('about_image', $pid); // can be array or id based on ACF settings

// --- Image helper: supports ACF array or ID ---
if ( ! function_exists('mz_get_img_data') ) {
  function mz_get_img_data($img) {
    $data = [
      'id'     => 0,
      'url'    => '',
      'alt'    => '',
      'w'      => 0,
      'h'      => 0,
      'srcset' => '',
      'sizes'  => '',
    ];

    if (is_array($img)) {
      $data['id']  = !empty($img['ID']) ? (int)$img['ID'] : 0;
      $data['url'] = !empty($img['url']) ? esc_url($img['url']) : '';
      $data['alt'] = !empty($img['alt']) ? esc_attr($img['alt']) : '';
      $data['w']   = !empty($img['width']) ? (int)$img['width'] : 0;
      $data['h']   = !empty($img['height']) ? (int)$img['height'] : 0;
    } elseif (is_numeric($img)) {
      $data['id']  = (int)$img;
      $data['url'] = esc_url(wp_get_attachment_image_url($data['id'], 'large'));
      $data['alt'] = esc_attr(get_post_meta($data['id'], '_wp_attachment_image_alt', true));
      $meta = wp_get_attachment_metadata($data['id']);
      if (!empty($meta['width']))  $data['w'] = (int)$meta['width'];
      if (!empty($meta['height'])) $data['h'] = (int)$meta['height'];
    }

    // srcset / sizes (best for perf)
    if ($data['id']) {
      $data['srcset'] = wp_get_attachment_image_srcset($data['id'], 'large') ?: '';
      // About image: right column, so smaller than full width
      $data['sizes']  = '(max-width: 767px) 92vw, (max-width: 1024px) 48vw, 620px';
    }

    return $data;
  }
}

$img = mz_get_img_data($image);
$img_url = $img['url'];
$img_alt = $img['alt'] ?: ($title ? wp_strip_all_tags($title) : 'About image');

$content_html = $content ? wp_kses_post($content) : '';

// Nothing to show? bail
if ( ! $title && ! $subtitle && ! $content_html && ! $img_url ) return;
?>

<section class="mz-w-full mz-bg-white">
  <div class="mz-max-w-[1290px] mz-mx-auto mz-px-4 mz-py-10 mz-pb-0 md:mz-py-20 md:mz-pb-0 xl:mz-px-0">

    <div class="mz-grid md:mz-grid-cols-2 mz-text-center md:mz-gap-10 xl:mz-grid-cols-[1fr,1fr,1fr]">

      <?php if ($title): ?>
        <header class="section-header md:mz-col-span-2 md:mz-text-center xl:md:mz-col-span-1 xl:mz-text-left xl:mz-mt-14">
          <h2 class="mz-text-[36px] xl:mz-text-[50px] mz-leading-[1.05] mz-font-extrabold mz-tracking-tight mz-text-brand-accent mz-mb-8 xl:mz-mb-5">
            <?php echo wp_kses($title, ['br' => []]); ?>
          </h2>

          <?php if ($subtitle): ?>
            <p class="mz-mt-4 mz-text-[18px] md:mz-text-[18px] mz-font-semibold mz-text-text-heading">
              <?php echo esc_html($subtitle); ?>
            </p>
          <?php endif; ?>
        </header>
      <?php endif; ?>

      <div class="section-content md:mz-text-left md:mz-mt-5 xl:mz-mt-14">
        <?php if ($content_html): ?>
          <div class="mz-text-[14px] md:mz-text-[16px] mz-leading-7 mz-text-text-body">
            <?php echo $content_html; ?>
          </div>
        <?php endif; ?>

        <?php if ($btn_text && $btn_url): ?>
          <div class="mz-mt-8 mz-flex md:mz-justify-start mz-justify-center">
            <a
              href="<?php echo esc_url($btn_url); ?>"
              class="mz-inline-block mz-bg-brand-accent mz-text-white mz-px-5 mz-py-3 mz-rounded-lg hover:mz-bg-opacity-90 mz-transition
                     mz-text-sm mz-font-bold hover:mz-bg-brand-primary hover:mz-text-white
                     md:mz-min-w-[140px] md:mz-py-4 md:mz-text-center
                     xl:mz-min-w-[150px] xl:mz-py-[18px] xl:mz-text-center xl:mz-text-[15px] xl:mz-rounded-xl"
            >
              <?php echo esc_html($btn_text); ?>
            </a>
          </div>
        <?php endif; ?>
      </div>

      <div class="section-media mz-mt-10 md:mz-mt-5 xl:mz-mt-0">
        <?php if ($img_url): ?>
          <div class="mz-relative mz-w-full mz-max-w-[520px] md:mz-max-w-[620px] mz-mx-auto md:mz-ml-auto">
            <img
              src="<?php echo esc_url($img_url); ?>"
              <?php if (!empty($img['srcset'])): ?>srcset="<?php echo esc_attr($img['srcset']); ?>"<?php endif; ?>
              <?php if (!empty($img['sizes'])): ?>sizes="<?php echo esc_attr($img['sizes']); ?>"<?php endif; ?>
              alt="<?php echo esc_attr($img_alt); ?>"
              width="<?php echo (int)($img['w'] ?: 620); ?>"
              height="<?php echo (int)($img['h'] ?: 620); ?>"
              class="mz-relative mz-z-[2] mz-w-full md:mz-max-h-[570px] mz-h-auto mz-object-contain mz-block"
              loading="lazy"
              decoding="async"
              style="<?php
                $w = (int)($img['w'] ?: 620);
                $h = (int)($img['h'] ?: 620);
                echo 'aspect-ratio:' . $w . '/' . $h . ';';
              ?>"
            />
          </div>
        <?php endif; ?>
      </div>

    </div>
  </div>
</section>
