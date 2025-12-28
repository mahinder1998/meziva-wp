<?php
/**
 * Template Name: Terms & Conditions
 */

get_header();

// ACF Fields
$main_heading   = get_field('tc_main_heading');
$main_subheading = get_field('tc_main_subheading');

$heading_1 = get_field('tc_heading_1');
$desc_1    = get_field('tc_desc_1');

$heading_2 = get_field('tc_heading_2');
$desc_2    = get_field('tc_desc_2');

$heading_3 = get_field('tc_heading_3');
$desc_3    = get_field('tc_desc_3');
?>

<section class="terms-conditions py-5">
  <div class="container">

    <!-- Main Heading -->
    <div class="text-center mb-5">
      <?php if ($main_heading): ?>
        <h1><?php echo esc_html($main_heading); ?></h1>
      <?php endif; ?>

      <?php if ($main_subheading): ?>
        <p class="text-muted"><?php echo esc_html($main_subheading); ?></p>
      <?php endif; ?>
    </div>

    <!-- Section 1 -->
    <?php if ($heading_1 && $desc_1): ?>
      <div class="mb-4">
        <h3><?php echo esc_html($heading_1); ?></h3>
        <div class="content">
          <?php echo wp_kses_post($desc_1); ?>
        </div>
      </div>
    <?php endif; ?>

    <!-- Section 2 -->
    <?php if ($heading_2 && $desc_2): ?>
      <div class="mb-4">
        <h3><?php echo esc_html($heading_2); ?></h3>
        <div class="content">
          <?php echo wp_kses_post($desc_2); ?>
        </div>
      </div>
    <?php endif; ?>

    <!-- Section 3 -->
    <?php if ($heading_3 && $desc_3): ?>
      <div class="mb-4">
        <h3><?php echo esc_html($heading_3); ?></h3>
        <div class="content">
          <?php echo wp_kses_post($desc_3); ?>
        </div>
      </div>
    <?php endif; ?>

  </div>
</section>

<?php get_footer(); ?>
