<?php
if ( ! defined('ABSPATH') ) exit;

$page_id  = get_queried_object_id();
$front_id = (int) get_option('page_on_front');
$pid      = $page_id ?: $front_id;

$title    = function_exists('get_field') ? (string) get_field('about_title', $pid) : '';
$subtitle = function_exists('get_field') ? (string) get_field('about_subtitle', $pid) : '';
$content  = function_exists('get_field') ? get_field('about_content', $pid) : '';
$btn_text = function_exists('get_field') ? (string) get_field('about_btn_text', $pid) : '';
$btn_url  = function_exists('get_field') ? (string) get_field('about_btn_url', $pid) : '';

$image    = function_exists('get_field') ? get_field('about_image', $pid) : null;
$img_url  = (is_array($image) && !empty($image['url'])) ? $image['url'] : '';
$img_alt  = (is_array($image) && !empty($image['alt'])) ? $image['alt'] : ($title ?: 'About image');

$outline_enable = function_exists('get_field') ? (bool) get_field('about_outline_enable', $pid) : false;
$outline_color  = function_exists('get_field') ? (string) get_field('about_outline_color', $pid) : '#84cc16';

$content_html = $content ? wp_kses_post($content) : '';

if ( ! $title && ! $subtitle && ! $content_html && ! $img_url ) return;
?>

<section class="mz-w-full mz-bg-white">
  <div class="mz-max-w-[1240px] mz-mx-auto mz-px-4 mz-py-10 mz-pb-0 md:mz-py-20  md:mz-pb-0  xl:mz-px-0">

    <!-- Ref-like layout: desktop 2 columns, mobile stacked + centered text -->
    <div class="mz-grid md:mz-grid-cols-2 mz-text-center md:mz-gap-10 
      xl:mz-grid-cols-[1fr,1fr,1fr]
    ">

          <?php if ($title): ?>
            <div class="section-header md:mz-col-span-2 md:mz-text-center xl:md:mz-col-span-1 xl:mz-text-left xl:mz-mt-14">
                <h2 class="mz-text-[36px] xl:mz-text-[50px] mz-leading-[1.05] mz-font-extrabold mz-tracking-tight mz-text-brand-accent
                mz-mb-8 xl:mz-mb-5
                ">
                  <?php echo wp_kses($title, ['br' => []]); ?>
                </h2>
                 <?php if ($subtitle): ?>
                <p class="mz-mt-4 mz-text-[18px] md:mz-text-[18px] mz-font-semibold mz-text-text-heading">
                  <?php echo esc_html($subtitle); ?>
                  
                </p>
              <?php endif; ?>

            </div>
          <?php endif; ?>

          <div class="section-content md:mz-text-left md:mz-mt-5 xl:mz-mt-14">
              <?php if ($content_html): ?>
                <div class="mz-text-[14px] md:mz-text-[16px] mz-leading-7 mz-text-text-body">
                  <?php echo $content_html; ?>
                </div>
              <?php endif; ?>

              <?php if ($btn_text && $btn_url): ?>
                <div class="mz-mt-8 mz-flex md:mz-justify-start mz-justify-center">
                  <a href="<?php echo esc_url($btn_url); ?>"
                    class="mmz-inline-block mz-bg-primary mz-bg-brand-accent mz-text-white mz-px-5 mz-py-3 mz-rounded-lg hover:mz-bg-opacity-90 mz-transition
                mz-text-sm mz-font-bold hover:mz-bg-brand-primary hover:mz-text-white
                  md:mz-min-w-[140px] md:mz-py-4 md:mz-text-center xl:mz-min-w-[150px] xl:mz-py-[18px]  xl:mz-text-center xl:mz-text-[15px] xl:mz-rounded-xl
                ">
                    <?php echo esc_html($btn_text); ?>
                  </a>
                </div>
              <?php endif; ?>

              </div>
               <div class="section-media mz-mt-10  md:mz-mt-5 xl:mz-mt-0">
                   <?php if ($img_url): ?>
          <div class="mz-relative mz-w-full mz-max-w-[520px] md:mz-max-w-[620px]">
            <img
              src="<?php echo esc_url($img_url); ?>"
              alt="<?php echo esc_attr($img_alt); ?>"
              class="mz-relative mz-z-[2] mz-w-full md:mz-max-h-[570px] mz-h-auto mz-object-contain"
              loading="lazy"
            />
          </div>
        <?php endif; ?>


      
        


       

        </div>
      </div>

    


  </div>
</section>
