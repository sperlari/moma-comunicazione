<?php
if (!defined('ABSPATH')) exit;
if (!function_exists('get_field')) return;
if (!post_type_exists('case_study')) return;

$title = get_field('srv_projects_title', 'option') ?: 'i nostri progetti';
$mode  = get_field('srv_projects_mode', 'option') ?: 'auto';
$posts = [];

if ($mode === 'manual') {
  $ids = get_field('srv_projects_manual', 'option');
  if (is_array($ids) && $ids) {
    $posts = get_posts([
      'post_type' => 'case_study',
      'post_status' => 'publish',
      'post__in' => array_map('intval', $ids),
      'orderby' => 'post__in',
      'posts_per_page' => 2,
    ]);
  }
}

if (!$posts) {
  $featured = get_posts([
    'post_type'      => 'case_study',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'meta_key'       => 'cs_featured',
    'meta_value'     => 1,
  ]);

  if (count($featured) >= 2) {
    shuffle($featured);
    $posts = array_slice($featured, 0, 2);
  } elseif (count($featured) === 1) {
    $left = $featured[0];
    $right = get_posts([
      'post_type'      => 'case_study',
      'post_status'    => 'publish',
      'posts_per_page' => 1,
      'orderby'        => 'rand',
      'post__not_in'   => [$left->ID],
    ]);
    $posts = [$left];
    if (!empty($right[0])) $posts[] = $right[0];
  } else {
    $posts = get_posts([
      'post_type'      => 'case_study',
      'post_status'    => 'publish',
      'posts_per_page' => 2,
      'orderby'        => 'rand',
    ]);
  }
}

if (count($posts) < 1) return;

$cursor_enabled = (bool) get_field('srv_projects_cursor_enabled', 'option');
$cursor_img = get_field('srv_projects_cursor_image', 'option');
$cursor_url = (is_array($cursor_img) && !empty($cursor_img['url'])) ? $cursor_img['url'] : '';
$cursor_attrs = ($cursor_enabled && $cursor_url)
  ? sprintf(' data-cursor-scope="1" data-cursor-img="%s"', esc_url($cursor_url))
  : '';
?>
<section class="relative z-20 container mx-auto py-16 lg:py-24 moma-servizi-after">
  <div class="flex items-center gap-3" data-moma-reveal="fade-up" data-reveal-y="28" data-reveal-duration="1.05" data-reveal-delay="0.02" data-reveal-start="top 78%" data-reveal-ease="power3.out" data-reveal-once="1">
    <h2 class="font-[Fraunces] text-[#18085a] text-3xl lg:text-4xl"><?php echo esc_html($title); ?></h2>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-8">
    <?php foreach ($posts as $i => $p): ?>
      <?php
        $pid = (int) $p->ID;
        $subtitle = function_exists('get_field') ? (string) get_field('cs_subtitle', $pid) : '';
        if (!$subtitle) $subtitle = has_excerpt($pid) ? get_the_excerpt($pid) : '';

        $chip_terms = [];
        $terms = get_the_terms($pid, 'case_study_category');
        if (!is_wp_error($terms) && !empty($terms)) $chip_terms = array_slice($terms, 0, 3);
        $thumb_id = (int) get_post_thumbnail_id($pid);
      ?>
      <article class="moma-project-card relative overflow-visible" data-moma-reveal="fade-up" data-reveal-y="34" data-reveal-duration="1.10" data-reveal-delay="<?php echo esc_attr(number_format(0.10 + ($i * 0.08), 2, '.', '')); ?>" data-reveal-start="top 82%" data-reveal-ease="power3.out" data-reveal-once="1">
        <?php if ($thumb_id): ?>
          <a class="moma-project-card__media block" href="<?php echo esc_url(get_permalink($pid)); ?>" aria-label="<?php echo esc_attr(get_the_title($pid)); ?>"<?php echo $cursor_attrs; ?>>
            <?php echo wp_get_attachment_image($thumb_id, 'large', false, ['class' => 'moma-project-card__img w-full h-auto block object-cover', 'loading' => ($i < 2 ? 'eager' : 'lazy'), 'decoding' => 'async', 'alt' => get_the_title($pid)]); ?>
          </a>
        <?php endif; ?>

        <div class="moma-project-card__body p-5 md:p-6">
          <div class="flex items-start justify-between gap-4">
            <div class="min-w-0">
              <h3 class="m-0 font-semibold text-[#18085a] text-lg md:text-xl leading-snug tracking-[-0.01em]">
                <a class="text-inherit no-underline" href="<?php echo esc_url(get_permalink($pid)); ?>"><?php echo esc_html(get_the_title($pid)); ?></a>
              </h3>
              <?php if ($subtitle): ?><p class="m-0 mt-2 text-[#18085a]/80 text-sm leading-relaxed"><?php echo esc_html($subtitle); ?></p><?php endif; ?>
            </div>

            <a class="moma-project-card__cta moma-servizi-card__cta shrink-0" href="<?php echo esc_url(get_permalink($pid)); ?>" aria-label="<?php echo esc_attr(sprintf('Vai al case study %s', get_the_title($pid))); ?>">
              <svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false"><path d="M7 17L17 7" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" /><path d="M9 7h8v8" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
            </a>
          </div>

          <?php if (!empty($chip_terms)): ?>
            <ul class="flex flex-wrap gap-2 mt-4 m-0 p-0 list-none" aria-label="Categorie case study">
              <?php foreach ($chip_terms as $t): $term_url = get_term_link($t); if (is_wp_error($term_url)) continue; ?>
                <li><a class="m-btn m-btn--dot m-btn--sm m-btn--tag" href="<?php echo esc_url($term_url); ?>"><span class="m-btn__label"><?php echo esc_html($t->name); ?></span></a></li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
</section>
