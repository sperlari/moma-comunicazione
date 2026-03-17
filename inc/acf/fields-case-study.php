<?php
if (!defined('ABSPATH')) exit;

add_action('acf/init', function () {
  if (!function_exists('acf_add_local_field_group')) return;

  acf_add_local_field_group([
    'key' => 'group_cs_featured',
    'title' => 'Case Study – Opzioni',
    'fields' => [
      [
        'key' => 'field_cs_featured',
        'label' => 'In evidenza (priorità in Home)',
        'name' => 'cs_featured',
        'type' => 'true_false',
        'ui' => 1,
        'message' => 'Attivo',
        'ui_on_text' => 'ON',
        'ui_off_text' => 'OFF',
        'instructions' => 'Se attivo, questo case study viene mostrato prima degli altri nelle selezioni automatiche (Hero/stack).',
        'default_value' => 0,
      ],
    ],
    'location' => [
      [
        [
          'param' => 'post_type',
          'operator' => '==',
          'value' => 'case_study',
        ],
      ],
    ],
    'position' => 'side',
    'style' => 'default',
  ]);
});
