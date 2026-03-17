<?php
if (!defined('ABSPATH')) exit;

$defaults = [
  'title'        => 'Team',
  'text'         => 'Qui trovi persone su cui puoi contare, un punto di riferimento vero per ogni fase del percorso.',
  'button_label' => 'Fai il punto con noi.',
  'button_link'  => ['url' => home_url('/contatti'), 'title' => 'Contatti', 'target' => ''],
];

$title        = function_exists('get_field') ? (get_field('home_team_title') ?: $defaults['title']) : $defaults['title'];
$text         = function_exists('get_field') ? (get_field('home_team_text') ?: $defaults['text']) : $defaults['text'];
$button_label = function_exists('get_field') ? (get_field('home_team_button_label') ?: $defaults['button_label']) : $defaults['button_label'];
$button_link  = function_exists('get_field') ? (get_field('home_team_button_link') ?: $defaults['button_link']) : $defaults['button_link'];

// Cursor (override pagina > fallback options)
// Nota: default attivo se non impostato.
$cursor_enabled_raw = function_exists('get_field') ? get_field('home_team_cursor_enabled') : null;
$cursor_enabled = ($cursor_enabled_raw === null) ? true : (bool) $cursor_enabled_raw;

$cursor_img = function_exists('get_field') ? get_field('home_team_cursor_image') : null; // array
if (!$cursor_img && function_exists('get_field')) $cursor_img = get_field('cursor_team_image', 'option');

// opzionale: enable globale (se presente)
$global_enabled_raw = function_exists('get_field') ? get_field('cursor_team_enabled', 'option') : null;
$global_enabled = ($global_enabled_raw === null) ? true : (bool) $global_enabled_raw;

$cursor_url = (is_array($cursor_img) && !empty($cursor_img['url'])) ? $cursor_img['url'] : '';

// Reveal (editable)
$reveal_enabled  = function_exists('get_field') ? (bool) get_field('home_team_reveal_enabled') : true;
$reveal_y        = function_exists('get_field') ? (int) (get_field('home_team_reveal_y') ?: 34) : 34;
$reveal_duration = function_exists('get_field') ? (float) (get_field('home_team_reveal_duration') ?: 1.25) : 1.25;
$reveal_delay    = function_exists('get_field') ? (float) (get_field('home_team_reveal_delay') ?: 0.10) : 0.10;
$reveal_start    = function_exists('get_field') ? (get_field('home_team_reveal_start') ?: 'top 70%') : 'top 70%';
$reveal_ease     = function_exists('get_field') ? (get_field('home_team_reveal_ease') ?: 'power3.out') : 'power3.out';
$reveal_once     = function_exists('get_field') ? (bool) get_field('home_team_reveal_once') : true;

$reveal_attrs = '';
if ($reveal_enabled) {
  $reveal_attrs = sprintf(
    ' data-moma-reveal="fade-up" data-reveal-y="%d" data-reveal-duration="%s" data-reveal-delay="%s" data-reveal-start="%s" data-reveal-ease="%s" data-reveal-once="%d"',
    $reveal_y,
    esc_attr($reveal_duration),
    esc_attr($reveal_delay),
    esc_attr($reveal_start),
    esc_attr($reveal_ease),
    $reveal_once ? 1 : 0
  );
}

$cursor_attrs = '';
if ($global_enabled && $cursor_enabled && $cursor_url) {
  $cursor_attrs = sprintf(' data-cursor-scope="1" data-cursor-img="%s"', esc_url($cursor_url));
}
?>

<section class="moma-team-banner py-10 md:py-12">
  <div class="mx-auto px-4 max-w-6xl">
    <div class="relative px-6 md:px-12 py-8 md:py-10 rounded-2xl md:rounded-3xl overflow-hidden text-white min-h-[280px] md:min-h-[340px] lg:min-h-[360px]"
      <?php echo $reveal_attrs; ?>
      <?php echo $cursor_attrs; ?>
    >
      <div class="absolute inset-0 bg-[linear-gradient(90deg,#a85412_0%,#e9770b_55%,#f18810_100%)]"></div>
      <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,rgba(0,0,0,0.22)_0%,rgba(0,0,0,0)_55%)] opacity-80"></div>
      <div class="left-0 absolute inset-y-0 bg-[linear-gradient(90deg,rgba(0,0,0,0.18)_0%,rgba(0,0,0,0)_70%)] w-1/3"></div>

      <div class="relative flex flex-col h-full">
        <h2 class="font-franceus text-5xl md:text-6xl leading-none"><?php echo esc_html($title); ?></h2>

        <p class="mt-4 max-w-2xl font-epilogue text-white/90 text-sm md:text-base leading-relaxed">
          <?php echo esc_html($text); ?>
        </p>

        <?php if (!empty($button_link['url'])): ?>
          <div class="flex justify-end mt-auto pt-10">
            <a
              href="<?php echo esc_url($button_link['url']); ?>"
              <?php if (!empty($button_link['target'])): ?>target="<?php echo esc_attr($button_link['target']); ?>" rel="noopener"<?php endif; ?>
              data-cursor-ui="1"
              class="m-btn m-btn--secondary m-btn--dot"
            >
              <span class="m-btn__label"><?php echo esc_html($button_label); ?></span>
            </a>
          </div>
        <?php endif; ?>
      </div>

    </div>
  </div>
</section>