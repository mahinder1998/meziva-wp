<?php
/**
 * Template Name: About Us
 */
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
?>

<!-- =========================
     ABOUT HERO SECTION
========================= -->
<section class="about-us-section mz-py-20 mz-bg-white">
  <div class="mz-max-w-7xl mz-mx-auto mz-grid mz-grid-cols-1 md:mz-grid-cols-2 mz-gap-12 mz-items-center mz-px-4">

    <!-- LEFT CONTENT -->
    <div class="mz-flex mz-flex-col mz-gap-4">
      <?php if ($section_title): ?>
        <span class="mz-text-sm mz-uppercase mz-tracking-wide mz-text-gray-500">
          <?php echo esc_html($section_title); ?>
        </span>
      <?php endif; ?>

      <?php if ($section_heading): ?>
        <h1 class="mz-text-4xl lg:mz-text-5xl mz-font-bold mz-text-gray-900">
          <?php echo esc_html($section_heading); ?>
        </h1>
      <?php endif; ?>

      <?php if ($section_desc1): ?>
        <p class="mz-text-lg mz-text-gray-600">
          <?php echo esc_html($section_desc1); ?>
        </p>
      <?php endif; ?>

      <?php if ($section_desc2): ?>
        <p class="mz-text-base mz-text-gray-600">
          <?php echo esc_html($section_desc2); ?>
        </p>
      <?php endif; ?>

      <?php if ($button_text && $button_url): ?>
        <a href="<?php echo esc_url($button_url); ?>"
           class="mz-inline-flex mz-items-center mz-justify-center mz-w-fit
                  mz-bg-[#9B4A6A] mz-text-white mz-font-semibold
                  mz-px-8 mz-py-3 mz-rounded-lg
                  hover:mz-bg-[#7a3650] mz-transition">
          <?php echo esc_html($button_text); ?>
        </a>
      <?php endif; ?>
    </div>

    <!-- RIGHT IMAGE -->
    <div class="mz-flex mz-justify-center">
      <?php
      if ($section_image) {
        if (is_array($section_image) && isset($section_image['url'])) {
          $hero_img = $section_image['url'];
        } elseif (is_numeric($section_image)) {
          $hero_img = wp_get_attachment_image_url($section_image, 'full');
        } elseif (is_string($section_image)) {
          $hero_img = $section_image;
        }

        if (!empty($hero_img)):
      ?>
        <img src="<?php echo esc_url($hero_img); ?>"
             class="mz-rounded-xl mz-shadow-xl mz-max-w-full"
             alt="">
      <?php endif; } ?>
    </div>

  </div>
</section>

<!-- =========================
     4 BOXES SECTION (NO LOOP)
========================= -->
<section class="about-us-4boxes mz-py-20 mz-bg-gray-50">
  <div class="mz-max-w-7xl mz-mx-auto mz-px-4">
    <div class="mz-grid mz-grid-cols-1 sm:mz-grid-cols-2 lg:mz-grid-cols-4 mz-gap-8">

      <?php
      // ================= BOX 1 =================
      $image1   = get_field('box1_image');
      $heading1 = get_field('box1_heading');
      $desc1    = get_field('box1_desc');

      $img1 = '';
      if (is_array($image1) && isset($image1['ID'])) {
        $img1 = wp_get_attachment_image_url($image1['ID'], 'full');
      } elseif (is_numeric($image1)) {
        $img1 = wp_get_attachment_image_url($image1, 'full');
      } elseif (is_string($image1)) {
        $img1 = $image1;
      }
      ?>

      <div class="mz-bg-white mz-rounded-xl mz-shadow-md mz-p-8 mz-text-center">
        <?php if ($img1): ?>
          <img src="<?php echo esc_url($img1); ?>" class="mz-w-16 mz-h-16 mz-mx-auto mz-mb-4" alt="">
        <?php endif; ?>
        <?php if ($heading1): ?>
          <h3 class="mz-text-xl mz-font-semibold mz-mb-2"><?php echo esc_html($heading1); ?></h3>
        <?php endif; ?>
        <?php if ($desc1): ?>
          <p class="mz-text-sm mz-text-gray-600"><?php echo esc_html($desc1); ?></p>
        <?php endif; ?>
      </div>

      <?php
      // ================= BOX 2 =================
      $image2   = get_field('box2_image');
      $heading2 = get_field('box2_heading');
      $desc2    = get_field('box2_desc');

      $img2 = '';
      if (is_array($image2) && isset($image2['ID'])) {
        $img2 = wp_get_attachment_image_url($image2['ID'], 'full');
      } elseif (is_numeric($image2)) {
        $img2 = wp_get_attachment_image_url($image2, 'full');
      } elseif (is_string($image2)) {
        $img2 = $image2;
      }
      ?>

      <div class="mz-bg-white mz-rounded-xl mz-shadow-md mz-p-8 mz-text-center">
        <?php if ($img2): ?>
          <img src="<?php echo esc_url($img2); ?>" class="mz-w-16 mz-h-16 mz-mx-auto mz-mb-4" alt="">
        <?php endif; ?>
        <?php if ($heading2): ?>
          <h3 class="mz-text-xl mz-font-semibold mz-mb-2"><?php echo esc_html($heading2); ?></h3>
        <?php endif; ?>
        <?php if ($desc2): ?>
          <p class="mz-text-sm mz-text-gray-600"><?php echo esc_html($desc2); ?></p>
        <?php endif; ?>
      </div>

      <?php
      // ================= BOX 3 =================
      $image3   = get_field('box3_image');
      $heading3 = get_field('box3_heading');
      $desc3    = get_field('box3_desc');

      $img3 = '';
      if (is_array($image3) && isset($image3['ID'])) {
        $img3 = wp_get_attachment_image_url($image3['ID'], 'full');
      } elseif (is_numeric($image3)) {
        $img3 = wp_get_attachment_image_url($image3, 'full');
      } elseif (is_string($image3)) {
        $img3 = $image3;
      }
      ?>

      <div class="mz-bg-white mz-rounded-xl mz-shadow-md mz-p-8 mz-text-center">
        <?php if ($img3): ?>
          <img src="<?php echo esc_url($img3); ?>" class="mz-w-16 mz-h-16 mz-mx-auto mz-mb-4" alt="">
        <?php endif; ?>
        <?php if ($heading3): ?>
          <h3 class="mz-text-xl mz-font-semibold mz-mb-2"><?php echo esc_html($heading3); ?></h3>
        <?php endif; ?>
        <?php if ($desc3): ?>
          <p class="mz-text-sm mz-text-gray-600"><?php echo esc_html($desc3); ?></p>
        <?php endif; ?>
      </div>

      <?php
      // ================= BOX 4 =================
      $image4   = get_field('box4_image');
      $heading4 = get_field('box4_heading');
      $desc4    = get_field('box4_desc');

      $img4 = '';
      if (is_array($image4) && isset($image4['ID'])) {
        $img4 = wp_get_attachment_image_url($image4['ID'], 'full');
      } elseif (is_numeric($image4)) {
        $img4 = wp_get_attachment_image_url($image4, 'full');
      } elseif (is_string($image4)) {
        $img4 = $image4;
      }
      ?>

      <div class="mz-bg-white mz-rounded-xl mz-shadow-md mz-p-8 mz-text-center">
        <?php if ($img4): ?>
          <img src="<?php echo esc_url($img4); ?>" class="mz-w-16 mz-h-16 mz-mx-auto mz-mb-4" alt="">
        <?php endif; ?>
        <?php if ($heading4): ?>
          <h3 class="mz-text-xl mz-font-semibold mz-mb-2"><?php echo esc_html($heading4); ?></h3>
        <?php endif; ?>
        <?php if ($desc4): ?>
          <p class="mz-text-sm mz-text-gray-600"><?php echo esc_html($desc4); ?></p>
        <?php endif; ?>
      </div>

    </div>
  </div>
</section>
<?php get_footer(); ?>
