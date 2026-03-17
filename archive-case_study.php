<?php
if (!defined('ABSPATH')) exit;

get_header();

$all_posts = moma_case_study_collect_posts();
$statement_post = moma_case_study_pick_statement_post($all_posts);
$statement_id = $statement_post instanceof WP_Post ? (int) $statement_post->ID : 0;
$grid_posts = array_values(array_filter($all_posts, static function ($post_obj) use ($statement_id) {
  return ($post_obj instanceof WP_Post) && (int) $post_obj->ID !== $statement_id;
}));
$filter_terms = moma_case_study_collect_filter_terms($grid_posts);
$cards_cursor_attrs = moma_case_study_cursor_attrs_from_fields('csa_cards_cursor_enabled', 'csa_cards_cursor_image', 'option');

get_template_part('template-parts/site/archive/case-studies/hero');
get_template_part('template-parts/site/archive/case-studies/statement', null, [
  'statement_post'     => $statement_post,
  'card_cursor_attrs'  => $cards_cursor_attrs,
]);
get_template_part('template-parts/site/archive/case-studies/filters', null, [
  'terms' => $filter_terms,
  'mode'  => 'filter',
]);
get_template_part('template-parts/site/archive/case-studies/grid', null, [
  'posts'              => $grid_posts,
  'card_cursor_attrs'  => $cards_cursor_attrs,
]);
get_template_part('template-parts/site/archive/case-studies/services-banner');

get_footer();
