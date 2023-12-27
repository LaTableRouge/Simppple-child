<?php

if (!defined('ABSPATH')) {
    exit;
}

define('SIMPPPLECHILD_IS_VITE_DEVELOPMENT', false);

/*
 * ================================
 *  THEME FUNCTIONS
 */
require get_template_directory() . '/inc/vite.php';
// Front assets
simppplechild_vite_enqueue_script('/assets/js/front.js', 'wp_enqueue_scripts', 'wp_footer');

// Admin assets
simppplechild_vite_enqueue_script('/assets/js/admin.js', 'admin_enqueue_scripts', 'admin_footer');

// Editor assets
simppplechild_vite_enqueue_script('/assets/js/editor.js', 'enqueue_block_editor_assets');

// Theme customization
require get_template_directory() . '/inc/theme-customization/wp_customization.php';
require get_template_directory() . '/inc/theme-customization/theme-remove-default-settings.php';
require get_template_directory() . '/inc/theme-customization/color-add-rgb.php';
require get_template_directory() . '/inc/theme-customization/color-add-hsl.php';

// Blocks
require get_template_directory() . '/inc/blocks/acf/blocks-helpers.php';
require get_template_directory() . '/inc/blocks/acf/categories-register.php';
require get_template_directory() . '/inc/blocks/acf/blocks-register.php';

require get_template_directory() . '/inc/blocks/react/blocks-register.php';

// Patterns
require get_template_directory() . '/inc/patterns/categories-register.php';
