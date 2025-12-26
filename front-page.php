<?php
if ( ! defined('ABSPATH') ) exit;

get_header();
get_template_part('template-parts/section-hero-banner');
get_template_part('template-parts/section-about-product');
get_template_part('template-parts/section-Ingredients');
get_template_part('template-parts/section-how-it-works');

if ( have_posts() ) {
  while ( have_posts() ) {
    the_post();
    the_content();
  }
}

get_footer();
