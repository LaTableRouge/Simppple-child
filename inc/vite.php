<?php

if (!defined('ABSPATH')) {
    exit;
}

define('SIMPPPLECHILD_VITE_SERVER', 'http://localhost:5173');
define('SIMPPPLECHILD_DIST_FOLDER', 'build');
define('SIMPPPLECHILD_DIST_URI', get_stylesheet_directory_uri() . '/' . SIMPPPLECHILD_DIST_FOLDER);
define('SIMPPPLECHILD_DIST_PATH', get_template_directory() . '/' . SIMPPPLECHILD_DIST_FOLDER);

function simppplechild_vite_fetch_asset_from_manifest($fileThemePath, $assetType) {
    $returnedArray = [];

    $fileName = basename($fileThemePath);
    $fileNameWithoutExtension = substr($fileName, 0, strrpos($fileName, '.'));

    // Use manifest json to know which asset to enqueue
    if (file_exists(SIMPPPLECHILD_DIST_PATH . '/.vite/manifest.json')) {
        $manifest = json_decode(file_get_contents(SIMPPPLECHILD_DIST_PATH . '/.vite/manifest.json'), true);

        if (is_array($manifest)) {
            $manifest_keys = array_keys($manifest);
            $fileKey = null;
            foreach ($manifest_keys as $key => $asset) {
                if (str_contains($asset, $fileName)) {
                    $fileKey = $asset;
                    break;
                }
            }

            if ($fileKey && isset($manifest[$fileKey])) {
                $returnedArray = [
                    'path' => SIMPPPLECHILD_DIST_URI . "/{$manifest[$fileKey]['file']}",
                    'slug' => "simppplechild_vite_{$fileNameWithoutExtension}_{$assetType}"
                ];

                // In case of scss files included in Javascript
                if (isset($manifest[$fileKey]['css']) && !empty($manifest[$fileKey]['css'])) {
                    foreach ($manifest[$fileKey]['css'] as $stylePath) {
                        $styleFile = basename($stylePath);
                        $styleFileWithoutExtension = substr($styleFile, 0, strrpos($styleFile, '.'));
                        $styleFileWithoutVersionning = substr($styleFile, 0, strrpos($styleFileWithoutExtension, '.'));

                        $returnedArray['css'][] = [
                            'path' => SIMPPPLECHILD_DIST_URI . "/{$stylePath}",
                            'slug' => "simppplechild_vite_{$styleFileWithoutVersionning}_style"
                        ];
                    }
                }
            }
        }
    }

    return $returnedArray;
}

function simppplechild_vite_enqueue_dev_dependencies() {
    wp_enqueue_script('jquery');
    wp_enqueue_script('wp-i18n');
    wp_enqueue_script('wp-blocks');
    echo "<script>
            const wpparams = {
                ajax_url: '" . admin_url('admin-ajax.php') . "',
                rest_url: '" . esc_url_raw(get_rest_url(null, '/wp/v2')) . "',
                rest_nonce: '" . wp_create_nonce('wp_rest') . "',
                template_directory: '" . get_stylesheet_directory_uri() . "',
                plugins_directory: '" . plugins_url() . "',
                pictures_directory: '" . get_stylesheet_directory_uri() . '/build/assets/img' . "',
                posts_per_page: '" . get_option('posts_per_page') . "'
            }
        </script>";
}

function simppplechild_vite_enqueue_style($fileThemePath, $hookBuild, $hookDev = false) {
    if (!$hookDev) {
        $hookDev = $hookBuild;
    }

    $adminAsset = str_contains($hookBuild, 'admin') || str_contains($hookBuild, 'block') || str_contains($hookBuild, 'editor');
    if ($adminAsset && !is_admin()) {
        return;
    }

    if (defined('SIMPPPLECHILD_IS_VITE_DEVELOPMENT') && SIMPPPLECHILD_IS_VITE_DEVELOPMENT === true) {
        /*
        * ================================ Inject assets in DOM
        * insert link tag for styles
        */
        $themePath = get_stylesheet_directory_uri();
        $themePath = parse_url($themePath, PHP_URL_PATH);

        add_action($hookDev, function () use ($themePath, $fileThemePath) {
            echo '<link rel="stylesheet" href="' . SIMPPPLECHILD_VITE_SERVER . $themePath . $fileThemePath . '">';
        });
    } else {
        /*
        * ================================ Call assets with WP hooks
        */
        $manifestFileInfos = simppplechild_vite_fetch_asset_from_manifest($fileThemePath, 'style');
        if (!empty($manifestFileInfos)) {
            $filePath = $manifestFileInfos['path'];
            $fileSlug = $manifestFileInfos['slug'];
            add_action(
                $hookBuild,
                function () use ($fileSlug, $filePath) {
                    wp_enqueue_style(
                        $fileSlug,
                        $filePath,
                        [],
                        time(),
                        'all'
                    );
                },
                20
            );
        }
    }
}

function simppplechild_vite_enqueue_script($fileThemePath, $hookBuild, $hookDev = false, $footerEnqueue = true) {
    if (!$hookDev) {
        $hookDev = $hookBuild;
    }

    $adminAsset = str_contains($hookBuild, 'admin') || str_contains($hookBuild, 'block') || str_contains($hookBuild, 'editor');
    if ($adminAsset && !is_admin()) {
        return;
    }

    if (defined('SIMPPPLECHILD_IS_VITE_DEVELOPMENT') && SIMPPPLECHILD_IS_VITE_DEVELOPMENT === true) {
        /*
        * ================================ Inject assets in DOM
        * insert script tag for scripts
        */
        $themePath = get_stylesheet_directory_uri();
        $themePath = parse_url($themePath, PHP_URL_PATH);

        remove_action($hookDev, 'simppplechild_vite_enqueue_dev_dependencies');
        add_action($hookDev, 'simppplechild_vite_enqueue_dev_dependencies');
        add_action($hookDev, function () use ($themePath, $fileThemePath) {
            echo '<script type="module" crossorigin src="' . SIMPPPLECHILD_VITE_SERVER . $themePath . $fileThemePath . '"></script>';
        });
    } else {
        /*
        * ================================ Call assets with WP hooks
        */
        $manifestFileInfos = simppplechild_vite_fetch_asset_from_manifest($fileThemePath, 'script');
        if (!empty($manifestFileInfos)) {
            if (isset($manifestFileInfos['css'])) {
                foreach ($manifestFileInfos['css'] as $style) {
                    $filePath = $style['path'];
                    $fileSlug = $style['slug'];
                    add_action(
                        $hookBuild,
                        function () use ($fileSlug, $filePath) {
                            wp_enqueue_style(
                                $fileSlug,
                                $filePath,
                                [],
                                time(),
                                'all'
                            );
                        },
                        20
                    );
                }
            }

            $filePath = $manifestFileInfos['path'];
            $fileSlug = $manifestFileInfos['slug'];
            add_action(
                $hookBuild,
                function () use ($fileSlug, $filePath, $footerEnqueue) {
                    wp_register_script(
                        $fileSlug,
                        $filePath,
                        [], // Libraries to use
                        time(),
                        [
                            'in_footer' => $footerEnqueue,
                            'strategy' => 'defer'
                        ]
                    );

                    wp_set_script_translations(
                        $fileSlug,
                        'simppple',
                        get_template_directory() . '/lang'
                    );

                    wp_localize_script(
                        $fileSlug,
                        'wpparams',
                        [
                            'ajax_url' => admin_url('admin-ajax.php'),
                            'rest_url' => esc_url_raw(get_rest_url(null, '/wp/v2')),
                            'rest_nonce' => wp_create_nonce('wp_rest'),
                            'template_directory' => get_stylesheet_directory_uri(),
                            'plugins_directory' => plugins_url(),
                            'pictures_directory' => get_stylesheet_directory_uri() . '/build/assets/img',
                            'posts_per_page' => get_option('posts_per_page'),
                        ]
                    );

                    wp_enqueue_script($fileSlug);
                },
                20
            );
        }
    }
}

function simppplechild_vite_enqueue_style_editor($fileThemePath, $hook) {
    /*
    * ================================ Call assets with WP hooks
    */
    $manifestFileInfos = simppplechild_vite_fetch_asset_from_manifest($fileThemePath, 'script');
    if (!empty($manifestFileInfos)) {
        $filePath = $manifestFileInfos['path'];

        add_action(
            $hook,
            function () use ($filePath) {
                add_editor_style($filePath);
            },
            20
        );
    } else {
        echo 'Please compile (build/prod) to see the editor style';
    }
}
