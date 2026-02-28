<?php
/**
 * ACF (Free) fields (Options OR Home page):
 * - urgency_enable (true/false)
 * - urgency_text (text)            e.g. "Hurry! Only 20 Left In Stock!"
 * - urgency_btn_label (text)       e.g. "BUY NOW"
 * - urgency_btn_url (url)
 * - urgency_bg_image (image)       (optional) for the pink background
 * - urgency_bg_color (color)       (optional) fallback bg
 * - urgency_text_color (color)     (optional)
 */

if ( function_exists('acf_add_options_page') ) {
  $ctx = 'option';
} else {
  $ctx = (int) get_option('page_on_front');
  if ( ! $ctx ) { $ctx = get_the_ID(); }
}

$enable = get_field('urgency_enable', $ctx);
if ($enable === null) { $enable = true; }
if ( ! $enable ) return;

function mz_bg_url($img){
  if (empty($img)) return '';
  $id = is_array($img) ? ($img['ID'] ?? 0) : (int)$img;
  if (!$id) return '';
  $src = wp_get_attachment_image_url($id, 'large');
  return $src ? esc_url($src) : '';
}

$text      = get_field('urgency_text', $ctx) ?: 'Hurry! Only 20 Left In Stock!';
$btn_label = get_field('urgency_btn_label', $ctx) ?: 'BUY NOW';
$btn_url   = get_field('urgency_btn_url', $ctx) ?: '#';

$bg_img    = get_field('urgency_bg_image', $ctx);
$bg_url    = mz_bg_url($bg_img);

$bg_color  = get_field('urgency_bg_color', $ctx) ?: '#FFF1F5';
$text_col  = get_field('urgency_text_color', $ctx) ?: '#8C2F52';
?>

<section class="mz-w-full mz-relative" id="urgency-cta">
  <div class="mz-max-w-[1290px] mz-mx-auto mz-px-4 md:mz-px-6 xl:mz-px-0 mz-py-6 md:mz-py-10">

    <div
      class="mz-relative mz-overflow-hidden mz-rounded-2xl md:mz-rounded-3xl mz-border mz-border-black/5 mz-shadow-sm"
      style="
        background-color: <?php echo esc_attr($bg_color); ?>;
        <?php if ($bg_url): ?>background-image: url('<?php echo $bg_url; ?>');<?php endif; ?>
        background-size: cover;
        background-position: center;
      "
    >
      <!-- Soft overlay to ensure text readable on image -->
      <div class="mz-absolute mz-inset-0 mz-bg-white/55"></div>

      <div class="mz-relative mz-flex mz-flex-col mz-items-center mz-justify-center mz-text-center mz-gap-4 md:mz-gap-5 mz-px-4 mz-py-10 sm:mz-py-12 md:mz-py-14">
        <h3
          class="mz-text-[22px] sm:mz-text-[28px] md:mz-text-[36px] mz-font-extrabold mz-leading-tight mz-tracking-tight"
          style="color: <?php echo esc_attr($text_col); ?>;"
        >
          <?php echo esc_html($text); ?>
        </h3>

        <a
          href="<?php echo esc_url($btn_url); ?>"
          class="mz-inline-flex mz-items-center mz-justify-center
                 mz-min-w-[180px] sm:mz-min-w-[220px] md:mz-min-w-[260px]
                 mz-px-8 sm:mz-px-10 md:mz-px-12
                 mz-py-3.5 md:mz-py-4
                 mz-rounded-2xl
                 mz-bg-brand-accent mz-text-white
                 mz-font-extrabold mz-tracking-wide
                 mz-text-[14px] sm:mz-text-[16px] md:mz-text-[18px]
                 hover:mz-bg-brand-primary mz-transition"
        >
          <?php echo esc_html($btn_label); ?>
        </a>
      </div>
    </div>

  </div> 
</section>