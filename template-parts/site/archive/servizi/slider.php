<?php
if (!defined('ABSPATH')) exit;

$posts = get_posts([
  'post_type'      => 'servizio',
  'post_status'    => 'publish',
  'posts_per_page' => -1,
  'orderby'        => 'menu_order',
  'order'          => 'ASC',
]);

if (empty($posts)) return;
?>
<section id="servizi-slider" class="moma-case-studies moma-servizi-stack moma-servizi-page-bg" data-moma-case-studies>
  <div class="moma-case-studies__component">
    <div class="case-studies">
      <?php foreach ($posts as $i => $p): ?>
        <?php
          $pid = (int) $p->ID;
          $bg = '#ffffff';
          $img = function_exists('get_field') ? get_field('srv_page_slider_image', $pid) : null;
          if (!$img && function_exists('get_field')) $img = get_field('srv_slider_image', $pid);
          $thumb_id = (is_array($img) && !empty($img['ID'])) ? (int) $img['ID'] : ((int) $img ?: 0);
          if (!$thumb_id) $thumb_id = (int) get_post_thumbnail_id($pid);

          $copy = function_exists('get_field') ? (string) (get_field('srv_page_slider_text', $pid) ?: '') : '';
          if ($copy === '' && function_exists('get_field')) $copy = (string) (get_field('srv_slider_text', $pid) ?: '');
          if (function_exists('moma_glossary_parse_tokens') && $copy) {
            $copy = moma_glossary_parse_tokens($copy, 0);
          }
          $z = 1000 + $i;
          $anchor_id = 'servizio-' . $p->post_name;
        ?>
        <div class="case-studies_item" style="<?php echo esc_attr("--cs-bg: {$bg}; --cs-z: {$z};"); ?>">
          <div class="case-card_sticky-frame">
            <div class="case-card_sticky-trigger" id="<?php echo esc_attr($anchor_id); ?>"></div>

            <div class="case-card_sticky-element" data-cs-sticky>
              <article class="moma-servizio-card" style="<?php echo esc_attr("background-color: {$bg};"); ?>" aria-label="<?php echo esc_attr(get_the_title($pid)); ?>">
                <div class="moma-servizio-card__inner">
                  <div class="moma-servizio-card__panel">
                    <div class="moma-servizio-card__media">
                      <?php if ($thumb_id): ?>
                        <?php echo wp_get_attachment_image(
                          $thumb_id,
                          'large',
                          false,
                          [
                            'class'    => 'moma-servizio-card__img',
                            'loading'  => ($i === 0 ? 'eager' : 'lazy'),
                            'decoding' => 'async',
                            'alt'      => get_the_title($pid),
                          ]
                        ); ?>
                      <?php endif; ?>
                    </div>

                    <div class="moma-servizio-card__content">
                      <h2 class="moma-servizio-card__title font-fraunces"><?php echo esc_html(get_the_title($pid)); ?></h2>

                      <?php if (trim($copy) !== ''): ?>
                        <div class="moma-servizio-card__text">
                          <?php echo wp_kses_post($copy); ?>
                        </div>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
              </article>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
