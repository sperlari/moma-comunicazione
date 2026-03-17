<?php
if (!defined('ABSPATH')) exit;

$post_type = 'case_study';
if (!post_type_exists($post_type)) return;

$limit = 10;

// 1) Featured (ACF true/false = 1)
$featured = get_posts([
  'post_type'      => $post_type,
  'post_status'    => 'publish',
  'posts_per_page' => $limit,
  'meta_key'       => 'cs_featured',
  'meta_value'     => '1',
  'orderby'        => 'date',
  'order'          => 'DESC',
  'meta_query' => [
    [
      'key'     => '_thumbnail_id',
      'compare' => 'EXISTS',
    ],
  ],
]);

$featured_ids = array_map(fn($p) => (int)$p->ID, $featured);

// 2) Fill con ultime pubblicate (escludi featured già presi)
$remaining = $limit - count($featured);
$latest = [];

if ($remaining > 0) {
  $latest = get_posts([
    'post_type'      => $post_type,
    'post_status'    => 'publish',
    'posts_per_page' => $remaining,
    'post__not_in'   => $featured_ids,
    'orderby'        => 'date',
    'order'          => 'DESC',
    'meta_query' => [
      [
        'key'     => '_thumbnail_id',
        'compare' => 'EXISTS',
      ],
    ],
  ]);
}

$posts = array_merge($featured, $latest);
if (!$posts) return;

// pattern size (omogeneo, pochi “lg”)
$sizePattern = ['lg','md','md','sm','md','md','sm','md','sm','md'];

// Cursor immagine (Hero Home)
$cursor_enabled_raw = function_exists('get_field') ? get_field('home_hero_cursor_enabled') : null;
$cursor_enabled = ($cursor_enabled_raw === null) ? true : (bool) $cursor_enabled_raw;

$cursor_img = function_exists('get_field') ? get_field('home_hero_cursor_image') : null; // array
if (!$cursor_img && function_exists('get_field')) $cursor_img = get_field('cursor_home_hero_image', 'option');

$global_enabled_raw = function_exists('get_field') ? get_field('cursor_home_hero_enabled', 'option') : null;
$global_enabled = ($global_enabled_raw === null) ? true : (bool) $global_enabled_raw;

$cursor_url = (is_array($cursor_img) && !empty($cursor_img['url'])) ? $cursor_img['url'] : '';
$cursor_attrs = ($global_enabled && $cursor_enabled && $cursor_url)
  ? sprintf(' data-cursor-scope="1" data-cursor-img="%s"', esc_url($cursor_url))
  : '';
?>
<section class="moma-hero">

  <div class="z-10 absolute inset-0 place-items-center grid pointer-events-none">
    <h1 class="font-fraunces text-[88px] md:text-[120px] leading-none moma-hero__wordmark">
      moma<span class="moma-hero__dot">.</span>
    </h1>

  </div>

  <div class="moma-hero__canvas">
    <div class="moma-hero__canvas-inner">
      <?php foreach ($posts as $i => $p):
        $pid = (int) $p->ID;
        // immagine quadrata dedicata per Hero (fallback: featured image)
        $square_id = function_exists('get_field') ? (int) (get_field('cs_image_square', $pid) ?: 0) : 0;
        $thumb_id = $square_id ?: (int) get_post_thumbnail_id($pid);
        if (!$thumb_id) continue;

        $sizeKey = $sizePattern[$i % count($sizePattern)];
        $imgSize = 'moma_cs_sq_md';
        if ($sizeKey === 'lg') $imgSize = 'moma_cs_sq_lg';
        if ($sizeKey === 'sm') $imgSize = 'moma_cs_sq_sm';
      ?>
        <div class="moma-hero__card" data-size="<?php echo esc_attr($sizeKey); ?>" data-depth="0.14"<?php echo $cursor_attrs; ?>>
          <a class="moma-hero__link" href="<?php echo esc_url(get_permalink($pid)); ?>" aria-label="<?php echo esc_attr(get_the_title($pid)); ?>">
            <?php
              echo wp_get_attachment_image(
                $thumb_id,
                $imgSize,
                false,
                [
                  'class' => 'moma-hero__img',
                  'loading' => ($i < 4 ? 'eager' : 'lazy'),
                  'decoding' => 'async',
                  'alt' => get_the_title($pid),
                ]
              );
            ?>
          </a>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
