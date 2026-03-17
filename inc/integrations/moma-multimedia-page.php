<?php
if (!defined('ABSPATH')) exit;

add_filter('query_vars', function ($vars) {
  $vars[] = 'moma_video_category';
  $vars[] = 'csmedia';
  return $vars;
});

add_action('init', function () {
  $page = function_exists('moma_multimedia_get_page') ? moma_multimedia_get_page() : null;
  if (!($page instanceof WP_Post)) return;

  $path = moma_multimedia_get_page_path((int) $page->ID);
  if ($path === '') return;

  add_rewrite_rule(
    '^' . preg_quote($path, '/') . '/categoria-video/([^/]+)/pagina/([0-9]+)/?$',
    'index.php?page_id=' . (int) $page->ID . '&moma_video_category=$matches[1]&csmedia=$matches[2]',
    'top'
  );

  add_rewrite_rule(
    '^' . preg_quote($path, '/') . '/categoria-video/([^/]+)/?$',
    'index.php?page_id=' . (int) $page->ID . '&moma_video_category=$matches[1]',
    'top'
  );

  add_rewrite_rule(
    '^' . preg_quote($path, '/') . '/pagina/([0-9]+)/?$',
    'index.php?page_id=' . (int) $page->ID . '&csmedia=$matches[1]',
    'top'
  );
});

add_action('init', function () {
  $version = 'moma_multimedia_rewrite_v1';
  if (get_option('moma_multimedia_rewrite_version') === $version) return;

  flush_rewrite_rules(false);
  update_option('moma_multimedia_rewrite_version', $version, false);
}, 20);

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
