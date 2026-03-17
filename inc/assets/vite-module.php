<?php
if (!defined('ABSPATH')) exit;

/**
 * Forza type="module" per gli script Vite (dev e build) anche se
 * TailPress/WordPress li stampa con type="text/javascript".
 * Aggiunge anche crossorigin (utile in dev cross-origin).
 */
add_filter('script_loader_tag', function ($tag, $handle, $src) {

  // Vite dev client / entry / build assets
  $is_vite_client = (strpos($src, '/@vite/client') !== false);
  $is_vite_entry  = (strpos($src, '/resources/js/app.js') !== false);
  $is_vite_build  = (strpos($src, '/dist/assets/') !== false && str_ends_with(parse_url($src, PHP_URL_PATH) ?? '', '.js'));

  // Alcune config TailPress usano host completo (localhost:5173)
  $is_vite_hosted = (strpos($src, '://localhost:5173/') !== false) || (strpos($src, '://127.0.0.1:5173/') !== false);

  if (!($is_vite_client || $is_vite_entry || $is_vite_build || $is_vite_hosted)) {
    return $tag;
  }

  // 1) rimuovi qualsiasi type=... già presente (es. text/javascript)
  $tag = preg_replace('/\s+type=(["\']).*?\1/', '', $tag);

  // 2) aggiungi type="module" (sempre)
  if (strpos($tag, 'type="module"') === false) {
    $tag = str_replace('<script ', '<script type="module" ', $tag);
  }

  // 3) aggiungi crossorigin se non c'è
  if (strpos($tag, 'crossorigin') === false) {
    $tag = str_replace('<script ', '<script crossorigin ', $tag);
  }

  return $tag;
}, 20, 3);
