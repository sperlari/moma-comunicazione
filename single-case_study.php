<?php
if (!defined('ABSPATH')) exit;

require_once get_theme_file_path('/inc/helpers/case-study-single.php');

get_header();

if (have_posts()) :
  while (have_posts()) : the_post();
    $post_id = get_the_ID();

    $cursor_attrs = moma_cs_single_cursor_attrs();
    $title = get_the_title();
    $logo = function_exists('get_field') ? get_field('cs_client_logo', $post_id) : null;
    $hero_lightbox = function_exists('get_field') ? (bool) get_field('cs_hero_enable_lightbox', $post_id) : true;

    $client_title = function_exists('get_field') ? trim((string) get_field('cs_client_title', $post_id)) : '';
    $client_text = function_exists('get_field') ? trim((string) get_field('cs_client_text', $post_id)) : '';

    $problem_title = function_exists('get_field') ? trim((string) get_field('cs_problem_title', $post_id)) : '';
    $problem_text = function_exists('get_field') ? trim((string) get_field('cs_problem_text', $post_id)) : '';
    $metric_title = function_exists('get_field') ? trim((string) get_field('cs_metric_title', $post_id)) : '';
    $metric_value = function_exists('get_field') ? trim((string) get_field('cs_metric_value', $post_id)) : '';
    $metric_prefix = function_exists('get_field') ? trim((string) get_field('cs_metric_prefix', $post_id)) : '';
    $metric_suffix = function_exists('get_field') ? trim((string) get_field('cs_metric_suffix', $post_id)) : '';

    $process_title = function_exists('get_field') ? trim((string) get_field('cs_process_title', $post_id)) : '';
    $process_steps = function_exists('get_field') ? (array) get_field('cs_process_steps', $post_id) : [];

    $tech_title = function_exists('get_field') ? trim((string) get_field('cs_tech_title', $post_id)) : '';
    $tech_text = function_exists('get_field') ? trim((string) get_field('cs_tech_text', $post_id)) : '';
    $tech_mode = function_exists('get_field') ? (string) get_field('cs_tech_mode', $post_id) : 'logos';
    $tech_media_items = function_exists('get_field') ? (array) get_field('cs_tech_media_items', $post_id) : [];
    $tech_logo_items = function_exists('get_field') ? (array) get_field('cs_tech_logo_items', $post_id) : [];

    $results_title = function_exists('get_field') ? trim((string) get_field('cs_results_title', $post_id)) : '';
    $results_text = function_exists('get_field') ? trim((string) get_field('cs_results_text', $post_id)) : '';
    $results_items = function_exists('get_field') ? (array) get_field('cs_results_items', $post_id) : [];

    $feature_video = function_exists('get_field') ? (array) get_field('cs_feature_video', $post_id) : [];
    $work_cta_title = function_exists('get_field') ? trim((string) get_field('cs_work_cta_title', $post_id)) : '';
    $work_cta_button_label = function_exists('get_field') ? trim((string) get_field('cs_work_cta_button_label', $post_id)) : '';
    $work_cta_url = function_exists('get_field') ? trim((string) get_field('cs_work_cta_url', $post_id)) : '';

    $related_title = function_exists('get_field') ? trim((string) get_field('cs_related_title', $post_id)) : '';
    $related_manual = function_exists('get_field') ? (array) get_field('cs_related_manual', $post_id) : [];
    $related_posts = moma_cs_single_pick_related_posts($post_id, $related_manual, 2);

    $terms = get_the_terms($post_id, 'case_study_category');
    $chip_terms = (!is_wp_error($terms) && !empty($terms)) ? array_slice($terms, 0, 3) : [];

    $hero_media = [];
    if (has_post_thumbnail($post_id)) {
      $hero_media = [
        'media_type' => 'image',
        'image' => get_post_thumbnail_id($post_id),
        'enable_lightbox' => $hero_lightbox,
      ];
    }

    $has_client_section = ($client_title !== '' || $client_text !== '');
    $has_problem_section = ($problem_title !== '' || $problem_text !== '' || $metric_title !== '' || $metric_value !== '');
    $has_process_section = ($process_title !== '' || !empty($process_steps));
    $has_tech_media = false;
    foreach ($tech_media_items as $media_item) {
      if (!empty(moma_cs_single_normalize_media($media_item['media_item'] ?? []))) {
        $has_tech_media = true;
        break;
      }
    }
    $has_tech_logos = false;
    foreach ($tech_logo_items as $logo_item) {
      if (!empty($logo_item['brand_name']) || !empty($logo_item['logo_image'])) {
        $has_tech_logos = true;
        break;
      }
    }
    $has_tech_section = ($tech_title !== '' || $tech_text !== '') && (($tech_mode === 'media' && $has_tech_media) || ($tech_mode === 'logos' && $has_tech_logos));

    $has_results_items = false;
    foreach ($results_items as $result_item) {
      if (!empty($result_item['label']) || !empty($result_item['value']) || !empty($result_item['prefix']) || !empty($result_item['suffix'])) {
        $has_results_items = true;
        break;
      }
    }
    $has_results_section = ($results_title !== '' || $results_text !== '' || $has_results_items);

    $has_video_section = !empty(moma_cs_single_normalize_media(array_merge(['media_type' => 'video'], $feature_video), ['image_size' => 'moma_cs_detail_video']));
    $has_work_cta = ($work_cta_title !== '' && $work_cta_url !== '');
    $has_related = !empty($related_posts);
    $show_contact_banner = function_exists('get_field') && (
      get_field('srv_banner_title', 'option') || get_field('srv_banner_text', 'option') || get_field('srv_banner_btn_url', 'option')
    );
    ?>

    <main class="bg-[#fbf7f1] text-[#18085a] moma-case-single" data-moma-case-study-single="1">
      <div class="mx-auto px-4 py-8 md:py-12 lg:py-16 max-w-6xl">

        <section class="mt-24 moma-case-single__hero" aria-labelledby="moma-case-single-title">
          <?php if (!empty($chip_terms)): ?>
            <ul class="flex flex-wrap gap-2 m-0 mb-6 p-0 list-none" data-moma-reveal="fade-up" data-reveal-y="22" data-reveal-duration="1.00" data-reveal-delay="0.02" data-reveal-start="top 88%" data-reveal-once="1">
              <?php foreach ($chip_terms as $index => $term): ?>
                <li><?php echo moma_case_study_render_term_chip($term, ['dot' => $index === 0]); ?></li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>

          <div class="md:items-end gap-8 grid md:grid-cols-[minmax(0,1fr)_220px]" data-moma-reveal="fade-up" data-reveal-y="34" data-reveal-duration="1.10" data-reveal-delay="0.04" data-reveal-start="top 82%" data-reveal-once="1">
            <div>
              <h1 id="moma-case-single-title" class="font-[Fraunces] text-[#18085a] text-[clamp(2.8rem,6vw,5.35rem)] leading-[0.96] tracking-[-0.03em] [text-wrap:balance]">
                <?php echo esc_html($title); ?>
              </h1>
            </div>

            <?php if (!empty($logo)): ?>
              <div class="flex justify-start md:justify-end moma-case-single__brand">
                <?php echo wp_get_attachment_image(moma_cs_single_get_image_id($logo), 'medium_large', false, [
                  'class' => 'moma-case-single__brand-logo h-auto w-auto max-h-[84px] max-w-[180px] object-contain',
                  'loading' => 'eager',
                  'decoding' => 'async',
                  'alt' => $title,
                ]); ?>
              </div>
            <?php endif; ?>
          </div>

          <?php if (!empty($hero_media)): ?>
            <div class="mt-10" data-moma-reveal="fade-up" data-reveal-y="42" data-reveal-duration="1.15" data-reveal-delay="0.08" data-reveal-start="top 82%" data-reveal-once="1">
              <?php
              echo moma_cs_single_render_media($hero_media, [
                'image_size' => 'moma_cs_detail_hero',
                'wrapper_class' => 'moma-case-single__hero-media',
                'link_class' => 'block',
                'media_class' => 'moma-case-single__hero-img h-auto w-full rounded-[24px] object-cover',
                'cursor_attrs' => $cursor_attrs,
                'alt' => $title,
                'loading' => 'eager',
              ]);
              ?>
            </div>
          <?php endif; ?>
        </section>

        <?php if ($has_client_section): ?>
          <section class="mt-14 md:mt-18 moma-case-single__section" data-moma-reveal="fade-up" data-reveal-y="28" data-reveal-duration="1.05" data-reveal-delay="0.02" data-reveal-start="top 82%" data-reveal-once="1">
            <?php if ($client_title !== ''): ?>
              <h2 class="moma-case-single__heading"><?php echo esc_html($client_title); ?></h2>
            <?php endif; ?>
            <?php if ($client_text !== ''): ?>
              <div class="max-w-4xl text-[#18085a]/75 moma-case-single__copy"><?php echo wpautop(esc_html($client_text)); ?></div>
            <?php endif; ?>
          </section>
        <?php endif; ?>

        <?php if ($has_problem_section): ?>
          <section class="mt-14 md:mt-18 moma-case-single__section">
            <div class="gap-4 grid md:grid-cols-12">
              <?php if ($problem_title !== '' || $problem_text !== ''): ?>
                <div class="md:col-span-8 bg-[#f0ece8] px-6 md:px-8 py-6 md:py-8 rounded-[20px]" data-moma-reveal="fade-up" data-reveal-y="28" data-reveal-duration="1.05" data-reveal-delay="0.02" data-reveal-start="top 82%" data-reveal-once="1">
                  <?php if ($problem_title !== ''): ?>
                    <h3 class="moma-case-single__box-title"><?php echo esc_html($problem_title); ?></h3>
                  <?php endif; ?>
                  <?php if ($problem_text !== ''): ?>
                    <div class="text-[#18085a]/75 moma-case-single__copy"><?php echo wpautop(esc_html($problem_text)); ?></div>
                  <?php endif; ?>
                </div>
              <?php endif; ?>

              <?php if ($metric_title !== '' || $metric_value !== ''): ?>
                <div class="md:col-span-4 bg-[#f0ece8] px-6 md:px-8 py-6 md:py-8 rounded-[20px]" data-moma-reveal="fade-up" data-reveal-y="28" data-reveal-duration="1.05" data-reveal-delay="0.10" data-reveal-start="top 82%" data-reveal-once="1">
                  <?php if ($metric_title !== ''): ?>
                    <p class="mb-6 font-medium text-[#18085a]/70 text-sm uppercase tracking-[0.02em] moma-case-single__metric-label"><?php echo esc_html($metric_title); ?></p>
                  <?php endif; ?>
                  <?php if ($metric_value !== ''): ?>
                    <p class="m-0 font-[Fraunces] text-[#18085a] text-[clamp(2.4rem,6vw,4.5rem)] leading-none tracking-[-0.04em] moma-case-single__metric-value">
                      <span class="moma-case-single__metric-affix"><?php echo esc_html($metric_prefix); ?></span><span data-countup-target data-countup-value="<?php echo esc_attr($metric_value); ?>">0</span><span class="moma-case-single__metric-affix"><?php echo esc_html($metric_suffix); ?></span>
                    </p>
                  <?php endif; ?>
                </div>
              <?php endif; ?>
            </div>
          </section>
        <?php endif; ?>

        <?php if ($has_process_section): ?>
          <section class="mt-16 md:mt-22 moma-case-single__section">
            <?php if ($process_title !== ''): ?>
              <h2 class="moma-case-single__process-title" data-moma-reveal="fade-up" data-reveal-y="30" data-reveal-duration="1.10" data-reveal-delay="0.02" data-reveal-start="top 82%" data-reveal-once="1"><?php echo esc_html($process_title); ?></h2>
            <?php endif; ?>

            <div class="space-y-12 md:space-y-16 mt-10">
              <?php foreach ($process_steps as $index => $step):
                $layout = $step['layout'] ?? 'text_media';
                $step_title = trim((string) ($step['step_title'] ?? ''));
                $step_text = trim((string) ($step['step_text'] ?? ''));
                $step_media = $step['step_media'] ?? [];
                $grid_primary = $step['grid_primary_media'] ?? [];
                $grid_top_one = $step['grid_top_one_media'] ?? [];
                $grid_top_two = $step['grid_top_two_media'] ?? [];
                $grid_bottom = $step['grid_bottom_media'] ?? [];

                $single_media_html = '';
                if ($layout === 'text_media') {
                  $single_media_html = moma_cs_single_render_media($step_media, [
                    'image_size' => 'moma_cs_detail_step',
                    'wrapper_class' => 'moma-case-single__step-media mt-6',
                    'link_class' => 'block',
                    'media_class' => 'h-auto w-full rounded-[18px] object-cover moma-case-single__step-single-img',
                    'cursor_attrs' => $cursor_attrs,
                    'alt' => $step_title ?: $title,
                  ]);
                }

                $grid_slots = [
                  'primary' => moma_cs_single_render_media($grid_primary, [
                    'image_size' => 'moma_cs_detail_step_large',
                    'wrapper_class' => 'moma-case-single__grid-slot moma-case-single__grid-slot--primary',
                    'link_class' => 'block h-full',
                    'media_class' => 'h-full w-full rounded-[18px] object-cover',
                    'cursor_attrs' => $cursor_attrs,
                    'alt' => $step_title ?: $title,
                  ]),
                  'top-one' => moma_cs_single_render_media($grid_top_one, [
                    'image_size' => 'moma_cs_detail_step_square',
                    'wrapper_class' => 'moma-case-single__grid-slot moma-case-single__grid-slot--top-one',
                    'link_class' => 'block h-full',
                    'media_class' => 'h-full w-full rounded-[18px] object-cover',
                    'cursor_attrs' => $cursor_attrs,
                    'alt' => $step_title ?: $title,
                  ]),
                  'top-two' => moma_cs_single_render_media($grid_top_two, [
                    'image_size' => 'moma_cs_detail_step_square',
                    'wrapper_class' => 'moma-case-single__grid-slot moma-case-single__grid-slot--top-two',
                    'link_class' => 'block h-full',
                    'media_class' => 'h-full w-full rounded-[18px] object-cover',
                    'cursor_attrs' => $cursor_attrs,
                    'alt' => $step_title ?: $title,
                  ]),
                  'bottom' => moma_cs_single_render_media($grid_bottom, [
                    'image_size' => 'moma_cs_detail_step_wide',
                    'wrapper_class' => 'moma-case-single__grid-slot moma-case-single__grid-slot--bottom',
                    'link_class' => 'block h-full',
                    'media_class' => 'h-full w-full rounded-[18px] object-cover',
                    'cursor_attrs' => $cursor_attrs,
                    'alt' => $step_title ?: $title,
                  ]),
                ];

                $has_grid_media = implode('', array_filter($grid_slots)) !== '';
                $has_content = ($step_title !== '' || $step_text !== '' || $single_media_html !== '' || $has_grid_media);
                if (!$has_content) continue;
                ?>
                <article class="moma-case-single__step" data-moma-reveal="fade-up" data-reveal-y="32" data-reveal-duration="1.08" data-reveal-delay="<?php echo esc_attr(number_format(0.04 + ($index * 0.04), 2, '.', '')); ?>" data-reveal-start="top 84%" data-reveal-once="1">
                  <?php if ($step_title !== ''): ?>
                    <h3 class="moma-case-single__step-title"><?php echo esc_html($step_title); ?></h3>
                  <?php endif; ?>

                  <?php if ($step_text !== ''): ?>
                    <div class="max-w-4xl text-[#18085a]/75 moma-case-single__copy"><?php echo wpautop(esc_html($step_text)); ?></div>
                  <?php endif; ?>

                  <?php if ($layout === 'text_media' && $single_media_html !== ''): ?>
                    <?php echo $single_media_html; ?>
                  <?php endif; ?>

                  <?php if ($layout === 'media_grid' && $has_grid_media): ?>
                    <div class="moma-case-single__media-grid mt-6">
                      <?php foreach ($grid_slots as $slot_html): ?>
                        <?php if ($slot_html !== '') echo $slot_html; ?>
                      <?php endforeach; ?>
                    </div>
                  <?php endif; ?>
                </article>
              <?php endforeach; ?>
            </div>
          </section>
        <?php endif; ?>

        <?php if ($has_tech_section): ?>
          <section class="mt-16 md:mt-20 moma-case-single__section" data-moma-reveal="fade-up" data-reveal-y="28" data-reveal-duration="1.06" data-reveal-delay="0.02" data-reveal-start="top 84%" data-reveal-once="1">
            <?php if ($tech_title !== ''): ?>
              <h3 class="moma-case-single__subheading"><?php echo esc_html($tech_title); ?></h3>
            <?php endif; ?>
            <?php if ($tech_text !== ''): ?>
              <div class="max-w-4xl text-[#18085a]/75 moma-case-single__copy"><?php echo wpautop(esc_html($tech_text)); ?></div>
            <?php endif; ?>

            <?php if ($tech_mode === 'media' && $has_tech_media): ?>
              <div class="gap-4 grid md:grid-cols-3 mt-6">
                <?php foreach ($tech_media_items as $item):
                  $label = trim((string) ($item['label'] ?? ''));
                  $media_html = moma_cs_single_render_media($item['media_item'] ?? [], [
                    'image_size' => 'moma_cs_detail_logo_card',
                    'wrapper_class' => 'moma-case-single__tech-media-item',
                    'link_class' => 'block h-full',
                    'media_class' => 'h-[180px] w-full rounded-[16px] object-contain bg-white p-6',
                    'cursor_attrs' => $cursor_attrs,
                    'alt' => $label ?: $title,
                  ]);
                  if ($media_html === '') continue;
                  ?>
                  <article class="bg-[#f0ece8] p-4 md:p-5 rounded-[18px]">
                    <?php if ($label !== ''): ?>
                      <p class="mb-3 font-medium text-[#18085a]/72 text-sm"><?php echo esc_html($label); ?></p>
                    <?php endif; ?>
                    <?php echo $media_html; ?>
                  </article>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>

            <?php if ($tech_mode === 'logos' && $has_tech_logos): ?>
              <div class="gap-4 grid md:grid-cols-3 mt-6">
                <?php foreach ($tech_logo_items as $item):
                  $brand_name = trim((string) ($item['brand_name'] ?? ''));
                  $brand_logo = $item['logo_image'] ?? null;
                  if ($brand_name === '' && empty($brand_logo)) continue;
                  ?>
                  <article class="flex flex-col bg-[#f0ece8] p-5 rounded-[18px] min-h-[180px] text-center">
                    <?php if ($brand_name !== ''): ?>
                      <p class="mb-4 font-medium text-[#18085a]/72 text-sm"><?php echo esc_html($brand_name); ?></p>
                    <?php endif; ?>
                    <div class="flex flex-1 justify-center items-center">
                      <?php if (!empty($brand_logo)): ?>
                        <?php echo wp_get_attachment_image(moma_cs_single_get_image_id($brand_logo), 'moma_cs_detail_logo_card', false, [
                          'class' => 'mx-auto h-auto max-h-[70px] w-auto max-w-full object-contain',
                          'loading' => 'lazy',
                          'decoding' => 'async',
                          'alt' => $brand_name ?: $title,
                        ]); ?>
                      <?php endif; ?>
                    </div>
                  </article>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </section>
        <?php endif; ?>

        <?php if ($has_results_section): ?>
          <section class="mt-16 md:mt-20 moma-case-single__section" data-moma-reveal="fade-up" data-reveal-y="28" data-reveal-duration="1.06" data-reveal-delay="0.02" data-reveal-start="top 84%" data-reveal-once="1">
            <?php if ($results_title !== ''): ?>
              <h3 class="moma-case-single__subheading"><?php echo esc_html($results_title); ?></h3>
            <?php endif; ?>
            <?php if ($results_text !== ''): ?>
              <div class="max-w-4xl text-[#18085a]/75 moma-case-single__copy"><?php echo wpautop(esc_html($results_text)); ?></div>
            <?php endif; ?>

            <?php if ($has_results_items): ?>
              <div class="gap-4 grid md:grid-cols-3 mt-6">
                <?php foreach ($results_items as $item):
                  $label = trim((string) ($item['label'] ?? ''));
                  $value = trim((string) ($item['value'] ?? ''));
                  $prefix = trim((string) ($item['prefix'] ?? ''));
                  $suffix = trim((string) ($item['suffix'] ?? ''));
                  if ($label === '' && $value === '' && $prefix === '' && $suffix === '') continue;
                  ?>
                  <article class="bg-[#f0ece8] px-5 md:px-6 py-5 md:py-6 rounded-[18px]">
                    <?php if ($label !== ''): ?>
                      <p class="mb-4 font-medium text-[#18085a]/70 text-sm"><?php echo esc_html($label); ?></p>
                    <?php endif; ?>
                    <?php if ($value !== ''): ?>
                      <p class="m-0 font-[Fraunces] text-[#18085a] text-[clamp(2.2rem,5vw,4rem)] leading-none tracking-[-0.04em]">
                        <span><?php echo esc_html($prefix); ?></span><span data-countup-target data-countup-value="<?php echo esc_attr($value); ?>">0</span><span><?php echo esc_html($suffix); ?></span>
                      </p>
                    <?php endif; ?>
                  </article>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </section>
        <?php endif; ?>

        <?php if ($has_video_section): ?>
          <section class="mt-16 md:mt-20 moma-case-single__section" data-moma-reveal="fade-up" data-reveal-y="32" data-reveal-duration="1.10" data-reveal-delay="0.04" data-reveal-start="top 84%" data-reveal-once="1">
            <?php
              echo moma_cs_single_render_media(array_merge(['media_type' => 'video'], $feature_video), [
                'image_size' => 'moma_cs_detail_video',
                'wrapper_class' => 'moma-case-single__feature-video',
                'link_class' => 'block overflow-hidden rounded-[20px]',
                'media_class' => 'h-auto w-full rounded-[20px] object-cover moma-case-single__feature-video-thumb',
                'cursor_attrs' => $cursor_attrs,
                'alt' => $title,
                'play_class' => 'moma-case-single__play--lg',
                'overlay_class' => 'moma-case-single__video-overlay',
              ]);
            ?>
          </section>
        <?php endif; ?>

        <?php if ($has_work_cta): ?>
          <section class="mt-8 md:mt-10 moma-case-single__section" data-moma-reveal="fade-up" data-reveal-y="24" data-reveal-duration="1.00" data-reveal-delay="0.02" data-reveal-start="top 88%" data-reveal-once="1">
            <a class="moma-case-single__work-banner" href="<?php echo esc_url($work_cta_url); ?>" aria-label="<?php echo esc_attr($work_cta_button_label ?: $work_cta_title); ?>">
              <span class="moma-case-single__work-banner-title"><?php echo esc_html($work_cta_title); ?></span>
              <span class="moma-case-single__work-banner-cta" aria-hidden="true">
                <svg viewBox="0 0 24 24" width="18" height="18" focusable="false" aria-hidden="true"><path d="M7 17L17 7" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" /><path d="M9 7h8v8" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
              </span>
            </a>
          </section>
        <?php endif; ?>

        <?php if ($has_related): ?>
          <section class="mt-16 md:mt-20 moma-case-single__section">
            <?php if ($related_title !== ''): ?>
              <div class="flex items-center gap-3" data-moma-reveal="fade-up" data-reveal-y="26" data-reveal-duration="1.02" data-reveal-delay="0.02" data-reveal-start="top 86%" data-reveal-once="1">
                <h2 class="font-[Fraunces] text-[#18085a] text-3xl md:text-4xl leading-none tracking-[-0.03em]"><?php echo esc_html($related_title); ?></h2>
              </div>
            <?php endif; ?>

            <div class="gap-8 grid grid-cols-1 lg:grid-cols-2 mt-8">
              <?php foreach ($related_posts as $i => $related_post):
                $related_id = (int) $related_post->ID;
                $subtitle = function_exists('get_field') ? trim((string) get_field('cs_subtitle', $related_id)) : '';
                if ($subtitle === '' && has_excerpt($related_id)) {
                  $subtitle = get_the_excerpt($related_id);
                }
                $related_terms = get_the_terms($related_id, 'case_study_category');
                $related_chip_terms = (!is_wp_error($related_terms) && !empty($related_terms)) ? array_slice($related_terms, 0, 3) : [];
                $thumb_id = (int) get_post_thumbnail_id($related_id);
                ?>
                <article class="relative overflow-visible moma-project-card" data-moma-reveal="fade-up" data-reveal-y="32" data-reveal-duration="1.08" data-reveal-delay="<?php echo esc_attr(number_format(0.08 + ($i * 0.06), 2, '.', '')); ?>" data-reveal-start="top 86%" data-reveal-once="1">
                  <?php if ($thumb_id): ?>
                    <a class="block moma-project-card__media" href="<?php echo esc_url(get_permalink($related_id)); ?>" aria-label="<?php echo esc_attr(get_the_title($related_id)); ?>"<?php echo $cursor_attrs; ?>>
                      <?php echo wp_get_attachment_image($thumb_id, 'moma_cs_detail_related', false, [
                        'class' => 'moma-project-card__img block h-auto w-full object-cover',
                        'loading' => 'lazy',
                        'decoding' => 'async',
                        'alt' => get_the_title($related_id),
                      ]); ?>
                    </a>
                  <?php endif; ?>

                  <div class="p-5 md:p-6 moma-project-card__body">
                    <div class="flex justify-between items-start gap-4">
                      <div class="min-w-0">
                        <h3 class="m-0 font-semibold text-[#18085a] text-lg md:text-xl leading-snug tracking-[-0.01em]">
                          <a class="text-inherit no-underline" href="<?php echo esc_url(get_permalink($related_id)); ?>"><?php echo esc_html(get_the_title($related_id)); ?></a>
                        </h3>
                        <?php if ($subtitle): ?>
                          <p class="m-0 mt-2 text-[#18085a]/80 text-sm leading-relaxed"><?php echo esc_html($subtitle); ?></p>
                        <?php endif; ?>
                      </div>

                      <a class="moma-project-card__cta moma-servizi-card__cta shrink-0" href="<?php echo esc_url(get_permalink($related_id)); ?>" aria-label="<?php echo esc_attr(sprintf('Vai al case study %s', get_the_title($related_id))); ?>">
                        <svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false"><path d="M7 17L17 7" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" /><path d="M9 7h8v8" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
                      </a>
                    </div>

                    <?php if (!empty($related_chip_terms)): ?>
                      <ul class="flex flex-wrap gap-2 m-0 mt-4 p-0 list-none" aria-label="Categorie case study">
                        <?php foreach ($related_chip_terms as $index => $term): ?>
                          <li><?php echo moma_case_study_render_term_chip($term, ['dot' => $index === 0]); ?></li>
                        <?php endforeach; ?>
                      </ul>
                    <?php endif; ?>
                  </div>
                </article>
              <?php endforeach; ?>
            </div>
          </section>
        <?php endif; ?>
      </div>

      <?php if ($show_contact_banner): ?>
        <?php get_template_part('template-parts/site/archive/servizi/contact-banner', null, ['disable_cursor' => true]); ?>
      <?php endif; ?>
    </main>

    <?php
  endwhile;
endif;

get_footer();
