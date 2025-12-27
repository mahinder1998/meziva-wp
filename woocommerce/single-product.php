<?php
defined('ABSPATH') || exit;

get_header('shop'); ?>

<div class="mz-max-w-[1290px] mz-mx-auto mz-px-4 md:mz-px-0 mz-py-8">
  <?php while (have_posts()) : the_post(); ?>
    <?php wc_get_template_part('content', 'single-product'); ?>
  <?php endwhile; ?>
</div>

<?php get_footer('shop');