<?php
if (!defined('ABSPATH')) exit;

if (!function_exists('moma_case_study_term_url')) {
  function moma_case_study_term_url($term): string
  {
    if (!($term instanceof WP_Term)) return '';
    $url = get_term_link($term);
    return is_wp_error($url) ? '' : (string) $url;
  }
}

if (!function_exists('moma_case_study_render_term_chip')) {
  function moma_case_study_render_term_chip($term, array $args = []): string
  {
    if (!($term instanceof WP_Term)) return '';

    $args = wp_parse_args($args, [
      'dot' => false,
      'current' => false,
      'class' => '',
      'tag' => 'a',
      'href' => '',
      'attrs' => '',
    ]);

    $classes = trim('m-btn m-btn--tag m-btn--sm' . (!empty($args['dot']) ? ' m-btn--dot' : '') . ' ' . (string) $args['class']);
    $label = '<span class="m-btn__label">' . esc_html($term->name) . '</span>';
    $extra_attrs = trim((string) $args['attrs']);

    if ($args['tag'] === 'span') {
      return '<span class="' . esc_attr($classes) . '"' . ($extra_attrs ? ' ' . $extra_attrs : '') . '>' . $label . '</span>';
    }

    $href = (string) $args['href'];
    if ($href === '') {
      $href = moma_case_study_term_url($term);
    }
    if ($href === '') {
      return '<span class="' . esc_attr($classes) . '">' . $label . '</span>';
    }

    $aria_current = !empty($args['current']) ? ' aria-current="page"' : '';

    return '<a class="' . esc_attr($classes) . '" href="' . esc_url($href) . '"' . $aria_current . ($extra_attrs ? ' ' . $extra_attrs : '') . '>' . $label . '</a>';
  }
}

if (!function_exists('moma_case_study_collect_posts')) {
  function moma_case_study_collect_posts(array $query_args = []): array
  {
    $defaults = [
      'post_type'      => 'case_study',
      'post_status'    => 'publish',
      'posts_per_page' => -1,
      'orderby'        => 'date',
      'order'          => 'DESC',
      'meta_query'     => [
        [
          'key'     => '_thumbnail_id',
          'compare' => 'EXISTS',
        ],
      ],
    ];

    $posts = get_posts(wp_parse_args($query_args, $defaults));
    if (!$posts) return [];

    usort($posts, static function ($a, $b) {
      $a_featured = function_exists('get_field') ? (bool) get_field('cs_featured', $a->ID) : false;
      $b_featured = function_exists('get_field') ? (bool) get_field('cs_featured', $b->ID) : false;

      if ($a_featured !== $b_featured) {
        return $a_featured ? -1 : 1;
      }

      return strtotime((string) $b->post_date) <=> strtotime((string) $a->post_date);
    });

    return $posts;
  }
}

if (!function_exists('moma_case_study_pick_statement_post')) {
  function moma_case_study_pick_statement_post(array $posts): ?WP_Post
  {
    $statement_id = 0;
    if (function_exists('get_field')) {
      $statement_rel = get_field('csa_statement_case_study', 'option');
      if (is_array($statement_rel) && !empty($statement_rel[0])) {
        $statement_id = (int) $statement_rel[0];
      } elseif (is_numeric($statement_rel)) {
        $statement_id = (int) $statement_rel;
      }
    }

    if ($statement_id) {
      foreach ($posts as $post_obj) {
        if ((int) $post_obj->ID === $statement_id) {
          return $post_obj;
        }
      }
    }

    return !empty($posts[0]) && $posts[0] instanceof WP_Post ? $posts[0] : null;
  }
}

if (!function_exists('moma_case_study_collect_filter_terms')) {
  function moma_case_study_collect_filter_terms(array $posts = []): array
  {
    $term_map = [];

    if (!empty($posts)) {
      foreach ($posts as $post_obj) {
        if (!($post_obj instanceof WP_Post)) continue;
        $terms = get_the_terms($post_obj->ID, 'case_study_category');
        if (is_wp_error($terms) || empty($terms)) continue;

        foreach ($terms as $term) {
          $slug = sanitize_title($term->slug);
          if ($slug === '' || isset($term_map[$slug])) continue;
          $term_map[$slug] = [
            'slug' => $slug,
            'name' => $term->name,
            'id'   => (int) $term->term_id,
            'term' => $term,
          ];
        }
      }

      return array_values($term_map);
    }

    $terms = get_terms([
      'taxonomy'   => 'case_study_category',
      'hide_empty' => true,
      'orderby'    => 'name',
      'order'      => 'ASC',
    ]);

    if (is_wp_error($terms) || empty($terms)) return [];

    foreach ($terms as $term) {
      $slug = sanitize_title($term->slug);
      $term_map[$slug] = [
        'slug' => $slug,
        'name' => $term->name,
        'id'   => (int) $term->term_id,
        'term' => $term,
      ];
    }

    return array_values($term_map);
  }
}

if (!function_exists('moma_case_study_cursor_attrs_from_fields')) {
  function moma_case_study_cursor_attrs_from_fields(string $enabled_field, string $image_field, $object_id = 'option'): string
  {
    if (!function_exists('get_field')) return '';

    $enabled = (bool) get_field($enabled_field, $object_id);
    $image   = get_field($image_field, $object_id);
    $url     = (is_array($image) && !empty($image['url'])) ? (string) $image['url'] : '';

    if (!$enabled || $url === '') return '';

    return sprintf(' data-cursor-scope="1" data-cursor-img="%s"', esc_url($url));
  }
}
