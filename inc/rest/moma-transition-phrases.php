<?php
if (!defined('ABSPATH')) exit;

add_action('rest_api_init', function () {

  register_rest_route('moma/v1', '/transition-phrase', [
    'methods'  => 'GET',
    'permission_callback' => '__return_true',
    'callback' => function () {

      $posts = get_posts([
        'post_type'      => 'moma_tphrase', 
        'post_status'    => 'publish',
        'posts_per_page' => 1,
        'orderby'        => 'rand',
        'no_found_rows'  => true,
      ]);

      $text = $posts ? get_the_title($posts[0]) : '';

      return rest_ensure_response(['text' => $text]);
    },
  ]);

});
