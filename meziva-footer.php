<?php
$front_id = (int) get_option('page_on_front');
$footer_text = '© ' . date('Y') . ' Meziva Beauty. All rights reserved.';
$newsletter_title = 'Get updates & offers';

if ($front_id && function_exists('get_field')) {
  $footer_text = get_field('meziva_footer_text', $front_id) ?: $footer_text;
  $newsletter_title = get_field('meziva_footer_newsletter_title', $front_id) ?: $newsletter_title;
}
?>

<footer class="mz-bg-brand-secondary mz-border-t mz-border-black/5">
  <div class="mz-max-w-[1240px] mz-mx-auto mz-px-4 mz-py-10">
    <div class="mz-grid mz-gap-8 md:mz-grid-cols-3">

      <div>
        <div class="mz-font-heading mz-text-text-heading mz-text-xl">Meziva Beauty</div>
        <p class="mz-mt-3 mz-font-body mz-text-text-body mz-text-sm mz-leading-6 mz-max-w-[320px]">
          Clean • Modern • Confident Beauty
        </p>
      </div>

      <div>
        <div class="mz-font-heading mz-text-text-heading mz-text-base">Quick Links</div>
        <div class="mz-mt-3 mz-flex mz-flex-col mz-gap-2 mz-font-body mz-text-text-body mz-text-sm">
          <a class="hover:mz-opacity-80 mz-transition" href="<?php echo esc_url(home_url('/shop')); ?>">Shop</a>
          <a class="hover:mz-opacity-80 mz-transition" href="<?php echo esc_url(home_url('/about')); ?>">About</a>
          <a class="hover:mz-opacity-80 mz-transition" href="<?php echo esc_url(home_url('/contact')); ?>">Contact</a>
        </div>
      </div>

      <div>
        <div class="mz-font-heading mz-text-text-heading mz-text-base"><?php echo esc_html($newsletter_title); ?></div>

        <form class="mz-mt-3 mz-flex mz-gap-2" method="post" action="#">
          <input
            type="email"
            required
            placeholder="Email address"
            class="mz-w-full mz-rounded-full mz-px-4 mz-py-3 mz-font-body mz-text-sm mz-bg-white/70 mz-border mz-border-black/10 mz-outline-none focus:mz-ring-2 focus:mz-ring-black/10"
          />
          <button
            type="submit"
            class="mz-rounded-full mz-px-5 mz-py-3 mz-bg-brand-accent mz-text-white mz-font-body mz-text-sm hover:mz-opacity-95 mz-transition"
          >
            Subscribe
          </button>
        </form>

        <div class="mz-mt-4 mz-flex mz-gap-4 mz-text-text-heading">
          <a class="hover:mz-opacity-80 mz-transition" href="#" aria-label="Instagram">IG</a>
          <a class="hover:mz-opacity-80 mz-transition" href="#" aria-label="Facebook">FB</a>
          <a class="hover:mz-opacity-80 mz-transition" href="#" aria-label="YouTube">YT</a>
        </div>
      </div>

    </div>

    <div class="mz-mt-10 mz-pt-6 mz-border-t mz-border-black/5 mz-font-body mz-text-text-body mz-text-sm mz-flex mz-flex-col md:mz-flex-row mz-gap-3 md:mz-items-center md:mz-justify-between">
      <div><?php echo esc_html($footer_text); ?></div>
      <div class="mz-flex mz-gap-4">
        <a class="hover:mz-opacity-80 mz-transition" href="<?php echo esc_url(home_url('/privacy-policy')); ?>">Privacy</a>
        <a class="hover:mz-opacity-80 mz-transition" href="<?php echo esc_url(home_url('/terms-conditions')); ?>">Terms</a>
        <a class="hover:mz-opacity-80 mz-transition" href="<?php echo esc_url(home_url('/refund-policy')); ?>">Refund</a>
      </div>
    </div>
  </div>
</footer>
