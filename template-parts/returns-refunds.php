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

<!-- ===============================
     RETURNS & REFUNDS SECTION
================================ -->
<section class="mz-bg-white mz-py-16 lg:mz-py-16">

  <div class="mz-max-w-4xl mz-mx-auto mz-px-4">

    <!-- MAIN HEADING -->
    <?php if ($rr_main_heading): ?>
      <div class="mz-text-center mz-mb-12">
        <h1 class="mz-text-3xl md:mz-text-4xl mz-font-semibold mz-text-gray-900">
          <?php echo esc_html($rr_main_heading); ?>
        </h1>
      </div>
    <?php endif; ?>

    <!-- CONTENT AREA -->
    <div class="mz-space-y-10">

      <!-- SECTION 1 -->
      <?php if ($rr_heading_1 && $rr_desc_1): ?> 
        <div>
          <h2 class="mz-text-xl md:mz-text-2xl mz-font-semibold mz-text-gray-900 mz-mb-3">
            <?php echo esc_html($rr_heading_1); ?>
          </h2>

          <div class="mz-text-gray-600 mz-text-sm md:mz-text-base mz-leading-7">
            <?php echo wp_kses_post($rr_desc_1); ?>
          </div>
        </div>
      <?php endif; ?>

      <!-- SECTION 2 -->
      <?php if ($rr_heading_2 && $rr_desc_2): ?>
        <div>
          <h2 class="mz-text-xl md:mz-text-2xl mz-font-semibold mz-text-gray-900 mz-mb-3">
            <?php echo esc_html($rr_heading_2); ?>
          </h2>

          <div class="mz-text-gray-600 mz-text-sm md:mz-text-base mz-leading-7">
            <?php echo wp_kses_post($rr_desc_2); ?>
          </div>
        </div>
      <?php endif; ?>

    </div>

  </div>
</section>

<?php get_footer(); ?>
