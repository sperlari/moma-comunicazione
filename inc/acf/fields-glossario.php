<?php
if (!defined('ABSPATH')) exit;

add_action('acf/init', function () {
  if (!function_exists('acf_add_local_field_group')) return;

  /**
   * CPT: Glossario
   */
  acf_add_local_field_group([
    'key' => 'group_moma_glossary_cpt',
    'title' => 'Glossario – Contenuti termine',
    'fields' => [
      [
        'key' => 'field_glossary_help',
        'label' => 'Come si usa il glossario',
        'name' => 'glossary_help',
        'type' => 'message',
        'message' => '<strong>Inserimento termini nel testo</strong><br>Per attivare un termine del glossario nei campi testo hai due modalità.<br><br><strong>Metodo consigliato:</strong> scrivi il testo normalmente, seleziona la parola o la frase desiderata e applica il <strong>grassetto</strong>. Se esiste un termine del Glossario con chiave o titolo corrispondente, nel frontend quella parola o frase diventerà interattiva.<br><br><strong>Metodo avanzato:</strong> puoi inserire manualmente il token nel formato <code>{{chiave|testo visibile}}</code>.<br>Esempio: <code>{{punto-fermo|punto fermo}}</code><br><br><strong>Chiave (slug)</strong>: usa solo lettere minuscole, numeri e trattini. Se non compili la chiave qui sotto, verrà usato automaticamente lo slug del termine.',
        'esc_html' => 0,
      ],
      [
        'key' => 'field_glossary_key',
        'label' => 'Chiave (slug)',
        'name' => 'glossary_key',
        'type' => 'text',
        'instructions' => "Facoltativa. Se vuota, viene usato lo slug del termine. Usa solo lettere minuscole, numeri e trattini. Esempio: punto-fermo.",
      ],
      [
        'key' => 'field_glossary_body',
        'label' => 'Testo card',
        'name' => 'body',
        'type' => 'wysiwyg',
        'toolbar' => 'basic',
        'media_upload' => 0,
      ],
      [
        'key' => 'field_glossary_btn1_link',
        'label' => 'Pulsante 1',
        'name' => 'btn1_link',
        'type' => 'link',
        'return_format' => 'array',
        'instructions' => 'Di default viene usato "Contattaci" con link a /contatti. Qui puoi selezionare una pagina interna oppure inserire un URL esterno. Puoi anche scegliere se aprire in una nuova scheda.',
      ],
      [
        'key' => 'field_glossary_btn2_link',
        'label' => 'Pulsante 2',
        'name' => 'btn2_link',
        'type' => 'link',
        'return_format' => 'array',
        'instructions' => 'Di default viene usato "Case studies" con link a /case-studies. Qui puoi selezionare una pagina interna oppure inserire un URL esterno. Puoi anche scegliere se aprire in una nuova scheda.',
      ],
    ],
    'location' => [[[
      'param' => 'post_type',
      'operator' => '==',
      'value' => 'glossario',
    ]]],
  ]);

  /**
   * OVERRIDE: pulsanti glossario per singolo contenuto
   */
  $override_fields = [
    [
      'key' => 'field_glossary_override_term_key',
      'label' => 'Termine (chiave)',
      'name' => 'term_key',
      'type' => 'text',
      'instructions' => "Inserisci la stessa chiave del glossario. Esempio: punto-fermo",
      'required' => 1,
    ],
    [
      'key' => 'field_glossary_override_btn1_link',
      'label' => 'Pulsante 1 (override)',
      'name' => 'btn1_link',
      'type' => 'link',
      'return_format' => 'array',
      'instructions' => 'Se compilato, sostituisce il pulsante 1 di default per questo contenuto.',
    ],
    [
      'key' => 'field_glossary_override_btn2_link',
      'label' => 'Pulsante 2 (override)',
      'name' => 'btn2_link',
      'type' => 'link',
      'return_format' => 'array',
      'instructions' => 'Se compilato, sostituisce il pulsante 2 di default per questo contenuto.',
    ],
  ];

  acf_add_local_field_group([
    'key' => 'group_moma_glossary_overrides_servizio',
    'title' => 'Glossario – Override pulsanti (Servizio)',
    'fields' => [[
      'key' => 'field_glossary_overrides_servizio',
      'label' => 'Override',
      'name' => 'glossary_overrides',
      'type' => 'repeater',
      'layout' => 'row',
      'button_label' => 'Aggiungi override',
      'sub_fields' => $override_fields,
    ]],
    'location' => [[[
      'param' => 'post_type',
      'operator' => '==',
      'value' => 'servizio',
    ]]],
  ]);

  acf_add_local_field_group([
    'key' => 'group_moma_glossary_overrides_case_study',
    'title' => 'Glossario – Override pulsanti (Case study)',
    'fields' => [[
      'key' => 'field_glossary_overrides_case_study',
      'label' => 'Override',
      'name' => 'glossary_overrides',
      'type' => 'repeater',
      'layout' => 'row',
      'button_label' => 'Aggiungi override',
      'sub_fields' => $override_fields,
    ]],
    'location' => [[[
      'param' => 'post_type',
      'operator' => '==',
      'value' => 'case_study',
    ]]],
  ]);
});
