<?php

namespace SimpppleChild\Blocks\ACF;

class BlockHelpers {
    /**
     * Converts custom property strings to CSS variable format.
     *
     * @param string $value The custom property string to convert.
     * @return string The converted CSS variable string.
     */
    public static function convertCustomProperties(string $value): string {
        $prefix = 'var:';
        $prefix_len = mb_strlen($prefix);
        $token_in = '|';
        $token_out = '--';
        if (str_starts_with($value, $prefix)) {
            $unwrapped_name = str_replace(
                $token_in,
                $token_out,
                mb_substr($value, $prefix_len)
            );
            $value = "var(--wp--{$unwrapped_name})";
        }

        return $value;
    }

    /**
     * Returns inline block style properties as a CSS string.
     *
     * @param array $block The block data.
     * @return string The inline CSS style properties.
     */
    public static function getBlockStyleInline(array $block): string {
        $attrs = [];

        // Map of preset properties to their CSS variable formatters
        // Each formatter is a closure that generates the appropriate CSS variable string
        // e.g., fontSize: 'large' -> var(--wp--preset--font-size--large)
        $presetMap = [
            'fontSize' => fn ($v) => "var(--wp--preset--font-size--{$v})",
            'textColor' => fn ($v) => "var(--wp--preset--color--{$v})",
            'backgroundColor' => fn ($v) => "var(--wp--preset--color--{$v})",
        ];

        // Process each preset property if it exists in the block
        foreach ($presetMap as $key => $formatter) {
            if (isset($block[$key])) {
                // Convert camelCase property names to kebab-case CSS properties
                $cssKey = match ($key) {
                    'fontSize' => 'font-size',
                    'textColor' => 'color',
                    'backgroundColor' => 'background-color',
                };
                $attrs[$cssKey] = $formatter($block[$key]);
            }
        }

        // Spacing
        if (isset($block['style']['spacing'])) {
            foreach (['margin', 'padding'] as $prop) {
                $spacing = $block['style']['spacing'][$prop] ?? null;
                if (is_array($spacing)) {
                    foreach ($spacing as $dir => $value) {
                        $attrs["{$prop}-{$dir}"] = self::convertCustomProperties($value);
                    }
                }
            }
        }

        // Colors
        if (isset($block['style']['color'])) {
            foreach (['text' => 'color', 'background' => 'background-color'] as $key => $cssKey) {
                $color = $block['style']['color'][$key] ?? null;
                if ($color) {
                    $attrs[$cssKey] = self::convertCustomProperties($color);
                }
            }
        }

        // Convert attributes array to CSS string
        // If no attributes exist, return empty string
        // Otherwise, map each key-value pair to "key:value" format and join with semicolons
        return empty($attrs) ? '' : implode(';', array_map(
            fn ($k, $v) => "{$k}:{$v}",
            array_keys($attrs),
            $attrs
        ));
    }

    /**
     * Retrieves block classes based on back-office settings.
     *
     * @param array $block The block data.
     * @return string The block classes as a space-separated string.
     */
    public static function getBlockClass(array $block): string {
        $classes = [];

        if (isset($block['align'])) {
            $align = $block['align'];
            $classes[] = "align{$align}";
        }

        if (isset($block['className'])) {
            $classes[] = $block['className'];
        }

        if (!empty($classes)) {
            $classes = implode(' ', $classes);
        }

        return $classes;
    }

    /**
     * Gets theme color value from a color label.
     *
     * @param string $label The color label to look up.
     * @param string $returnKey The key to return from the color data (default: 'slug').
     * @return string The color value or empty string if not found.
     */
    public static function getThemeColorsFromLabel(string $label, string $returnKey = 'slug'): string {
        $theme_json = \WP_Theme_JSON_Resolver::get_merged_data('theme');
        $theme_data = $theme_json->get_raw_data();
        $theme_colors = $theme_data['settings']['color']['palette']['theme'] ?? [];

        if (empty($theme_colors)) {
            return '';
        }

        // Use array_filter to find the color with matching name
        $matching_colors = array_filter($theme_colors, function ($color) use ($label) {
            return $color['name'] === $label;
        });

        // If we found a match, return its color value
        if (!empty($matching_colors)) {
            $first_match = reset($matching_colors);

            return $first_match[$returnKey];
        }

        // Return empty string if no match found
        return '';
    }
}
