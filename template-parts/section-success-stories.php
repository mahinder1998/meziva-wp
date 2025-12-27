<?php
$heading    = get_field('ss_heading') ?: '';
$subheading = get_field('ss_subheading') ?: '';

$bg_color   = get_field('ss_bg_color') ?: '#ffffff';
$text_color = get_field('ss_text_color') ?: '#000000';

$reviews = [
  [
    'quote' => get_field('ss_r1_quote'),
    'name'  => get_field('ss_r1_name'),
    'role'  => get_field('ss_r1_role'),
  ],
  [
    'quote' => get_field('ss_r2_quote'),
    'name'  => get_field('ss_r2_name'),
    'role'  => get_field('ss_r2_role'),
  ],
  [
    'quote' => get_field('ss_r3_quote'),
    'name'  => get_field('ss_r3_name'),
    'role'  => get_field('ss_r3_role'),
  ],
];

$before = get_field('ss_before_image');
$after  = get_field('ss_after_image');
?>

<section
  style="background-color: <?php echo esc_attr($bg_color); ?>; color: <?php echo esc_attr($text_color); ?>;"
>
  <div class="mz-max-w-[1290px] mz-mx-auto mz-px-4 mz-py-10 md:mz-py-20 xl:mz-px-0">

    <div class="mz-grid mz-grid-cols-1 md:mz-grid-cols-12 mz-gap-10 xl:mz-gap-[100px] ">

      <!-- LEFT -->
      <div class="md:mz-col-span-6 mz-overflow-hidden">
         <?php if ($heading): ?>
            <div class="section-header md:mz-col-span-2 mz-mb-8 mz-text-center  xl:md:mz-col-span-1 xl:mz-text-left">
                <h2 class="mz-text-[36px] xl:mz-text-[50px] mz-leading-[1.05] mz-font-extrabold mz-tracking-tight mz-text-brand-accent
                mz-mb-8 xl:mz-mb-5
                ">
                    <?php echo wp_kses($heading, ['br' => []]); ?>
                </h2>
                    <?php if ($subheading): ?>
                <p class="mz-mt-4 mz-text-[18px] md:mz-text-[18px] mz-font-semibold mz-text-text-heading">
                    <?php echo esc_html($subheading); ?> 
                    
                </p>
                <?php endif; ?>

            </div>
            <?php endif; ?>


        <div class="mz-mt-10 xl:mz-gap-[30px] md:mz-flex xl:mz-pl-[40px]">
            <div class="mz-text-gray-300 mz-mb-10 mz-w-[60px] mz-h-[60px] mz-mx-auto xl:mz-w-[80px] xl:mz-h-[80px] xl:mz-absolute">
                    <svg width="80px" height="80px" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M18.8533 9.11599C11.3227 13.9523 7.13913 19.5812 6.30256 26.0029C5.00021 36 13.9404 40.8933 18.4703 36.4967C23.0002 32.1002 20.2848 26.5196 17.0047 24.9942C13.7246 23.4687 11.7187 24 12.0686 21.9616C12.4185 19.9231 17.0851 14.2713 21.1849 11.6392C21.4569 11.4079 21.5604 10.9591 21.2985 10.6187C21.1262 10.3947 20.7883 9.95557 20.2848 9.30114C19.8445 8.72888 19.4227 8.75029 18.8533 9.11599Z" fill="#C58BAA"/>
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M38.6789 9.11599C31.1484 13.9523 26.9648 19.5812 26.1282 26.0029C24.8259 36 33.7661 40.8933 38.296 36.4967C42.8259 32.1002 40.1105 26.5196 36.8304 24.9942C33.5503 23.4687 31.5443 24 31.8943 21.9616C32.2442 19.9231 36.9108 14.2713 41.0106 11.6392C41.2826 11.4079 41.3861 10.9591 41.1241 10.6187C40.9519 10.3947 40.614 9.95557 40.1105 9.30114C39.6702 8.72888 39.2484 8.75029 38.6789 9.11599Z" fill="#C58BAA"/>
                    </svg>
                  </div>

          <div class="md:mz-ml-[40px] xl:mz-ml-[100px]">      
          <div class="keen-slider mz-keen-reviews">
            <?php foreach ($reviews as $r): if (!$r['quote']) continue; ?>
              <div class="keen-slider__slide">
                <div class="mz-flex mz-gap-6">
                  <div class="mz-text-center md:mz-text-left">
                    <?php
                    $rating = (int) ($rating ?? 5);
                    $rating = max(0, min(5, $rating));
                    ?>
                    <p class="mz-text-[18px] mz-font-heading mz-text-text-heading md:mz-text-[20px] xl:mz-text-[24px] mz-leading-relaxed">
                      <?php echo esc_html($r['quote']); ?>
                    </p>
                     <div class="mz-flex mz-pt-4 mz-items-center mz-justify-center md:mz-justify-start" aria-label="<?php echo esc_attr($rating); ?> out of 5 stars">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <svg class="mz-w-5 mz-h-5 <?php echo $i <= $rating ? 'mz-text-[#FF6A4D]' : 'mz-text-[#E6E6E6]'; ?>"
                                viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path d="M12 17.27l-5.18 3.04 1.4-5.81L3.5 10.6l6.04-.52L12 4.5l2.46 5.58 6.04.52-4.72 3.89 1.4 5.81L12 17.27z"/>
                            </svg>
                        <?php endfor; ?>
                        </div>
                    <div class="mz-mt-4 mz-text-base mz-font-semibold mz-text-brand-primary">
                      <?php echo esc_html($r['name']); ?>  <?php echo esc_html($r['role']); ?>
                    </div>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
           
          </div>
          <div class="">
            <div class="mz-mt-6 mz-flex mz-gap-3 mz-justify-center md:mz-justify-start" data-keen-dots>
                <button type="button" class="mz-dot mz-w-[8px] mz-h-[8px] mz-rounded-full mz-bg-[#D8D8D8]"></button>
                <button type="button" class="mz-dot mz-w-[8px] mz-h-[8px] mz-rounded-full mz-bg-[#D8D8D8]"></button>
                <button type="button" class="mz-dot mz-w-[8px] mz-h-[8px] mz-rounded-full mz-bg-[#D8D8D8]"></button>
            </div>
          </div>
           </div>  
       

        </div>
      </div>

      <!-- RIGHT -->
      <div class="md:mz-col-span-6">
       <div
        class="mz-compare mz-relative mz-w-full mz-overflow-hidden mz-bg-[#f2f2f2]
                mz-aspect-[1/1] md:mz-aspect-[1/1] lg:mz-aspect-[1/1]"
        data-compare
        style="--mz-cut: 50%;"
        >
        <!-- AFTER (bottom) -->
        <div
            class="mz-absolute mz-inset-0 mz-bg-top mz-bg-no-repeat"
            style="background-image:url('<?php echo esc_url($after['url']); ?>');"
            aria-hidden="true"
        ></div>

        <!-- BEFORE (top, clipped) -->
        <div
            class="mz-compare-top mz-absolute mz-inset-0 mz-bg-top mz-bg-no-repeat"
            style="
            background-image:url('<?php echo esc_url($before['url']); ?>');
            clip-path: polygon(0 0, var(--mz-cut) 0, var(--mz-cut) 100%, 0 100%);
            "
            aria-hidden="true"
        ></div>

        <!-- Divider line -->
        <div
            class="mz-compare-line mz-absolute mz-top-0 mz-bottom-0 mz-w-[3px] mz-bg-white mz-shadow-lg"
            style="left: var(--mz-cut);"
            aria-hidden="true"
        ></div>

        <!-- Handle -->
        <button
            type="button"
            class="mz-compare-handle mz-absolute mz-top-1/2 -mz-translate-y-1/2
                mz-w-[40px] mz-h-[40px] mz-rounded-full mz-bg-white
                mz-shadow-[0_18px_40px_rgba(0,0,0,0.22)]
                mz-flex mz-items-center mz-justify-center
                mz-touch-none
                "
            style="left: var(--mz-cut); transform: translate(-50%, -50%);"
            aria-label="Drag to compare"
        >
            <span class="mz-text-[18px] mz-p-3">
                <svg width="100%" height="100%" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M21 21V3M3 21V3M9 8V16C9 16.9319 9 17.3978 9.15224 17.7654C9.35523 18.2554 9.74458 18.6448 10.2346 18.8478C10.6022 19 11.0681 19 12 19C12.9319 19 13.3978 19 13.7654 18.8478C14.2554 18.6448 14.6448 18.2554 14.8478 17.7654C15 17.3978 15 16.9319 15 16V8C15 7.06812 15 6.60218 14.8478 6.23463C14.6448 5.74458 14.2554 5.35523 13.7654 5.15224C13.3978 5 12.9319 5 12 5C11.0681 5 10.6022 5 10.2346 5.15224C9.74458 5.35523 9.35523 5.74458 9.15224 6.23463C9 6.60218 9 7.06812 9 8Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span> 
        </button>
        </div> 
    </div>
  </div>
</section> 
 