<?php
if (!defined('ABSPATH')) exit;

/**
 * ACF – campi necessari per:
 * - Home: titolo + testo sotto sezione "I nostri punti di forza"
 * - CPT Servizi: testo dedicato allo slider + (opzionale) immagine override
 */

add_action('acf/init', function () {
  if (!function_exists('acf_add_local_field_group')) return;

  // HOME
  acf_add_local_field_group([
    'key'    => 'group_home_srv_section',
    'title'  => 'Home – Sezione Servizi (Punti di forza)',
    'fields' => [
      [
        'key'   => 'field_home_srv_title',
        'label' => 'Titolo sezione',
        'name'  => 'home_srv_title',
        'type'  => 'text',
        'instructions' => 'Titolo mostrato sopra lo slider. Se termina con un punto, il punto viene evidenziato graficamente.',
        'wrapper' => ['width' => '50'],
        'default_value' => 'i nostri punti di forza.',
      ],
      [
        'key'   => 'field_home_srv_bottom_text',
        'label' => 'Testo sotto lo slider',
        'name'  => 'home_srv_bottom_text',
        'type'  => 'wysiwyg',
        'instructions' => 'Testo descrittivo mostrato sotto lo slider.',
        'wrapper' => ['width' => '100'],
        'tabs'  => 'all',
        'toolbar' => 'basic',
        'media_upload' => 0,
        'default_value' => 'I nostri servizi non sono voci su un elenco, ma punti di svolta nella comunicazione delle aziende. È così che mettiamo ordine, apriamo strade e trasformiamo la complessità in un percorso leggibile.',
      ],
    ],
    'location' => [
      [
        [
          'param'    => 'page_type',
          'operator' => '==',
          'value'    => 'front_page',
        ],
      ],
    ],
    'position' => 'normal',
    'style'    => 'default',
  ]);

  // SERVIZI
  acf_add_local_field_group([
    'key'    => 'group_srv_slider',
    'title'  => 'Servizi – Contenuti slider home',
    'fields' => [
      [
        'key'   => 'field_srv_slider_text',
        'label' => 'Testo nello slider (Home)',
        'name'  => 'srv_slider_text',
        'type'  => 'wysiwyg',
        'tabs'  => 'all',
        'toolbar' => 'basic',
        'media_upload' => 0,
        'instructions' => 'Questo testo appare solo nella card dello slider in Home (non nel contenuto della pagina del servizio). Per i termini glossario usa il menu Glossario nella barra dell\'editor oppure scrivi manualmente {{chiave|testo visibile}}.',
        'wrapper' => ['width' => '100'],
      ],
      [
        'key'   => 'field_srv_slider_image',
        'label' => 'Immagine nello slider (override)',
        'name'  => 'srv_slider_image',
        'type'  => 'image',
        'return_format' => 'id',
        'preview_size'  => 'medium',
        'instructions'  => 'Opzionale. Se vuota, viene usata l’immagine in evidenza del servizio.',
        'wrapper' => ['width' => '50'],
      ],
    ],
    'location' => [
      [
        [
          'param'    => 'post_type',
          'operator' => '==',
          'value'    => 'servizio',
        ],
      ],
    ],
    'position' => 'normal',
    'style'    => 'default',
  ]);
});
