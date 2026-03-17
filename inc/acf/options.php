<?php
add_action('acf/init', function () {
  if (!function_exists('acf_add_options_page')) return;

  acf_add_options_page([
    'page_title' => 'Impostazioni Tema',
    'menu_title' => 'Impostazioni Tema',
    'menu_slug'  => 'moma-theme-settings',
    'capability' => 'manage_options',
    'redirect'   => false,
  ]);

  acf_add_options_sub_page([
  'page_title'  => 'Cursori',
  'menu_title'  => 'Cursori',
  'parent_slug' => 'moma-theme-settings',
  'menu_slug'   => 'moma-cursori',
]);

  acf_add_options_sub_page([
    'page_title'  => 'Footer',
    'menu_title'  => 'Footer',
    'parent_slug' => 'moma-theme-settings',
    'menu_slug'   => 'moma-footer',
  ]);

  // Aggiungi dove già fai acf_add_options_page / acf_add_options_sub_page
acf_add_options_sub_page([
  'page_title'  => 'Pagina Servizi',
  'menu_title'  => 'Pagina Servizi',
  'menu_slug'   => 'moma-servizi-page',
  'parent_slug' => 'moma-theme-settings', // usa il parent slug reale che hai già
]);



acf_add_options_sub_page([
  'page_title'  => 'Pagina Case Studies',
  'menu_title'  => 'Pagina Case Studies',
  'menu_slug'   => 'moma-case-studies-page',
  'parent_slug' => 'moma-theme-settings',
]);

});