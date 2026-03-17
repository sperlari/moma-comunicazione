<?php
if (!defined('ABSPATH')) exit;

$defaults = [
  'eyebrow'  => 'Esperienza, strategia, visione.',
  'title'    => 'Siamo un punto fermo.',
  'mode'     => 'auto',
  'auto_max' => 8,
];

$eyebrow  = $defaults['eyebrow'];
$title    = $defaults['title'];
$mode     = $defaults['mode'];
$auto_max = $defaults['auto_max'];
$manual   = [];

if (function_exists('get_field')) {
  $eyebrow  = get_field('home_cs_eyebrow') ?: $eyebrow;
  $title    = get_field('home_cs_title') ?: $title;
  $mode     = get_field('home_cs_mode') ?: $mode;
  $auto_max = (int) (get_field('home_cs_auto_max') ?: $auto_max);
  $manual   = get_field('home_cs_manual') ?: [];
}

$args = [
  'eyebrow' => $eyebrow,
  'title'   => $title,
  'title_class' => 'font-fraunces',
  'title_typewriter' => true,
  'colors'  => ['#ccc0b1', '#f38c12'],
];

// Cursor immagine (Case Studies – solo sulla media)
$cursor_enabled_raw = function_exists('get_field') ? get_field('home_cs_cursor_enabled') : null;
$cursor_enabled = ($cursor_enabled_raw === null) ? true : (bool) $cursor_enabled_raw;

$cursor_img = function_exists('get_field') ? get_field('home_cs_cursor_image') : null; // array
if (!$cursor_img && function_exists('get_field')) $cursor_img = get_field('cursor_home_cs_image', 'option');

$global_enabled_raw = function_exists('get_field') ? get_field('cursor_home_cs_enabled', 'option') : null;
$global_enabled = ($global_enabled_raw === null) ? true : (bool) $global_enabled_raw;

$cursor_url = (is_array($cursor_img) && !empty($cursor_img['url'])) ? $cursor_img['url'] : '';
$args['cursor_enabled'] = ($global_enabled && $cursor_enabled && $cursor_url);
$args['cursor_url'] = $cursor_url;

// manuale: usa l’ordine scelto in ACF
if ($mode === 'manual' && !empty($manual)) {
  $posts = array_values(array_filter(array_map('get_post', (array)$manual), function ($p) {
    return ($p instanceof WP_Post) && $p->post_status === 'publish';
  }));

  if (!empty($posts)) {
    $args['posts'] = $posts;
    $args['limit'] = count($posts);
  } else {
    $args['limit'] = max(1, $auto_max);
  }
} else {
  // automatico: ordine pubblicazione (mantiene la tua logica featured + latest nel component)
  $args['limit'] = max(1, $auto_max);
}

get_template_part('template-parts/site/components/case-studies-stack', null, $args);