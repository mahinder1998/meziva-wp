
<?php
/**
 * SECTION: CTA / Try freshbody today (ACF FREE) + Tailwind prefix mz-
 */

if ( ! function_exists('get_field') ) return;

$img        = get_field('cta_image');
$heading    = get_field('cta_heading') ?: 'Try freshbody today';
$subtext    = get_field('cta_subtext') ?: 'Best price here, burn fat and save more';
$btn_text   = get_field('cta_btn_text') ?: 'Shop Now';
$btn_link   = get_field('cta_btn_link') ?: '#';
$call_label = get_field('cta_call_label') ?: 'Or Call us at';
$phone      = get_field('cta_phone') ?: '+844 - 1800 3355';
$note       = get_field('cta_note') ?: '* Free shipping for US and Canada only';

$bg         = get_field('cta_bg_color') ?: '#FFFFFF';
$h_color    = get_field('cta_heading_color') ?: '#86B400';
$t_color    = get_field('cta_text_color') ?: '#111111';
$btn_bg     = get_field('cta_btn_bg') ?: '#9FD3C6';
$btn_tcolor = get_field('cta_btn_text_color') ?: '#FFFFFF';

/**
 * ✅ Image optimizer payload (ACF image array)
 * Uses WP srcset + sizes for better LCP/CLS
 */
function mz_img_payload_cta($img, $size = 'large') {
  if (empty($img) || !is_array($img)) return null;

  $id = !empty($img['ID']) ? (int) $img['ID'] : 0;

  // pick requested size if exists, else fallback url
  $src = $img['sizes'][$size] ?? ($img['url'] ?? '');
  if (!$src) return null;

  $w = $img['sizes'][$size . '-width']  ?? '';
  $h = $img['sizes'][$size . '-height'] ?? '';

  $srcset = $id ? wp_get_attachment_image_srcset($id, $size) : '';
  // your layout shows image ~250-300px, so tell browser correctly
  $sizes  = '(max-width: 640px) 250px, (max-width: 1024px) 280px, 300px';

  return [
    'src'    => $src,
    'srcset' => $srcset,
    'sizes'  => $sizes,
    'alt'    => $img['alt'] ?? '',
    'w'      => $w,
    'h'      => $h,
  ];
}

$imgp = mz_img_payload_cta($img, 'large');
$img_alt = ($imgp && !empty($imgp['alt'])) ? $imgp['alt'] : 'Product';
?>

<section class="mz-w-full" style="background-color: <?php echo esc_attr($bg); ?>;">
  <div class="mz-max-w-[1290px] mz-mx-auto mz-px-4 md:mz-px-6 mz-py-14 md:mz-py-20">

    <div class="mz-grid mz-grid-cols-1 md:mz-grid-cols-12 md:mz-gap-10 mz-items-center">

      <!-- LEFT: Image -->
      <div class="md:mz-col-span-5">
        <div class="mz-flex mz-justify-center md:mz-justify-end">
          <?php if ($imgp): ?>
            <img
              src="<?php echo esc_url($imgp['src']); ?>"
              <?php if (!empty($imgp['srcset'])): ?>
                srcset="<?php echo esc_attr($imgp['srcset']); ?>"
              <?php endif; ?>
              sizes="<?php echo esc_attr($imgp['sizes']); ?>"
              alt="<?php echo esc_attr($img_alt); ?>"
              <?php if (!empty($imgp['w']) && !empty($imgp['h'])): ?>
                width="<?php echo esc_attr($imgp['w']); ?>"
                height="<?php echo esc_attr($imgp['h']); ?>"
              <?php endif; ?>
              class="mz-w-[250px] sm:mz-w-[280px] md:mz-w-[280px] lg:mz-w-[350px] mz-h-auto"
              loading="lazy"
              decoding="async"
              fetchpriority="low"
            />
          <?php endif; ?>
        </div>
      </div>

      <!-- RIGHT: Content -->
      <div class="md:mz-col-span-7 mz-mt-10 md:mz-mt-0">
        <div class="mz-max-w-[560px] md:mz-ml-auto md:mz-text-left"> 
        <?php if ($heading): ?>
          <div class="section-header md:mz-col-span-2 mz-mb-8 mz-text-center  xl:md:mz-col-span-1 xl:mz-text-left">
              <h2 class="mz-text-[36px] xl:mz-text-[50px] mz-leading-[1.05] mz-font-extrabold mz-tracking-tight mz-text-brand-accent
              mz-mb-8 xl:mz-mb-5
              ">
                  <?php echo wp_kses($heading, ['br' => []]); ?>
              </h2>
                  <?php if ($subtext): ?>
              <p class="mz-text-[18px] md:mz-text-[18px] mz-font-semibold mz-text-text-heading">
                  <?php echo esc_html($subtext); ?>
                  
              </p>
              <?php endif; ?>

          </div>
          <?php endif; ?>




          <div class="mz-mt-8 mz-text-center xl:mz-text-left">
            <a   href="<?php echo esc_url($btn_link); ?>" class="
            mz-inline-block mz-bg-primary mz-bg-brand-accent mz-text-white mz-px-5 mz-py-3 mz-rounded-lg hover:mz-bg-opacity-90 mz-transition
                mz-text-sm mz-font-bold hover:mz-bg-brand-primary hover:mz-text-white
                  md:mz-min-w-[140px] md:mz-py-4 md:mz-text-center xl:mz-min-w-[150px] xl:mz-py-[18px]  xl:mz-text-center xl:mz-text-[15px] xl:mz-rounded-xl start
                ">
                  <?php echo esc_html($btn_text); ?>
            </a>


          </div>

          <div class="mz-mt-10 mz-text-center xl:mz-text-left">
            <div class="mz-text-[14px] mz-opacity-70" style="color: <?php echo esc_attr($t_color); ?>;">
              <?php echo esc_html($call_label); ?>
            </div>

            <div class="mz-font-extrabold mz-text-[26px] sm:mz-text-[30px] md:mz-text-[32px]"
                 style="color: <?php echo esc_attr($t_color); ?>;">
              <?php echo esc_html($phone); ?>
            </div>

            <div class="mz-mt-4 mz-text-[13px] mz-opacity-60" style="color: <?php echo esc_attr($t_color); ?>;">
              <?php echo esc_html($note); ?>
            </div>
          </div>  

        </div>
      </div> 

    </div>
  </div>
</section>
