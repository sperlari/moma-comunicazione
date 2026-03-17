<?php
/**
 * Theme header template.
 *
 * @package TailPress
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">
    <?php wp_head(); ?>
</head>
<body <?php body_class('bg-white text-zinc-900 antialiased'); ?>>
<?php do_action('tailpress_site_before'); ?>

<div id="page" class="flex flex-col min-h-screen">
    <?php do_action('tailpress_header'); ?>

    <?php get_template_part('template-parts/site/header/moma-nav'); ?>

    <div id="content" class="site-content grow">
        <?php if (is_front_page()): ?>
        <?php endif; ?>

        <?php do_action('tailpress_content_start'); ?>
        <main>
