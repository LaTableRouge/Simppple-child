<?php

namespace SimpppleChild\Blocks\ACF;

/**
 * Registers ACF blocks using metadata from `block.json` files.
 *
 * This function registers blocks and their assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 * @see wp-includes\blocks.php
 *
 * @return void
 */
function register_acf_blocks(): void {
    // Include all files in the block ACF folder
    $dirpath = get_stylesheet_directory() . '/blocks/acf/';

    $files = glob($dirpath . '**/block.json');

    if (!empty($files)) {
        foreach ($files as $file) {
            if (file_exists($file)) {

                // Get all the block metadata
                $metadata = wp_json_file_decode($file, ['associative' => true]);
                if (!is_array($metadata) || empty($metadata['name'])) {
                    continue;
                }

                // Register the block
                $metadata['file'] = wp_normalize_path(realpath($file));
                register_block_type($metadata['file']);

                // Handle translation for the block
                $scriptEditorHandle = generate_block_asset_handle($metadata['name'], 'editorScript');
                $scriptHandle = generate_block_asset_handle($metadata['name'], 'viewScript');
                $handles = [];
                if (!empty($scriptEditorHandle)) {
                    $handles[] = $scriptEditorHandle;
                }
                if (!empty($scriptHandle)) {
                    $handles[] = $scriptHandle;
                }

                if (!empty($handles)) {
                    foreach ($handles as $handle) {
                        wp_set_script_translations(
                            $handle,
                            'simpple',
                            get_stylesheet_directory() . '/lang'
                        );
                    }
                }
            }

        }
    }
}
add_action('init', __NAMESPACE__ . '\register_acf_blocks', 9);

