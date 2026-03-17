<?php
if (!defined('ABSPATH')) exit;

if (!function_exists('moma_allow_svg_uploads')) {
  add_filter('upload_mimes', function ($mimes) {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
  });

  add_filter('wp_check_filetype_and_ext', function ($data, $file, $filename, $mimes) {
    if (strtolower((string) pathinfo((string) $filename, PATHINFO_EXTENSION)) === 'svg') {
      $data['ext'] = 'svg';
      $data['type'] = 'image/svg+xml';
    }
    return $data;
  }, 10, 4);

  function moma_allow_svg_uploads(): bool
  {
    return true;
  }
}

if (!function_exists('moma_get_inline_svg')) {
  function moma_get_inline_svg($file, array $args = []): string
  {
    $args = wp_parse_args($args, [
      'class'      => '',
      'role'       => 'img',
      'aria_label' => '',
    ]);

    $path = '';

    if (is_array($file) && !empty($file['ID'])) {
      $path = (string) get_attached_file((int) $file['ID']);
    } elseif (is_numeric($file)) {
      $path = (string) get_attached_file((int) $file);
    } elseif (is_array($file) && !empty($file['url'])) {
      $upload = wp_get_upload_dir();
      $baseurl = (string) ($upload['baseurl'] ?? '');
      $basedir = (string) ($upload['basedir'] ?? '');
      if ($baseurl && $basedir && str_starts_with((string) $file['url'], $baseurl)) {
        $path = $basedir . str_replace($baseurl, '', (string) $file['url']);
      }
    } elseif (is_string($file)) {
      $path = $file;
    }

    if ($path === '' || !is_file($path) || strtolower((string) pathinfo($path, PATHINFO_EXTENSION)) !== 'svg') {
      return '';
    }

    $svg = file_get_contents($path);
    if (!is_string($svg) || trim($svg) === '') {
      return '';
    }

    $class = trim((string) $args['class']);
    if ($class !== '') {
      if (preg_match('/<svg\b([^>]*)class="([^"]*)"/i', $svg)) {
        $svg = preg_replace('/<svg\b([^>]*)class="([^"]*)"/i', '<svg$1class="$2 ' . esc_attr($class) . '"', $svg, 1);
      } else {
        $svg = preg_replace('/<svg\b/i', '<svg class="' . esc_attr($class) . '"', $svg, 1);
      }
    }

    $role = trim((string) $args['role']);
    if ($role !== '' && !preg_match('/<svg\b[^>]*\brole="/i', $svg)) {
      $svg = preg_replace('/<svg\b/i', '<svg role="' . esc_attr($role) . '"', $svg, 1);
    }

    $aria_label = trim((string) $args['aria_label']);
    if ($aria_label !== '' && !preg_match('/<svg\b[^>]*\baria-label="/i', $svg)) {
      $svg = preg_replace('/<svg\b/i', '<svg aria-label="' . esc_attr($aria_label) . '"', $svg, 1);
    }

    if (!preg_match('/<svg\b[^>]*\bpreserveAspectRatio="/i', $svg)) {
      $svg = preg_replace('/<svg\b/i', '<svg preserveAspectRatio="xMidYMid meet"', $svg, 1);
    }

    return $svg;
  }
}
