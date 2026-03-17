<?php
/**
 * Home – Sezione “I nostri punti di forza” (Servizi)
 */

if (!defined('ABSPATH')) exit;

$defaults = [
  'title'       => 'i nostri punti di forza.',
  'bottom_text' => 'I nostri servizi non sono voci su un elenco, ma punti di svolta nella comunicazione delle aziende. È così che mettiamo ordine, apriamo strade e trasformiamo la complessità in un percorso leggibile.',
];

$title       = $defaults['title'];
$bottom_text = $defaults['bottom_text'];

if (function_exists('get_field')) {
  $title       = get_field('home_srv_title') ?: $title;
  $bottom_text = get_field('home_srv_bottom_text') ?: $bottom_text;
}

// CPT "servizio" (slug: servizi)
if (!post_type_exists('servizio')) return;

$posts = get_posts([
  'post_type'      => 'servizio',
  'post_status'    => 'publish',
  'posts_per_page' => -1,
  'orderby'        => 'menu_order',
  'order'          => 'ASC',
]);

if (!$posts) return;

$title_raw  = trim((string) $title);
$has_dot    = ($title_raw !== '' && substr($title_raw, -1) === '.');
$title_base = $has_dot ? trim(substr($title_raw, 0, -1)) : $title_raw;

?>

<section class="moma-servizi" data-moma-servizi-slider>
  <div class="container moma-servizi__container">
    <?php if ($title_base !== ''): ?>
      <h2 class="moma-servizi__title moma-typewriter" data-moma-typewriter>
        <span><?php echo esc_html($title_base); ?></span><?php if ($has_dot): ?><span class="moma-servizi__dot" aria-hidden="true">.</span><?php endif; ?>
      </h2>
    <?php endif; ?>

    <div class="moma-servizi-slider" data-srv-slider data-moma-reveal="fade-up" data-reveal-y="34" data-reveal-duration="1.25" data-reveal-delay="0.10" data-reveal-start="top 70%" data-reveal-ease="power3.out" data-reveal-once="1">
      <ul class="moma-servizi-slider__list" role="list" data-srv-track>
        <?php foreach ($posts as $i => $p):
          $pid = (int) $p->ID;

          $copy = '';
          $override_img_id = 0;

          if (function_exists('get_field')) {
            $copy = (string) get_field('srv_slider_text', $pid);
            $override_img_id = (int) (get_field('srv_slider_image', $pid) ?: 0);
          }

          if (function_exists('moma_glossary_parse_tokens') && trim($copy) !== '') {
            $copy = moma_glossary_parse_tokens($copy, 0);
          }

          $thumb_id = $override_img_id ?: (int) get_post_thumbnail_id($pid);
          if (!$thumb_id) {
            continue;
          }
        ?>
          <li class="moma-servizi-slide" data-srv-slide>
            <article class="moma-servizi-card" aria-label="<?php echo esc_attr(get_the_title($pid)); ?>">
              <div class="moma-servizi-card__media">
                <a class="moma-servizi-card__media-link" href="<?php echo esc_url(get_post_type_archive_link('servizio') . '#servizio-' . $p->post_name); ?>" aria-label="<?php echo esc_attr(sprintf('Vai alla sezione del servizio %s', get_the_title($pid))); ?>">
                  <?php
                    echo wp_get_attachment_image(
                      $thumb_id,
                      'large',
                      false,
                      [
                        'class'    => 'moma-servizi-card__img',
                        'loading'  => ($i === 0 ? 'eager' : 'lazy'),
                        'decoding' => 'async',
                        'alt'      => get_the_title($pid),
                      ]
                    );
                  ?>
                </a>
              </div>

              <div class="moma-servizi-card__content">
                <h3 class="moma-servizi-card__title"><?php echo esc_html(get_the_title($pid)); ?></h3>

                <?php if (trim($copy) !== ''): ?>
                  <div class="moma-servizi-card__text">
                    <?php echo wp_kses_post($copy); ?>
                  </div>
                <?php endif; ?>

                <a class="moma-servizi-card__cta" href="<?php echo esc_url(get_post_type_archive_link('servizio') . '#servizio-' . $p->post_name); ?>" aria-label="<?php echo esc_attr(sprintf('Vai alla sezione del servizio %s', get_the_title($pid))); ?>">
                  <svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false">
                    <path d="M7 17L17 7" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                    <path d="M9 7h8v8" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                  </svg>
                </a>
              </div>
            </article>
          </li>
        <?php endforeach; ?>
      </ul>

      <div class="moma-servizi-slider__dots" data-srv-dots role="tablist" aria-label="Seleziona una slide"></div>
    </div>

    <?php if (trim((string) $bottom_text) !== ''): ?>
      <div class="moma-servizi__bottom">
        <?php echo wp_kses_post($bottom_text); ?>
      </div>
    <?php endif; ?>
  </div>
</section>