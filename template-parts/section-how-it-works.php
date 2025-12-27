<?php
/**
 * SECTION: How It Works (ACF FREE – No Repeater)
 */

$heading = get_field('section_heading') ?: 'How it works';
$desc    = get_field('section_description');

$steps = [
  [
    'image' => get_field('step_1_image'),
    'title' => get_field('step_1_title') ?: 'CLEANSE',
    'desc'  => get_field('step_1_description'),
  ],
  [
    'image' => get_field('step_2_image'),
    'title' => get_field('step_2_title') ?: 'MASK',
    'desc'  => get_field('step_2_description'),
  ],
  [
    'image' => get_field('step_3_image'),
    'title' => get_field('step_3_title') ?: 'TREAT',
    'desc'  => get_field('step_3_description'),
  ],
  [
    'image' => get_field('step_4_image'),
    'title' => get_field('step_4_title') ?: 'MOISTURIZE',
    'desc'  => get_field('step_4_description'),
  ],
];

$show_stat   = get_field('show_stat');
?>


<style>
    .how-it-items:hover .background-shad{
        background:#C58BAA  !important;
    }
    .how-it-items:hover .background-count{
         background:#F6EFEA  !important;
         color:#2B1C23 !important;
    }
     .how-it-items:hover .background-count span{
         color:#2B1C23 !important;
    }
</style>

<section class="mz-w-full">
  <div class="mz-max-w-[1290px] mz-mx-auto mz-px-4 mz-py-10 md:mz-py-20 xl:mz-px-0">
  <?php if ($heading): ?>
    <div class="section-header md:mz-col-span-2 mz-mb-8 mz-text-center  xl:md:mz-col-span-1 xl:mz-text-left">
        <h2 class="mz-text-[36px] xl:mz-text-[50px] mz-leading-[1.05] mz-font-extrabold mz-tracking-tight mz-text-brand-accent
        mz-mb-8 xl:mz-mb-5
        ">
            <?php echo wp_kses($heading, ['br' => []]); ?>
        </h2>
            <?php if ($desc): ?>
        <p class="mz-mt-4 mz-text-[18px] md:mz-text-[18px] mz-font-semibold mz-text-text-heading">
            <?php echo esc_html($desc); ?>
            
        </p>
        <?php endif; ?>

    </div>
    <?php endif; ?>

    <?php if ($show_stat): ?>
      <div class="mz-absolute mz-right-0 mz-top-[120px] mz-bg-white mz-shadow-lg mz-px-4 mz-py-3 mz-text-center">
        <div class="mz-text-[26px] mz-font-semibold mz-text-[#2E9B3C]">
          <?php echo esc_html($stat_number); ?>
        </div>
        <div class="mz-text-[12px] mz-font-semibold">
          <?php echo esc_html($stat_label); ?>
        </div>
      </div>
    <?php endif; ?>

    <div class="mz-grid mz-grid-cols-1  md:mz-grid-cols-2 xl:mz-grid-cols-4 mz-gap-y-14 mz-gap-x-10">
      <?php foreach ($steps as $i => $step): ?>
        <div class="mz-text-center mz-group how-it-items mz-grid mz-gap-3 xl:mz-gap-3">
          <div class="mz-relative mz-w-[220px] mz-h-[220px] mz-mx-auto">
            <div class="mz-absolute mz-inset-0 mz-rounded-full mz-bg-brand-secondary
            mz-transition-colors mz-duration-300 
           mz-group-hover:mz-bg-brand-accent background-shad
            "></div>

            <div class="mz-absolute mz-inset-[14px] mz-w-[200px] mz-left-0 mz-h-[200px] mz-rounded-full mz-overflow-hidden mz-bg-white">
              <?php if (!empty($step['image'])): ?>
                <img
                  src="<?php echo esc_url($step['image']['sizes']['large']); ?>"
                  alt=""
                  class="mz-w-full mz-h-full mz-object-cover"
                >
              <?php endif; ?>
            </div>

            <div class="mz-absolute background-count mz-top-[-10px] mz-left-[-10px] mz-w-[50px] mz-h-[50px] mz-bg-brand-accent mz-transition-colors mz-font-heading mz-rounded-full mz-flex mz-items-center mz-justify-center
               mz-duration-300 
           mz-group-hover:mz-bg-white
            ">
              <span class="mz-text-white mz-text-base mz-font-heading mz-font-bold">
                <?php echo $i + 1; ?>
              </span>
            </div>
          </div>

          <h3 class="mz-text-2xl mz-font-heading md:mz-text-2xl mz-font-semibold mz-leading-snug xl:mz-pt-3">
            <?php echo esc_html($step['title']); ?>
          </h3>

          <?php if ($step['desc']): ?>
            <p class="mz-text-base md:mz-text-[15px] mz-max-w-[240px] mz-mx-auto xl:mz-text-[16px] mz-leading-relaxed  mz-px-4 md:mz-px-0">
              <?php echo esc_html($step['desc']); ?>
            </p>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>

  </div>
</section>
  