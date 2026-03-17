<?php
if (!defined('ABSPATH')) exit;

if (!function_exists('moma_cs_single_cursor_attrs')) {
  function moma_cs_single_cursor_attrs(): string
  {
    if (!function_exists('get_field')) return '';

    $enabled = (bool) get_field('csa_single_cursor_enabled', 'option');
    $image   = get_field('csa_single_cursor_image', 'option');
    $url     = (is_array($image) && !empty($image['url'])) ? (string) $image['url'] : '';

    if (!$enabled || $url === '') return '';

    return sprintf(' data-cursor-scope="1" data-cursor-img="%s"', esc_url($url));
  }
}

if (!function_exists('moma_cs_single_get_image_url')) {
  function moma_cs_single_get_image_url($image, string $size = 'large'): string
  {
    if (is_numeric($image)) {
      return (string) wp_get_attachment_image_url((int) $image, $size);
    }

    if (is_array($image)) {
      if (!empty($image['sizes'][$size])) return (string) $image['sizes'][$size];
      if (!empty($image['url'])) return (string) $image['url'];
      if (!empty($image['ID'])) return (string) wp_get_attachment_image_url((int) $image['ID'], $size);
    }

    return '';
  }
}

if (!function_exists('moma_cs_single_get_image_id')) {
  function moma_cs_single_get_image_id($image): int
  {
    if (is_numeric($image)) return (int) $image;
    if (is_array($image) && !empty($image['ID'])) return (int) $image['ID'];
    return 0;
  }
}

if (!function_exists('moma_cs_single_parse_video_url')) {
  function moma_cs_single_parse_video_url(string $url): array
  {
    $url = trim($url);
    if ($url === '') return ['provider' => '', 'embed' => '', 'watch' => ''];

    $youtube_patterns = [
      '~(?:youtube\.com/watch\?v=|youtu\.be/|youtube\.com/embed/)([A-Za-z0-9_-]{6,})~i',
      '~youtube\.com/shorts/([A-Za-z0-9_-]{6,})~i',
    ];

    foreach ($youtube_patterns as $pattern) {
      if (preg_match($pattern, $url, $matches)) {
        $id = $matches[1];
        return [
          'provider' => 'youtube',
          'watch'    => 'https://www.youtube.com/watch?v=' . $id,
          'embed'    => 'https://www.youtube.com/embed/' . $id . '?autoplay=1&rel=0',
        ];
      }
    }

    if (preg_match('~vimeo\.com/(?:video/)?([0-9]+)~i', $url, $matches)) {
      $id = $matches[1];
      return [
        'provider' => 'vimeo',
        'watch'    => 'https://vimeo.com/' . $id,
        'embed'    => 'https://player.vimeo.com/video/' . $id . '?autoplay=1',
      ];
    }

    return ['provider' => '', 'embed' => '', 'watch' => $url];
  }
}

if (!function_exists('moma_cs_single_build_lightbox_attrs')) {
  function moma_cs_single_build_lightbox_attrs(array $payload): string
  {
    $attrs = ['data-moma-lightbox="1"'];

    foreach ($payload as $key => $value) {
      if ($value === null || $value === '') continue;
      $attrs[] = sprintf('%s="%s"', esc_attr($key), esc_attr((string) $value));
    }

    return ' ' . implode(' ', $attrs);
  }
}

if (!function_exists('moma_cs_single_normalize_media')) {
  function moma_cs_single_normalize_media($media, array $args = []): array
  {
    $defaults = [
      'image_size' => 'large',
      'alt'        => '',
    ];

    $args = wp_parse_args($args, $defaults);

    if (!is_array($media)) {
      return [];
    }

    $type = $media['media_type'] ?? 'image';
    $alt  = trim((string) ($args['alt'] ?? ''));

    if ($type === 'video') {
      $source = $media['video_source'] ?? 'upload';
      $thumb  = $media['video_thumbnail'] ?? null;
      $thumb_id  = moma_cs_single_get_image_id($thumb);
      $thumb_url = moma_cs_single_get_image_url($thumb, $args['image_size']);
      $thumb_full = moma_cs_single_get_image_url($thumb, 'full');

      $payload = [
        'type'        => 'video',
        'source-type' => $source,
      ];

      $href = '#';

      if ($source === 'upload') {
        $mp4  = $media['video_file_mp4'] ?? null;
        $webm = $media['video_file_webm'] ?? null;
        $mp4_url  = is_array($mp4) && !empty($mp4['url']) ? (string) $mp4['url'] : '';
        $webm_url = is_array($webm) && !empty($webm['url']) ? (string) $webm['url'] : '';
        if ($mp4_url === '' && $webm_url === '') return [];

        $payload['mp4']  = $mp4_url;
        $payload['webm'] = $webm_url;
        $href = $mp4_url ?: $webm_url;
      } else {
        $raw_url = trim((string) ($media['video_url'] ?? ''));
        $parsed  = moma_cs_single_parse_video_url($raw_url);
        if (empty($parsed['embed'])) return [];

        $payload['embed'] = $parsed['embed'];
        $href = $parsed['watch'] ?: $raw_url;
      }

      if ($thumb_full) {
        $payload['thumb'] = $thumb_full;
      }

      return [
        'kind'             => 'video',
        'href'             => $href,
        'thumb_id'         => $thumb_id,
        'thumb_url'        => $thumb_url,
        'thumb_full_url'   => $thumb_full,
        'thumb_alt'        => $alt,
        'lightbox_attrs'   => moma_cs_single_build_lightbox_attrs($payload),
      ];
    }

    $image    = $media['image'] ?? null;
    $image_id = moma_cs_single_get_image_id($image);
    if (!$image_id) return [];

    $full_url  = moma_cs_single_get_image_url($image, 'full');
    $lightbox  = !empty($media['enable_lightbox']);

    return [
      'kind'           => 'image',
      'image_id'       => $image_id,
      'image_url'      => moma_cs_single_get_image_url($image, $args['image_size']),
      'full_url'       => $full_url,
      'lightbox'       => $lightbox,
      'alt'            => $alt,
      'lightbox_attrs' => $lightbox && $full_url ? moma_cs_single_build_lightbox_attrs([
        'type' => 'image',
        'src'  => $full_url,
      ]) : '',
    ];
  }
}

if (!function_exists('moma_cs_single_render_media')) {
  function moma_cs_single_render_media($media, array $args = []): string
  {
    $defaults = [
      'image_size'        => 'large',
      'wrapper_class'     => '',
      'media_class'       => '',
      'cursor_attrs'      => '',
      'alt'               => '',
      'play_class'        => '',
      'overlay_class'     => '',
      'link_class'        => '',
      'show_play_button'  => true,
      'loading'           => 'lazy',
    ];

    $args = wp_parse_args($args, $defaults);
    $item = moma_cs_single_normalize_media($media, $args);
    if (!$item) return '';

    $wrapper_class = trim((string) $args['wrapper_class']);
    $media_class   = trim((string) $args['media_class']);
    $cursor_attrs  = (string) ($args['cursor_attrs'] ?? '');
    $link_class    = trim((string) $args['link_class']);
    $play_class    = trim((string) $args['play_class']);
    $overlay_class = trim((string) $args['overlay_class']);
    $loading       = trim((string) $args['loading']);

    ob_start();
    ?>
    <div class="<?php echo esc_attr($wrapper_class); ?>">
      <?php if ($item['kind'] === 'image'): ?>
        <?php if (!empty($item['lightbox']) && !empty($item['lightbox_attrs'])): ?>
          <a href="<?php echo esc_url($item['full_url']); ?>" class="<?php echo esc_attr($link_class); ?>"<?php echo $cursor_attrs; ?><?php echo $item['lightbox_attrs']; ?>>
            <?php echo wp_get_attachment_image($item['image_id'], $args['image_size'], false, [
              'class'    => $media_class,
              'loading'  => $loading,
              'decoding' => 'async',
              'alt'      => $item['alt'],
            ]); ?>
          </a>
        <?php else: ?>
          <div class="<?php echo esc_attr($link_class); ?>"<?php echo $cursor_attrs; ?>>
            <?php echo wp_get_attachment_image($item['image_id'], $args['image_size'], false, [
              'class'    => $media_class,
              'loading'  => $loading,
              'decoding' => 'async',
              'alt'      => $item['alt'],
            ]); ?>
          </div>
        <?php endif; ?>
      <?php else: ?>
        <a href="<?php echo esc_url($item['href']); ?>" class="moma-case-single__media-link <?php echo esc_attr($link_class); ?>"<?php echo $cursor_attrs; ?><?php echo $item['lightbox_attrs']; ?>>
          <?php if (!empty($item['thumb_id'])): ?>
            <?php echo wp_get_attachment_image($item['thumb_id'], $args['image_size'], false, [
              'class'    => $media_class,
              'loading'  => $loading,
              'decoding' => 'async',
              'alt'      => $item['thumb_alt'],
            ]); ?>
          <?php else: ?>
            <div class="<?php echo esc_attr(trim($media_class . ' moma-case-single__media-placeholder')); ?>"></div>
          <?php endif; ?>

          <?php if (!empty($overlay_class)): ?>
            <span class="<?php echo esc_attr($overlay_class); ?>" aria-hidden="true"></span>
          <?php endif; ?>

          <?php if (!empty($args['show_play_button'])): ?>
            <span class="moma-case-single__play <?php echo esc_attr($play_class); ?>" aria-hidden="true">
              <svg viewBox="0 0 44 44" width="44" height="44" focusable="false" aria-hidden="true">
                <circle cx="22" cy="22" r="22" fill="currentColor"></circle>
                <path d="M18 14.5L31 22L18 29.5V14.5Z" fill="#f7f3ee"></path>
              </svg>
            </span>
          <?php endif; ?>
        </a>
      <?php endif; ?>
    </div>
    <?php

    return trim((string) ob_get_clean());
  }
}

if (!function_exists('moma_cs_single_pick_related_posts')) {
  function moma_cs_single_pick_related_posts(int $post_id, array $manual_ids = [], int $limit = 2): array
  {
    $manual_ids = array_values(array_filter(array_map('intval', $manual_ids)));
    if (!empty($manual_ids)) {
      return get_posts([
        'post_type'      => 'case_study',
        'post_status'    => 'publish',
        'post__in'       => $manual_ids,
        'post__not_in'   => [$post_id],
        'orderby'        => 'post__in',
        'posts_per_page' => $limit,
      ]);
    }

    $term_ids = wp_get_post_terms($post_id, 'case_study_category', ['fields' => 'ids']);

    $query_args = [
      'post_type'      => 'case_study',
      'post_status'    => 'publish',
      'post__not_in'   => [$post_id],
      'posts_per_page' => $limit,
      'orderby'        => 'date',
      'order'          => 'DESC',
    ];

    if (!is_wp_error($term_ids) && !empty($term_ids)) {
      $query_args['tax_query'] = [[
        'taxonomy' => 'case_study_category',
        'field'    => 'term_id',
        'terms'    => array_map('intval', $term_ids),
      ]];
    }

    $posts = get_posts($query_args);

    if (count($posts) < $limit) {
      $existing = array_merge([$post_id], array_map(static fn($post) => (int) $post->ID, $posts));
      $fallback = get_posts([
        'post_type'      => 'case_study',
        'post_status'    => 'publish',
        'post__not_in'   => $existing,
        'posts_per_page' => $limit - count($posts),
        'orderby'        => 'rand',
      ]);

      $posts = array_merge($posts, $fallback);
    }

    return $posts;
  }
}
