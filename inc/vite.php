<?php

/**
 * Vite integration for WordPress theme
 *
 * @package SimpppleChild
 * @subpackage Vite
 */

declare(strict_types=1);

namespace SimpppleChild\Vite;

if (!defined('ABSPATH')) {
    exit;
}

class Vite {
    private const VITE_SERVER = 'http://localhost:5173';
    private const DIST_FOLDER = 'build';
    private string $distUri;
    private string $distPath;
    private string $version;
    private string $paramsName;
    private array $params;
    private string $textDomain;
    private bool $paramsEnqueued = false;

    public function __construct() {
        $this->distUri = get_stylesheet_directory_uri() . '/' . self::DIST_FOLDER;
        $this->distPath = get_stylesheet_directory() . '/' . self::DIST_FOLDER;
        $this->version = '1.2.0';
        $this->paramsName = 'wpchildparams';
        $this->params = [
            'ajax_url' => admin_url('admin-ajax.php'),
            'rest_url' => esc_url_raw(get_rest_url(null, '/wp/v2')),
            'rest_nonce' => wp_create_nonce('wp_rest'),
            'template_directory' => get_stylesheet_directory_uri(),
            'plugins_directory' => plugins_url(),
            'pictures_directory' => get_stylesheet_directory_uri() . '/build/assets/img',
            'posts_per_page' => get_option('posts_per_page'),
        ];
        $this->textDomain = 'simppple';
    }

    /**
     * Fetch asset information from Vite manifest
     *
     * @param string $fileThemePath Path to the file relative to theme root
     * @param string $assetType Type of asset ('script' or 'style')
     * @return array{
     *  path?: string,
     *  slug?: string,
     *  css?: array<array{path: string, slug: string}>
     * }
     */
    public function fetchAssetFromManifest(string $fileThemePath, string $assetType): array {
        $returnedArray = [];

        $fileName = basename($fileThemePath);
        $fileNameWithoutExtension = substr($fileName, 0, (int) strrpos($fileName, '.'));

        $manifestPath = $this->distPath . '/.vite/manifest.json';
        if (!file_exists($manifestPath)) {
            return $returnedArray;
        }

        /** @var array<string, array{file: string, css?: array<string>}> $manifest */
        $manifest = json_decode(file_get_contents($manifestPath), true);

        $fileKey = array_reduce(
            array_keys($manifest),
            fn(?string $carry, string $asset) => str_contains($asset, $fileName) ? $asset : $carry,
            null
        );

        if ($fileKey && isset($manifest[$fileKey])) {
            $returnedArray = [
                'path' => $this->distUri . "/{$manifest[$fileKey]['file']}",
                'slug' => "simppplechild_vite_{$fileNameWithoutExtension}_{$assetType}"
            ];

            if (isset($manifest[$fileKey]['css']) && !empty($manifest[$fileKey]['css'])) {
                foreach ($manifest[$fileKey]['css'] as $stylePath) {
                    $styleFile = basename($stylePath);
                    $styleFile = substr($styleFile, 0, (int) strrpos($styleFile, '.'));
                    if (strpos($styleFile, 'ver=')) {
                        $styleFile = remove_query_arg('ver', $styleFile);
                    }

                    $returnedArray['css'][] = [
                        'path' => $this->distUri . "/{$stylePath}",
                        'slug' => "simppplechild_vite_{$styleFile}_style"
                    ];
                }
            }
        }

        return $returnedArray;
    }

    /**
     * Enqueue development dependencies
     *
     * @return void
     */
    public function enqueueDevDependencies(): void {
        wp_enqueue_script('jquery');
        wp_enqueue_script('wp-i18n');
        wp_enqueue_script('wp-blocks');

        printf(
            '<script>var %s = %s;</script>',
            $this->paramsName,
            wp_json_encode($this->params)
        );
    }

    /**
     * Enqueue style assets
     *
     * @param string $fileThemePath Path to the file relative to theme root
     * @param string $hookBuild WordPress hook for build mode
     * @param string|false $hookDev WordPress hook for dev mode
     * @return void
     */
    public function enqueueStyle(string $fileThemePath, string $hookBuild, string|false $hookDev = false): void {
        $hookDev = $hookDev ?: $hookBuild;

        $adminAsset = str_contains($hookBuild, 'admin') || str_contains($hookBuild, 'editor');

        if ($adminAsset && !is_admin()) {
            return;
        }

        // @phpstan-ignore booleanAnd.rightAlwaysFalse
        if (defined('SIMPPPLECHILD_IS_VITE_DEVELOPMENT') && SIMPPPLECHILD_IS_VITE_DEVELOPMENT) {
            $themePath = parse_url(get_stylesheet_directory_uri(), PHP_URL_PATH);
            add_action($hookDev, function () use ($themePath, $fileThemePath): void {
                printf(
                    '<link rel="stylesheet" href="%s">',
                    esc_url(self::VITE_SERVER . $themePath . $fileThemePath)
                );
            });

            return;
        }

        $manifestFileInfos = $this->fetchAssetFromManifest($fileThemePath, 'style');
        if (empty($manifestFileInfos)) {
            return;
        }

        $filePath = $manifestFileInfos['path'];
        $fileSlug = $manifestFileInfos['slug'];

        add_action(
            $hookBuild,
            function () use ($fileSlug, $filePath): void {
                wp_enqueue_style(
                    $fileSlug,
                    $filePath,
                    [],
                    $this->version,
                    'all'
                );
            },
            21
        );
    }

    /**
     * Enqueue script assets
     *
     * @param string $fileThemePath Path to the file relative to theme root
     * @param string $hookBuild WordPress hook for build mode
     * @param string|false $hookDev WordPress hook for dev mode
     * @param bool $footerEnqueue Whether to enqueue in footer
     * @param string $type Script type (e.g., 'module')
     * @param int $order Action priority
     * @return void
     */
    public function enqueueScript(
        string $fileThemePath,
        string $hookBuild,
        string|false $hookDev = false,
        bool $footerEnqueue = true,
        string $type = '',
        int $order = 21
    ): void {
        $hookDev = $hookDev ?: $hookBuild;

        $adminAsset = str_contains($hookBuild, 'admin') || str_contains($hookBuild, 'editor');

        if ($adminAsset && !is_admin()) {
            return;
        }

        // @phpstan-ignore booleanAnd.rightAlwaysFalse
        if (defined('SIMPPPLECHILD_IS_VITE_DEVELOPMENT') && SIMPPPLECHILD_IS_VITE_DEVELOPMENT) {
            $themePath = parse_url(get_stylesheet_directory_uri(), PHP_URL_PATH);

            remove_action($hookDev, [$this, 'enqueueDevDependencies']);
            add_action($hookDev, [$this, 'enqueueDevDependencies']);
            add_action($hookDev, function () use ($themePath, $fileThemePath): void {
                printf(
                    '<script type="module" crossorigin src="%s"></script>',
                    esc_url(self::VITE_SERVER . $themePath . $fileThemePath)
                );
            });

            return;
        }

        $manifestFileInfos = $this->fetchAssetFromManifest($fileThemePath, 'script');
        if (empty($manifestFileInfos)) {
            return;
        }

        // Canvas CSS must not ride enqueue_block_editor_assets: Gutenberg copies
        // those sheets into the iframe via a compat layer and warns. editor.js
        // CSS is loaded separately via enqueueStyleEditor() → enqueue_block_assets.
        $enqueueAssociatedCss = 'enqueue_block_editor_assets' !== $hookBuild;

        if ($enqueueAssociatedCss && isset($manifestFileInfos['css'])) {
            foreach ($manifestFileInfos['css'] as $style) {
                add_action(
                    $hookBuild,
                    function () use ($style): void {
                        wp_enqueue_style(
                            $style['slug'],
                            $style['path'],
                            [],
                            $this->version,
                            'all'
                        );
                    },
                    $order
                );
            }
        }

        $filePath = $manifestFileInfos['path'];
        $fileSlug = $manifestFileInfos['slug'];

        add_action(
            $hookBuild,
            function () use ($fileSlug, $filePath, $footerEnqueue, $type): void {
                if ($type === 'module') {
                    // Script modules cannot use wp_localize_script. Print the classic global
                    // so existing JS (`wpchildparams`, `wp.i18n`) keeps working.
                    wp_enqueue_script('wp-i18n');
                    $this->enqueueParamsAsGlobal();

                    wp_register_script_module(
                        $fileSlug,
                        $filePath,
                        [],
                        $this->version
                    );

                    add_filter(
                        "script_module_data_{$fileSlug}",
                        function (array $data) use ($fileSlug, $filePath): array {
                            $translationsPath = get_stylesheet_directory() . '/lang';
                            $jsonTranslations = $this->loadScriptTranslations($fileSlug, $filePath, $this->textDomain, $translationsPath);

                            if ($jsonTranslations) {
                                $data['translations'] = json_decode($jsonTranslations, true);
                                $data['textDomain'] = $this->textDomain;
                            }

                            $data['params'] = $this->params;
                            $data['paramsName'] = $this->paramsName;

                            return $data;
                        }
                    );

                    wp_enqueue_script_module($fileSlug);
                } else {
                    wp_register_script(
                        $fileSlug,
                        $filePath,
                        ['wp-i18n', 'jquery'],
                        $this->version,
                        [
                            'in_footer' => $footerEnqueue,
                            'strategy' => 'defer'
                        ]
                    );

                    wp_set_script_translations(
                        $fileSlug,
                        $this->textDomain,
                        get_stylesheet_directory() . '/lang'
                    );

                    wp_localize_script(
                        $fileSlug,
                        $this->paramsName,
                        $this->params
                    );

                    wp_enqueue_script($fileSlug, $filePath);
                }
            },
            $order
        );
    }

    /**
     * Print `wpchildparams` as a classic global. `wp_localize_script` does not run for script modules.
     */
    private function enqueueParamsAsGlobal(): void {
        if ($this->paramsEnqueued) {
            return;
        }

        $this->paramsEnqueued = true;
        $handle = 'simppplechild_vite_params';

        wp_register_script($handle, '', [], $this->version, false);
        wp_localize_script($handle, $this->paramsName, $this->params);
        wp_enqueue_script($handle);
    }

    /**
     * Load script translations for a given handle
     *
     * @param string $handle Script handle
     * @param string $src Script source URL
     * @param string $domain Text domain
     * @param string $path Path to translation files
     * @return string|false JSON-encoded translations or false on failure
     */
    private function loadScriptTranslations(string $handle, string $src, string $domain, string $path): string|false {
        if (!function_exists('load_script_textdomain')) {
            return false;
        }

        // Register a dummy (never enqueued) so load_script_textdomain() can
        // resolve the .json file from the script src. Left registered on
        // purpose: wp_deregister_script() trips Theme Check.
        $tempHandle = $handle . '_temp_translations';
        wp_register_script($tempHandle, $src, [], $this->version);
        wp_set_script_translations($tempHandle, $domain, $path);

        return load_script_textdomain($tempHandle, $domain, $path);
    }

    /**
     * Enqueue editor canvas styles inside the iframed editor.
     *
     * `enqueue_block_assets` + is_admin() is the supported path. add_editor_style()
     * and enqueue_block_editor_assets both inject via the iframe compat layer.
     *
     * @param string $fileThemePath Path to the file relative to theme root
     * @param int $order Action priority
     * @return void
     */
    public function enqueueStyleEditor(string $fileThemePath, int $order = 20): void {
        $manifestFileInfos = $this->fetchAssetFromManifest($fileThemePath, 'script');
        if (empty($manifestFileInfos['css'])) {
            return;
        }

        add_action(
            'enqueue_block_assets',
            function () use ($manifestFileInfos): void {
                if (!is_admin()) {
                    return;
                }

                foreach ($manifestFileInfos['css'] as $style) {
                    wp_enqueue_style(
                        $style['slug'],
                        $style['path'],
                        [],
                        $this->version,
                        'all'
                    );
                }
            },
            $order
        );
    }
}
