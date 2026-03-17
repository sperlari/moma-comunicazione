<?php
if (!defined('ABSPATH')) exit;

add_action('init', function () {
  if (post_type_exists('servizio')) return;

  $labels = [
    'name'               => 'Servizi',
    'singular_name'      => 'Servizio',
    'menu_name'          => 'Servizi',
    'add_new'            => 'Aggiungi nuovo',
    'add_new_item'       => 'Aggiungi nuovo servizio',
    'edit_item'          => 'Modifica servizio',
    'new_item'           => 'Nuovo servizio',
    'view_item'          => 'Vedi servizio',
    'search_items'       => 'Cerca servizi',
    'not_found'          => 'Nessun servizio trovato',
    'not_found_in_trash' => 'Nessun servizio nel cestino',
    'all_items'          => 'Tutti i servizi',
  ];

  register_post_type('servizio', [
    'labels'        => $labels,
    'public'        => true,
    'show_in_rest'  => true,
    'menu_position' => 21,
    'menu_icon'     => 'dashicons-admin-tools',
    // editor = contenuto pagina servizio (NON usato nello slider)
    'supports'      => ['title', 'editor', 'thumbnail', 'excerpt', 'page-attributes'],
    'has_archive'   => true,
    'rewrite'       => [
      'slug'       => 'servizi',
      'with_front' => false,
    ],
  ]);
});
