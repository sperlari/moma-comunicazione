<?php
if (!defined('ABSPATH')) exit;

/**
 * Primary menu links -> m-btn m-btn--menu (solo depth 0)
 * - aria-expanded aggiunto ai parent con dropdown (JS poi lo aggiorna)
 * - is-open aggiunto di default se item è current parent/ancestor
 */
if (!function_exists('moma_primary_menu_link_attrs')) {
  function moma_primary_menu_link_attrs($atts, $item, $args, $depth) {
    if (!isset($args->theme_location) || $args->theme_location !== 'primary') return $atts;
    if ((int) $depth !== 0) return $atts; // solo voci principali

    $existing = isset($atts['class']) ? (string) $atts['class'] : '';
    $classes  = preg_split('/\s+/', trim($existing)) ?: [];
    $classes  = array_filter($classes);

    $classes[] = 'm-btn';
    $classes[] = 'm-btn--menu';

    $item_classes = is_array($item->classes) ? $item->classes : [];
    $has_children = in_array('menu-item-has-children', $item_classes, true);

    $is_open_default = $has_children && (
      in_array('current-menu-ancestor', $item_classes, true) ||
      in_array('current-menu-parent', $item_classes, true)
    );

    if ($is_open_default) $classes[] = 'is-open';

    $atts['class'] = implode(' ', array_unique($classes));

    if ($has_children) {
      $atts['aria-haspopup'] = 'true';
      $atts['aria-expanded'] = $is_open_default ? 'true' : 'false';
    }

    return $atts;
  }
  add_filter('nav_menu_link_attributes', 'moma_primary_menu_link_attrs', 10, 4);
}

$menu = wp_nav_menu([
  'theme_location' => 'primary',
  'container'      => false,
  'menu_class'     => 'moma-nav__list',
  'fallback_cb'    => false,
  'echo'           => false,

  // Wrappa il testo della voce (per coerenza con il button system)
  'link_before'    => '<span class="m-btn__label">',
  'link_after'     => '</span>',
]);

?>
<header class="moma-header" id="moma-header">
  <div class="moma-navBar" role="banner">
    <a class="moma-logo" href="<?php echo esc_url(home_url('/')); ?>" aria-label="Moma, home">
      <span class="moma-logo__clip">
        <span class="moma-logo__word">
          <span class="moma-logo__text">mom</span><span class="moma-logo__aChar">a</span><span class="moma-logo__dot">.</span>
        </span>

        <span class="moma-logo__mark" aria-hidden="true">
          <span class="moma-logo__circle"></span>
          <span class="moma-logo__a">a</span>
          <span class="moma-logo__aDot"></span>
        </span>
      </span>
    </a>

    <nav class="moma-nav" aria-label="Menu principale">
      <?php echo $menu; ?>
    </nav>

    <button class="moma-burger" type="button" aria-label="Apri menu" aria-controls="moma-mobile" aria-expanded="false">
      <span class="moma-burger__icon" aria-hidden="true"></span>
    </button>
  </div>

  <!-- Mobile overlay -->
  <div class="moma-mobile" id="moma-mobile" hidden>
    <button class="moma-mobile__close" type="button" aria-label="Chiudi menu">
      <span aria-hidden="true">×</span>
    </button>

    <div class="moma-mobile__inner">
      <nav class="moma-mobile__nav" aria-label="Menu mobile">
        <?php
        // riuso lo stesso menu: lo stile cambia via CSS in mobile overlay
        echo $menu;
        ?>
      </nav>
    </div>
  </div>
</header>
