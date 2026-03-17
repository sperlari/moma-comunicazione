<?php
/**
 * ACF Block: Banner Team
 *
 * Fields attesi:
 * - title (text)
 * - text (textarea)
 * - button_label (text)
 * - button_link (link)
 * - cursor_enabled (true/false)
 * - cursor_image (image) (array)
 */

$title        = get_field('title') ?: 'Team';
$text         = get_field('text') ?: 'Qui trovi persone su cui puoi contare, un punto di riferimento vero per ogni fase del percorso.';
$button_label = get_field('button_label') ?: 'Fai il punto con noi.';
$button_link  = get_field('button_link');

$cursor_enabled_raw = get_field('cursor_enabled');
$cursor_enabled = ($cursor_enabled_raw === null) ? true : (bool) $cursor_enabled_raw;
$cursor_img     = get_field('cursor_image'); // array o null

// fallback da Options (se non impostato nel blocco)
if (!$cursor_img) {
  $cursor_img = get_field('cursor_team_image', 'option'); // image field in Options
}

$global_enabled_raw = get_field('cursor_team_enabled', 'option');
$global_enabled = ($global_enabled_raw === null) ? true : (bool) $global_enabled_raw;

$cursor_url = is_array($cursor_img) && !empty($cursor_img['url']) ? $cursor_img['url'] : '';
?>

<section class="moma-team-banner py-10 md:py-12">
  <div class="mx-auto px-4 max-w-6xl">
    <div
      class="relative px-6 md:px-12 py-8 md:py-10 rounded-2xl md:rounded-3xl overflow-hidden text-white min-h-[280px] md:min-h-[340px] lg:min-h-[360px]"
      data-moma-reveal="fade-up"
      data-reveal-y="34"
      data-reveal-duration="1.25"
      data-reveal-delay="0.10"
      data-reveal-start="top 70%"
      data-reveal-ease="power3.out"
      data-reveal-once="1"
      <?php if ($global_enabled && $cursor_enabled && $cursor_url): ?>
        data-cursor-scope="1"
        data-cursor-img="<?php echo esc_url($cursor_url); ?>"
      <?php endif; ?>
    >
      <!-- Background gradient (simile Figma) -->
      <div class="absolute inset-0 bg-[linear-gradient(90deg,#a85412_0%,#e9770b_55%,#f18810_100%)]"></div>

      <!-- vignetta/ombra soft (per profondità) -->
      <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,rgba(0,0,0,0.22)_0%,rgba(0,0,0,0)_55%)] opacity-80"></div>

      <!-- fascia più scura a sinistra -->
      <div class="left-0 absolute inset-y-0 bg-[linear-gradient(90deg,rgba(0,0,0,0.18)_0%,rgba(0,0,0,0)_70%)] w-1/3"></div>

      <!-- contenuto -->
      <div class="relative flex flex-col h-full">
        <h2 class="font-franceus text-5xl md:text-6xl leading-none">
          <?php echo esc_html($title); ?>
        </h2>

        <p class="mt-4 max-w-2xl font-epilogue text-white/90 text-sm md:text-base leading-relaxed">
          <?php echo esc_html($text); ?>
        </p>

        <?php if ($button_link && !empty($button_link['url'])): ?>
          <div class="flex justify-end mt-auto pt-10">
            <a
              href="<?php echo esc_url($button_link['url']); ?>"
              <?php if (!empty($button_link['target'])): ?>
                target="<?php echo esc_attr($button_link['target']); ?>"
                rel="noopener"
              <?php endif; ?>
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