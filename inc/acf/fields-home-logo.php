<?php
if (!defined('ABSPATH')) exit;

add_action('acf/init', function () {
  if (!function_exists('acf_add_local_field_group')) return;

  acf_add_local_field_group([
    'key' => 'group_moma_home_logo',
    'title' => 'Home - Logo hero',
    'style' => 'default',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'location' => [[[
      'param' => 'page_type',
      'operator' => '==',
      'value' => 'front_page',
    ]]],
    'fields' => [
      [
        'key' => 'field_home_logo_intro',
        'label' => 'Indicazioni',
        'name' => '',
        'type' => 'message',
        'message' => 'Qui puoi sostituire il wordmark testuale della hero con un file SVG. Carica un SVG vettoriale pulito, con viewBox corretto, senza bitmap incorporate e preferibilmente con lettere/path separati se vuoi provare l\'animazione per parti.',
      ],
      [
        'key' => 'field_home_hero_use_svg_logo',
        'label' => 'Usa logo SVG nella hero',
        'name' => 'home_hero_use_svg_logo',
        'type' => 'true_false',
        'ui' => 1,
        'default_value' => 0,
      ],
      [
        'key' => 'field_home_hero_logo_svg',
        'label' => 'File SVG hero',
        'name' => 'home_hero_logo_svg',
        'type' => 'file',
        'return_format' => 'array',
        'mime_types' => 'svg',
        'conditional_logic' => [[[
          'field' => 'field_home_hero_use_svg_logo',
          'operator' => '==',
          'value' => '1',
        ]]],
        'instructions' => 'Formato consigliato: SVG orizzontale, con colori finali già definiti e dimensione originale ampia.',
      ],
      [
        'key' => 'field_home_hero_logo_animation_mode',
        'label' => 'Animazione logo',
        'name' => 'home_hero_logo_animation_mode',
        'type' => 'button_group',
        'choices' => [
          'whole' => 'Intero logo',
          'parts' => 'Parti / lettere',
        ],
        'default_value' => 'whole',
        'layout' => 'horizontal',
        'conditional_logic' => [[[
          'field' => 'field_home_hero_use_svg_logo',
          'operator' => '==',
          'value' => '1',
        ]]],
        'instructions' => 'Usa “Intero logo” come scelta più sicura. “Parti / lettere” funziona bene solo se il file SVG è strutturato in elementi separati.',
      ],
    ],
  ]);
});
