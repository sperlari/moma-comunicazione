<?php
if (!defined('ABSPATH')) exit;

add_action('acf/init', function () {
  if (!function_exists('acf_add_local_field_group')) return;

  acf_add_local_field_group([
    'key' => 'group_moma_footer_hubspot_extra',
    'title' => 'Footer - Extra HubSpot',
    'style' => 'default',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'location' => [[[
      'param' => 'options_page',
      'operator' => '==',
      'value' => 'moma-footer',
    ]]],
    'fields' => [
      [
        'key' => 'field_footer_hubspot_region',
        'label' => 'Region',
        'name' => 'footer_hubspot_region',
        'type' => 'text',
        'default_value' => 'eu1',
        'instructions' => 'Valore HubSpot da passare alla create del form. Per il progetto attuale usa <code>eu1</code>.',
      ],
    ],
  ]);

  acf_add_local_field_group([
    'key' => 'group_moma_contact_hubspot_options',
    'title' => 'Pagina Contatti - Form HubSpot',
    'style' => 'default',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'location' => [[[
      'param' => 'options_page',
      'operator' => '==',
      'value' => 'moma-contatti-page',
    ]]],
    'fields' => [
      [
        'key' => 'field_contact_hubspot_intro',
        'label' => 'Indicazioni',
        'name' => '',
        'type' => 'message',
        'message' => 'Configura qui il form HubSpot della pagina Contatti. Il form può essere richiamato nel contenuto pagina con lo shortcode <code>[moma_hubspot_form context="contact"]</code>.',
      ],
      [
        'key' => 'field_contact_hubspot_portal_id',
        'label' => 'Portal ID',
        'name' => 'contact_hubspot_portal_id',
        'type' => 'text',
        'wrapper' => ['width' => '33'],
      ],
      [
        'key' => 'field_contact_hubspot_form_id',
        'label' => 'Form ID',
        'name' => 'contact_hubspot_form_id',
        'type' => 'text',
        'wrapper' => ['width' => '33'],
      ],
      [
        'key' => 'field_contact_hubspot_region',
        'label' => 'Region',
        'name' => 'contact_hubspot_region',
        'type' => 'text',
        'default_value' => 'eu1',
        'wrapper' => ['width' => '34'],
      ],
      [
        'key' => 'field_contact_hubspot_target',
        'label' => 'Target selector',
        'name' => 'contact_hubspot_target',
        'type' => 'text',
        'default_value' => '#hs-contact-form',
        'instructions' => 'Il renderer genera in automatico un contenitore con questo ID quando usi lo shortcode.',
      ],
    ],
  ]);
});
