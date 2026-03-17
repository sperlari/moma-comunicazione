<?php
if (!defined('ABSPATH')) exit;

if (!function_exists('moma_multimedia_sanitize_category_slug')) {
  function moma_multimedia_sanitize_category_slug(string $value): string
  {
    return sanitize_title(wp_strip_all_tags($value));
  }
}

if (!function_exists('moma_multimedia_get_video_category_registry')) {
  function moma_multimedia_get_video_category_registry(): array
  {
    if (!function_exists('get_field')) return [];

    $rows = (array) get_field('moma_multimedia_video_categories', 'option');
    $registry = [];

    foreach ($rows as $row) {
      $label = trim((string) ($row['label'] ?? ''));
      $slug  = moma_multimedia_sanitize_category_slug((string) ($row['slug'] ?? $label));
      if ($label === '' || $slug === '') continue;

      $registry[$slug] = [
        'label' => $label,
        'slug'  => $slug,
      ];
    }

    return $registry;
  }
}

if (!function_exists('moma_multimedia_get_video_categories_for_post')) {
  function moma_multimedia_get_video_categories_for_post(int $post_id): array
  {
    if (!function_exists('get_field')) return [];

    $feature_video = (array) get_field('cs_feature_video', $post_id);
    $selected = $feature_video['video_categories'] ?? [];
    $selected = is_array($selected) ? $selected : [$selected];
    $selected = array_values(array_unique(array_filter(array_map('strval', $selected))));

    if (empty($selected)) return [];

    $registry = moma_multimedia_get_video_category_registry();
    $categories = [];

    foreach ($selected as $raw) {
      $slug = moma_multimedia_sanitize_category_slug($raw);
      if ($slug === '') continue;

      $categories[$slug] = [
        'slug'  => $slug,
        'label' => $registry[$slug]['label'] ?? ucwords(str_replace('-', ' ', $slug)),
      ];
    }

    return array_values($categories);
  }
}

if (!function_exists('moma_multimedia_collect_video_categories')) {
  function moma_multimedia_collect_video_categories(array $items): array
  {
    $categories = [];

    foreach ($items as $item) {
      $item_categories = $item['video_categories'] ?? [];
      if (!is_array($item_categories)) continue;

      foreach ($item_categories as $category) {
        $slug  = moma_multimedia_sanitize_category_slug((string) ($category['slug'] ?? ''));
        $label = trim((string) ($category['label'] ?? ''));
        if ($slug === '' || $label === '') continue;

        if (!isset($categories[$slug])) {
          $categories[$slug] = [
            'slug'  => $slug,
            'label' => $label,
          ];
        }
      }
    }

    uasort($categories, static function ($a, $b) {
      return strcasecmp((string) $a['label'], (string) $b['label']);
    });

    return array_values($categories);
  }
}

if (!function_exists('moma_multimedia_get_page')) {
  function moma_multimedia_get_page(): ?WP_Post
  {
    static $page = null;
    static $loaded = false;

    if ($loaded) {
      return $page;
    }

    $loaded = true;
    $pages = get_posts([
      'post_type'      => 'page',
      'post_status'    => 'publish',
      'posts_per_page' => 1,
      'meta_key'       => '_wp_page_template',
      'meta_value'     => 'templates/page-multimedia.php',
      'orderby'        => 'menu_order title',
      'order'          => 'ASC',
    ]);

    $page = !empty($pages[0]) && $pages[0] instanceof WP_Post ? $pages[0] : null;
    return $page;
  }
}

if (!function_exists('moma_multimedia_get_page_path')) {
  function moma_multimedia_get_page_path(int $page_id = 0): string
  {
    if (!$page_id) {
      $page = moma_multimedia_get_page();
      $page_id = $page instanceof WP_Post ? (int) $page->ID : 0;
    }

    if (!$page_id) return '';

    $permalink = get_permalink($page_id);
    if (!$permalink) return '';

    $path = (string) wp_parse_url($permalink, PHP_URL_PATH);
    return trim($path, '/');
  }
}

if (!function_exists('moma_multimedia_get_page_url')) {
  function moma_multimedia_get_page_url(int $page_id = 0, int $page_number = 1): string
  {
    if (!$page_id) {
      $page = moma_multimedia_get_page();
      $page_id = $page instanceof WP_Post ? (int) $page->ID : 0;
    }

    if (!$page_id) return home_url('/');

    $base = trailingslashit(get_permalink($page_id));
    if ($page_number > 1) {
      return trailingslashit($base . 'pagina/' . $page_number);
    }

    return $base;
  }
}

if (!function_exists('moma_multimedia_get_video_category_url')) {
  function moma_multimedia_get_video_category_url(string $slug, int $page_id = 0, int $page_number = 1): string
  {
    $slug = moma_multimedia_sanitize_category_slug($slug);
    if ($slug === '') {
      return moma_multimedia_get_page_url($page_id, $page_number);
    }

    if (!$page_id) {
      $page = moma_multimedia_get_page();
      $page_id = $page instanceof WP_Post ? (int) $page->ID : 0;
    }

    if (!$page_id) return home_url('/');

    $base = trailingslashit(get_permalink($page_id)) . 'categoria-video/' . $slug . '/';
    if ($page_number > 1) {
      return trailingslashit($base . 'pagina/' . $page_number);
    }

    return $base;
  }
}

if (!function_exists('moma_render_custom_chip')) {
  function moma_render_custom_chip(string $label, array $args = []): string
  {
    $label = trim($label);
    if ($label === '') return '';

    $args = wp_parse_args($args, [
      'dot'     => false,
      'current' => false,
      'class'   => '',
      'tag'     => 'a',
      'href'    => '',
      'attrs'   => '',
    ]);

    $classes = trim('m-btn m-btn--tag m-btn--sm' . (!empty($args['dot']) ? ' m-btn--dot' : '') . ' ' . (string) $args['class']);
    $label_html = '<span class="m-btn__label">' . esc_html($label) . '</span>';
    $extra_attrs = trim((string) $args['attrs']);

    if ($args['tag'] === 'span' || (string) $args['href'] === '') {
      return '<span class="' . esc_attr($classes) . '"' . ($extra_attrs ? ' ' . $extra_attrs : '') . '>' . $label_html . '</span>';
    }

    $aria_current = !empty($args['current']) ? ' aria-current="page"' : '';

    return '<a class="' . esc_attr($classes) . '" href="' . esc_url((string) $args['href']) . '"' . $aria_current . ($extra_attrs ? ' ' . $extra_attrs : '') . '>' . $label_html . '</a>';
  }
}
