<?php
if (!defined('ABSPATH')) exit;

function moma_glossary_normalize_link($link, string $fallback_label, string $fallback_url): array {
  if (is_array($link) && !empty($link['url'])) {
    return [
      'label'  => !empty($link['title']) ? (string) $link['title'] : $fallback_label,
      'url'    => (string) $link['url'],
      'target' => !empty($link['target']) ? (string) $link['target'] : '',
    ];
  }

  return [
    'label'  => $fallback_label,
    'url'    => $fallback_url,
    'target' => '',
  ];
}

function moma_glossary_terms_from_options(): array {
  if (!function_exists('get_field')) return [];
  $rows = get_field('glossary_terms', 'option');
  if (!is_array($rows)) return [];

  $out = [];
  foreach ($rows as $r) {
    $key = isset($r['key']) ? sanitize_title((string) $r['key']) : '';
    if (!$key) continue;

    $btn1 = moma_glossary_normalize_link($r['btn1_link'] ?? null, 'Contattaci', home_url('/contatti'));
    $btn2 = moma_glossary_normalize_link($r['btn2_link'] ?? null, 'Case studies', home_url('/case-studies'));

    $out[$key] = [
      'key'         => $key,
      'title'       => (string) ($r['title'] ?? ''),
      'body'        => (string) ($r['body'] ?? ''),
      'btn1_label'  => $btn1['label'],
      'btn1_url'    => $btn1['url'],
      'btn1_target' => $btn1['target'],
      'btn2_label'  => $btn2['label'],
      'btn2_url'    => $btn2['url'],
      'btn2_target' => $btn2['target'],
    ];
  }
  return $out;
}

function moma_glossary_terms_indexed(): array {
  $out = [];

  if (post_type_exists('glossario')) {
    $posts = get_posts([
      'post_type'      => 'glossario',
      'post_status'    => 'publish',
      'posts_per_page' => -1,
      'orderby'        => ['menu_order' => 'ASC', 'title' => 'ASC'],
      'order'          => 'ASC',
    ]);

    foreach ($posts as $p) {
      $pid = (int) $p->ID;
      $raw_key = function_exists('get_field') ? (string) get_field('glossary_key', $pid) : '';
      $key = sanitize_title($raw_key ?: $p->post_name);
      if ($key === '') continue;

      $body = function_exists('get_field') ? (string) get_field('body', $pid) : '';
      $btn1 = moma_glossary_normalize_link(function_exists('get_field') ? get_field('btn1_link', $pid) : null, 'Contattaci', home_url('/contatti'));
      $btn2 = moma_glossary_normalize_link(function_exists('get_field') ? get_field('btn2_link', $pid) : null, 'Case studies', home_url('/case-studies'));

      $out[$key] = [
        'key'         => $key,
        'title'       => get_the_title($pid),
        'body'        => $body,
        'btn1_label'  => $btn1['label'],
        'btn1_url'    => $btn1['url'],
        'btn1_target' => $btn1['target'],
        'btn2_label'  => $btn2['label'],
        'btn2_url'    => $btn2['url'],
        'btn2_target' => $btn2['target'],
      ];
    }
  }

  // Fallback di sicurezza: se non esistono ancora termini CPT, usa le vecchie Options.
  if (!$out) {
    $out = moma_glossary_terms_from_options();
  }

  return $out;
}

function moma_glossary_overrides_indexed(int $post_id): array {
  if (!function_exists('get_field') || $post_id <= 0) return [];
  $rows = get_field('glossary_overrides', $post_id);
  if (!is_array($rows)) return [];

  $out = [];
  foreach ($rows as $r) {
    $key = isset($r['term_key']) ? sanitize_title((string) $r['term_key']) : '';
    if (!$key) continue;

    $btn1 = !empty($r['btn1_link']) && is_array($r['btn1_link']) ? $r['btn1_link'] : null;
    $btn2 = !empty($r['btn2_link']) && is_array($r['btn2_link']) ? $r['btn2_link'] : null;

    $out[$key] = [
      'btn1_label'  => is_array($btn1) && !empty($btn1['title']) ? (string) $btn1['title'] : '',
      'btn1_url'    => is_array($btn1) && !empty($btn1['url']) ? (string) $btn1['url'] : '',
      'btn1_target' => is_array($btn1) && !empty($btn1['target']) ? (string) $btn1['target'] : '',
      'btn2_label'  => is_array($btn2) && !empty($btn2['title']) ? (string) $btn2['title'] : '',
      'btn2_url'    => is_array($btn2) && !empty($btn2['url']) ? (string) $btn2['url'] : '',
      'btn2_target' => is_array($btn2) && !empty($btn2['target']) ? (string) $btn2['target'] : '',
    ];
  }
  return $out;
}

function moma_glossary_terms_for_context(int $post_id = 0): array {
  $terms = moma_glossary_terms_indexed();
  if (!$terms) return [];

  $ov = $post_id > 0 ? moma_glossary_overrides_indexed($post_id) : [];
  if (!$ov) return $terms;

  foreach ($ov as $k => $btns) {
    if (!isset($terms[$k])) continue;
    foreach (['btn1_label','btn1_url','btn1_target','btn2_label','btn2_url','btn2_target'] as $f) {
      if (!empty($btns[$f])) {
        $terms[$k][$f] = $btns[$f];
      }
    }
  }

  return $terms;
}

function moma_glossary_parse_tokens(string $html, int $post_id = 0): string {
  if (trim($html) === '') return $html;

  $terms = moma_glossary_terms_for_context($post_id);
  $keys = array_keys($terms);

  $replaceToken = function ($m) use ($keys) {
    $rawKey = trim((string) ($m[1] ?? ''));
    $key = sanitize_title($rawKey);
    $label = isset($m[2]) && trim((string) $m[2]) !== '' ? trim((string) $m[2]) : $rawKey;
    if ($key === '' || $label === '' || !in_array($key, $keys, true)) return $m[0];
    return '<button type="button" class="font-semibold moma-glossary-trigger" data-glossary-key="' . esc_attr($key) . '">' . esc_html($label) . '</button>';
  };

  $html = preg_replace_callback('/\{\{\s*([a-z0-9_\-]+)\s*(?:\|\s*([^}]+?)\s*)?\}\}/i', $replaceToken, $html);
  $html = preg_replace_callback('/\[\[\s*([a-z0-9_\-]+)\s*(?:\|\s*([^\]]+?)\s*)?\]\]/i', $replaceToken, $html);

  $html = preg_replace_callback('/<strong[^>]*>(.*?)<\/strong>/is', function ($m) use ($terms, $keys) {
    $label = trim(wp_strip_all_tags($m[1] ?? ''));
    if ($label === '') return $m[0];

    $candidate = sanitize_title($label);
    $resolvedKey = '';

    if (in_array($candidate, $keys, true)) {
      $resolvedKey = $candidate;
    } else {
      foreach ($terms as $termKey => $term) {
        $titleSlug = sanitize_title((string) ($term['title'] ?? ''));
        if ($titleSlug !== '' && $titleSlug === $candidate) {
          $resolvedKey = $termKey;
          break;
        }
      }
    }

    if ($resolvedKey === '') return $m[0];

    return '<button type="button" class="font-semibold moma-glossary-trigger" data-glossary-key="' . esc_attr($resolvedKey) . '">' . esc_html($label) . '</button>';
  }, $html);

  return $html;
}

function moma_glossary_render_data_node(int $post_id = 0): void {
  static $rendered = false;
  if ($rendered) return;
  $data = moma_glossary_terms_for_context($post_id);
  echo '<script id="moma-glossary-data" type="application/json">' . wp_json_encode($data) . '</script>';
  $rendered = true;
}

add_filter('the_content', function ($content) {
  if (is_singular('case_study') || is_singular('servizio')) {
    return moma_glossary_parse_tokens($content, (int) get_the_ID());
  }
  return $content;
}, 20);


/**
 * Migrazione one-shot da vecchie Options ACF al CPT glossario.
 * Mantiene il frontend funzionante anche prima della ricreazione manuale dei termini.
 */
function moma_glossary_maybe_migrate_options_to_cpt(): void {
  if (!is_admin() || !current_user_can('manage_options')) return;
  if (!post_type_exists('glossario')) return;
  if (!function_exists('get_field')) return;

  if (get_option('moma_glossario_cpt_migrated_v1')) return;

  $rows = get_field('glossary_terms', 'option');
  if (!is_array($rows) || !$rows) {
    update_option('moma_glossario_cpt_migrated_v1', 1, false);
    return;
  }

  $existing = get_posts([
    'post_type'      => 'glossario',
    'post_status'    => 'any',
    'posts_per_page' => 1,
    'fields'         => 'ids',
  ]);

  if (!empty($existing)) {
    update_option('moma_glossario_cpt_migrated_v1', 1, false);
    return;
  }

  foreach ($rows as $row) {
    $raw_key = isset($row['key']) ? (string) $row['key'] : '';
    $key = sanitize_title($raw_key);
    $title = isset($row['title']) && trim((string) $row['title']) !== '' ? (string) $row['title'] : $key;
    if ($key === '' || $title === '') continue;

    $post_id = wp_insert_post([
      'post_type'   => 'glossario',
      'post_status' => 'publish',
      'post_title'  => $title,
      'post_name'   => $key,
      'menu_order'  => 0,
    ], true);

    if (is_wp_error($post_id) || !$post_id) continue;

    $body = isset($row['body']) ? (string) $row['body'] : '';
    $btn1 = $row['btn1_link'] ?? null;
    $btn2 = $row['btn2_link'] ?? null;

    if (function_exists('update_field')) {
      update_field('glossary_key', $key, $post_id);
      update_field('body', $body, $post_id);
      if (is_array($btn1)) update_field('btn1_link', $btn1, $post_id);
      if (is_array($btn2)) update_field('btn2_link', $btn2, $post_id);
    } else {
      update_post_meta($post_id, 'glossary_key', $key);
      update_post_meta($post_id, 'body', $body);
      if (is_array($btn1)) update_post_meta($post_id, 'btn1_link', $btn1);
      if (is_array($btn2)) update_post_meta($post_id, 'btn2_link', $btn2);
    }
  }

  update_option('moma_glossario_cpt_migrated_v1', 1, false);
}
add_action('admin_init', 'moma_glossary_maybe_migrate_options_to_cpt');
