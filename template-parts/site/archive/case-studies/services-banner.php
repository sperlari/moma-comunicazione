<?php
if (!defined('ABSPATH')) exit;
if (!function_exists('get_field')) return;

$title = get_field('csa_banner_title', 'option') ?: 'Scopri i nostri servizi';
$text  = get_field('csa_banner_text', 'option') ?: '';
$label = get_field('csa_banner_btn_label', 'option') ?: 'vai ai servizi';
$url   = get_field('csa_banner_btn_url', 'option') ?: home_url('/servizi');

$cursor_enabled = (bool) get_field('csa_banner_cursor_enabled', 'option');
$cursor_image   = get_field('csa_banner_cursor_image', 'option');
$cursor_url     = (is_array($cursor_image) && !empty($cursor_image['url'])) ? $cursor_image['url'] : '';
$cursor_attrs   = ($cursor_enabled && $cursor_url)
  ? sprintf(' data-cursor-scope="1" data-cursor-img="%s"', esc_url($cursor_url))
  : '';
?>
<section class="py-10 md:py-12 moma-team-banner">
  <div class="mx-auto px-4 max-w-6xl">
    <div class="relative px-6 md:px-12 py-8 md:py-10 rounded-2xl md:rounded-3xl min-h-[280px] md:min-h-[340px] lg:min-h-[360px] overflow-hidden text-white" data-moma-reveal="fade-up" data-reveal-y="36" data-reveal-duration="1.20" data-reveal-delay="0.06" data-reveal-start="top 78%" data-reveal-ease="power3.out" data-reveal-once="1"<?php echo $cursor_attrs; ?>>
      <div class="absolute inset-0 bg-[linear-gradient(90deg,#a85412_0%,#e9770b_55%,#f18810_100%)]"></div>
      <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,rgba(0,0,0,0.22)_0%,rgba(0,0,0,0)_55%)] opacity-80"></div>
      <div class="left-0 absolute inset-y-0 bg-[linear-gradient(90deg,rgba(0,0,0,0.18)_0%,rgba(0,0,0,0)_70%)] w-1/3"></div>

      <div class="relative flex flex-col h-full">
        <h2 class="font-fraunces text-5xl md:text-6xl leading-none"><?php echo esc_html($title); ?></h2>

        <?php if (trim((string) $text) !== ''): ?>
          <p class="mt-4 max-w-2xl font-epilogue text-white/90 text-sm md:text-base leading-relaxed"><?php echo esc_html($text); ?></p>
        <?php endif; ?>

        <div class="flex justify-end mt-auto pt-10">
          <a href="<?php echo esc_url($url); ?>" data-cursor-ui="1" class="m-btn m-btn--secondary m-btn--dot">
            <span class="m-btn__label"><?php echo esc_html($label); ?></span>
          </a>
        </div>
      </div>
    </div>
  </div>
</section>
