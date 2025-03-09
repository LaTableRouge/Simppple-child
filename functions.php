<?php

if (!defined('ABSPATH')) {
    exit;
}

define('SIMPPPLECHILD_IS_VITE_DEVELOPMENT', false);

/*
 * ================================
 *  THEME FUNCTIONS
 */
require get_stylesheet_directory() . '/inc/vite.php';
// Front assets
SimpppleChild\Vite\enqueue_script('/src/scripts/parts.js', 'wp_enqueue_scripts', 'wp_footer', false, 'module');
SimpppleChild\Vite\enqueue_script('/src/scripts/front.js', 'wp_enqueue_scripts', 'wp_footer', false);

// Admin assets
SimpppleChild\Vite\enqueue_script('/src/scripts/admin.js', 'admin_enqueue_scripts', 'admin_footer');

// Editor assets
SimpppleChild\Vite\enqueue_script('/src/scripts/editor.js', 'enqueue_block_editor_assets');

// Theme customization
require get_stylesheet_directory() . '/inc/theme-customization/wp_customization.php';

// Blocks
require get_stylesheet_directory() . '/inc/blocks/categories-register.php';

require get_stylesheet_directory() . '/inc/blocks/acf/blocks-helpers.php';
require get_stylesheet_directory() . '/inc/blocks/acf/blocks-register.php';

require get_stylesheet_directory() . '/inc/blocks/react/blocks-register.php';
