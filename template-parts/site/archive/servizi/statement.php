<?php
if (!defined('ABSPATH')) exit;

if (!function_exists('get_field')) return;
if (!post_type_exists('servizio')) return;

$title = get_field('srv_welcome_title', 'option') ?: 'Welcome statement pagina servizi';
$text  = get_field('srv_welcome_text', 'option') ?: '';
$btn_classes = 'm-btn m-btn--tag m-btn--dot m-btn--sm';

$posts = get_posts([
  'post_type'      => 'servizio',
  'post_status'    => 'publish',
  'posts_per_page' => -1,
  'orderby'        => 'menu_order',
  'order'          => 'ASC',
]);

$title_raw  = trim((string) $title);
$has_dot    = ($title_raw !== '' && substr($title_raw, -1) === '.');
$title_base = $has_dot ? trim(substr($title_raw, 0, -1)) : $title_raw;
?>
<section class="container mx-auto py-16 lg:py-24" data-moma-servizi-page>
  <?php if ($title_base !== ''): ?>
    <h1 class="moma-typewriter font-[Fraunces] text-[#18085a] text-4xl lg:text-6xl leading-tight" data-moma-typewriter>
      <span><?php echo esc_html($title_base); ?></span><?php if ($has_dot): ?><span class="moma-servizi__dot" aria-hidden="true">.</span><?php endif; ?>
    </h1>
  <?php endif; ?>

  <?php if (!empty($posts)): ?>
    <div class="flex flex-wrap gap-3 mt-8">
      <?php foreach ($posts as $p): ?>
        <?php $anchor_id = 'servizio-' . $p->post_name; ?>
        <a class="<?php echo esc_attr($btn_classes); ?>"
          href="#<?php echo esc_attr($anchor_id); ?>"
          data-srv-nav
          data-srv-target="<?php echo esc_attr($anchor_id); ?>">
          <span class="m-btn__label"><?php echo esc_html(get_the_title($p)); ?></span>
        </a>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <?php if (trim((string)$text) !== ''): ?>
    <div class="mt-6 max-w-3xl text-[#18085a]" data-moma-reveal="fade-up" data-reveal-y="22" data-reveal-duration="1.05" data-reveal-delay="0.10" data-reveal-start="top 78%" data-reveal-ease="power3.out" data-reveal-once="1">
      <?php echo wp_kses_post(nl2br($text)); ?>
    </div>
  <?php endif; ?>
</section>
