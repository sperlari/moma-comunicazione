<?php
if (!defined('ABSPATH')) exit;

$args = wp_parse_args($args ?? [], [
  'posts' => [],
  'card_cursor_attrs' => '',
]);

$posts = array_values(array_filter((array) $args['posts'], static fn($p) => $p instanceof WP_Post));
$empty_text = function_exists('get_field') ? (get_field('csa_empty_results_text', 'option') ?: 'Nessun case study disponibile per questa categoria.') : 'Nessun case study disponibile per questa categoria.';
?>
<section class="container mx-auto pb-16 lg:pb-24">
  <div class="moma-case-archive-grid" data-case-grid>
    <?php foreach ($posts as $index => $post_obj): ?>
      <?php
        $reveal_delay = number_format(0.06 + ($index * 0.06), 2, '.', '');
        get_template_part('template-parts/site/components/case-study-archive-card', null, [
          'post' => $post_obj,
          'card_cursor_attrs' => (string) $args['card_cursor_attrs'],
          'featured' => $index === 0,
          'extra_class' => $index === 0 ? 'is-featured' : '',
          'reveal_attrs' => 'data-moma-reveal="fade-up" data-reveal-y="30" data-reveal-duration="1.08" data-reveal-delay="' . esc_attr($reveal_delay) . '" data-reveal-start="top 86%" data-reveal-ease="power3.out" data-reveal-once="1"',
        ]);
      ?>
    <?php endforeach; ?>
  </div>

  <div class="moma-case-archive-empty<?php echo !empty($posts) ? ' hidden' : ''; ?>" data-case-empty>
    <p><?php echo esc_html($empty_text); ?></p>
  </div>
</section>
