<?php
/**
 * Template Name: Returns & Refunds
 */

get_header();

// ACF Fields – Returns & Refunds
$rr_main_heading = get_field('returns_refunds_main_heading');

$rr_heading_1 = get_field('returns_refunds_heading_1');
$rr_desc_1    = get_field('returns_refunds_desc_1');

$rr_heading_2 = get_field('returns_refunds_heading_2');
$rr_desc_2    = get_field('returns_refunds_desc_2');
?>

<section class="returns-refunds py-5">
  <div class="container">

    <!-- Main Heading -->
    <?php if ($rr_main_heading): ?>
      <div class="text-center mb-5">
        <h1><?php echo esc_html($rr_main_heading); ?></h1>
      </div>
    <?php endif; ?>

    <!-- Section 1 -->
    <?php if ($rr_heading_1 && $rr_desc_1): ?>
      <div class="mb-5">
        <h3><?php echo esc_html($rr_heading_1); ?></h3>
        <div class="content">
          <?php echo wp_kses_post($rr_desc_1); ?>
        </div>
      </div>
    <?php endif; ?>

    <!-- Section 2 -->
    <?php if ($rr_heading_2 && $rr_desc_2): ?>
      <div class="mb-5">
        <h3><?php echo esc_html($rr_heading_2); ?></h3>
        <div class="content">
          <?php echo wp_kses_post($rr_desc_2); ?>
        </div>
      </div>
    <?php endif; ?>

  </div>
</section>

<?php get_footer(); ?>
