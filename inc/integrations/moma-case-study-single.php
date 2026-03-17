<?php
if (!defined('ABSPATH')) exit;

add_action('wp_enqueue_scripts', function () {
  if (!is_singular('case_study')) return;

  $css_path = get_theme_file_path('/assets/css/moma-case-study-single.css');
  if (is_file($css_path)) {
    wp_enqueue_style(
      'moma-case-study-single',
      get_theme_file_uri('/assets/css/moma-case-study-single.css'),
      ['moma-footer'],
      filemtime($css_path)
    );
  }

  $js_path = get_theme_file_path('/assets/js/moma-case-study-single.js');
  if (is_file($js_path)) {
    wp_enqueue_script(
      'moma-case-study-single',
      get_theme_file_uri('/assets/js/moma-case-study-single.js'),
      [],
      filemtime($js_path),
      true
    );
  }
}, 30);
