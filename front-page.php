<?php
if ( ! defined('ABSPATH') ) exit;

get_header();
get_template_part('template-parts/section-hero-banner');
get_template_part('template-parts/section-trust_icons_section');
get_template_part('template-parts/section-choose-shade');
get_template_part('template-parts/section-about-product');
get_template_part('template-parts/section-Ingredients');
get_template_part('template-parts/section-how-it-works');
get_template_part('template-parts/section-success-stories');
get_template_part('template-parts/section-cta-freshbody');

if ( have_posts() ) {
  while ( have_posts() ) {
    the_post();
    the_content();
  }
}

get_footer();
