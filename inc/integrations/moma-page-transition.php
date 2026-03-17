<?php
if (!defined('ABSPATH')) exit;

/**
 * Page Transition Overlay (Moma)
 * - aggiunge classe "moma-preload" subito in <head>
 * - espone endpoint REST per frase random
 * - stampa markup overlay in apertura <body> (hook TailPress)
 */

add_action('wp_head', function () {
  $endpoint = esc_url_raw(rest_url('moma/v1/transition-phrase'));

  // CSS critico (prima del CSS compilato) per evitare flash.
  echo '<style id="moma-transition-critical">'
    . 'html.moma-preload body{overflow:hidden;}'
    . 'html.moma-preload body>*:not(.moma-transition){visibility:hidden;}'
    . '.moma-transition{position:fixed;inset:0;z-index:99999;pointer-events:none;opacity:0;visibility:hidden;}'
    . '.moma-transition.is-active{pointer-events:auto;opacity:1;visibility:visible;}'
    . '</style>';

  // JS critico: setta endpoint + preload class il prima possibile.
  echo '<script>'
    . 'window.MomaTransition = window.MomaTransition || {};'
    . 'window.MomaTransition.endpoint = ' . json_encode($endpoint) . ';'
    . 'document.documentElement.classList.add("moma-preload");'
    . '</script>';
}, 0);

// Stampa overlay a inizio body (TailPress hook presente in header.php)
add_action('tailpress_site_before', function () {
  get_template_part('template-parts/site/components/page-transition');
}, 0);
