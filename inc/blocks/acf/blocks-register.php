<?php

function simppplechild_register_acf_blocks() {
    /**
     * Registers the blocks using the metadata loaded from the `block.json` files.
     * Behind the scenes, it registers also all assets so they can be enqueued
     * through the block editor in the corresponding context.
     *
     * @see https://developer.wordpress.org/reference/functions/register_block_type/
     * @see wp-includes\blocks.php
     */

    // Include all files in the block react folder
    $dirpath = get_stylesheet_directory() . '/blocks/acf/';

    $files = [];
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
                if (is_array($scriptEditorHandle)) {
                    array_merge($handles, $scriptEditorHandle);
                } else {
                    $handles[] = $scriptEditorHandle;
                }
                if (is_array($scriptHandle)) {
                    array_merge($handles, $scriptHandle);
                } else {
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
add_action('init', 'simppplechild_register_acf_blocks', 9);

