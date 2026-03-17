<?php
if (!defined('ABSPATH')) exit;

/**
 * Footer integrations
 * - HubSpot forms enqueue
 * - Case Study random (con cache ID) + esclusione del corrente in single-case_study
 */

/**
 * Enqueue HubSpot forms script SOLO se configurato in Options.
 */
add_action('wp_enqueue_scripts', function () {
  if (!function_exists('get_field')) return;

  $portal = (string) (get_field('footer_hubspot_portal_id', 'option') ?: '');
  $form   = (string) (get_field('footer_hubspot_form_id', 'option') ?: '');
  if (!$portal || !$form) return;

  wp_enqueue_script(
    'hubspot-forms',
    'https://js.hsforms.net/forms/v2.js',
    [],
    null,
    true
  );
}, 20);

/**
 * Cache degli ID dei case study pubblicati.
 */
function moma_get_case_study_ids_cached(): array {
  $ids = get_transient('moma_case_study_ids');
  if ($ids !== false && is_array($ids)) return $ids;

  $ids = get_posts([
    'post_type'      => 'case_study',
    'post_status'    => 'publish',
    'numberposts'    => -1,
    'fields'         => 'ids',
    'no_found_rows'  => true,
    'orderby'        => 'date',
    'order'          => 'DESC',
  ]);

  set_transient('moma_case_study_ids', $ids, 12 * HOUR_IN_SECONDS);
  return is_array($ids) ? $ids : [];
}

/**
 * Ritorna un WP_Post case_study per il footer:
 * - modalità random (default)
 * - oppure manuale (da opzioni)
 */
function moma_get_footer_case_study(int $exclude_id = 0): ?WP_Post {
  $mode = function_exists('get_field') ? (string) (get_field('footer_case_study_mode', 'option') ?: 'random') : 'random';
  if ($mode === 'off') return null;

  if ($mode === 'manual' && function_exists('get_field')) {
    $manual_id = (int) (get_field('footer_case_study_manual', 'option') ?: 0);
    if ($manual_id && $manual_id !== $exclude_id) {
      $p = get_post($manual_id);
      return ($p instanceof WP_Post) ? $p : null;
    }
    // se manual = corrente, nascondi (coerente con richiesta)
    if ($manual_id && $manual_id === $exclude_id) return null;
  }

  $ids = moma_get_case_study_ids_cached();
  if (!$ids) return null;

  if ($exclude_id) {
    $ids = array_values(array_diff($ids, [$exclude_id]));
    if (!$ids) return null;
  }

  $random_id = $ids[ wp_rand(0, count($ids) - 1) ];
  $post = get_post($random_id);
  return ($post instanceof WP_Post) ? $post : null;
}

/**
 * Flush cache quando cambia un case study.
 */
add_action('save_post_case_study', function () {
  delete_transient('moma_case_study_ids');
});

add_action('trashed_post', function ($post_id) {
  if (get_post_type($post_id) === 'case_study') {
    delete_transient('moma_case_study_ids');
  }
});
