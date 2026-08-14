<?php

if (!defined('ABSPATH')) {
    exit;
}

define('SIMPPPLECHILD_IS_VITE_DEVELOPMENT', false);

/*
 * ================================
 *  THEME FUNCTIONS
 */
use SimpppleChild\Vite\Vite;

require_once get_stylesheet_directory() . '/inc/vite.php';
$vite = new Vite();

// Front assets
$vite->enqueueScript('/src/scripts/parts.js', 'enqueue_block_assets', 'wp_footer', false, 'module');
$vite->enqueueScript('/src/scripts/front.js', 'wp_enqueue_scripts', 'wp_footer', false);

// Admin assets
$vite->enqueueScript('/src/scripts/admin.js', 'admin_enqueue_scripts', 'admin_footer');

// Editor: JS in the chrome, CSS in the iframe canvas
$vite->enqueueScript('/src/scripts/editor.js', 'enqueue_block_editor_assets');
$vite->enqueueStyleEditor('/src/scripts/editor.js');

// Theme customization
require get_stylesheet_directory() . '/inc/theme-customization/wp_customization.php';

// Blocks
require get_stylesheet_directory() . '/inc/blocks/categories-register.php';
require get_stylesheet_directory() . '/inc/blocks/blocks-register.php';
require get_stylesheet_directory() . '/inc/blocks/acf/blocks-helpers.php';
