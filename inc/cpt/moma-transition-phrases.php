<?php
if (!defined('ABSPATH')) exit;

add_action('init', function () {

  $labels = [
    'name'               => 'Frasi Transizione',
    'singular_name'      => 'Frase Transizione',
    'add_new'            => 'Aggiungi nuova',
    'add_new_item'       => 'Aggiungi nuova frase',
    'edit_item'          => 'Modifica frase',
    'new_item'           => 'Nuova frase',
    'view_item'          => 'Vedi frase',
    'search_items'       => 'Cerca frasi',
    'not_found'          => 'Nessuna frase trovata',
    'not_found_in_trash' => 'Nessuna frase nel cestino',
    'menu_name'          => 'Frasi Transizione',
  ];

  register_post_type('moma_tphrase', [
    'labels' => $labels,

    'public' => false,
    'show_ui' => true,
    'show_in_menu' => true,
    'menu_icon' => 'dashicons-format-quote',

    'publicly_queryable' => false,
    'exclude_from_search' => true,
    'rewrite' => false,
    'query_var' => false,
    'has_archive' => false,

    // usiamo SOLO titolo
    'supports' => ['title'],

    // ok per REST
    'show_in_rest' => true,
  ]);
});
