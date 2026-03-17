<?php
if (!defined('ABSPATH')) exit;

add_action('init', function () {
  if (post_type_exists('glossario')) return;

  $labels = [
    'name'               => 'Glossario',
    'singular_name'      => 'Termine glossario',
    'menu_name'          => 'Glossario',
    'name_admin_bar'     => 'Termine glossario',
    'add_new'            => 'Aggiungi termine',
    'add_new_item'       => 'Aggiungi nuovo termine',
    'edit_item'          => 'Modifica termine',
    'new_item'           => 'Nuovo termine',
    'view_item'          => 'Vedi termine',
    'search_items'       => 'Cerca termini',
    'not_found'          => 'Nessun termine trovato',
    'not_found_in_trash' => 'Nessun termine nel cestino',
    'all_items'          => 'Tutti i termini',
  ];

  register_post_type('glossario', [
    'labels'              => $labels,
    'public'              => false,
    'show_ui'             => true,
    'show_in_menu'        => true,
    'show_in_admin_bar'   => true,
    'show_in_nav_menus'   => false,
    'show_in_rest'        => true,
    'publicly_queryable'  => false,
    'exclude_from_search' => true,
    'has_archive'         => false,
    'rewrite'             => false,
    'supports'            => ['title'],
    'menu_icon'           => 'dashicons-editor-help',
    'menu_position'       => 22,
  ]);
});
