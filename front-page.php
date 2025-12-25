<?php
if ( ! defined('ABSPATH') ) exit;

get_header();
get_template_part('template-parts/section-hero-banner');

if ( have_posts() ) {
  while ( have_posts() ) {
    the_post();
    the_content();
  }
}

get_footer();
