<?php
$t1 = get_field('mz_trust_1_text') ?: 'Free Shipping';
$t2 = get_field('mz_trust_2_text') ?: 'COD Available';
$t3 = get_field('mz_trust_3_text') ?: '3-5 Days Delivery';
$t4 = get_field('mz_trust_4_text') ?: 'Secure Razorpay Checkout';
?>

<section class="mz-w-full mz-bg-white mz-border mz-border-gray-200 mz-border-r-0 mz-border-l-0 mz-px-4 mz-py-3 lg:mz-py-5 md:mz-px-5">
  <!-- Mobile: 2 cols x 2 rows | Desktop: 4 cols in 1 row -->
  <div class="mz-grid mz-grid-cols-2 md:mz-grid-cols-4 mz-gap-x-4 mz-gap-y-3 md:mz-gap-y-0 md:mz-gap-x-6 md:mz-items-center md:mz-justify-items-center xl:mz-max-w-[1000px] mz-mx-auto">

    <!-- Item 1 -->
    <div class="mz-flex mz-items-center mz-gap-2 mz-py-1 md:mz-py-0">
      <span class="mz-inline-flex mz-h-10 mz-w-10 mz-items-center mz-justify-center mz-rounded-full mz-bg-brand-secondary mz-text-orange-700">
        <!-- Truck icon -->
        <svg class="mz-h-6 mz-w-6" viewBox="0 0 24 24" fill="none" aria-hidden="true">
          <path d="M3 7.5h11v9H3v-9Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
          <path d="M14 10h4.2l2.8 3.2V16.5H14V10Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
          <path d="M6.5 18.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Z" stroke="currentColor" stroke-width="1.6"/>
          <path d="M17.5 18.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Z" stroke="currentColor" stroke-width="1.6"/>
        </svg>
      </span>
      <span class="mz-text-sm md:mz-text-[15px] mz-font-medium mz-text-gray-900">
        <?php echo esc_html($t1); ?>
      </span>
    </div>

    <!-- Item 2 -->
    <div class="mz-flex mz-items-center mz-gap-2 mz-py-1 md:mz-py-0">
      <span class="mz-inline-flex mz-h-10 mz-w-10 mz-items-center mz-justify-center mz-rounded-full mz-bg-brand-secondary mz-text-orange-700">
        <!-- Wallet/COD icon -->
        <svg class="mz-h-6 mz-w-6" viewBox="0 0 24 24" fill="none" aria-hidden="true">
          <path d="M4 7.5A2.5 2.5 0 0 1 6.5 5H19a1 1 0 0 1 1 1v2.2" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
          <path d="M4 8.5h15.5A2.5 2.5 0 0 1 22 11v6a2.5 2.5 0 0 1-2.5 2.5H6.5A2.5 2.5 0 0 1 4 17V8.5Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
          <path d="M18.2 14h.01" stroke="currentColor" stroke-width="2.6" stroke-linecap="round"/>
        </svg>
      </span>
      <span class="mz-text-sm md:mz-text-[15px] mz-font-medium mz-text-gray-900">
        <?php echo esc_html($t2); ?>
      </span>
    </div>

    <!-- Item 3 -->
    <div class="mz-flex mz-items-center mz-gap-2 mz-py-1 md:mz-py-0">
      <span class="mz-inline-flex mz-h-10 mz-w-10 mz-items-center mz-justify-center mz-rounded-full mz-bg-brand-secondary mz-text-orange-700">
        <!-- Calendar/Delivery icon -->
        <svg class="mz-h-6 mz-w-6" viewBox="0 0 24 24" fill="none" aria-hidden="true">
          <path d="M7 4.5v3M17 4.5v3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
          <path d="M5.5 8h13A2 2 0 0 1 20.5 10v9A2 2 0 0 1 18.5 21h-13A2 2 0 0 1 3.5 19v-9A2 2 0 0 1 5.5 8Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
          <path d="M7.5 12h3M7.5 15.5h3M13.5 12h3M13.5 15.5h3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
        </svg>
      </span>
      <span class="mz-text-sm md:mz-text-[15px] mz-font-medium mz-text-gray-900">
        <?php echo esc_html($t3); ?>
      </span>
    </div>

    <!-- Item 4 -->
    <div class="mz-flex mz-items-center mz-gap-2 mz-py-1 md:mz-py-0">
      <span class="mz-inline-flex mz-h-10 mz-w-10 mz-items-center mz-justify-center mz-rounded-full mz-bg-brand-secondary mz-text-orange-700">
        <!-- Shield/Lock icon -->
        <svg class="mz-h-6 mz-w-6" viewBox="0 0 24 24" fill="none" aria-hidden="true">
          <path d="M12 3.5l7 3v6.2c0 4.7-3 8-7 9.8-4-1.8-7-5.1-7-9.8V6.5l7-3Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
          <path d="M9.5 11.5a2.5 2.5 0 0 1 5 0v2.5H9.5v-2.5Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
          <path d="M12 16.8h.01" stroke="currentColor" stroke-width="2.6" stroke-linecap="round"/>
        </svg>
      </span>
      <span class="mz-text-sm md:mz-text-[15px] mz-font-medium mz-text-gray-900">
        <?php echo esc_html($t4); ?>
      </span>
    </div>

  </div> 
</section>