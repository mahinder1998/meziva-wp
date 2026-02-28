<?php
/**
 * Template Name: About Us
 */
if (!defined('ABSPATH')) exit;

get_header();

/* =========================
   HERO SECTION ACF FIELDS
========================= */
$section_title   = get_field('about_section_title');
$section_heading = get_field('about_section_heading');
$section_desc1   = get_field('about_section_desc1');
$section_desc2   = get_field('about_section_desc2');
$button_text     = get_field('about_section_btn_text');
$button_url      = get_field('about_section_btn_url');
$section_image   = get_field('about_section_image');

/* =========================
   Image helper (array/id/url safe)
========================= */
function mz_get_img_url_any($img){
  if (empty($img)) return '';
  if (is_array($img) && !empty($img['ID'])) {
    return (string) wp_get_attachment_image_url((int)$img['ID'], 'full');
  }
  if (is_array($img) && !empty($img['url'])) {
    return (string) $img['url'];
  }
  if (is_numeric($img)) {
    return (string) wp_get_attachment_image_url((int)$img, 'full');
  }
  if (is_string($img)) {
    return (string) $img;
  }
  return '';
}

$hero_img = mz_get_img_url_any($section_image);

/* =========================
   4 BOXES DATA (ACF)
========================= */
$boxes = [];
for ($i=1; $i<=4; $i++){
  $img     = get_field("box{$i}_image");
  $heading = get_field("box{$i}_heading");
  $desc    = get_field("box{$i}_desc");

  $boxes[] = [
    'img' => mz_get_img_url_any($img),
    'heading' => $heading,
    'desc' => $desc,
  ];
}
?>

<style>
  /* Small polish (safe) */
  .mz-about-blob{ filter: blur(50px); opacity: .35; transform: translateZ(0); }
</style>

<!-- =========================
     ABOUT HERO SECTION (IMPROVED)
========================= -->
<section class="mz-relative mz-bg-white mz-overflow-hidden">
  <!-- subtle decorative blobs -->
  <div class="mz-absolute mz-inset-0 mz-pointer-events-none">
    <div class="mz-about-blob mz-absolute -mz-top-16 -mz-left-20 mz-w-[320px] mz-h-[320px] mz-rounded-full" style="background:#F7C6D6;"></div>
    <div class="mz-about-blob mz-absolute -mz-bottom-24 -mz-right-20 mz-w-[360px] mz-h-[360px] mz-rounded-full" style="background:#FBE6B7;"></div>
  </div>

  <div class="mz-relative mz-max-w-[1290px] mz-mx-auto mz-px-4 xl:mz-px-0 mz-py-10 md:mz-py-16">
    <div class="mz-grid mz-grid-cols-1 lg:mz-grid-cols-12 mz-gap-10 mz-items-center">

      <!-- LEFT CONTENT -->
      <div class="lg:mz-col-span-6 mz-flex mz-flex-col mz-gap-4">
        <?php if ($section_title): ?>
          <span class="mz-inline-flex mz-items-center mz-gap-2 mz-w-fit mz-text-xs mz-font-extrabold mz-uppercase mz-tracking-widest mz-px-3 mz-py-1.5 mz-rounded-full mz-bg-white/80 mz-border mz-border-black/5 mz-text-[#9B4A6A]">
            <span class="mz-w-2 mz-h-2 mz-rounded-full" style="background:#9B4A6A;"></span>
            <?php echo esc_html($section_title); ?>
          </span>
        <?php endif; ?>

        <?php if ($section_heading): ?>
          <h1 class="mz-text-[22px] sm:mz-text-[24px] md:mz-text-[30px] xl:mz-text-[30px] mz-leading-[1.08] mz-font-extrabold mz-tracking-tight">
            <?php echo esc_html($section_heading); ?>
          </h1>
        <?php endif; ?>

        <?php if ($section_desc1): ?>
          <p class="mz-text-[15px] md:mz-text-[18px] mz-leading-7 mz-text-text-body mz-opacity-90">
            <?php echo esc_html($section_desc1); ?>
          </p>
        <?php endif; ?>

        <?php if ($section_desc2): ?>
          <p class="mz-text-[14px] md:mz-text-[16px] mz-leading-7 mz-text-text-body mz-opacity-80">
            <?php echo esc_html($section_desc2); ?>
          </p>
        <?php endif; ?>

        <!-- CTA Row -->
        <div class="mz-mt-2 mz-flex mz-flex-wrap mz-items-center mz-gap-3">
          <?php if ($button_text && $button_url): ?>
            <a href="<?php echo esc_url($button_url); ?>"
               class="mz-inline-flex mz-items-center mz-justify-center mz-gap-2
                      mz-bg-[#9B4A6A] mz-text-white mz-font-extrabold
                      mz-px-7 mz-py-3.5 md:mz-py-4 mz-rounded-xl
                      hover:mz-bg-[#7a3650] mz-transition mz-duration-200">
              <?php echo esc_html($button_text); ?>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M5 12h12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                <path d="M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </a>
          <?php endif; ?>

          <div class="mz-text-xs mz-text-text-body mz-opacity-70">
            Cruelty-free • Skin-loving • Made for daily use
          </div>
        </div>
      </div>

      <!-- RIGHT IMAGE -->
      <div class="lg:mz-col-span-6">
        <div class="mz-relative mz-rounded-3xl mz-overflow-hidden mz-border mz-border-black/5 mz-bg-white mz-shadow-lg">
          <?php if (!empty($hero_img)): ?>
            <img
              src="<?php echo esc_url($hero_img); ?>"
              class="mz-w-full mz-h-auto mz-block"
              alt="<?php echo esc_attr($section_heading ?: 'About Meziva'); ?>"
              loading="eager"
              decoding="async"
            />
          <?php else: ?>
            <div class="mz-aspect-[16/10] mz-flex mz-items-center mz-justify-center mz-text-sm mz-text-text-body mz-opacity-70">
              Add About hero image in ACF
            </div>
          <?php endif; ?>

          <!-- overlay label -->
          <div class="mz-absolute mz-bottom-4 mz-left-4 mz-right-4 mz-rounded-2xl mz-bg-white/80 mz-backdrop-blur mz-border mz-border-black/5 mz-px-4 mz-py-3">
            <div class="mz-text-sm mz-font-extrabold mz-text-text-heading">Meziva Beauty</div>
            <div class="mz-text-xs mz-text-text-body mz-opacity-75">Hydration-first formulas designed for everyday comfort.</div>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- =========================
     4 BOXES SECTION (IMPROVED)
========================= -->
<section class="mz-bg-[#FAFAFB] mz-border-t mz-border-black/5">
  <div class="mz-max-w-[1290px] mz-mx-auto mz-px-4 xl:mz-px-0 mz-py-12 md:mz-py-16">

    <div class="mz-flex mz-items-end mz-justify-between mz-gap-6 mz-flex-wrap">
      <div>
        <h2 class="mz-text-[22px] sm:mz-text-[24px] md:mz-text-[30px] xl:mz-text-[30px] mz-leading-[1.08] mz-font-extrabold mz-tracking-tight">Why people choose Meziva</h2>
        <p class="mz-mt-2 mz-text-[14px] md:mz-text-[16px] mz-text-text-body mz-opacity-80">Simple, honest, and effective beauty essentials.</p>
      </div>
    </div>

    <div class="mz-mt-8 md:mz-mt-10 mz-grid mz-grid-cols-1 sm:mz-grid-cols-2 lg:mz-grid-cols-4 mz-gap-5 md:mz-gap-7">
      <?php foreach ($boxes as $b): ?>
        <div class="mz-group mz-h-full mz-bg-white mz-rounded-3xl mz-border mz-border-black/5 mz-shadow-sm
                    mz-p-6 md:mz-p-7 hover:mz-shadow-xl hover:mz--translate-y-[2px] mz-transition mz-duration-300">
          <div class="mz-flex mz-items-start mz-gap-4">
            <!-- icon -->
            <div class="mz-w-14 mz-h-14 mz-rounded-2xl mz-bg-[#FFF1F5] mz-border mz-border-black/5 mz-flex mz-items-center mz-justify-center mz-flex-shrink-0">
              <?php if (!empty($b['img'])): ?>
                <img src="<?php echo esc_url($b['img']); ?>" class="mz-w-8 mz-h-8 mz-object-contain" alt="" loading="lazy" decoding="async">
              <?php else: ?>
                <span class="mz-text-[#9B4A6A] mz-font-extrabold">✓</span>
              <?php endif; ?>
            </div>

            <!-- text -->
            <div class="mz-min-w-0">
              <?php if (!empty($b['heading'])): ?>
                <h3 class="mz-text-[16px] md:mz-text-[17px] mz-font-extrabold mz-text-text-heading">
                  <?php echo esc_html($b['heading']); ?>
                </h3>
              <?php endif; ?>

              <?php if (!empty($b['desc'])): ?>
                <p class="mz-mt-1 mz-text-[13px] md:mz-text-[14px] mz-leading-6 mz-text-text-body mz-opacity-80">
                  <?php echo esc_html($b['desc']); ?>
                </p>
              <?php endif; ?>
            </div>
          </div>

          <!-- bottom accent line -->
          <div class="mz-mt-5 mz-h-[3px] mz-w-full mz-opacity-0 group-hover:mz-opacity-100 mz-transition"
               style="background: linear-gradient(90deg, rgba(155,74,106,0), rgba(155,74,106,.7), rgba(155,74,106,0));"></div>
        </div>
      <?php endforeach; ?>
    </div>

  </div>
</section>

<?php get_footer(); ?>  