<?php
if (!defined('ABSPATH')) exit;

$args = wp_parse_args($args ?? [], [
  'post' => null,
  'card_cursor_attrs' => '',
  'featured' => false,
  'extra_class' => '',
  'reveal_attrs' => '',
  'in_grid' => true,
]);

$post_obj = $args['post'];
if (!$post_obj instanceof WP_Post) return;

$pid = (int) $post_obj->ID;
$thumb_id = (int) get_post_thumbnail_id($pid);
if (!$thumb_id) return;

$title = get_the_title($pid);
$subtitle = function_exists('get_field') ? (string) get_field('cs_subtitle', $pid) : '';
if (!$subtitle) {
  $subtitle = has_excerpt($pid) ? get_the_excerpt($pid) : '';
}

$terms = get_the_terms($pid, 'case_study_category');
$chip_terms = [];
$term_slugs = [];
if (!is_wp_error($terms) && !empty($terms)) {
  $chip_terms = array_slice($terms, 0, 3);
  $term_slugs = array_values(array_unique(array_map(static function ($term) {
    return sanitize_title($term->slug ?? '');
  }, $terms)));
}

$classes = [
  'moma-project-card',
  'moma-case-archive-card',
  $args['featured'] ? 'moma-case-archive-card--featured' : '',
  (string) $args['extra_class'],
];
$classes = implode(' ', array_filter($classes));
?>
<article
  class="<?php echo esc_attr($classes); ?>"
  <?php if (!empty($args['in_grid'])): ?>data-case-card data-case-terms="<?php echo esc_attr(implode(',', $term_slugs)); ?>"<?php endif; ?>
  <?php echo $args['reveal_attrs']; ?>
>
  <a class="moma-project-card__media moma-case-archive-card__media block" href="<?php echo esc_url(get_permalink($pid)); ?>" aria-label="<?php echo esc_attr($title); ?>"<?php echo (string) $args['card_cursor_attrs']; ?>>
    <?php
      echo wp_get_attachment_image(
        $thumb_id,
        'large',
        false,
        [
          'class' => 'moma-project-card__img moma-case-archive-card__img w-full h-auto block object-cover',
          'loading' => $args['featured'] ? 'eager' : 'lazy',
          'decoding' => 'async',
          'alt' => $title,
        ]
      );
    ?>
  </a>

  <div class="moma-project-card__body moma-case-archive-card__body">
    <div class="flex items-start justify-between gap-4">
      <div class="min-w-0">
        <h3 class="moma-case-archive-card__title m-0 font-semibold text-[#18085a] leading-snug tracking-[-0.01em]">
          <a class="text-inherit no-underline" href="<?php echo esc_url(get_permalink($pid)); ?>"><?php echo esc_html($title); ?></a>
        </h3>
        <?php if ($subtitle): ?>
          <p class="moma-case-archive-card__subtitle m-0 mt-2 text-[#18085a]/70 leading-relaxed"><?php echo esc_html($subtitle); ?></p>
        <?php endif; ?>
      </div>

      <a class="moma-project-card__cta moma-servizi-card__cta shrink-0" href="<?php echo esc_url(get_permalink($pid)); ?>" aria-label="<?php echo esc_attr(sprintf('Vai al case study %s', $title)); ?>">
        <svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false"><path d="M7 17L17 7" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" /><path d="M9 7h8v8" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
      </a>
    </div>

    <?php if (!empty($chip_terms)): ?>
      <ul class="flex flex-wrap gap-2 mt-4 m-0 p-0 list-none" aria-label="Categorie case study">
        <?php foreach ($chip_terms as $index => $term): ?>
          <li><?php echo moma_case_study_render_term_chip($term, ['dot' => $index === 0]); ?></li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </div>
</article>
