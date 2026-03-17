<?php
if (!defined('ABSPATH')) exit;

add_action('wp_enqueue_scripts', function () {
  $css = get_theme_file_path('/assets/css/moma-servizi-fixes.css');
  if (is_file($css)) {
    wp_enqueue_style('moma-servizi-fixes', get_theme_file_uri('/assets/css/moma-servizi-fixes.css'), [], filemtime($css));
  }

  $js = get_theme_file_path('/assets/js/moma-servizi-fixes.js');
  if (is_file($js)) {
    wp_enqueue_script('moma-servizi-fixes', get_theme_file_uri('/assets/js/moma-servizi-fixes.js'), [], filemtime($js), true);
  }
}, 30);
