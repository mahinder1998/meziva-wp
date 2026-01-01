<?php
/**
 * Template Name: Privacy Policy
 */

get_header();

// ACF Fields (Privacy Policy)
$pp_main_heading   = get_field('privacy_policy_main_heading');
$pp_main_subheading = get_field('privacy_policy_main_subheading');

$pp_heading_1 = get_field('privacy_policy_heading_1');
$pp_desc_1    = get_field('privacy_policy_desc_1');

$pp_heading_2 = get_field('privacy_policy_heading_2');
$pp_desc_2    = get_field('privacy_policy_desc_2');

$pp_heading_3 = get_field('privacy_policy_heading_3');
$pp_desc_3    = get_field('privacy_policy_desc_3');
?>

<section class="privacy-policy py-5">
  <div class="container">

    <!-- Main Heading -->
    <div class="text-center mb-5">
      <?php if ($pp_main_heading): ?>
        <h1><?php echo esc_html($pp_main_heading); ?></h1>
      <?php endif; ?>

      <?php if ($pp_main_subheading): ?>
        <p class="text-muted"><?php echo esc_html($pp_main_subheading); ?></p>
      <?php endif; ?>
    </div>

    <!-- Section 1 -->
    <?php if ($pp_heading_1 && $pp_desc_1): ?>
      <div class="mb-4">
        <h3><?php echo esc_html($pp_heading_1); ?></h3>
        <div class="content">
          <?php echo wp_kses_post($pp_desc_1); ?>
        </div>
      </div>
    <?php endif; ?>

    <!-- Section 2 -->
    <?php if ($pp_heading_2 && $pp_desc_2): ?>
      <div class="mb-4">
        <h3><?php echo esc_html($pp_heading_2); ?></h3>
        <div class="content">
          <?php echo wp_kses_post($pp_desc_2); ?>
        </div>
      </div>
    <?php endif; ?>

    <!-- Section 3 -->
    <?php if ($pp_heading_3 && $pp_desc_3): ?>
      <div class="mb-4">
        <h3><?php echo esc_html($pp_heading_3); ?></h3>
        <div class="content">
          <?php echo wp_kses_post($pp_desc_3); ?>
        </div>
      </div>
    <?php endif; ?> 

  </div>
</section>

<?php get_footer(); ?>
