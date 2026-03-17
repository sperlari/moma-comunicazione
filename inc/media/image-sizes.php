<?php
if (!defined('ABSPATH')) exit;

/**
 * Immagini quadrate per il “canvas” della hero.
 * Hard crop = WP taglia al centro (comportamento standard).
 */
add_action('after_setup_theme', function () {
  add_theme_support('post-thumbnails');

  // Se vuoi limitarlo al CPT: add_post_type_support('case_study', 'thumbnail');

  add_image_size('moma_cs_sq_sm', 480, 480, true);
  add_image_size('moma_cs_sq_md', 720, 720, true);
  add_image_size('moma_cs_sq_lg', 1080, 1080, true);

  add_image_size('moma_cs_detail_hero', 1600, 900, true);
  add_image_size('moma_cs_detail_step', 1400, 880, true);
  add_image_size('moma_cs_detail_step_large', 1100, 930, true);
  add_image_size('moma_cs_detail_step_square', 760, 760, true);
  add_image_size('moma_cs_detail_step_wide', 1300, 550, true);
  add_image_size('moma_cs_detail_logo_card', 520, 320, true);
  add_image_size('moma_cs_detail_video', 1500, 820, true);
  add_image_size('moma_cs_detail_related', 900, 640, true);
});
