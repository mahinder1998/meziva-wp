<?php
/**
 * Template Name: About Us
 */

get_header();

// ACF fields
$section_title   = get_field('about_section_title');
$section_heading = get_field('about_section_heading');
$section_desc1   = get_field('about_section_desc1');
$section_desc2   = get_field('about_section_desc2');
$button_text     = get_field('about_section_btn_text');
$button_url      = get_field('about_section_btn_url');
$section_image   = get_field('about_section_image');
?>

<!-- About Us Section -->
<section class="about-us-section mz-py-16 mz-bg-white">
  <div class="container mz-mx-auto mz-grid mz-grid-cols-1 md:mz-grid-cols-2 mz-gap-8">

    <!-- Left Column: Text -->
    <div class="about-us-text mz-flex mz-flex-col mz-justify-center mz-gap-4">
      <?php if ($section_title): ?>
        <div class="text-sm mz-font-medium mz-text-gray-500"><?php echo esc_html($section_title); ?></div>
      <?php endif; ?>

      <?php if ($section_heading): ?>
        <h2 class="text-3xl md:text-4xl mz-font-bold"><?php echo esc_html($section_heading); ?></h2>
      <?php endif; ?>

      <?php if ($section_desc1): ?>
        <p class="text-base mz-text-gray-700"><?php echo esc_html($section_desc1); ?></p>
      <?php endif; ?>

      <?php if ($section_desc2): ?>
        <p class="text-base mz-text-gray-700"><?php echo esc_html($section_desc2); ?></p>
      <?php endif; ?>

      <?php if ($button_text && $button_url): ?>
        <a href="<?php echo esc_url($button_url); ?>" class="mz-inline-block mz-bg-[#9B4A6A] mz-text-white mz-px-6 mz-py-3 mz-rounded-lg hover:mz-opacity-90 mz-transition">
          <?php echo esc_html($button_text); ?>
        </a>
      <?php endif; ?>
    </div>

    <!-- Right Column: Image -->
    <div class="about-us-image mz-flex mz-items-center mz-justify-center">
      <?php if ($section_image && isset($section_image['url'])): ?>
        <img src="<?php echo esc_url($section_image['url']); ?>" alt="<?php echo esc_attr($section_heading); ?>" class="mz-w-full mz-h-auto mz-rounded-lg">
      <?php endif; ?>
    </div>

  </div>
</section>


<?php
$boxes = get_field('about_us_boxes');
?>

<section class="about-us-4boxes mz-py-16 mz-bg-white">
  <div class="container mx-auto">
    <div class="row">

      <?php for ($i = 1; $i <= 4; $i++): 
        $image   = get_field("box{$i}_image");
        $heading = get_field("box{$i}_heading");
        $desc    = get_field("box{$i}_desc");
      ?>
        <div class="col-md-3 text-center mb-6">

          <?php if ($image): ?>
            <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($heading); ?>" style="width:80px;margin-bottom:15px;">
          <?php endif; ?>

          <?php if ($heading): ?>
            <h3 class="text-xl font-semibold mb-2"><?php echo esc_html($heading); ?></h3>
          <?php endif; ?>

          <?php if ($desc): ?>
            <p class="text-gray-600 text-sm"><?php echo esc_html($desc); ?></p>
          <?php endif; ?>

        </div>
      <?php endfor; ?>

    </div>
  </div>
</section>





<?php get_footer(); ?>

