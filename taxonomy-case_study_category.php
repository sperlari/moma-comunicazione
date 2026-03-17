<?php
if (!defined('ABSPATH')) exit;

get_header();

$current_term = get_queried_object();
if (!($current_term instanceof WP_Term)) {
  get_footer();
  return;
}

$all_posts = moma_case_study_collect_posts([
  'tax_query' => [[
    'taxonomy' => 'case_study_category',
    'field'    => 'term_id',
    'terms'    => [(int) $current_term->term_id],
  ]],
]);

$statement_post = moma_case_study_pick_statement_post($all_posts);
if ($statement_post instanceof WP_Post) {
  $statement_terms = get_the_terms($statement_post->ID, 'case_study_category');
  $matches_current = false;
  if (!is_wp_error($statement_terms) && !empty($statement_terms)) {
    foreach ($statement_terms as $statement_term) {
      if ((int) $statement_term->term_id === (int) $current_term->term_id) {
        $matches_current = true;
        break;
      }
    }
  }
  if (!$matches_current) {
    $statement_post = !empty($all_posts[0]) ? $all_posts[0] : null;
  }
}

$statement_id = $statement_post instanceof WP_Post ? (int) $statement_post->ID : 0;
$grid_posts = array_values(array_filter($all_posts, static function ($post_obj) use ($statement_id) {
  return ($post_obj instanceof WP_Post) && (int) $post_obj->ID !== $statement_id;
}));
$filter_terms = moma_case_study_collect_filter_terms();
$cards_cursor_attrs = moma_case_study_cursor_attrs_from_fields('csa_cards_cursor_enabled', 'csa_cards_cursor_image', 'option');

get_template_part('template-parts/site/archive/case-studies/hero');
get_template_part('template-parts/site/archive/case-studies/statement', null, [
  'statement_post'    => $statement_post,
  'card_cursor_attrs' => $cards_cursor_attrs,
]);
get_template_part('template-parts/site/archive/case-studies/filters', null, [
  'terms'        => $filter_terms,
  'mode'         => 'links',
  'active_slug'  => $current_term->slug,
  'all_label'    => 'Tutti',
  'all_url'      => get_post_type_archive_link('case_study'),
]);
get_template_part('template-parts/site/archive/case-studies/grid', null, [
  'posts'             => $grid_posts,
  'card_cursor_attrs' => $cards_cursor_attrs,
]);
get_template_part('template-parts/site/archive/case-studies/services-banner');

get_footer();
