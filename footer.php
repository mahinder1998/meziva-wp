<?php if ( ! defined('ABSPATH') ) exit; ?>

<?php
/**
 * FOOTER (Same Design) - Global ACF values
 * - If ACF Options Page exists => use 'option'
 * - Else use Front Page (Home) ID so footer settings apply site-wide
 */

// Context decide
if ( function_exists('acf_add_options_page') ) {
  $ctx = 'option';
} else {
  // Use front page ID as global source
  $ctx = (int) get_option('page_on_front');
  if ( ! $ctx ) {
    $ctx = 9; // fallback home page id (change if needed)
  }
}

/** Brand block (replaces newsletter) */
$brand_desc = get_field('ft_brand_desc', $ctx) ?: "Meziva is a modern lip care brand crafted for everyday glow.
We blend nourishing ingredients with soft tints and SPF protection — giving your lips comfort, care and confidence in every swipe.";

/** Socials */
$fb = get_field('ft_fb_url', $ctx) ?: '#';
$yt = get_field('ft_yt_url', $ctx) ?: '#';
$tw = get_field('ft_tw_url', $ctx) ?: '#';
$ig = get_field('ft_ig_url', $ctx) ?: '#';

/** Copyright */
$copy_text   = get_field('ft_copyright_text', $ctx) ?: 'Copyright © ' . date('Y');
$copy_brand  = get_field('ft_copyright_brand', $ctx) ?: 'Meziva.';
$copy_line2  = get_field('ft_copyright_line2', $ctx) ?: 'All Rights Reserved.';

/** Footer menus */
$c1 = [
  ['t'=>get_field('ft_c1_l1_text',$ctx) ?: 'Brands',         'u'=>get_field('ft_c1_l1_url',$ctx) ?: '#'],
  ['t'=>get_field('ft_c1_l2_text',$ctx) ?: 'Gift Vouchers',   'u'=>get_field('ft_c1_l2_url',$ctx) ?: '#'],
  ['t'=>get_field('ft_c1_l3_text',$ctx) ?: 'Affiliates',      'u'=>get_field('ft_c1_l3_url',$ctx) ?: '#'],
];

$c2 = [
  ['t'=>get_field('ft_c2_l1_text',$ctx) ?: 'About Us', 'u'=>get_field('ft_c2_l1_url',$ctx) ?: '#'],
  ['t'=>get_field('ft_c2_l2_text',$ctx) ?: 'FAQs',     'u'=>get_field('ft_c2_l2_url',$ctx) ?: '#'],
  ['t'=>get_field('ft_c2_l3_text',$ctx) ?: 'Blog',     'u'=>get_field('ft_c2_l3_url',$ctx) ?: '#'],
];

/** Colors */
$bg          = get_field('ft_bg_color', $ctx) ?: '#9B4A6A';
$text_color  = get_field('ft_text_color', $ctx) ?: '#F6EFEA';
$social_bg   = get_field('ft_social_bg', $ctx) ?: '#FFFFFF';
$social_col  = get_field('ft_social_color', $ctx) ?: '#9B4A6A';

function mz_footer_link($item) {
  $t = trim($item['t'] ?? '');
  $u = trim($item['u'] ?? '');
  if (!$t || !$u) return;
  echo '<li><a class="mz-inline-flex hover:mz-opacity-70 mz-transition mz-text-[15px]" href="'.esc_url($u).'">'.esc_html($t).'</a></li>';
}
?>

<style>
  footer ul > li > a:hover { color: #F6EFEA; }

  /* Heading style for footer menus */
  .mz-ft-heading{
    font-size:14px;
    font-weight:600;
    margin-bottom:10px;
    color:#fff;
    letter-spacing:.3px;
  }

  /* Make WP custom logo fit nicely */
  footer .custom-logo-link img{
    max-height: 44px;
    width: auto;
  }
</style>

<footer
  class="mz-w-full mz-border-t mz-border-black/5"
  style="background-color: <?php echo esc_attr($bg); ?>; color: <?php echo esc_attr($text_color); ?>;"
>
  <div class="mz-max-w-[1290px] mz-mx-auto mz-px-4 md:mz-px-6 mz-py-12 md:mz-py-14 xl:mz-px-0">

    <!-- Layout: mobile stacked / desktop 4 columns -->
    <div class="mz-grid mz-grid-cols-1 md:mz-grid-cols-12 mz-gap-10 md:mz-gap-8 mz-items-start">

      <!-- 1) Brand (replaces Newsletter) -->
      <div class="md:mz-col-span-4 mz-text-center md:mz-text-left">
        <div class="mz-mb-4 mz-flex mz-justify-center md:mz-justify-start">
          <?php
            if ( has_custom_logo() ) {
              the_custom_logo();
            } else {
              echo '<span class="mz-text-[22px] mz-font-semibold mz-text-white">MEZIVA</span>';
            }
          ?>
        </div>

        <p class="mz-text-[14px] mz-leading-[1.7] mz-max-w-[360px] mz-mx-auto md:mz-mx-0">
          <?php echo nl2br(esc_html($brand_desc)); ?>
        </p>
      </div>

      <!-- 2) Socials + Copyright (UNCHANGED SVGs) -->
      <div class="md:mz-col-span-4">
        <div class="mz-flex mz-justify-center md:mz-justify-center mz-gap-3">
          <!-- Facebook -->
          <a target="_blank" href="<?php echo esc_url($fb); ?>"
             class="mz-w-[44px] mz-h-[44px] mz-rounded-full mz-flex mz-items-center mz-justify-center
                    hover:mz-opacity-80 mz-transition" 
             style="background-color: <?php echo esc_attr($social_bg); ?>; color: <?php echo esc_attr($social_col); ?>;"
             aria-label="Facebook"
          >
            <svg class="mz-w-[18px] mz-h-[18px]" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
              <path d="M22 12a10 10 0 1 0-11.6 9.9v-7H8v-3h2.4V9.7c0-2.4 1.4-3.7 3.6-3.7 1 0 2 .2 2 .2v2.2h-1.1c-1.1 0-1.4.7-1.4 1.4V12H16l-.4 3h-2.1v7A10 10 0 0 0 22 12z"/>
            </svg>
          </a>

           <!-- Instagram -->
          <a target="_blank" href="<?php echo esc_url($ig); ?>"
             class="mz-w-[44px] mz-h-[44px] mz-rounded-full mz-flex mz-items-center mz-justify-center
                    hover:mz-opacity-80 mz-transition"
             style="background-color: <?php echo esc_attr($social_bg); ?>; color: <?php echo esc_attr($social_col); ?>;"
             aria-label="Instagram"
          >
            <svg class="mz-w-[18px] mz-h-[18px]" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
              <path d="M7 2h10a5 5 0 0 1 5 5v10a5 5 0 0 1-5 5H7a5 5 0 0 1-5-5V7a5 5 0 0 1 5-5zm10 2H7a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3V7a3 3 0 0 0-3-3zm-5 4.5A3.5 3.5 0 1 1 8.5 12 3.5 3.5 0 0 1 12 8.5zm0 2A1.5 1.5 0 1 0 13.5 12 1.5 1.5 0 0 0 12 10.5zM18 6.2a.8.8 0 1 1-.8.8.8.8 0 0 1 .8-.8z"/>
            </svg>
          </a>

          <!-- YouTube -->
          <a target="_blank" href="<?php echo esc_url($yt); ?>"
             class="mz-w-[44px] mz-h-[44px] mz-rounded-full mz-flex mz-items-center mz-justify-center
                    hover:mz-opacity-80 mz-transition"
             style="background-color: <?php echo esc_attr($social_bg); ?>; color: <?php echo esc_attr($social_col); ?>;"
             aria-label="YouTube"
          >
            <svg class="mz-w-[18px] mz-h-[18px]" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
              <path d="M21.6 7.2s-.2-1.6-.8-2.3c-.8-.8-1.7-.8-2.1-.9C15.8 3.7 12 3.7 12 3.7h0s-3.8 0-6.7.3c-.4 0-1.3.1-2.1.9-.6.7-.8 2.3-.8 2.3S2 9 2 10.8v1.7c0 1.8.4 3.6.4 3.6s.2 1.6.8 2.3c.8.8 1.9.8 2.4.9 1.7.2 6.4.3 6.4.3s3.8 0 6.7-.3c.4 0 1.3-.1 2.1-.9.6-.7.8-2.3.8-2.3s.4-1.8.4-3.6v-1.7c0-1.8-.4-3.6-.4-3.6zM10 14.9V8.9l6 3-6 3z"/>
            </svg>
          </a>

          <!-- linkedin -->
          <a target="_blank"  href="<?php echo esc_url($tw); ?>"
             class="mz-w-[44px] mz-h-[44px] mz-rounded-full mz-flex mz-items-center mz-justify-center
                    hover:mz-opacity-80 mz-transition"
             style="background-color: <?php echo esc_attr($social_bg); ?>; color: <?php echo esc_attr($social_col); ?>;"
             aria-label="Twitter"
          >
            <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
              <path fill-rule="evenodd" d="M12.51 8.796v1.697a3.738 3.738 0 0 1 3.288-1.684c3.455 0 4.202 2.16 4.202 4.97V19.5h-3.2v-5.072c0-1.21-.244-2.766-2.128-2.766-1.827 0-2.139 1.317-2.139 2.676V19.5h-3.19V8.796h3.168ZM7.2 6.106a1.61 1.61 0 0 1-.988 1.483 1.595 1.595 0 0 1-1.743-.348A1.607 1.607 0 0 1 5.6 4.5a1.601 1.601 0 0 1 1.6 1.606Z" clip-rule="evenodd"/>
              <path d="M7.2 8.809H4V19.5h3.2V8.809Z"/>
            </svg>

          </a>

         
        </div>

        <div class="mz-mt-6 mz-text-center mz-text-[14px] mz-leading-[1.6]">
          <div>
            <?php echo esc_html($copy_text); ?>
            <span class="mz-font-semibold"><?php echo esc_html($copy_brand); ?></span>
          </div>
          <div><?php echo esc_html($copy_line2); ?></div>
        </div>
      </div>

      <!-- 3) Links Column 1 -->
      <div class="md:mz-col-span-2">
        <div class=" mz-text-center md:mz-text-[16px] mz-font-semibold mz-mb-3  md:mz-text-left">Explore</div>
        <ul class="mz-space-y-2 mz-text-center md:mz-text-left">
          <?php foreach ($c1 as $it) { mz_footer_link($it); } ?>
        </ul>
      </div>

      <!-- 4) Links Column 2 -->
      <div class="md:mz-col-span-2">
        <div class="mz-text-center md:mz-text-[16px] mz-font-semibold mz-mb-3  md:mz-text-left" >Company</div>
        <ul class="mz-space-y-2 mz-text-center md:mz-text-left">
          <?php foreach ($c2 as $it) { mz_footer_link($it); } ?>
        </ul>
      </div>

    </div>
  </div>
</footer>



<?php wp_footer(); ?>



<script>
document.addEventListener("DOMContentLoaded", function () {

    var scrollBtn = document.getElementById("ast-scroll-top");
    if (!scrollBtn) return;

    window.addEventListener("scroll", function () {
        scrollBtn.style.display = window.scrollY > 300 ? "flex" : "none";
    });

    scrollBtn.addEventListener("click", function () {
        window.scrollTo({ top: 0, behavior: "smooth" });
    });

});
</script>
</body>
</html>
