<?php if ( ! defined('ABSPATH') ) exit; ?>

<footer class="mz-bg-brand-secondary mz-pt-12 mz-pb-6">
  <div class="mz-max-w-[1240px] mz-mx-auto mz-px-4 mz-grid md:mz-grid-cols-3 mz-gap-8">
    <div>
      <h4 class="mz-font-semibold mz-mb-2">Meziva Beauty</h4>
      <p class="mz-text-sm">Clean • Modern • Confident Beauty</p>
    </div>

    <div>
      <h4 class="mz-font-semibold mz-mb-2">Quick Links</h4>
      <ul class="mz-text-sm mz-space-y-1">
        <li><a class="hover:mz-opacity-80" href="/shop">Shop</a></li>
        <li><a class="hover:mz-opacity-80" href="/about">About</a></li>
        <li><a class="hover:mz-opacity-80" href="/contact">Contact</a></li>
      </ul>
    </div>

    <div>
      <h4 class="mz-font-semibold mz-mb-2">Get updates & offers</h4>
      <div class="mz-flex mz-gap-2">
        <input type="email" placeholder="Email address"
               class="mz-w-full mz-border mz-border-black/10 mz-px-3 mz-py-2 mz-rounded-lg">
        <button class="mz-px-4 mz-rounded-lg mz-bg-brand-accent mz-text-white hover:mz-opacity-90 mz-transition">
          Subscribe
        </button>
      </div>
    </div>
  </div>

  <div class="mz-max-w-[1240px] mz-mx-auto mz-px-4 mz-mt-8 mz-flex mz-flex-col md:mz-flex-row mz-items-center md:mz-justify-between mz-gap-3 mz-text-xs mz-text-text-body">
    <span>© <?php echo date('Y'); ?> Meziva Beauty. All rights reserved.</span>
    <div class="mz-flex mz-gap-4">
      <a class="hover:mz-opacity-80" href="/privacy-policy">Privacy</a>
      <a class="hover:mz-opacity-80" href="/terms-conditions">Terms</a>
      <a class="hover:mz-opacity-80" href="/refund-policy">Refund</a>
    </div>
  </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
