<?php

namespace SimpppleChild\Blocks;

/**
 * Add a custom block category for Simppple blocks.
 *
 * @param array $categories Existing block categories.
 * @param \WP_Post $post The current post object.
 * @return array Modified list of block categories.
 */
function blocks_category(array $categories, \WP_Post $post): array {
    $category_slugs = wp_list_pluck($categories, 'slug');

    return in_array('simppple-blocks', $category_slugs, true) ? $categories : array_merge(
        [
            [
                'slug' => 'simppple-blocks',
                'title' => __('Simppple - Blocks', 'simppple'),
                'icon' => null, // Pour la gestion de l'icone de la catégorie voir editor.js
            ],
        ],
        $categories
    );
}
add_filter('block_categories_all', __NAMESPACE__ . '\blocks_category', 10, 2);

