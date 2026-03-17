<?php
if (!defined('ABSPATH')) exit;

add_action('init', function () {

  $labels = [
    'name'               => 'Case studies',
    'singular_name'      => 'Case study',
    'menu_name'          => 'Case studies',
    'add_new'            => 'Aggiungi nuovo',
    'add_new_item'       => 'Aggiungi nuovo case study',
    'edit_item'          => 'Modifica case study',
    'new_item'           => 'Nuovo case study',
    'view_item'          => 'Vedi case study',
    'search_items'       => 'Cerca case studies',
    'not_found'          => 'Nessun case study trovato',
    'not_found_in_trash' => 'Nessun case study nel cestino',
    'all_items'          => 'Tutti i case studies',
  ];

  register_post_type('case_study', [
    'labels' => $labels,
    'public' => true,
    'show_in_rest' => true,
    'menu_position' => 20,
    'menu_icon' => 'dashicons-portfolio',
    'supports' => ['title', 'editor', 'thumbnail', 'excerpt'],
    'has_archive' => true,
    'rewrite' => [
      'slug' => 'case-studies',
      'with_front' => false,
    ],
  ]);

    // Tassonomia: Categorie Case Study (pill in home)
  $tax_labels = [
    'name'          => 'Categorie Case Study',
    'singular_name' => 'Categoria Case Study',
    'search_items'  => 'Cerca categorie',
    'all_items'     => 'Tutte le categorie',
    'edit_item'     => 'Modifica categoria',
    'update_item'   => 'Aggiorna categoria',
    'add_new_item'  => 'Aggiungi nuova categoria',
    'new_item_name' => 'Nuova categoria',
    'menu_name'     => 'Categorie',
  ];

  register_taxonomy('case_study_category', ['case_study'], [
    'labels'            => $tax_labels,
    'public'            => true,
    'hierarchical'      => true,        // “categorie” classiche (checkbox)
    'show_ui'           => true,
    'show_admin_column' => true,
    'show_in_rest'      => true,
    'rewrite'           => [
      'slug'       => 'case-studies-category',
      'with_front' => false,
    ],
  ]);
});
