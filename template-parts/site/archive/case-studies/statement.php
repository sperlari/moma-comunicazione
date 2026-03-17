<?php
if (!defined('ABSPATH')) exit;

$args = wp_parse_args($args ?? [], [
  'statement_post' => null,
  'card_cursor_attrs' => '',
]);

$title = function_exists('get_field') ? (get_field('csa_statement_title', 'option') ?: 'Welcome statement pagina progetti') : 'Welcome statement pagina progetti';
$text  = function_exists('get_field') ? (string) (get_field('csa_statement_text', 'option') ?: '') : '';

$title_raw  = trim((string) $title);
$has_dot    = ($title_raw !== '' && substr($title_raw, -1) === '.');
$title_base = $has_dot ? trim(substr($title_raw, 0, -1)) : $title_raw;
?>
<section class="moma-case-archive-intro container mx-auto py-16 lg:py-24" data-moma-case-studies-archive>
  <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 items-start">
    <div class="lg:col-span-6 xl:col-span-5">
      <?php if ($title_base !== ''): ?>
        <h1 class="moma-case-archive-intro__title moma-typewriter font-fraunces text-[#18085a]" data-moma-typewriter>
          <span><?php echo esc_html($title_base); ?></span><?php if ($has_dot): ?><span class="moma-case-archive-intro__dot" aria-hidden="true">.</span><?php endif; ?>
        </h1>
      <?php endif; ?>

      <?php if (trim($text) !== ''): ?>
        <div class="mt-6 max-w-2xl text-[#18085a] text-base leading-relaxed" data-moma-reveal="fade-up" data-reveal-y="24" data-reveal-duration="1.05" data-reveal-delay="0.12" data-reveal-start="top 82%" data-reveal-ease="power3.out" data-reveal-once="1">
          <?php echo wp_kses_post(nl2br($text)); ?>
        </div>
      <?php endif; ?>
    </div>

    <div class="lg:col-span-6 xl:col-span-4 xl:col-start-8">
      <?php if ($args['statement_post'] instanceof WP_Post): ?>
        <?php
          get_template_part('template-parts/site/components/case-study-archive-card', null, [
            'post' => $args['statement_post'],
            'card_cursor_attrs' => (string) $args['card_cursor_attrs'],
            'featured' => false,
            'extra_class' => 'moma-case-archive-intro-card',
            'reveal_attrs' => 'data-moma-reveal="fade-up" data-reveal-y="28" data-reveal-duration="1.10" data-reveal-delay="0.18" data-reveal-start="top 82%" data-reveal-ease="power3.out" data-reveal-once="1"',
            'in_grid' => false,
          ]);
        ?>
      <?php endif; ?>
    </div>
  </div>
</section>
