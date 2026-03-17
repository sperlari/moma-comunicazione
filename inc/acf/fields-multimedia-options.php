<?php
if (!defined('ABSPATH')) exit;

add_action('acf/init', function () {
  if (!function_exists('acf_add_local_field_group')) return;

  acf_add_local_field_group([
    'key' => 'group_moma_multimedia_options',
    'title' => 'Pagina Multimedia - Categorie video',
    'style' => 'default',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'location' => [[[
      'param' => 'options_page',
      'operator' => '==',
      'value' => 'moma-multimedia-page',
    ]]],
    'fields' => [
      [
        'key' => 'field_mmo_intro',
        'label' => 'Indicazioni',
        'name' => '',
        'type' => 'message',
        'message' => 'Gestisci qui le categorie video usate nella pagina Multimedia. Ogni categoria deve avere una label leggibile e uno slug SEO-friendly, ad esempio <code>fashion-luxury</code>. Dopo averla creata qui, potrai assegnarla ai video dentro i singoli case studies.',
      ],
      [
        'key' => 'field_mmo_video_categories',
        'label' => 'Categorie video',
        'name' => 'moma_multimedia_video_categories',
        'type' => 'repeater',
        'layout' => 'table',
        'button_label' => 'Aggiungi categoria video',
        'collapsed' => 'field_mmo_video_category_label',
        'sub_fields' => [
          [
            'key' => 'field_mmo_video_category_label',
            'label' => 'Label',
            'name' => 'label',
            'type' => 'text',
            'required' => 1,
            'wrapper' => ['width' => '45'],
            'instructions' => 'Testo mostrato nei pulsanti della pagina Multimedia.',
          ],
          [
            'key' => 'field_mmo_video_category_slug',
            'label' => 'Slug',
            'name' => 'slug',
            'type' => 'text',
            'required' => 1,
            'wrapper' => ['width' => '35'],
            'instructions' => 'Usa solo minuscole, numeri e trattini. Questo valore finisce nell\'URL.',
          ],
          [
            'key' => 'field_mmo_video_category_note',
            'label' => 'Nota interna',
            'name' => 'note',
            'type' => 'text',
            'required' => 0,
            'wrapper' => ['width' => '20'],
          ],
        ],
      ],
    ],
  ]);
});
