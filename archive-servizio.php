<?php
if (!defined('ABSPATH')) exit;

get_header();

get_template_part('template-parts/site/archive/servizi/hero');
get_template_part('template-parts/site/archive/servizi/statement');
get_template_part('template-parts/site/archive/servizi/slider');

echo '<div class="bg-white moma-servizi-after">';
get_template_part('template-parts/site/archive/servizi/projects');
get_template_part('template-parts/site/archive/servizi/contact-banner');
echo '</div>';

get_footer();
