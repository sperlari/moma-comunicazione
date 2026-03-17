<?php
/**
 * Theme footer template.
 *
 * @package TailPress
 */
?>
        </main>

        <?php do_action('tailpress_content_end'); ?>
    </div>

    <?php do_action('tailpress_content_after'); ?>

    <?php get_template_part('template-parts/site/footer/main'); ?>
</div>

<?php
  // Cursor base (dot) + cursor per aree (immagine)
  $dot_enabled_raw = function_exists('get_field') ? get_field('cursor_dot_enabled', 'option') : null;
  $dot_enabled = ($dot_enabled_raw === null) ? true : (bool) $dot_enabled_raw;

  $dot_color = function_exists('get_field') ? (get_field('cursor_dot_color', 'option') ?: '#2b1e72') : '#2b1e72';
  $dot_size  = function_exists('get_field') ? (int) (get_field('cursor_dot_size', 'option') ?: 10) : 10;
?>

<?php if ($dot_enabled): ?>
  <div id="moma-dot" aria-hidden="true" style="--moma-dot-color: <?php echo esc_attr($dot_color); ?>; --moma-dot-size: <?php echo esc_attr($dot_size); ?>px;"></div>
<?php endif; ?>

<div id="moma-cursor" aria-hidden="true">
  <img alt="" />
</div>

<?php
if (function_exists('moma_glossary_render_data_node')) {
  $ctx = is_singular() ? get_queried_object_id() : 0;
  moma_glossary_render_data_node($ctx);
}
?>

<?php wp_footer(); ?>
</body>
</html>
