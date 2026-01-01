<?php
/**
 * Template Name: FAQ Page
 */
get_header();
?>

<section class="mz-bg-white mz-py-20">
  <div class="mz-max-w-5xl mz-mx-auto mz-px-4">

    <!-- ===== SECTION HEADING ===== -->
    <?php if (get_field('section_heading')): ?>
      <h2 class="mz-text-4xl mz-font-bold mz-text-center mz-mb-2">
        <?php echo esc_html(get_field('section_heading')); ?>
      </h2>
    <?php endif; ?>

    <?php if (get_field('sub_heading')): ?>
      <p class="mz-text-center mz-text-gray-500 mz-mb-2">
        <?php echo esc_html(get_field('sub_heading')); ?>
      </p>
    <?php endif; ?>

    <?php if (get_field('frequently_asked_questions')): ?>
      <h3 class="mz-text-center mz-text-lg mz-font-medium mz-mb-10">
        <?php echo esc_html(get_field('frequently_asked_questions')); ?>
      </h3>
    <?php endif; ?>

    <!-- ===== FAQ TABS ===== -->
    <div class="faq-wrapper mz-flex mz-flex-col mz-gap-[5px]">

      <?php
      for ($i = 1; $i <= 4; $i++):
        $heading = get_field("heading_tab_{$i}") ?: get_field("tab_heading_{$i}");
        $desc    = get_field("description_tab_{$i}") ?: get_field("tab_description_{$i}");
        if (!$heading || !$desc) continue;
      ?>

      <div class="faq-item mz-border mz-rounded-lg mz-overflow-hidden">

        <button type="button"
          class="faq-btn mz-w-full mz-flex mz-justify-between mz-items-center 
                 mz-px-6 mz-py-4 mz-bg-transparent mz-transition">

          <span class="faq-title mz-text-lg mz-font-medium mz-text-gray-900">
            <?php echo esc_html($heading); ?>
          </span>

          <span class="faq-icon mz-text-2xl mz-font-bold mz-text-gray-900">+</span>
        </button>

        <div class="faq-content mz-hidden mz-px-6 mz-py-4 mz-text-gray-700">
          <?php echo wp_kses_post($desc); ?>
        </div>

      </div>

      <?php endfor; ?>

    </div>
  </div>
</section>

<!-- =========================
     SHIPPING TABS SECTION
========================= -->
<section class="mz-bg-white mz-py-20">
  <div class="mz-max-w-5xl mz-mx-auto mz-px-4">

    <!-- ===== SECTION HEADING ===== -->
    <?php if (get_field('shipping_section_heading')): ?>
      <h2 class="mz-text-3xl md:mz-text-4xl mz-font-bold mz-text-center mz-mb-10">
        <?php echo esc_html(get_field('shipping_section_heading')); ?>
      </h2>
    <?php endif; ?>

    <!-- ===== SHIPPING TABS ===== -->
    <div class="shipping-wrapper mz-flex mz-flex-col mz-gap-[5px]">

      <!-- ===== TAB 1 ===== -->
      <?php if (get_field('shipping__tab_heading_1') && get_field('shipping__tab_description_1')): ?>
      <div class="shipping-item mz-border mz-rounded-lg mz-overflow-hidden">
        <button type="button"
          class="shipping-btn mz-w-full mz-flex mz-justify-between mz-items-center mz-px-6 mz-py-4 mz-bg-transparent mz-transition">

          <span class="shipping-title mz-text-lg mz-font-medium mz-text-gray-900">
            <?php echo esc_html(get_field('shipping__tab_heading_1')); ?>
          </span>

          <span class="shipping-icon mz-text-2xl mz-font-bold mz-text-gray-900">+</span>
        </button>

        <div class="shipping-content mz-hidden mz-px-6 mz-py-4 mz-text-gray-700">
          <?php echo wp_kses_post(get_field('shipping__tab_description_1')); ?>
        </div>
      </div>
      <?php endif; ?>

      <!-- ===== TAB 2 ===== -->
      <?php if (get_field('shipping__tab_heading_2') && get_field('shipping_tab_description_2')): ?>
      <div class="shipping-item mz-border mz-rounded-lg mz-overflow-hidden">
        <button type="button"
          class="shipping-btn mz-w-full mz-flex mz-justify-between mz-items-center mz-px-6 mz-py-4 mz-bg-transparent mz-transition">

          <span class="shipping-title mz-text-lg mz-font-medium mz-text-gray-900">
            <?php echo esc_html(get_field('shipping__tab_heading_2')); ?>
          </span>

          <span class="shipping-icon mz-text-2xl mz-font-bold mz-text-gray-900">+</span>
        </button>

        <div class="shipping-content mz-hidden mz-px-6 mz-py-4 mz-text-gray-700">
          <?php echo wp_kses_post(get_field('shipping_tab_description_2')); ?>
        </div>
      </div>
      <?php endif; ?>

      <!-- ===== TAB 3 ===== -->
      <?php if (get_field('shipping__tab_heading_3') && get_field('shipping__tab_description_3')): ?>
      <div class="shipping-item mz-border mz-rounded-lg mz-overflow-hidden">
        <button type="button"
          class="shipping-btn mz-w-full mz-flex mz-justify-between mz-items-center mz-px-6 mz-py-4 mz-bg-transparent mz-transition">

          <span class="shipping-title mz-text-lg mz-font-medium mz-text-gray-900">
            <?php echo esc_html(get_field('shipping__tab_heading_3')); ?>
          </span>

          <span class="shipping-icon mz-text-2xl mz-font-bold mz-text-gray-900">+</span>
        </button>

        <div class="shipping-content mz-hidden mz-px-6 mz-py-4 mz-text-gray-700">
          <?php echo wp_kses_post(get_field('shipping__tab_description_3')); ?>
        </div>
      </div>
      <?php endif; ?>

    </div>
  </div>
</section>

<!-- ===== SHIPPING TAB SCRIPT ===== -->
<script>
document.addEventListener("DOMContentLoaded", function () {

  document.querySelectorAll(".shipping-btn").forEach(btn => {

    btn.addEventListener("click", function () {

      const item    = this.closest(".shipping-item");
      const content = item.querySelector(".shipping-content");
      const icon    = item.querySelector(".shipping-icon");
      const title   = item.querySelector(".shipping-title");

      const isOpen = !content.classList.contains("mz-hidden");

      // close all tabs
      document.querySelectorAll(".shipping-item").forEach(other => {
        other.querySelector(".shipping-content").classList.add("mz-hidden");
        other.querySelector(".shipping-icon").innerHTML = "+";
        other.querySelector(".shipping-btn").style.backgroundColor = "";
        other.querySelector(".shipping-title").classList.remove("mz-text-white");
        other.querySelector(".shipping-icon").classList.remove("mz-text-white");
      });

      // toggle same tab
      if (!isOpen) {
        content.classList.remove("mz-hidden");
        icon.innerHTML = "−";
        this.style.backgroundColor = "#9b4a6a";
        title.classList.add("mz-text-white");
        icon.classList.add("mz-text-white");
      }
    });

  });

});
</script>




<!-- ===== FAQ SCRIPT (FINAL FIXED) ===== -->
<script>
document.addEventListener("DOMContentLoaded", function () {

  document.querySelectorAll(".faq-btn").forEach(btn => {

    btn.addEventListener("click", function () {

      const item    = this.closest(".faq-item");
      const content = item.querySelector(".faq-content");
      const icon    = item.querySelector(".faq-icon");
      const title   = item.querySelector(".faq-title");

      const isOpen = !content.classList.contains("mz-hidden");

      // CLOSE ALL FIRST
      document.querySelectorAll(".faq-item").forEach(other => {
        other.querySelector(".faq-content").classList.add("mz-hidden");
        other.querySelector(".faq-icon").innerHTML = "+";
        other.querySelector(".faq-btn").style.backgroundColor = "";
        other.querySelector(".faq-title").classList.remove("mz-text-white");
        other.querySelector(".faq-icon").classList.remove("mz-text-white");
      });

      // TOGGLE SAME TAB
      if (!isOpen) {
        content.classList.remove("mz-hidden");
        icon.innerHTML = "−";
        this.style.backgroundColor = "#9b4a6a";
        title.classList.add("mz-text-white");
        icon.classList.add("mz-text-white");
      }
    });

  });

});
</script>

<?php get_footer(); ?>
