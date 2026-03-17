<?php
if (!defined('ABSPATH')) exit;

$args = wp_parse_args($args ?? [], [
  'post_type' => 'case_study',
  'posts'     => null,
  'limit'     => 8,
  'eyebrow'   => '',
  'title'     => '',
  'title_class' => '',
  'title_typewriter' => false,
  'colors'    => ['#ccc0b1', '#f38c12'],
  'cursor_enabled' => false,
  'cursor_url' => '',
]);

$post_type = (string) $args['post_type'];
if (!post_type_exists($post_type)) return;

$limit  = max(1, (int) $args['limit']);
$colors = array_values(array_filter((array) $args['colors']));
if (!$colors) $colors = ['#ccc0b1', '#f38c12'];

/** Featured prima, poi latest (come hero) */

$posts = [];

// Se arrivano post manuali, usali e salta featured/latest
if (!empty($args['posts']) && is_array($args['posts'])) {
  $posts = array_values(array_filter($args['posts'], fn($p) => $p instanceof WP_Post));
}

if (!$posts) {
  $featured = get_posts([
    'post_type'      => $post_type,
    'post_status'    => 'publish',
    'posts_per_page' => $limit,
    'meta_key'       => 'cs_featured',
    'meta_value'     => '1',
    'orderby'        => 'date',
    'order'          => 'DESC',
    'meta_query'     => [
      [ 'key' => '_thumbnail_id', 'compare' => 'EXISTS' ],
    ],
  ]);

  $featured_ids = array_map(fn($p) => (int)$p->ID, $featured);

  $remaining = $limit - count($featured);
  $latest = [];
  if ($remaining > 0) {
    $latest = get_posts([
      'post_type'      => $post_type,
      'post_status'    => 'publish',
      'posts_per_page' => $remaining,
      'post__not_in'   => $featured_ids,
      'orderby'        => 'date',
      'order'          => 'DESC',
      'meta_query'     => [
        [ 'key' => '_thumbnail_id', 'compare' => 'EXISTS' ],
      ],
    ]);
  }

  $posts = array_merge($featured, $latest);
}
if (!$posts) return;

$title_raw  = trim((string) $args['title']);
$has_dot    = ($title_raw !== '' && substr($title_raw, -1) === '.');
$title_base = $has_dot ? trim(substr($title_raw, 0, -1)) : $title_raw;

$normalize_chips = function ($raw) {
  if (!$raw) return [];
  if (is_array($raw)) {
    $out = [];
    foreach ($raw as $v) {
      if (is_array($v)) $v = $v['label'] ?? $v['text'] ?? $v['value'] ?? reset($v);
      $v = trim(wp_strip_all_tags((string) $v));
      if ($v !== '') $out[] = $v;
    }
    return array_values(array_unique($out));
  }
  $raw = trim(wp_strip_all_tags((string) $raw));
  if ($raw === '') return [];
  $parts = preg_split('/[,;|]/', $raw);
  $parts = array_filter(array_map('trim', $parts));
  return array_values(array_unique($parts));
};

$total = count($posts);

$cursor_attrs = '';
if (!empty($args['cursor_enabled']) && !empty($args['cursor_url'])) {
  $cursor_attrs = sprintf(' data-cursor-scope="1" data-cursor-img="%s"', esc_url((string) $args['cursor_url']));
}
?>
<section class="moma-case-studies" data-moma-case-studies>
  <div class="container moma-case-studies__heading">
    <?php if (!empty($args['eyebrow'])): ?>
      <p class="moma-case-studies__eyebrow"><?php echo esc_html($args['eyebrow']); ?></p>
    <?php endif; ?>

    <?php if ($title_base !== ''): ?>
      <h2
        class="moma-case-studies__title <?php echo esc_attr((string) $args['title_class']); ?> <?php echo !empty($args['title_typewriter']) ? 'moma-typewriter' : ''; ?>"
        <?php echo !empty($args['title_typewriter']) ? 'data-moma-typewriter' : ''; ?>
      >
        <span><?php echo esc_html($title_base); ?></span><?php if ($has_dot): ?><span class="moma-case-studies__dot" aria-hidden="true">.</span><?php endif; ?>
      </h2>
    <?php endif; ?>
  </div>

  <div class="moma-case-studies__component">
    <div class="case-studies">
      <?php foreach ($posts as $i => $p):
        $pid = (int) $p->ID;

        $bg = $colors[$i % count($colors)];

        $subtitle = '';
        if (function_exists('get_field')) {
          $subtitle = (string) get_field('cs_subtitle', $pid);
        }
        if (!$subtitle) {
          $subtitle = has_excerpt($pid) ? get_the_excerpt($pid) : '';
        }

        // Categorie (tag button) -> link alla pagina della categoria
        $chip_terms = [];
        $terms = get_the_terms($pid, 'case_study_category');
        if (!is_wp_error($terms) && !empty($terms)) {
          $chip_terms = array_slice($terms, 0, 3);
        }

        $thumb_id = get_post_thumbnail_id($pid);
        if (!$thumb_id) continue;

        // la successiva deve stare sopra la precedente
        $z = 1000 + $i;
      ?>
        <div class="case-studies_item" style="<?php echo esc_attr("--cs-bg: {$bg}; --cs-z: {$z};"); ?>">
          <div class="case-card_sticky-frame">
            <div class="case-card_sticky-trigger"></div>

            <div class="case-card_sticky-element" data-cs-sticky>
              <div class="moma-case-card" style="<?php echo esc_attr("background-color: {$bg};"); ?>">
                <a class="moma-case-card__link" href="<?php echo esc_url(get_permalink($pid)); ?>" aria-label="<?php echo esc_attr(get_the_title($pid)); ?>"></a>
                <div class="moma-case-card__inner">
                  <div class="moma-case-card__top">
                    <div class="moma-case-card__meta">
                      <h3 class="moma-case-card__client"><?php echo esc_html(get_the_title($pid)); ?></h3>
                      <?php if ($subtitle): ?>
                        <p class="moma-case-card__subtitle"><?php echo esc_html($subtitle); ?></p>
                      <?php endif; ?>
                    </div>

                    <?php if (!empty($chip_terms)): ?>
                      <ul class="moma-case-card__chips" aria-label="Categorie case study">
                        <?php foreach ($chip_terms as $t):
                          $term_url = get_term_link($t);
                          if (is_wp_error($term_url)) continue;
                        ?>
                          <li>
                            <a class="m-btn m-btn--tag m-btn--dot m-btn--sm" href="<?php echo esc_url($term_url); ?>">
                              <span class="m-btn__label"><?php echo esc_html($t->name); ?></span>
                            </a>
                          </li>
                        <?php endforeach; ?>
                      </ul>
                    <?php endif; ?>
                  </div>

                  <?php if ($thumb_id): ?>
                    <div class="moma-case-card__media"<?php echo $cursor_attrs; ?>>
                      <a class="moma-case-card__media-link" href="<?php echo esc_url(get_permalink($pid)); ?>" aria-hidden="true" tabindex="-1"></a>
                      <?php
                        echo wp_get_attachment_image(
                          $thumb_id,
                          'large',
                          false,
                          [
                            'class'    => 'moma-case-card__img',
                            'loading'  => ($i < 2 ? 'eager' : 'lazy'),
                            'decoding' => 'async',
                            'alt'      => get_the_title($pid),
                          ]
                        );
                      ?>
                    </div>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>