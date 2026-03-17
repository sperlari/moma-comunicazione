<?php
if (!defined('ABSPATH')) exit;

add_action('acf/init', function () {
  if (!function_exists('acf_add_local_field_group')) return;

  /**
   * OPTIONS: Pagina Servizi
   * Menu slug: moma-servizi-page
   */
  acf_add_local_field_group([
    'key' => 'group_moma_servizi_page',
    'title' => 'Pagina Servizi',
    'fields' => [
      [
        'key' => 'field_srv_hero_tab',
        'label' => 'Hero',
        'type' => 'tab',
        'placement' => 'top',
      ],
      [
        'key' => 'field_srv_hero_type',
        'label' => 'Tipo contenuto hero',
        'name' => 'srv_hero_type',
        'type' => 'radio',
        'layout' => 'horizontal',
        'choices' => [
          'video' => 'Video',
          'image' => 'Immagine',
        ],
        'default_value' => 'video',
        'instructions' => "Consiglio: Video per un impatto più forte. In caso di problemi di peso o compatibilità, usa l'immagine.",
      ],
      [
        'key' => 'field_srv_hero_video_webm',
        'label' => 'Video WebM (consigliato)',
        'name' => 'srv_hero_video_webm',
        'type' => 'file',
        'return_format' => 'array',
        'mime_types' => 'webm',
        'conditional_logic' => [[[
          'field' => 'field_srv_hero_type',
          'operator' => '==',
          'value' => 'video',
        ]]],
        'instructions' => "Formato consigliato per il web. Indicazioni: 1920x1080, 25/30fps, durata 5–12s, peso indicativo 3–12MB. Autoplay/muted/loop: evitare audio.",
      ],
      [
        'key' => 'field_srv_hero_video_mp4',
        'label' => 'Video MP4 (fallback)',
        'name' => 'srv_hero_video_mp4',
        'type' => 'file',
        'return_format' => 'array',
        'mime_types' => 'mp4',
        'conditional_logic' => [[[
          'field' => 'field_srv_hero_type',
          'operator' => '==',
          'value' => 'video',
        ]]],
        'instructions' => "Fallback per browser che non supportano WebM. Stesse indicazioni di peso/durata del WebM.",
      ],
      [
        'key' => 'field_srv_hero_video_poster',
        'label' => 'Poster video (immagine)',
        'name' => 'srv_hero_video_poster',
        'type' => 'image',
        'return_format' => 'array',
        'preview_size' => 'medium',
        'conditional_logic' => [[[
          'field' => 'field_srv_hero_type',
          'operator' => '==',
          'value' => 'video',
        ]]],
        'instructions' => "Immagine mostrata in caricamento. Consiglio: 1920x1080, JPG compressa.",
      ],
      [
        'key' => 'field_srv_hero_image',
        'label' => 'Immagine hero',
        'name' => 'srv_hero_image',
        'type' => 'image',
        'return_format' => 'array',
        'preview_size' => 'large',
        'conditional_logic' => [[[
          'field' => 'field_srv_hero_type',
          'operator' => '==',
          'value' => 'image',
        ]]],
        'instructions' => "Usa un'immagine leggera e di qualità. Consiglio: 1920x1080, JPG/WEBP, < 600KB se possibile.",
      ],
      [
        'key' => 'field_srv_hero_cursor_enabled',
        'label' => 'Cursore custom hero attivo',
        'name' => 'srv_hero_cursor_enabled',
        'type' => 'true_false',
        'ui' => 1,
        'default_value' => 1,
        'instructions' => 'Mostra il cursore con immagine quando il mouse passa sopra la hero della pagina Servizi.',
      ],
      [
        'key' => 'field_srv_hero_cursor_image',
        'label' => 'Immagine cursore hero',
        'name' => 'srv_hero_cursor_image',
        'type' => 'image',
        'return_format' => 'array',
        'preview_size' => 'medium',
        'conditional_logic' => [[[
          'field' => 'field_srv_hero_cursor_enabled',
          'operator' => '==',
          'value' => '1',
        ]]],
      ],

      [
        'key' => 'field_srv_statement_tab',
        'label' => 'Welcome statement',
        'type' => 'tab',
        'placement' => 'top',
      ],
      [
        'key' => 'field_srv_welcome_title',
        'label' => 'Titolo',
        'name' => 'srv_welcome_title',
        'type' => 'text',
        'default_value' => 'Welcome statement pagina servizi',
      ],
      [
        'key' => 'field_srv_welcome_text',
        'label' => 'Testo breve sotto i pulsanti',
        'name' => 'srv_welcome_text',
        'type' => 'textarea',
        'new_lines' => 'br',
        'instructions' => "Testo introduttivo breve. Evita muri di testo.",
      ],

      [
        'key' => 'field_srv_projects_tab',
        'label' => 'Sezione: i nostri progetti',
        'type' => 'tab',
        'placement' => 'top',
      ],
      [
        'key' => 'field_srv_projects_title',
        'label' => 'Titolo sezione',
        'name' => 'srv_projects_title',
        'type' => 'text',
        'default_value' => 'i nostri progetti',
      ],
      [
        'key' => 'field_srv_projects_mode',
        'label' => 'Modalità selezione case studies',
        'name' => 'srv_projects_mode',
        'type' => 'radio',
        'layout' => 'horizontal',
        'choices' => [
          'auto' => 'Automatico (random con priorità in evidenza)',
          'manual' => 'Manuale (seleziona 2)',
        ],
        'default_value' => 'auto',
      ],
      [
        'key' => 'field_srv_projects_manual',
        'label' => 'Seleziona case studies (max 2)',
        'name' => 'srv_projects_manual',
        'type' => 'relationship',
        'post_type' => ['case_study'],
        'filters' => ['search'],
        'max' => 2,
        'return_format' => 'id',
        'conditional_logic' => [[[
          'field' => 'field_srv_projects_mode',
          'operator' => '==',
          'value' => 'manual',
        ]]],
      ],
      [
        'key' => 'field_srv_projects_cursor_enabled',
        'label' => 'Cursore custom immagini progetti attivo',
        'name' => 'srv_projects_cursor_enabled',
        'type' => 'true_false',
        'ui' => 1,
        'default_value' => 1,
        'instructions' => 'Mostra il cursore con immagine solo sopra le immagini della sezione I nostri progetti.',
      ],
      [
        'key' => 'field_srv_projects_cursor_image',
        'label' => 'Immagine cursore progetti',
        'name' => 'srv_projects_cursor_image',
        'type' => 'image',
        'return_format' => 'array',
        'preview_size' => 'medium',
        'conditional_logic' => [[[
          'field' => 'field_srv_projects_cursor_enabled',
          'operator' => '==',
          'value' => '1',
        ]]],
      ],

      [
        'key' => 'field_srv_banner_tab',
        'label' => 'Banner Contattaci',
        'type' => 'tab',
        'placement' => 'top',
      ],
      [
        'key' => 'field_srv_banner_title',
        'label' => 'Titolo banner',
        'name' => 'srv_banner_title',
        'type' => 'text',
        'default_value' => 'Contattaci',
      ],
      [
        'key' => 'field_srv_banner_text',
        'label' => 'Testo banner',
        'name' => 'srv_banner_text',
        'type' => 'textarea',
        'new_lines' => 'br',
      ],
      [
        'key' => 'field_srv_banner_btn_label',
        'label' => 'Testo pulsante',
        'name' => 'srv_banner_btn_label',
        'type' => 'text',
        'default_value' => 'vai a contatti',
      ],
      [
        'key' => 'field_srv_banner_btn_url',
        'label' => 'Link pulsante',
        'name' => 'srv_banner_btn_url',
        'type' => 'url',
        'default_value' => home_url('/contatti'),
      ],
      [
        'key' => 'field_srv_banner_cursor_enabled',
        'label' => 'Cursore custom banner attivo',
        'name' => 'srv_banner_cursor_enabled',
        'type' => 'true_false',
        'ui' => 1,
        'default_value' => 1,
        'instructions' => 'Mostra il cursore con immagine sopra il banner Contattaci (escluso il pulsante).',
      ],
      [
        'key' => 'field_srv_banner_cursor_image',
        'label' => 'Immagine cursore banner',
        'name' => 'srv_banner_cursor_image',
        'type' => 'image',
        'return_format' => 'array',
        'preview_size' => 'medium',
        'conditional_logic' => [[[
          'field' => 'field_srv_banner_cursor_enabled',
          'operator' => '==',
          'value' => '1',
        ]]],
      ],
    ],
    'location' => [[[
      'param' => 'options_page',
      'operator' => '==',
      'value' => 'moma-servizi-page',
    ]]],
  ]);

  /**
   * CPT: Servizio – campi slider Pagina Servizi
   */
  acf_add_local_field_group([
    'key' => 'group_moma_servizio_page_fields',
    'title' => 'Servizio – Pagina Servizi',
    'fields' => [
      [
        'key' => 'field_srv_page_slider_text',
        'label' => 'Testo nello slider (Pagina Servizi)',
        'name' => 'srv_page_slider_text',
        'type' => 'wysiwyg',
        'media_upload' => 0,
        'toolbar' => 'basic',
        'instructions' =>
          "Testo mostrato nello slider della pagina Servizi.

Per attivare le card glossario puoi usare due modalità:
1) Metodo consigliato: seleziona una parola o frase e applica il grassetto.
2) Metodo avanzato: usa il token manuale {{chiave|testo visibile}}.
Esempio: {{punto-fermo|punto fermo}}",
      ],
      [
        'key' => 'field_srv_page_slider_image',
        'label' => 'Immagine slider (Pagina Servizi)',
        'name' => 'srv_page_slider_image',
        'type' => 'image',
        'return_format' => 'array',
        'preview_size' => 'medium',
        'instructions' => "Opzionale. Se vuota, usa l'immagine in evidenza del servizio.",
      ],
    ],
    'location' => [[[
      'param' => 'post_type',
      'operator' => '==',
      'value' => 'servizio',
    ]]],
  ]);
});
