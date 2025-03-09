<?php

namespace SimpppleChild\Blocks\ACF;

/**
 * Converts custom property strings to CSS variable format.
 *
 * @param string $value The custom property string to convert.
 * @return string The converted CSS variable string.
 */
function convert_custom_properties(string $value): string {
    $prefix = 'var:';
    $prefix_len = strlen($prefix);
    $token_in = '|';
    $token_out = '--';
    if (str_starts_with($value, $prefix)) {
        $unwrapped_name = str_replace(
            $token_in,
            $token_out,
            substr($value, $prefix_len)
        );
        $value = "var(--wp--{$unwrapped_name})";
    }

    return $value;
}

/**
 * Returns block style variables as a CSS string.
 *
 * @param array $block The block data.
 * @param string $block_slug The block slug.
 * @return string The CSS style variables.
 */
function get_block_style_variables(array $block, string $block_slug): string {
    $attrs = [];

    $themeJSONDatas = wp_get_global_styles(
        [],
        [
            'block_name' => $block['name'],
            'origin' => 'all',
        ]
    );

    if (!empty($themeJSONDatas)) {
        if (isset($themeJSONDatas['spacing'])) {
            $spacing = $themeJSONDatas['spacing'];

            if (isset($spacing['margin'])) {
                $margins = $spacing['margin'];
                if (!empty($margins)) {
                    foreach ($margins as $key => $margin) {
                        $attrs["--acfblock--{$block_slug}--margin-{$key}"] = $margin;
                    }
                }
            }

            if (isset($spacing['padding'])) {
                $paddings = $spacing['padding'];
                if (!empty($paddings)) {
                    foreach ($paddings as $key => $padding) {
                        $attrs["--acfblock--{$block_slug}--padding-{$key}"] = $padding;
                    }
                }
            }
        }

        if (isset($themeJSONDatas['color'])) {
            $colors = $themeJSONDatas['color'];
            if (!empty($colors)) {
                foreach ($colors as $key => $color) {
                    if ($key === 'text') {
                        $attrs["--acfblock--{$block_slug}--color"] = $color;
                    }
                    if ($key === 'background') {
                        $attrs["--acfblock--{$block_slug}--background-color"] = $color;
                    }
                }
            }
        }

        if (isset($themeJSONDatas['typography'])) {
            $settings = $themeJSONDatas['typography'];
            if (!empty($settings)) {
                foreach ($settings as $key => $setting) {
                    if ($key === 'fontSize') {
                        $attrs["--acfblock--{$block_slug}--font-size"] = $setting;
                    }
                }
            }
        }
    }

    // Retrieve block style edited in Back-office
    if (isset($block['fontSize'])) {
        $fontSize = $block['fontSize'];
        $attrs["--acfblock--{$block_slug}--font-size"] = "var(--wp--preset--font-size--{$fontSize})";
    }

    if (isset($block['textColor'])) {
        $color = $block['textColor'];
        $attrs["--acfblock--{$block_slug}--text-color"] = "var(--wp--preset--color--{$color})";
    }

    if (isset($block['backgroundColor'])) {
        $color = $block['backgroundColor'];
        $attrs["--acfblock--{$block_slug}--background-color"] = "var(--wp--preset--color--{$color})";
    }

    if (isset($block['style'])) {
        $style = $block['style'];

        if (isset($style['spacing'])) {
            $spacing = $style['spacing'];

            if (isset($spacing['margin'])) {
                $margins = $spacing['margin'];
                if (!empty($margins)) {
                    foreach ($margins as $key => $margin) {
                        $attrs["--acfblock--{$block_slug}--margin-{$key}"] = convert_custom_properties($margin);
                    }
                }
            }

            if (isset($spacing['padding'])) {
                $paddings = $spacing['padding'];
                if (!empty($paddings)) {
                    foreach ($paddings as $key => $padding) {
                        $attrs["--acfblock--{$block_slug}--padding-{$key}"] = convert_custom_properties($padding);
                    }
                }
            }
        }

        if (isset($style['color'])) {
            $colors = $style['color'];
            if (!empty($colors)) {
                foreach ($colors as $key => $color) {
                    if ($key === 'text') {
                        $attrs["--acfblock--{$block_slug}--color"] = convert_custom_properties($color);
                    }
                    if ($key === 'background') {
                        $attrs["--acfblock--{$block_slug}--background-color"] = convert_custom_properties($color);
                    }
                }
            }
        }
    }

    $attrsTemp = [];
    if (!empty($attrs)) {
        foreach ($attrs as $key => $value) {
            $attrsTemp[] = "{$key}:{$value}";
        }

        return implode(';', $attrsTemp);
    }

    return '';
}

/**
 * Returns inline block style properties as a CSS string.
 *
 * @param array $block The block data.
 * @return string The inline CSS style properties.
 */
function get_block_style_inline(array $block): string {
    $attrs = [];

    $themeJSONDatas = wp_get_global_styles(
        [],
        [
            'block_name' => $block['name'],
            'origin' => 'all',
        ]
    );

    if (!empty($themeJSONDatas)) {
        if (isset($themeJSONDatas['spacing'])) {
            $spacing = $themeJSONDatas['spacing'];

            if (isset($spacing['margin'])) {
                $margins = $spacing['margin'];
                if (!empty($margins)) {
                    foreach ($margins as $key => $margin) {
                        $attrs["margin-{$key}"] = $margin;
                    }
                }
            }

            if (isset($spacing['padding'])) {
                $paddings = $spacing['padding'];
                if (!empty($paddings)) {
                    foreach ($paddings as $key => $padding) {
                        $attrs["padding-{$key}"] = $padding;
                    }
                }
            }
        }

        if (isset($themeJSONDatas['color'])) {
            $colors = $themeJSONDatas['color'];
            if (!empty($colors)) {
                foreach ($colors as $key => $color) {
                    if ($key === 'text') {
                        $attrs['color'] = $color;
                    }
                    if ($key === 'background') {
                        $attrs['background-color'] = $color;
                    }
                }
            }
        }

        if (isset($themeJSONDatas['typography'])) {
            $settings = $themeJSONDatas['typography'];
            if (!empty($settings)) {
                foreach ($settings as $key => $setting) {
                    if ($key === 'fontSize') {
                        $attrs['font-size'] = $setting;
                    }
                }
            }
        }
    }

    if (isset($block['fontSize'])) {
        $fontSize = $block['fontSize'];
        $attrs['font-size'] = "var(--wp--preset--font-size--{$fontSize})";
    }

    if (isset($block['textColor'])) {
        $color = $block['textColor'];
        $attrs['color'] = "var(--wp--preset--color--{$color})";
    }

    if (isset($block['backgroundColor'])) {
        $color = $block['backgroundColor'];
        $attrs['background-color'] = "var(--wp--preset--color--{$color})";
    }

    if (isset($block['style'])) {
        $style = $block['style'];

        if (isset($style['spacing'])) {
            $spacing = $style['spacing'];

            if (isset($spacing['margin'])) {
                $margins = $spacing['margin'];
                if (!empty($margins)) {
                    foreach ($margins as $key => $margin) {
                        $attrs["margin-{$key}"] = convert_custom_properties($margin);
                    }
                }
            }

            if (isset($spacing['padding'])) {
                $paddings = $spacing['padding'];
                if (!empty($paddings)) {
                    foreach ($paddings as $key => $padding) {
                        $attrs["padding-{$key}"] = convert_custom_properties($padding);
                    }
                }
            }
        }

        if (isset($style['color'])) {
            $colors = $style['color'];
            if (!empty($colors)) {
                foreach ($colors as $key => $color) {
                    if ($key === 'text') {
                        $attrs['color'] = convert_custom_properties($color);
                    }
                    if ($key === 'background') {
                        $attrs['background-color'] = convert_custom_properties($color);
                    }
                }
            }
        }
    }

    $attrsTemp = [];
    if (!empty($attrs)) {
        foreach ($attrs as $key => $value) {
            $attrsTemp[] = "{$key}:{$value}";
        }

        return implode(';', $attrsTemp);
    }

    return '';
}

/**
 * Retrieves block classes based on back-office settings.
 *
 * @param array $block The block data.
 * @return string The block classes as a space-separated string.
 */
function get_block_class(array $block): string {
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
