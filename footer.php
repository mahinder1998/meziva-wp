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

$newsletter_text = get_field('ft_newsletter_text', $ctx) ?: 'Get the latest news, events & more delivered to your inbox.';
$placeholder     = get_field('ft_newsletter_placeholder', $ctx) ?: 'Email address...';
$btn_label       = get_field('ft_newsletter_btn_label', $ctx) ?: '→';

$fb = get_field('ft_fb_url', $ctx) ?: '#';
$yt = get_field('ft_yt_url', $ctx) ?: '#';
$tw = get_field('ft_tw_url', $ctx) ?: '#';
$ig = get_field('ft_ig_url', $ctx) ?: '#';

$copy_text   = get_field('ft_copyright_text', $ctx) ?: 'Copyright © ' . date('Y');
$copy_brand  = get_field('ft_copyright_brand', $ctx) ?: 'Smartic.';
$copy_line2  = get_field('ft_copyright_line2', $ctx) ?: 'All Rights Reserved.';

$c1 = [
  ['t'=>get_field('ft_c1_l1_text',$ctx) ?: 'Brands',         'u'=>get_field('ft_c1_l1_url',$ctx) ?: '#'],
  ['t'=>get_field('ft_c1_l2_text',$ctx) ?: 'Gift Vouchers',   'u'=>get_field('ft_c1_l2_url',$ctx) ?: '#'],
  ['t'=>get_field('ft_c1_l3_text',$ctx) ?: 'Affiliates',      'u'=>get_field('ft_c1_l3_url',$ctx) ?: '#'],
  ['t'=>get_field('ft_c1_l4_text',$ctx) ?: 'Specials',        'u'=>get_field('ft_c1_l4_url',$ctx) ?: '#'],
];

$c2 = [
  ['t'=>get_field('ft_c2_l1_text',$ctx) ?: 'About Us', 'u'=>get_field('ft_c2_l1_url',$ctx) ?: '#'],
  ['t'=>get_field('ft_c2_l2_text',$ctx) ?: 'FAQs',     'u'=>get_field('ft_c2_l2_url',$ctx) ?: '#'],
  ['t'=>get_field('ft_c2_l3_text',$ctx) ?: 'Blog',     'u'=>get_field('ft_c2_l3_url',$ctx) ?: '#'],
  ['t'=>get_field('ft_c2_l4_text',$ctx) ?: 'Contact',  'u'=>get_field('ft_c2_l4_url',$ctx) ?: '#'],
];

$bg          = get_field('ft_bg_color', $ctx) ?: '#FFFFFF';
$text_color  = get_field('ft_text_color', $ctx) ?: '#6B7280'; // gray-ish
$social_bg   = get_field('ft_social_bg', $ctx) ?: '#2E2E2E';
$social_col  = get_field('ft_social_color', $ctx) ?: '#FFFFFF';

function mz_footer_link($item) {
  $t = trim($item['t'] ?? '');
  $u = trim($item['u'] ?? '');
  if (!$t || !$u) return;
  echo '<li><a class="mz-inline-flex hover:mz-opacity-70 mz-transition mz-text-[15px]" href="'.esc_url($u).'">'.esc_html($t).'</a></li>';
}
?>

<style>
  .newsletter-form button:hover{
    background-color:transparent;
  }
  .newsletter-form button:hover svg,
  .newsletter-form button:hover svg path
  {
    fill:#9B4A6A;
  }
  footer ul > li > a:hover {
    color: #F6EFEA;
  }
</style>

<footer
  class="mz-w-full mz-border-t mz-border-black/5"
  style="background-color: <?php echo esc_attr($bg); ?>; color: <?php echo esc_attr($text_color); ?>;"
>
  <div class="mz-max-w-[1290px] mz-mx-auto mz-px-4 md:mz-px-6 mz-py-12 md:mz-py-14 xl:mz-px-0">

    <!-- Layout: mobile stacked / desktop 4 columns -->
    <div class="mz-grid mz-grid-cols-1 md:mz-grid-cols-12 mz-gap-10 md:mz-gap-8 mz-items-start">

      <!-- 1) Newsletter -->
      <div class="md:mz-col-span-4">
        <p class="mz-text-center md:mz-text-left mz-text-[15px] mz-font-medium mz-leading-[1.6]">
          <?php echo esc_html($newsletter_text); ?>
        </p>

        <form class="newsletter-form mz-mt-5 mz-max-w-[360px] md:mz-max-w-[420px] mz-mx-auto md:mz-mx-0">
          <div class="mz-relative">
            <input
              type="email"
              name="email"
              placeholder="<?php echo esc_attr($placeholder); ?>"
              class="mz-w-full mz-h-[60px] mz-rounded-xl mz-border mz-border-black/10
                     mz-bg-white mz-px-4 mz-pr-12 mz-text-[14px] mz-text-black
                     focus:mz-outline-none focus:mz-ring-2 focus:mz-ring-black/10
                     "
            />
            <button
              type="submit"
              class="mz-absolute mz-right-2 mz-top-1/2 -mz-translate-y-1/2
                     mz-w-[36px] mz-h-[36px] mz-rounded-[4px]
                     mz-flex mz-items-center mz-justify-center
                     hover:mz-opacity-80 mz-transition mz-text-text-heading mz-border-none hover:mz-bg-transparent
                     "
              aria-label="Subscribe"
            >
              <span class="mz-text-[18px]">
                <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                  <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 12H5m14 0-4 4m4-4-4-4"/>
                </svg>
              </span>
            </button>
          </div>
        </form>
      </div>

      <!-- 2) Socials + Copyright -->
      <div class="md:mz-col-span-4">
        <div class="mz-flex mz-justify-center md:mz-justify-center mz-gap-3">
          <!-- Facebook -->
          <a href="<?php echo esc_url($fb); ?>"
             class="mz-w-[44px] mz-h-[44px] mz-rounded-full mz-flex mz-items-center mz-justify-center
                    hover:mz-opacity-80 mz-transition"
             style="background-color: <?php echo esc_attr($social_bg); ?>; color: <?php echo esc_attr($social_col); ?>;"
             aria-label="Facebook"
          >
            <svg class="mz-w-[18px] mz-h-[18px]" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
              <path d="M22 12a10 10 0 1 0-11.6 9.9v-7H8v-3h2.4V9.7c0-2.4 1.4-3.7 3.6-3.7 1 0 2 .2 2 .2v2.2h-1.1c-1.1 0-1.4.7-1.4 1.4V12H16l-.4 3h-2.1v7A10 10 0 0 0 22 12z"/>
            </svg>
          </a>

          <!-- YouTube -->
          <a href="<?php echo esc_url($yt); ?>"
             class="mz-w-[44px] mz-h-[44px] mz-rounded-full mz-flex mz-items-center mz-justify-center
                    hover:mz-opacity-80 mz-transition"
             style="background-color: <?php echo esc_attr($social_bg); ?>; color: <?php echo esc_attr($social_col); ?>;"
             aria-label="YouTube"
          >
            <svg class="mz-w-[18px] mz-h-[18px]" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
              <path d="M21.6 7.2s-.2-1.6-.8-2.3c-.8-.8-1.7-.8-2.1-.9C15.8 3.7 12 3.7 12 3.7h0s-3.8 0-6.7.3c-.4 0-1.3.1-2.1.9-.6.7-.8 2.3-.8 2.3S2 9 2 10.8v1.7c0 1.8.4 3.6.4 3.6s.2 1.6.8 2.3c.8.8 1.9.8 2.4.9 1.7.2 6.4.3 6.4.3s3.8 0 6.7-.3c.4 0 1.3-.1 2.1-.9.6-.7.8-2.3.8-2.3s.4-1.8.4-3.6v-1.7c0-1.8-.4-3.6-.4-3.6zM10 14.9V8.9l6 3-6 3z"/>
            </svg>
          </a>

          <!-- Twitter/X -->
          <a href="<?php echo esc_url($tw); ?>"
             class="mz-w-[44px] mz-h-[44px] mz-rounded-full mz-flex mz-items-center mz-justify-center
                    hover:mz-opacity-80 mz-transition"
             style="background-color: <?php echo esc_attr($social_bg); ?>; color: <?php echo esc_attr($social_col); ?>;"
             aria-label="Twitter"
          >
            <svg class="mz-w-[18px] mz-h-[18px]" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
              <path d="M18.9 2H22l-6.8 7.8L23 22h-6.8l-5.3-6.8L4.7 22H2l7.3-8.4L1 2h6.9l4.8 6.1L18.9 2zm-1.2 18h1.9L7.1 3.9H5.1L17.7 20z"/>
            </svg>
          </a>

          <!-- Instagram -->
          <a href="<?php echo esc_url($ig); ?>"
             class="mz-w-[44px] mz-h-[44px] mz-rounded-full mz-flex mz-items-center mz-justify-center
                    hover:mz-opacity-80 mz-transition"
             style="background-color: <?php echo esc_attr($social_bg); ?>; color: <?php echo esc_attr($social_col); ?>;"
             aria-label="Instagram"
          >
            <svg class="mz-w-[18px] mz-h-[18px]" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
              <path d="M7 2h10a5 5 0 0 1 5 5v10a5 5 0 0 1-5 5H7a5 5 0 0 1-5-5V7a5 5 0 0 1 5-5zm10 2H7a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3V7a3 3 0 0 0-3-3zm-5 4.5A3.5 3.5 0 1 1 8.5 12 3.5 3.5 0 0 1 12 8.5zm0 2A1.5 1.5 0 1 0 13.5 12 1.5 1.5 0 0 0 12 10.5zM18 6.2a.8.8 0 1 1-.8.8.8.8 0 0 1 .8-.8z"/>
            </svg>
          </a>
        </div>

        <div class="mz-mt-6 mz-text-center mz-text-[14px] mz-leading-[1.6]">
          <div>
            <?php echo esc_html($copy_text); ?>
            <span class="mz-font-semibold "><?php echo esc_html($copy_brand); ?></span>
          </div>
          <div><?php echo esc_html($copy_line2); ?></div>
        </div>
      </div>

      <!-- 3) Links Column 1 -->
      <div class="md:mz-col-span-2">
        <ul class="mz-space-y-2 mz-text-center md:mz-text-left">
          <?php foreach ($c1 as $it) { mz_footer_link($it); } ?>
        </ul>
      </div>

      <!-- 4) Links Column 2 -->
      <div class="md:mz-col-span-2">
        <ul class="mz-space-y-2 mz-text-center md:mz-text-left">
          <?php foreach ($c2 as $it) { mz_footer_link($it); } ?>
        </ul>
      </div>

    </div>
  </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
