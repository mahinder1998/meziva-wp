<?php
/**
 * Template Name: Terms & Conditions
 */
get_header();

// ACF Fields
$main_heading    = get_field('tc_main_heading');
$main_subheading = get_field('tc_main_subheading');

$heading_1 = get_field('tc_heading_1');
$desc_1    = get_field('tc_desc_1');

$heading_2 = get_field('tc_heading_2');
$desc_2    = get_field('tc_desc_2');

$heading_3 = get_field('tc_heading_3');
$desc_3    = get_field('tc_desc_3');
?>

<!-- ===============================
     TERMS & CONDITIONS SECTION
================================ -->
<section class="mz-bg-white mz-py-16 lg:mz-py-16">

  <div class="mz-max-w-4xl mz-mx-auto mz-px-4">

    <!-- MAIN HEADING -->
    <div class="mz-text-center mz-mb-12">
      <?php if ($main_heading): ?>
        <h1 class="mz-text-3xl md:mz-text-4xl mz-font-semibold mz-text-gray-900 mz-mb-4">
          <?php echo esc_html($main_heading); ?> 
        </h1>
      <?php endif; ?>

      <?php if ($main_subheading): ?>
        <p class="mz-text-base mz-text-gray-500 mz-leading-relaxed">
          <?php echo esc_html($main_subheading); ?>
        </p>
      <?php endif; ?>
    </div>

    <!-- CONTENT AREA -->
    <div class="mz-space-y-10">

      <!-- SECTION 1 -->
      <?php if ($heading_1 && $desc_1): ?>
        <div>
          <h2 class="mz-text-xl md:mz-text-2xl mz-font-semibold mz-text-gray-900 mz-mb-3">
            <?php echo esc_html($heading_1); ?>
          </h2>

          <div class="mz-text-gray-600 mz-text-sm md:mz-text-base mz-leading-7">
            <?php echo wp_kses_post($desc_1); ?>
          </div>
        </div>
      <?php endif; ?>

      <!-- SECTION 2 -->
      <?php if ($heading_2 && $desc_2): ?>
        <div>
          <h2 class="mz-text-xl md:mz-text-2xl mz-font-semibold mz-text-gray-900 mz-mb-3">
            <?php echo esc_html($heading_2); ?>
          </h2>

          <div class="mz-text-gray-600 mz-text-sm md:mz-text-base mz-leading-7">
            <?php echo wp_kses_post($desc_2); ?>
          </div>
        </div>
      <?php endif; ?>

      <!-- SECTION 3 -->
      <?php if ($heading_3 && $desc_3): ?>
        <div>
          <h2 class="mz-text-xl md:mz-text-2xl mz-font-semibold mz-text-gray-900 mz-mb-3">
            <?php echo esc_html($heading_3); ?>
          </h2>

          <div class="mz-text-gray-600 mz-text-sm md:mz-text-base mz-leading-7">
            <?php echo wp_kses_post($desc_3); ?>
          </div>
        </div>
      <?php endif; ?>

    </div>

  </div>
</section>

<?php get_footer(); ?>
