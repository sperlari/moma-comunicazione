<?php
if (!defined('ABSPATH')) exit;

if (!function_exists('get_field')) return;

$type   = get_field('srv_hero_type', 'option') ?: 'video';
$webm   = get_field('srv_hero_video_webm', 'option');
$mp4    = get_field('srv_hero_video_mp4', 'option');
$poster = get_field('srv_hero_video_poster', 'option');
$image  = get_field('srv_hero_image', 'option');

$poster_url = is_array($poster) ? ($poster['url'] ?? '') : '';
$cursor_enabled = (bool) get_field('srv_hero_cursor_enabled', 'option');
$cursor_img = get_field('srv_hero_cursor_image', 'option');
$cursor_url = (is_array($cursor_img) && !empty($cursor_img['url'])) ? $cursor_img['url'] : '';
$cursor_attrs = ($cursor_enabled && $cursor_url)
  ? sprintf(' data-cursor-scope="1" data-cursor-img="%s"', esc_url($cursor_url))
  : '';
?>
<section class="relative w-full min-h-screen overflow-hidden"<?php echo $cursor_attrs; ?>>
  <?php if ($type === 'video' && (is_array($webm) || is_array($mp4))): ?>
    <video
      class="absolute inset-0 w-full h-full object-cover"
      autoplay
      muted
      loop
      playsinline
      preload="metadata"
      <?php if ($poster_url): ?>poster="<?php echo esc_url($poster_url); ?>"<?php endif; ?>
    >
      <?php if (is_array($webm) && !empty($webm['url'])): ?>
        <source src="<?php echo esc_url($webm['url']); ?>" type="video/webm">
      <?php endif; ?>
      <?php if (is_array($mp4) && !empty($mp4['url'])): ?>
        <source src="<?php echo esc_url($mp4['url']); ?>" type="video/mp4">
      <?php endif; ?>
    </video>
  <?php elseif ($type === 'image' && is_array($image) && !empty($image['url'])): ?>
    <img
      class="absolute inset-0 w-full h-full object-cover"
      src="<?php echo esc_url($image['url']); ?>"
      alt="<?php echo esc_attr($image['alt'] ?? ''); ?>"
      loading="eager"
      decoding="async"
    >
  <?php endif; ?>
</section>
