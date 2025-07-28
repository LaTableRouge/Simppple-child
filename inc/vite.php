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

    public function __construct() {
        $this->distUri = get_stylesheet_directory_uri() . '/' . self::DIST_FOLDER;
        $this->distPath = get_stylesheet_directory() . '/' . self::DIST_FOLDER;
        $this->version = '1.1.3';
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

        // Enqueue associated CSS files
        if (isset($manifestFileInfos['css'])) {
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

                if ($type === 'module') {
                    wp_enqueue_script_module($fileSlug, $filePath);
                } else {
                    wp_enqueue_script($fileSlug, $filePath);
                }
            },
            $order
        );
    }

    /**
     * Enqueue editor styles
     *
     * @param string $fileThemePath Path to the file relative to theme root
     * @param string $hook WordPress hook
     * @param int $order Action priority
     * @return void
     */
    public function enqueueStyleEditor(string $fileThemePath, string $hook, int $order = 20): void {
        $manifestFileInfos = $this->fetchAssetFromManifest($fileThemePath, 'script');
        if (empty($manifestFileInfos)) {
            echo 'Please compile (build/prod) to see the editor style';

            return;
        }

        $filePath = $manifestFileInfos['path'];
        add_action(
            $hook,
            function () use ($filePath): void {
                add_editor_style($filePath);
            },
            $order
        );
    }
}
