<?php
/**
 * Template Name: FAQ Page
 */
get_header();

function mz_render_faq_section($title_field, $icon_field, $q_prefix, $a_prefix, $max = 10) {
  $title = get_field($title_field);
  $icon  = get_field($icon_field);
  if (!$title) return;
  ?>
  <div class="mz-mb-14">

    
      
    </div>

    <div class="mz-border-t mz-border-gray-200 mz-accordion">
      <?php for ($i=1; $i <= $max; $i++): ?>
        <?php
          $q = get_field($q_prefix . $i);
          $a = get_field($a_prefix . $i);
          if (!$q || !$a) continue;
        ?>
        <div class="mz-acc-item mz-border-b mz-border-gray-200">
          <button type="button"
            class="mz-acc-btn mz-w-full mz-border-none mz-flex mz-items-center mz-justify-between mz-gap-6 mz-py-5 md:mz-py-6 mz-text-left"
            aria-expanded="false">
            <span class="mz-text-base md:mz-text-lg mz-font-medium mz-text-gray-900">
              <?php echo esc_html($q); ?>
            </span>
            <span class="mz-acc-icon mz-text-2xl mz-leading-none mz-text-gray-500">+</span>
          </button>

          <div class="mz-acc-panel" style="max-height:0;overflow:hidden;transition:max-height 320ms ease;">
            <div class="mz-pb-6">
              <div class="mz-text-sm md:mz-text-[15px] mz-leading-relaxed mz-text-gray-600">
                <?php echo wp_kses_post($a); ?>
              </div>
            </div>
          </div>
        </div>
      <?php endfor; ?>
    </div>

  </div>
  <?php
}
?>

<style>
.mz-acc-btn:hover{background:none;}
.mz-acc-btn:focus{background:none !important;
  box-shadow:none !important;
}

.mz-acc-icon{transition:transform .22s ease;}
.mz-acc-item.is-open .mz-acc-icon{transform:rotate(45deg);} /* + rotates like Sugar style */
</style>

<section class="mz-bg-white mz-max-w-6xl mz-mx-auto mz-px-4 sm:mz-px-6 lg:mz-px-8 mz-py-10 lg:mz-py-14">
  <div class="mz-max-w-3xl mz-mx-auto mz-px-4">

    <!-- <?php if (get_field('sub_heading')): ?>
      <p class="mz-text-center mz-text-xs mz-tracking-[0.25em] mz-uppercase mz-text-gray-500 mz-mb-3">
        <?php echo esc_html(get_field('sub_heading')); ?>
      </p>
    <?php endif; ?> -->

    <?php if (get_field('section_heading')): ?>
      <h1 class="mz-text-[36px] xl:mz-text-[50px] mz-text-center mz-leading-[1.05] mz-font-extrabold mz-tracking-tight mz-text-brand-accent mz-mb-8 xl:mz-mb-5">
         <?php echo esc_html(get_field('section_heading')); ?>
      </h1>
    <?php endif; ?>

    <?php
      // GENERAL
      mz_render_faq_section(
        'general_title',
        'general_icon',
        'general_q_',
        'general_a_',
        10
      );

      // SHIPPING
      mz_render_faq_section(
        'shipping_title',
        'shipping_icon',
        'shipping_q_',
        'shipping_a_',
        10
      );
    ?>

  </div>
</section>

<script>
document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll(".mz-accordion").forEach((accordion) => {
    const items = accordion.querySelectorAll(".mz-acc-item");

    function closeAll() {
      items.forEach((item) => {
        item.classList.remove("is-open");
        const btn = item.querySelector(".mz-acc-btn");
        const panel = item.querySelector(".mz-acc-panel");
        const icon = item.querySelector(".mz-acc-icon");

        btn.setAttribute("aria-expanded", "false");
        panel.style.maxHeight = "0px";
        icon.textContent = "+";
      });
    }

    items.forEach((item) => {
      const btn = item.querySelector(".mz-acc-btn");
      const panel = item.querySelector(".mz-acc-panel");
      const icon = item.querySelector(".mz-acc-icon");

      btn.addEventListener("click", () => {
        const isOpen = item.classList.contains("is-open");
        closeAll();

        if (!isOpen) {
          item.classList.add("is-open");
          btn.setAttribute("aria-expanded", "true");
          icon.textContent = "+";
          panel.style.maxHeight = panel.scrollHeight + "px";
        }
      });
    });
  });
});
</script>
 
<?php get_footer(); ?>
 