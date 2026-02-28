<?php
/**
 * Template Name: Contact Us (Meziva)
 */
defined('ABSPATH') || exit;
get_header();

// Helpers
function mz_get_field_safe($key, $default = '') {
  if (function_exists('get_field')) {
    $v = get_field($key);
    if ($v !== null && $v !== '') return $v;
  }
  return $default;
}

// Basic content (optional ACF fields)
$heading = mz_get_field_safe('mz_contact_heading', 'Contact Us');
$subtext = mz_get_field_safe('mz_contact_subtext', 'For any query or question kindly connect with us. We would love to help you!');

$phone = mz_get_field_safe('mz_support_phone', '+91 XXXXXXXXXX');
$hours = mz_get_field_safe('mz_support_hours', 'Mon - Fri | 10:00am - 06:00pm');
$email = mz_get_field_safe('mz_support_email', 'support@meziva.in');

// WhatsApp link
// WhatsApp link
$wa_raw  = preg_replace('/\D+/', '', mz_get_field_safe('mz_whatsapp_number', ''));
$default_msg = "Hi Meziva Beauty 👋\nI’m interested in your skincare products. Could you please share more details?";
$encoded_msg = rawurlencode($default_msg);
$wa_link = $wa_raw 
  ? ('https://wa.me/' . $wa_raw . '?text=' . $encoded_msg) 
  : '#';
// Illustration (ACF image can be URL/ID/Array)
$img = function_exists('get_field') ? get_field('mz_contact_illustration') : ''; 
$img_url = '';
if (is_array($img) && !empty($img['url'])) $img_url = $img['url'];
elseif (is_numeric($img)) $img_url = wp_get_attachment_url($img); 
elseif (is_string($img)) $img_url = $img;

// Status messages
$posted  = isset($_GET['mz_contact']) ? sanitize_text_field($_GET['mz_contact']) : '';
$ok_msg  = ($posted === 'sent') ? 'Thanks! We received your message. We’ll get back within 24 hours on business days.' : '';
$er_msg  = ($posted === 'fail') ? 'Something went wrong. Please try again or email us directly.' : '';

$site_key = (defined('MZ_TURNSTILE_SITE_KEY') && MZ_TURNSTILE_SITE_KEY) ? MZ_TURNSTILE_SITE_KEY : '';
?>

<section class="mz-w-full mz-bg-white">
  <div class="mz-max-w-6xl mz-mx-auto mz-px-4 sm:mz-px-6 lg:mz-px-8 mz-py-10 lg:mz-py-14">

    <h1 class="mz-text-[22px] sm:mz-text-[24px] md:mz-text-[30px] xl:mz-text-[30px] mz-leading-[1.08] mz-font-extrabold mz-tracking-tight mz-text-center"><?php echo esc_html($heading); ?></h1>

    <!-- Top -->
    <div class="mz-mt-6 mz-grid mz-grid-cols-1 lg:mz-grid-cols-12 mz-gap-6 lg:mz-gap-10 mz-items-center">
      <div class="lg:mz-col-span-3">
        <?php if ($img_url): ?>
          <img class="mz-w-full mz-max-w-[200px] sm:mz-max-w-[200px]
          mz-rounded-full mz-mx-auto
          "
               src="<?php echo esc_url($img_url); ?>" alt="Contact Illustration" loading="lazy">
        <?php else: ?>
          <div class="mz-w-full mz-max-w-[260px] sm:mz-max-w-[300px] mz-rounded-2xl mz-bg-pink-50 mz-p-10 mz-text-center mz-text-brand-accent">
            Illustration
          </div>
        <?php endif; ?>
      </div>

      <div class="lg:mz-col-span-9"> 
        <p class="mz-text-base sm:mz-text-lg mz-text-text-body mz-text-center lg:mz-text-left"><?php echo esc_html($subtext); ?></p>

        <div class="mz-mt-6 mz-grid mz-grid-cols-1 sm:mz-grid-cols-2 mz-gap-4">

          <!-- WhatsApp Card -->
          <a href="<?php echo esc_url($wa_link); ?>"
             class="mz-group mz-flex mz-items-center mz-justify-between mz-gap-3 mz-rounded-2xl mz-border mz-border-gray-200 mz-bg-white mz-p-4 hover:mz-border-pink-300 hover:mz-shadow-sm mz-transition"
             <?php echo $wa_raw ? 'target="_blank" rel="noopener"' : 'aria-disabled="true"'; ?>>
            <div class="mz-flex mz-items-center mz-gap-3">
              <span class="mz-inline-flex mz-h-10 mz-w-10 mz-items-center mz-justify-center mz-rounded-xl mz-bg-pink-50 mz-text-brand-accent">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                  <path d="M12 2a10 10 0 0 0-8.66 15l-1.14 4.17 4.27-1.12A10 10 0 1 0 12 2Zm0 18a8 8 0 0 1-4.08-1.12l-.29-.17-2.53.66.68-2.46-.19-.3A8 8 0 1 1 12 20Zm4.44-5.73c-.24-.12-1.41-.7-1.63-.78-.22-.08-.38-.12-.54.12-.16.24-.62.78-.76.94-.14.16-.28.18-.52.06-.24-.12-1.01-.37-1.92-1.19-.71-.63-1.19-1.4-1.33-1.64-.14-.24-.01-.37.11-.49.11-.11.24-.28.36-.42.12-.14.16-.24.24-.4.08-.16.04-.3-.02-.42-.06-.12-.54-1.3-.74-1.78-.19-.46-.39-.4-.54-.4h-.46c-.16 0-.42.06-.64.3-.22.24-.84.82-.84 2s.86 2.32.98 2.48c.12.16 1.7 2.59 4.12 3.63.58.25 1.03.4 1.38.51.58.18 1.1.16 1.52.1.46-.07 1.41-.58 1.61-1.14.2-.56.2-1.04.14-1.14-.06-.1-.22-.16-.46-.28Z"/>
                </svg>
              </span>
              <div>
                <div class="mz-font-semibold mz-text-text-body mz-mb-1">Chat with us via WhatsApp</div>
                <div class="mz-text-sm mz-text-brand-accent"><?php echo esc_html($hours); ?></div>
              </div>
            </div>
            <span class="mz-text-gray-400 group-hover:mz-text-pink-600 mz-transition">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 18l6-6-6-6"/>
              </svg>
            </span>
          </a>

          <!-- Phone Card + Copy -->
          <div class="mz-flex mz-items-center mz-justify-between mz-gap-3 mz-rounded-2xl mz-border mz-border-gray-200 mz-bg-white mz-p-4">
            <div class="mz-flex mz-items-center mz-gap-3">
              <span class="mz-inline-flex mz-h-10 mz-w-10 mz-items-center mz-justify-center mz-rounded-xl mz-bg-pink-50 mz-text-brand-accent">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                  <path d="M12 2a8 8 0 0 0-8 8v7a3 3 0 0 0 3 3h1v-8H7v-2a5 5 0 0 1 10 0v2h-1v8h1a3 3 0 0 0 3-3v-7a8 8 0 0 0-8-8Z"/>
                </svg>
              </span>
              <div>
                <div class="mz-font-semibold mz-text-text-body mz-mb-1">Get in touch : <?php echo esc_html($phone); ?></div>
                <div class="mz-text-sm mz-text-brand-accent"><?php echo esc_html($hours); ?></div>
              </div>
            </div>

            <button type="button"
              class="mz-inline-flex mz-h-10 mz-w-10 mz-items-center mz-justify-center mz-rounded-xl mz-border mz-border-gray-200 hover:mz-border-pink-300 hover:mz-bg-pink-50 mz-transition"
              data-mz-copy="<?php echo esc_attr($phone); ?>" aria-label="Copy phone number" title="Copy">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="9" y="9" width="13" height="13" rx="2"></rect>
                <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
              </svg>
            </button>
          </div>

        </div>
      </div>
    </div>

    <!-- Divider OR -->
    <div class="mz-my-8 mz-flex mz-items-center mz-gap-4">
      <div class="mz-h-px mz-w-full mz-bg-gray-200"></div>
      <div class="mz-text-sm mz-font-semibold mz-text-gray-500">OR</div>
      <div class="mz-h-px mz-w-full mz-bg-gray-200"></div>
    </div>

    <!-- Form -->
    <div class="mz-grid mz-grid-cols-1 lg:mz-grid-cols-12 mz-gap-6 lg:mz-gap-10">
      <div class="lg:mz-col-span-12">
        <div class="mz-rounded-2xl mz-border mz-border-gray-200 mz-bg-white mz-p-4 sm:mz-p-6">

          <div class="mz-flex mz-items-center mz-gap-3">
            <span class="mz-inline-flex mz-h-10 mz-w-10 mz-items-center mz-justify-center mz-rounded-xl mz-bg-pink-50 mz-text-brand-accent">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path d="M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2Zm0 4-8 5L4 8V6l8 5 8-5v2Z"/>
              </svg>
            </span>
            <div>
              <div class="mz-font-semibold mz-text-text-body mz-mb-1">Email your query</div>
              <div class="mz-text-sm mz-text-brand-accent">Expect a revert in 24 hours on business days</div>
            </div>
          </div>

          <?php if ($ok_msg): ?>
            <div class="mz-mt-4 mz-rounded-xl mz-border mz-border-green-200 mz-bg-green-50 mz-p-3 mz-text-sm mz-text-green-800">
              <?php echo esc_html($ok_msg); ?>
            </div>
          <?php elseif ($er_msg): ?>
            <div class="mz-mt-4 mz-rounded-xl mz-border mz-border-red-200 mz-bg-red-50 mz-p-3 mz-text-sm mz-text-red-800">
              <?php echo esc_html($er_msg); ?>
            </div>
          <?php endif; ?>

          <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" class="mz-mt-6 mz-space-y-4 contact_us_form">
            <?php wp_nonce_field('mz_contact_submit', 'mz_contact_nonce'); ?>
            <input type="hidden" name="action" value="mz_contact_submit" />

            <div>
              <label class="mz-block mz-text-sm mz-font-medium mz-text-text-body">Full Name *</label>
              <input name="full_name" required
                class="mz-mt-1 mz-w-full mz-rounded-xl mz-border mz-border-gray-200 mz-px-4 mz-py-3 focus:mz-border-pink-400 mz-outline-none" />
            </div>

            <div>
              <label class="mz-block mz-text-sm mz-font-medium mz-text-text-body">Email Address *</label>
              <input name="email" type="email" required
                class="mz-mt-1 mz-w-full mz-rounded-xl mz-border mz-border-gray-200 mz-px-4 mz-py-3 focus:mz-border-pink-400 mz-outline-none" />
            </div>

            <div>
              <label class="mz-block mz-text-sm mz-font-medium mz-text-text-body">Mobile Number *</label>
              <input name="phone" required inputmode="numeric" pattern="[0-9]{10}" placeholder="10 digit number"
                class="mz-mt-1 mz-w-full mz-rounded-xl mz-border mz-border-gray-200 mz-px-4 mz-py-3 focus:mz-border-pink-400 mz-outline-none" />
            </div>

            <div>
              <label class="mz-block mz-text-sm mz-font-medium mz-text-text-body">Message *</label>
              <textarea name="message" required rows="5"
                class="mz-mt-1 mz-w-full mz-rounded-xl mz-border mz-border-gray-200 mz-px-4 mz-py-3 focus:mz-border-pink-400 mz-outline-none"></textarea>
            </div>

            <!-- Turnstile -->
           <div class="mz-mt-2">
            <?php if ($site_key): ?>
              <div class="cf-turnstile" data-sitekey="<?php echo esc_attr($site_key); ?>"></div>
            <?php else: ?>
              <p class="mz-text-sm mz-text-red-600">Turnstile site key missing (define MZ_TURNSTILE_SITE_KEY).</p>
            <?php endif; ?>
          </div>


            <p class="mz-text-[13px] mz-text-text-body">
              By submitting this form, you agree to our <a class="mz-underline" href="<?php echo esc_url(home_url('/privacy-policy/')); ?>">Privacy Policy</a>.
            </p>

            <button type="submit"
              class="mz-rounded-xl mz-bg-brand-accent mz-cursor-pointer hover:mz-bg-brand-primary mz-text-white mz-px-6 mz-py-3 mz-font-semibold hover:mz-opacity-90 mz-transition">
              Send Message
            </button>
          </form>

        </div>
      </div>

      <!-- Right side (optional) -->
     
    </div>

  </div>
</section>

<script>
document.addEventListener('click', function(e){
  const btn = e.target.closest('[data-mz-copy]');
  if(!btn) return;
  const val = btn.getAttribute('data-mz-copy') || '';
  if(!val) return;
  navigator.clipboard.writeText(val).then(()=>{
    btn.classList.add('mz-bg-pink-50','mz-border-pink-300');
    setTimeout(()=>btn.classList.remove('mz-bg-pink-50','mz-border-pink-300'), 800);
  });
});
</script>

<?php get_footer(); ?>
