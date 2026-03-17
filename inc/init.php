<?php
if (!defined('ABSPATH')) exit;

// ACF
require_once get_theme_file_path('/inc/acf/acf-json.php');

// Fix type="module" per Vite/TailPress
require_once __DIR__ . '/assets/vite-module.php';

// CPT + media
require_once __DIR__ . '/cpt/case-studies.php';
require_once __DIR__ . '/cpt/servizi.php';
require_once __DIR__ . '/cpt/glossario.php';
require_once __DIR__ . '/media/image-sizes.php';
//require_once __DIR__ . '/acf/fields-case-study.php';
require_once __DIR__ . '/acf/fields-servizi.php';
require_once get_theme_file_path('/inc/cpt/moma-transition-phrases.php');
require_once get_theme_file_path('/inc/rest/moma-transition-phrases.php');
require_once get_theme_file_path('/inc/integrations/moma-page-transition.php');
require_once get_theme_file_path('inc/acf/blocks.php');
require_once get_theme_file_path('inc/acf/options.php');
// Integrazioni (se ti serve mettere logiche di enqueue condizionali, ecc.)
// require_once get_theme_file_path('/inc/integrations/gsap.php');

// Footer (HubSpot + Case Study random + opzioni)
require_once get_theme_file_path('/inc/integrations/moma-footer.php');

// Integrazione servizi + glossario
require_once __DIR__ . '/acf/fields-servizi-archive.php';
require_once __DIR__ . '/acf/fields-case-studies-archive.php';
require_once __DIR__ . '/acf/fields-glossario.php';
require_once __DIR__ . '/glossary.php';
require_once __DIR__ . '/integrations/moma-glossary-admin.php';
require_once __DIR__ . '/integrations/moma-servizi-fixes.php';
require_once __DIR__ . '/helpers/case-study-common.php';
require_once __DIR__ . '/acf/fields-case-study-single.php';
require_once __DIR__ . '/helpers/case-study-single.php';
require_once __DIR__ . '/integrations/moma-case-study-single.php';
require_once __DIR__ . '/acf/fields-multimedia-page.php';
require_once __DIR__ . '/integrations/moma-multimedia-page.php';
