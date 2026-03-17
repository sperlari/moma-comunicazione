<?php
if (!defined('ABSPATH')) exit;

$args = wp_parse_args($args ?? [], [
  'terms'       => [],
  'mode'        => 'filter',
  'active_slug' => '',
  'all_label'   => 'Tutti',
  'all_url'     => get_post_type_archive_link('case_study'),
]);

$terms = array_values(array_filter((array) $args['terms'], static function ($item) {
  return is_array($item) && !empty($item['slug']) && !empty($item['name']);
}));

if (empty($terms) && $args['mode'] !== 'links') return;
?>
<section class="container mx-auto pb-10 lg:pb-12">
  <div class="moma-case-archive-filters flex flex-wrap gap-3" data-moma-reveal="fade-up" data-reveal-y="24" data-reveal-duration="1.05" data-reveal-delay="0.04" data-reveal-start="top 85%" data-reveal-ease="power3.out" data-reveal-once="1">
    <?php if ($args['mode'] === 'links'): ?>
      <?php
      $all_is_current = ((string) $args['active_slug'] === '');
      ?>
      <a href="<?php echo esc_url((string) $args['all_url']); ?>" class="m-btn m-btn--tag m-btn--sm m-btn--dot moma-case-filter<?php echo $all_is_current ? ' is-active' : ''; ?>"<?php echo $all_is_current ? ' aria-current="page"' : ''; ?>>
        <span class="m-btn__label"><?php echo esc_html((string) $args['all_label']); ?></span>
      </a>

      <?php foreach ($terms as $index => $term_item): ?>
        <?php
        $term = $term_item['term'] ?? null;
        if (!($term instanceof WP_Term)) continue;
        $term_url = moma_case_study_term_url($term);
        if ($term_url === '') continue;
        $is_active = ((string) $args['active_slug'] === (string) $term_item['slug']);
        ?>
        <a href="<?php echo esc_url($term_url); ?>" class="m-btn m-btn--tag m-btn--sm moma-case-filter<?php echo $is_active ? ' is-active' : ''; ?>"<?php echo $is_active ? ' aria-current="page"' : ''; ?>>
          <span class="m-btn__label"><?php echo esc_html($term_item['name']); ?></span>
        </a>
      <?php endforeach; ?>
    <?php else: ?>
      <button type="button" class="m-btn m-btn--tag m-btn--sm m-btn--dot moma-case-filter is-active" data-case-filter="all" aria-pressed="true">
        <span class="m-btn__label">Tutti</span>
      </button>

      <?php foreach ($terms as $index => $term): ?>
        <button type="button" class="m-btn m-btn--tag m-btn--sm<?php echo $index === 0 ? ' m-btn--dot' : ''; ?> moma-case-filter" data-case-filter="<?php echo esc_attr($term['slug']); ?>" aria-pressed="false">
          <span class="m-btn__label"><?php echo esc_html($term['name']); ?></span>
        </button>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</section>
