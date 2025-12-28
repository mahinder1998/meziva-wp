<?php
/**
 * Template Name: Contact Us
 */

get_header();

// ACF Fields
$banner_heading = get_field('contact_banner_heading');
$banner_desc    = get_field('contact_banner_desc');
$banner_bg      = get_field('contact_banner_bg');
?>

<!-- Contact Banner Section -->
<section class="contact-banner"
  style="
    background-image: url('<?php echo esc_url($banner_bg['url'] ?? ''); ?>');
    background-size: cover;
    background-position: center;
    min-height: 450px;
    display: flex;
    align-items: center;
  "
>
  <div class="container mx-auto text-center">
    
    <?php if ($banner_heading): ?>
      <h2 class="text-3xl md:text-4xl font-semibold mb-4">
        <?php echo esc_html($banner_heading); ?>
      </h2>
    <?php endif; ?>

    <?php if ($banner_desc): ?>
      <p class="text-base text-gray-700 max-w-2xl mx-auto">
        <?php echo esc_html($banner_desc); ?>
      </p>
    <?php endif; ?>

  </div>
</section>



<?php
// Contact Info Fields
$info_heading = get_field('contact_info_heading');

// Box 1
$icon_1 = get_field('contact_icon_1');
$text_1 = get_field('contact_text_1');

// Box 2
$icon_2 = get_field('contact_icon_2');
$text_2 = get_field('contact_text_2');

// Box 3
$icon_3 = get_field('contact_icon_3');
$text_3 = get_field('contact_text_3');
?>

<section class="contact-info-section py-5">
  <div class="container">

    <!-- Section Heading -->
    <?php if ($info_heading): ?>
      <div class="row mb-4">
        <div class="col-12 text-center">
          <h2><?php echo esc_html($info_heading); ?></h2>
        </div>
      </div>
    <?php endif; ?>

    <!-- Info Boxes -->
    <div class="row text-center align-items-center">

      <!-- Box 1 -->
      <div class="col-md-4 mb-4">
        <?php if ($icon_1): ?>
          <img src="<?php echo esc_url($icon_1['url']); ?>" alt="" style="width:40px;margin-bottom:10px;">
        <?php endif; ?>
        <div><?php echo wp_kses_post($text_1); ?></div>
      </div>

      <!-- Box 2 -->
      <div class="col-md-4 mb-4">
        <?php if ($icon_2): ?>
          <img src="<?php echo esc_url($icon_2['url']); ?>" alt="" style="width:40px;margin-bottom:10px;">
        <?php endif; ?>
        <div><?php echo wp_kses_post($text_2); ?></div>
      </div>

      <!-- Box 3 -->
      <div class="col-md-4 mb-4">
        <?php if ($icon_3): ?>
          <img src="<?php echo esc_url($icon_3['url']); ?>" alt="" style="width:40px;margin-bottom:10px;">
        <?php endif; ?>
        <div><?php echo wp_kses_post($text_3); ?></div>
      </div>

    </div>
  </div>
</section>

<!-- Contact Form Section -->
<section class="contact-form-section py-5">
  <div class="container">

    <div class="row justify-content-center">
      <div class="col-md-8">

        <!-- Section Heading -->
        <div class="text-center mb-4">
          <h2>Contact Form</h2>
        </div>

        <!-- Contact Form 7 Shortcode -->
        <div class="contact-form-wrapper">
          <?php echo do_shortcode('[contact-form-7 id="5b8b205" title="Contact form 1"]'); ?>
        </div>

      </div>
    </div>

  </div>
</section>








<?php get_footer(); ?>
