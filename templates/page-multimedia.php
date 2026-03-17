<?php
/**
 * Template Name: Pagina Multimedia
 */
if (!defined('ABSPATH')) exit;

require_once get_theme_file_path('/inc/helpers/case-study-single.php');

get_header();

if (have_posts()) {
  the_post();
}
$page_id = get_the_ID();

$hero_type   = function_exists('get_field') ? (get_field('mmp_hero_type', $page_id) ?: 'image') : 'image';
$hero_image  = function_exists('get_field') ? get_field('mmp_hero_image', $page_id) : null;
$hero_webm   = function_exists('get_field') ? get_field('mmp_hero_video_webm', $page_id) : null;
$hero_mp4    = function_exists('get_field') ? get_field('mmp_hero_video_mp4', $page_id) : null;
$hero_poster = function_exists('get_field') ? get_field('mmp_hero_video_poster', $page_id) : null;

$intro_title = function_exists('get_field') ? trim((string) get_field('mmp_intro_title', $page_id)) : '';
if ($intro_title === '') {
  $intro_title = get_the_title($page_id);
}
$intro_text  = function_exists('get_field') ? trim((string) get_field('mmp_intro_text', $page_id)) : '';
$per_page    = function_exists('get_field') ? max(1, (int) get_field('mmp_posts_per_page', $page_id)) : 6;
if ($per_page < 1) $per_page = 6;

$show_banner = function_exists('get_field') ? (bool) get_field('mmp_banner_show', $page_id) : true;
$banner_title = function_exists('get_field') ? trim((string) get_field('mmp_banner_title', $page_id)) : 'Scopri i nostri lavori';
$banner_text  = function_exists('get_field') ? trim((string) get_field('mmp_banner_text', $page_id)) : '';
$banner_label = function_exists('get_field') ? trim((string) get_field('mmp_banner_button_label', $page_id)) : 'vai a contatti';
$banner_url   = function_exists('get_field') ? trim((string) get_field('mmp_banner_button_url', $page_id)) : home_url('/contatti');

$hero_cursor_attrs  = moma_case_study_cursor_attrs_from_fields('mmp_hero_cursor_enabled', 'mmp_hero_cursor_image', $page_id);
$media_cursor_attrs = moma_case_study_cursor_attrs_from_fields('mmp_media_cursor_enabled', 'mmp_media_cursor_image', $page_id);

$all_posts = moma_case_study_collect_posts();
$video_items = [];
foreach ($all_posts as $post_obj) {
  $pid = (int) $post_obj->ID;
  $feature_video = function_exists('get_field') ? (array) get_field('cs_feature_video', $pid) : [];
  $video_media = moma_cs_single_normalize_media(array_merge(['media_type' => 'video'], $feature_video), [
    'image_size' => 'moma_cs_detail_video',
    'alt'        => get_the_title($pid),
  ]);
  if (empty($video_media)) continue;

  $thumb_id = (int) get_post_thumbnail_id($pid);
  if (!$thumb_id) continue;

  $terms = get_the_terms($pid, 'case_study_category');
  $chip_terms = (!is_wp_error($terms) && !empty($terms)) ? array_slice($terms, 0, 3) : [];
  $subtitle = function_exists('get_field') ? trim((string) get_field('cs_subtitle', $pid)) : '';
  if ($subtitle === '' && has_excerpt($pid)) {
    $subtitle = get_the_excerpt($pid);
  }

  $video_items[] = [
    'post'       => $post_obj,
    'thumb_id'   => $thumb_id,
    'subtitle'   => $subtitle,
    'chip_terms' => $chip_terms,
    'video'      => $video_media,
  ];
}

$term_links = !empty($video_items) ? moma_case_study_collect_filter_terms(array_map(static fn($item) => $item['post'], $video_items)) : [];
$term_links = array_slice($term_links, 0, 6);

$current_page = isset($_GET['csmedia']) ? max(1, (int) $_GET['csmedia']) : 1;
$total_items = count($video_items);
$total_pages = max(1, (int) ceil($total_items / $per_page));
$current_page = min($current_page, $total_pages);
$offset = ($current_page - 1) * $per_page;
$paged_items = array_slice($video_items, $offset, $per_page);

$base_url = get_permalink($page_id);
$pagination_links = '';
if ($total_pages > 1) {
  $pagination_links = paginate_links([
    'base'      => esc_url_raw(add_query_arg('csmedia', '%#%', $base_url)),
    'format'    => '',
    'current'   => $current_page,
    'total'     => $total_pages,
    'type'      => 'list',
    'prev_next' => false,
    'end_size'  => 1,
    'mid_size'  => 1,
  ]);
}

$poster_url = (is_array($hero_poster) && !empty($hero_poster['url'])) ? $hero_poster['url'] : '';
?>

<main class="moma-multimedia-page bg-[#fbf7f1] text-[#18085a]" data-moma-multimedia-page="1">
  <section class="moma-multimedia-page__hero relative w-full min-h-screen overflow-hidden"<?php echo $hero_cursor_attrs; ?>>
    <?php if ($hero_type === 'video' && ((is_array($hero_webm) && !empty($hero_webm['url'])) || (is_array($hero_mp4) && !empty($hero_mp4['url'])))): ?>
      <video class="absolute inset-0 h-full w-full object-cover" autoplay muted loop playsinline preload="metadata"<?php echo $poster_url ? ' poster="' . esc_url($poster_url) . '"' : ''; ?>>
        <?php if (is_array($hero_webm) && !empty($hero_webm['url'])): ?>
          <source src="<?php echo esc_url($hero_webm['url']); ?>" type="video/webm">
        <?php endif; ?>
        <?php if (is_array($hero_mp4) && !empty($hero_mp4['url'])): ?>
          <source src="<?php echo esc_url($hero_mp4['url']); ?>" type="video/mp4">
        <?php endif; ?>
      </video>
    <?php elseif (is_array($hero_image) && !empty($hero_image['url'])): ?>
      <img class="absolute inset-0 h-full w-full object-cover" src="<?php echo esc_url($hero_image['url']); ?>" alt="<?php echo esc_attr($hero_image['alt'] ?? ''); ?>" loading="eager" decoding="async">
    <?php endif; ?>
  </section>

  <section class="container mx-auto px-4 py-16 md:py-20 lg:py-24">
    <?php if (!empty($term_links)): ?>
      <ul class="moma-multimedia-page__chips m-0 mb-8 flex list-none flex-wrap gap-2 p-0 justify-center" data-moma-reveal="fade-up" data-reveal-y="20" data-reveal-duration="1.00" data-reveal-delay="0.02" data-reveal-start="top 88%" data-reveal-once="1">
        <?php foreach ($term_links as $index => $term_item): ?>
          <?php if (($term_item['term'] ?? null) instanceof WP_Term): ?>
            <li><?php echo moma_case_study_render_term_chip($term_item['term'], ['dot' => $index === 0]); ?></li>
          <?php endif; ?>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>

    <?php if ($intro_title !== ''): ?>
      <h1 class="moma-multimedia-page__title mx-auto max-w-5xl text-center font-[Fraunces] text-[clamp(2.65rem,5vw,5rem)] leading-[0.96] tracking-[-0.03em]" data-moma-reveal="fade-up" data-reveal-y="28" data-reveal-duration="1.08" data-reveal-delay="0.04" data-reveal-start="top 86%" data-reveal-once="1"><?php echo esc_html($intro_title); ?></h1>
    <?php endif; ?>

    <?php if ($intro_text !== ''): ?>
      <div class="moma-multimedia-page__intro mx-auto mt-5 max-w-3xl text-center text-[#18085a]/75" data-moma-reveal="fade-up" data-reveal-y="24" data-reveal-duration="1.04" data-reveal-delay="0.08" data-reveal-start="top 86%" data-reveal-once="1"><?php echo wpautop(esc_html($intro_text)); ?></div>
    <?php endif; ?>

    <?php if (!empty($paged_items)): ?>
      <div class="moma-multimedia-page__grid mt-12 grid grid-cols-1 gap-x-8 gap-y-12 lg:grid-cols-2">
        <?php foreach ($paged_items as $index => $item):
          $post_obj = $item['post'];
          $pid = (int) $post_obj->ID;
          $video = $item['video'];
          $delay = number_format(0.08 + ($index * 0.06), 2, '.', '');
          ?>
          <article class="moma-project-card moma-multimedia-card relative overflow-visible" data-moma-lightbox-card="1" tabindex="0" role="button" aria-label="<?php echo esc_attr(sprintf('Apri il video del case study %s', get_the_title($pid))); ?>" data-moma-reveal="fade-up" data-reveal-y="30" data-reveal-duration="1.10" data-reveal-delay="<?php echo esc_attr($delay); ?>" data-reveal-start="top 86%" data-reveal-once="1">
            <a class="moma-project-card__media moma-multimedia-card__media block" href="<?php echo esc_url($video['href']); ?>" aria-label="<?php echo esc_attr(sprintf('Apri il video del case study %s', get_the_title($pid))); ?>"<?php echo $media_cursor_attrs; ?><?php echo $video['lightbox_attrs']; ?>>
              <?php echo wp_get_attachment_image($item['thumb_id'], 'large', false, [
                'class'    => 'moma-project-card__img moma-multimedia-card__img block h-auto w-full object-cover',
                'loading'  => ($index < 2 ? 'eager' : 'lazy'),
                'decoding' => 'async',
                'alt'      => get_the_title($pid),
              ]); ?>
            </a>

            <div class="moma-project-card__body moma-multimedia-card__body p-5 md:p-6">
              <div class="flex items-start justify-between gap-4">
                <div class="min-w-0">
                  <h2 class="m-0 text-lg font-semibold leading-snug tracking-[-0.01em] text-[#18085a] md:text-xl"><?php echo esc_html(get_the_title($pid)); ?></h2>
                  <?php if ($item['subtitle']): ?>
                    <p class="m-0 mt-2 text-sm leading-relaxed text-[#18085a]/80"><?php echo esc_html($item['subtitle']); ?></p>
                  <?php endif; ?>
                </div>

                <a class="moma-project-card__cta moma-servizi-card__cta moma-multimedia-card__cta shrink-0" href="<?php echo esc_url($video['href']); ?>" aria-label="<?php echo esc_attr(sprintf('Apri il video del case study %s', get_the_title($pid))); ?>"<?php echo $video['lightbox_attrs']; ?>>
                  <svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false"><path d="M8 6.5v11l9-5.5-9-5.5Z" fill="currentColor" /></svg>
                </a>
              </div>

              <?php if (!empty($item['chip_terms'])): ?>
                <ul class="m-0 mt-4 flex list-none flex-wrap gap-2 p-0" aria-label="Categorie case study">
                  <?php foreach ($item['chip_terms'] as $chip_index => $term): ?>
                    <li><?php echo moma_case_study_render_term_chip($term, ['dot' => $chip_index === 0]); ?></li>
                  <?php endforeach; ?>
                </ul>
              <?php endif; ?>
            </div>
          </article>
        <?php endforeach; ?>
      </div>

      <?php if ($pagination_links): ?>
        <nav class="moma-multimedia-page__pagination mt-10 md:mt-12" aria-label="Paginazione multimedia">
          <?php echo wp_kses_post($pagination_links); ?>
        </nav>
      <?php endif; ?>
    <?php else: ?>
      <div class="moma-multimedia-page__empty mt-12 rounded-[24px] border border-[#18085a]/10 bg-white/60 px-6 py-10 text-center text-[#18085a]/70">
        <p class="m-0">Nessun case study con video principale disponibile.</p>
      </div>
    <?php endif; ?>
  </section>

  <?php if ($show_banner && ($banner_title !== '' || $banner_text !== '' || $banner_url !== '')): ?>
    <section class="py-10 md:py-12">
      <div class="mx-auto max-w-6xl px-4">
        <div class="moma-multimedia-page__banner relative overflow-hidden rounded-[24px] px-6 py-8 text-white md:rounded-[30px] md:px-12 md:py-10" data-moma-reveal="fade-up" data-reveal-y="34" data-reveal-duration="1.16" data-reveal-delay="0.06" data-reveal-start="top 80%" data-reveal-once="1">
          <div class="absolute inset-0 bg-[linear-gradient(90deg,#a85412_0%,#e9770b_55%,#f18810_100%)]"></div>
          <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,rgba(0,0,0,0.22)_0%,rgba(0,0,0,0)_55%)] opacity-80"></div>
          <div class="absolute inset-y-0 left-0 w-1/3 bg-[linear-gradient(90deg,rgba(0,0,0,0.18)_0%,rgba(0,0,0,0)_70%)]"></div>

          <div class="relative flex min-h-[220px] flex-col">
            <?php if ($banner_title !== ''): ?>
              <h2 class="font-[Fraunces] text-[clamp(2.35rem,4.6vw,4.45rem)] leading-[0.96] tracking-[-0.03em]"><?php echo esc_html($banner_title); ?></h2>
            <?php endif; ?>

            <?php if ($banner_text !== ''): ?>
              <p class="mt-4 max-w-2xl text-sm leading-relaxed text-white/90 md:text-base"><?php echo esc_html($banner_text); ?></p>
            <?php endif; ?>

            <?php if ($banner_url !== ''): ?>
              <div class="mt-auto flex justify-end pt-10">
                <a href="<?php echo esc_url($banner_url); ?>" data-cursor-ui="1" class="m-btn m-btn--secondary m-btn--dot">
                  <span class="m-btn__label"><?php echo esc_html($banner_label ?: 'scopri'); ?></span>
                </a>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </section>
  <?php endif; ?>
</main>

<?php
get_footer();
