<?php

if (is_file(__DIR__.'/vendor/autoload_packages.php')) {
    require_once __DIR__.'/vendor/autoload_packages.php';
}

function tailpress(): TailPress\Framework\Theme
{
    return TailPress\Framework\Theme::instance()
        ->assets(fn($manager) => $manager
            ->withCompiler(new TailPress\Framework\Assets\ViteCompiler, fn($compiler) => $compiler
                ->registerAsset('resources/css/app.css')
                ->registerAsset('resources/js/app.js')
                ->editorStyleFile('resources/css/editor-style.css')
            )
            ->enqueueAssets()
        )
        ->features(fn($manager) => $manager->add(TailPress\Framework\Features\MenuOptions::class))
        ->menus(fn($manager) => $manager->add('primary', __( 'Primary Menu', 'tailpress')))
        ->themeSupport(fn($manager) => $manager->add([
            'title-tag',
            'custom-logo',
            'post-thumbnails',
            'align-wide',
            'wp-block-styles',
            'responsive-embeds',
            'html5' => [
                'search-form',
                'comment-form',
                'comment-list',
                'gallery',
                'caption',
            ]
        ]));
}

tailpress();

/**
 * Google Fonts
 * - Epilogue: font principale del sito
 * - Fraunces: titoli specifici (es. "Siamo un punto fermo")
 */
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style(
        'moma-google-fonts',
        'https://fonts.googleapis.com/css2?family=Epilogue:wght@300;400;500;600;700&family=Fraunces:opsz,wght@9..144,400;500;600;700&display=swap',
        [],
        null
    );
}, 5);

// CSS extra (non compilato da Vite): footer replica Figma
add_action('wp_enqueue_scripts', function () {
    $path = get_theme_file_path('/assets/css/moma-footer.css');
    if (!is_file($path)) return;

    wp_enqueue_style(
        'moma-footer',
        get_theme_file_uri('/assets/css/moma-footer.css'),
        [],
        filemtime($path)
    );
}, 25);

add_filter('wp_resource_hints', function ($urls, $relation_type) {
    if ($relation_type === 'preconnect') {
        $urls[] = 'https://fonts.googleapis.com';
        $urls[] = [
            'href' => 'https://fonts.gstatic.com',
            'crossorigin' => 'anonymous',
        ];
    }
    return $urls;
}, 10, 2);

// carico il resto (ACF, integrazioni, ecc.)
require_once get_theme_file_path('/inc/init.php');
