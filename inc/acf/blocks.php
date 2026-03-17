<?php
add_action('acf/init', function () {
  if (!function_exists('acf_register_block_type')) return;

  acf_register_block_type([
    'name'            => 'banner-team',
    'title'           => 'Banner Team',
    'description'     => 'Banner arancione con titolo/testo/pulsante e cursore custom.',
    'render_template' => 'template-parts/blocks/banner-team.php',
    'category'        => 'layout',
    'icon'            => 'format-image',
    'keywords'        => ['banner', 'team', 'cta'],
    'supports'        => [
      'align' => false,
      'jsx'   => false,
    ],
  ]);
});