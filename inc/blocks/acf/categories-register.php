<?php

if (function_exists('acf_register_block_type')) {
    function simppplechild_acf_blocks_category($categories, $post) {
        $category_slugs = wp_list_pluck($categories, 'slug');

        return in_array('simpple-blocks', $category_slugs, true) ? $categories : array_merge(
            [
                [
                    'slug' => 'simpple-blocks',
                    'title' => __('Simppple - Blocks', 'simppple'),
                    'icon' => null, // Pour la gestion de l'icone de la catégorie voir editor.js
                ],
            ],
            $categories
        );
    }
    add_filter('block_categories_all', 'simppplechild_acf_blocks_category', 10, 2);
}

