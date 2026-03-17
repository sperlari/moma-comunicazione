<?php
if (!defined('ABSPATH')) exit;

add_action('wp_enqueue_scripts', function () {
  if (!is_page_template('templates/page-multimedia.php')) return;

  $shared_css_path = get_theme_file_path('/assets/css/moma-case-study-single.css');
  if (is_file($shared_css_path)) {
    wp_enqueue_style(
      'moma-case-study-single',
      get_theme_file_uri('/assets/css/moma-case-study-single.css'),
      ['moma-footer'],
      filemtime($shared_css_path)
    );
  }

  $page_css_path = get_theme_file_path('/assets/css/moma-multimedia-page.css');
  if (is_file($page_css_path)) {
    wp_enqueue_style(
      'moma-multimedia-page',
      get_theme_file_uri('/assets/css/moma-multimedia-page.css'),
      ['moma-case-study-single'],
      filemtime($page_css_path)
    );
  }

  $shared_js_path = get_theme_file_path('/assets/js/moma-case-study-single.js');
  if (is_file($shared_js_path)) {
    wp_enqueue_script(
      'moma-case-study-single',
      get_theme_file_uri('/assets/js/moma-case-study-single.js'),
      [],
      filemtime($shared_js_path),
      true
    );
  }
}, 30);
