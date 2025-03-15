<?php

/**
 * Theme customization functions
 *
 * @package SimpppleChild
 * @subpackage ThemeCustomization
 */

declare(strict_types=1);

namespace SimpppleChild\ThemeCustomization;

if (!defined('ABSPATH')) {
    exit;
}

/*
* ================================ Headache
* Remove a bunch of things that are useless for the common humans
* who are using Wordpress
*/

// Disable login screen language switcher
add_filter('login_display_language_dropdown', '__return_false');

/**
 * Disable useless dashboard widgets.
 *
 * @return void
 */
function disable_dashboard_widgets(): void {
    global $wp_meta_boxes;
    // wp..
    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']);
    unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
    unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']);
    unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);
    unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_recent_drafts']);
    // plugins
    unset($wp_meta_boxes['dashboard']['normal']['core']['yoast_db_widget']);
    unset($wp_meta_boxes['dashboard']['normal']['core']['wpseo-dashboard-overview']);
    unset($wp_meta_boxes['dashboard']['normal']['core']['zerospam_dashboard_widget']);
    unset($wp_meta_boxes['dashboard']['normal']['core']['simple_history_dashboard_widget']);
    unset($wp_meta_boxes['dashboard']['normal']['core']['rg_forms_dashboard']);
}
add_action('wp_dashboard_setup', __NAMESPACE__ . '\disable_dashboard_widgets', 999);

/**
 * Disable useless admin toolbar nodes.
 *
 * @param \WP_Admin_Bar $wp_admin_bar The WP_Admin_Bar instance.
 * @return void
 */
function remove_toolbar_node($wp_admin_bar): void {
    $wp_admin_bar->remove_node('new-content');
    $wp_admin_bar->remove_node('comments');
    $wp_admin_bar->remove_node('search');
    $wp_admin_bar->remove_node('wpseo-menu');
    $wp_admin_bar->remove_node('duplicate-post');
    $wp_admin_bar->remove_node('wp-logo');
    $wp_admin_bar->remove_node('redis-cache');
}
add_action('admin_bar_menu', __NAMESPACE__ . '\remove_toolbar_node', 999);

/**
 * Add current post slug to body class.
 *
 * @param array $classes Existing body classes.
 * @return array Modified body classes.
 */
function add_slug_body_class(array $classes): array {
    global $post;
    if (isset($post)) {
        $classes[] = $post->post_type . '__' . $post->post_name;
    }

    return $classes;
}
add_filter('body_class', __NAMESPACE__ . '\add_slug_body_class');

/**
 * Remove author page URL.
 *
 * @param string $link The author link.
 * @return void
 */
function author_page_redirect(string $link): void {
    // No return needed for action callback
}
add_action('author_link', __NAMESPACE__ . '\author_page_redirect');

/**
 * Remove comment author URL.
 *
 * @param string $return The current return value.
 * @param string $author The comment author's name.
 * @param int $comment_ID The comment ID.
 * @return string The comment author's name without a link.
 */
function remove_comment_author_link($return, $author, $comment_ID): string {
    return $author;
}
add_filter('get_comment_author_link', __NAMESPACE__ . '\remove_comment_author_link', 10, 3);

/**
 * Remove comment website URL field.
 *
 * @param array $fields The comment form fields.
 * @return array Modified comment form fields.
 */
function disable_comment_url(array $fields): array {
    unset($fields['url']);

    return $fields;
}
/**
 * Add filter to remove comment website URL field.
 *
 * @return void
 */
function add_comment_url_filter(): void {
    add_filter('comment_form_default_fields', __NAMESPACE__ . '\disable_comment_url', 20);
}
add_action('after_setup_theme', __NAMESPACE__ . '\add_comment_url_filter');

/**
 * Filter function used to remove the tinymce emoji plugin.
 *
 * @param array $plugins The list of TinyMCE plugins.
 * @return array Modified list of TinyMCE plugins.
 */
function disable_emojis_tinymce($plugins): array {
    return array_diff($plugins, ['wpemoji']);
}

/**
 * Remove emoji CDN hostname from DNS prefetching hints.
 *
 * @param array $urls The list of URLs to prefetch.
 * @param string $relation_type The relation type.
 * @return array Modified list of URLs to prefetch.
 */
function disable_emojis_remove_dns_prefetch($urls, $relation_type): array {
    if ('dns-prefetch' == $relation_type) {
        $emoji_svg_url = apply_filters('emoji_svg_url', 'https://s.w.org/images/core/emoji/2/svg/');
        $urls = array_diff($urls, [$emoji_svg_url]);
    }

    return $urls;
}

/**
 * Disable emojis in WordPress.
 *
 * @return void
 */
function disable_emojis(): void {
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
    add_filter('tiny_mce_plugins', __NAMESPACE__ . '\disable_emojis_tinymce');
    add_filter('wp_resource_hints', __NAMESPACE__ . '\disable_emojis_remove_dns_prefetch', 10, 2);
}
add_action('init', __NAMESPACE__ . '\disable_emojis');

/**
 * Remove Dashicons from non-logged-in users.
 *
 * @return void
 */
function adminify_remove_dashicons(): void {
    if (!is_admin_bar_showing() && !is_customize_preview()) {
        wp_dequeue_style('dashicons');
        wp_deregister_style('dashicons');
    }
}
add_action('wp_print_styles', __NAMESPACE__ . '\adminify_remove_dashicons', 100);

// Disable XML RPC
add_filter('xmlrpc_enabled', '__return_false');
add_filter('xmlrpc_methods', '__return_false');

/**
 * Remove jQuery migrate script from the front-end.
 *
 * @param \WP_Scripts $scripts The WP_Scripts instance.
 * @return void
 */
function remove_jquery_migrate($scripts): void {
    if (!is_admin() && isset($scripts->registered['jquery'])) {
        $script = $scripts->registered['jquery'];
        if ($script->deps) {
            // Check whether the script has any dependencies
            $script->deps = array_diff($script->deps, ['jquery-migrate']);
        }
    }
}
add_action('wp_default_scripts', __NAMESPACE__ . '\remove_jquery_migrate');

/**
 * Hide WordPress version.
 *
 * @return string Empty string to hide the version.
 */
function remove_version(): string {
    return '';
}
add_filter('the_generator', __NAMESPACE__ . '\remove_version');
remove_action('wp_head', 'wp_generator');

// Remove wlwmanifest Link
remove_action('wp_head', 'wlwmanifest_link');

// Remove RSD Link
remove_action('wp_head', 'rsd_link');

// Remove generated icons.
remove_action('wp_head', 'wp_site_icon', 99);

// Remove shortlink tag from <head>.
remove_action('wp_head', 'wp_shortlink_wp_head', 10);

// Remove Shortlink
remove_action('template_redirect', 'wp_shortlink_header', 11);

/**
 * Disable RSS feeds.
 *
 * @return void
 */
function disable_feed(): void {
    wp_die(__('No feed available, please visit the <a href="' . esc_url(home_url('/')) . '">homepage</a>!'));
}
add_action('do_feed', __NAMESPACE__ . '\disable_feed', 1);
add_action('do_feed_rdf', __NAMESPACE__ . '\disable_feed', 1);
add_action('do_feed_rss', __NAMESPACE__ . '\disable_feed', 1);
add_action('do_feed_rss2', __NAMESPACE__ . '\disable_feed', 1);
add_action('do_feed_atom', __NAMESPACE__ . '\disable_feed', 1);
add_action('do_feed_rss2_comments', __NAMESPACE__ . '\disable_feed', 1);
add_action('do_feed_atom_comments', __NAMESPACE__ . '\disable_feed', 1);

// Remove RSS Feed links
remove_action('wp_head', 'feed_links_extra', 3);
remove_action('wp_head', 'feed_links', 2);

// Disable comments.
add_filter('comments_open', '__return_false');

// Remove meta rel=dns-prefetch href=//s.w.org
remove_action('wp_head', 'wp_resource_hints', 2);

// Remove relational links for the posts.
remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10);

/**
 * Disable self pingbacks.
 *
 * @param array &$links The list of pingback URLs.
 * @return void
 */
function no_self_ping(array &$links): void {
    $home = get_option('home');
    foreach ($links as $l => $link) {
        if (0 === strpos($link, $home)) {
            unset($links[$l]);
        }
    }
}
add_action('pre_ping', __NAMESPACE__ . '\no_self_ping');

/**
 * https://www.wp-tweaks.com/hackers-can-find-your-wordpress-username/
 * Disable default users API endpoints for security.
 *
 * @param array $endpoints The list of REST API endpoints.
 * @return array Modified list of REST API endpoints.
 */
function disable_rest_endpoints(array $endpoints): array {
    if (!is_user_logged_in()) {
        if (isset($endpoints['/wp/v2/users'])) {
            unset($endpoints['/wp/v2/users']);
        }

        if (isset($endpoints["/wp/v2/users/(?P<id>[\d]+)"])) {
            unset($endpoints["/wp/v2/users/(?P<id>[\d]+)"]);
        }
    }

    return $endpoints;
}
add_filter('rest_endpoints', __NAMESPACE__ . '\disable_rest_endpoints');

// Condition access to REST API
add_filter('rest_authentication_errors', function ($errors) {
    $route = $GLOBALS['wp']->query_vars['rest_route'];

    if (str_contains($route, '/wp/v2/')) {
        if (!is_user_logged_in()) {
            if (!isset($_REQUEST['nonce'])) {
                return new \WP_Error(
                    'rest_not_logged_in',
                    __('Nonce parameter missing from query', 'simppple'),
                    ['status' => 401]
                );
            } else {
                if (!wp_verify_nonce($_REQUEST['nonce'], 'wp_rest')) {
                    return new \WP_Error(
                        'rest_not_logged_in',
                        __('Incorrect nonce parameter in query', 'simppple'),
                        ['status' => 401]
                    );
                }
            }
        }
    }

    return $errors;
});

// Remove REST API links
remove_action('wp_head', 'rest_output_link_wp_head', 10);
remove_action('template_redirect', 'rest_output_link_header', 11);

// Remove oEmbeds.
remove_action('wp_head', 'wp_oembed_add_discovery_links', 10);
remove_action('wp_head', 'wp_oembed_add_host_js');

// Remove YOAST SEO HTML comments
add_filter('wpseo_debug_markers', '__return_false');

// Disable color scheme from user dashboard
remove_action('admin_color_scheme_picker', 'admin_color_scheme_picker');

/**
 * Remove JPEG compression.
 *
 * @return int JPEG quality level.
 */
function remove_jpeg_compression(): int {
    return 100;
}
add_filter('jpeg_quality', __NAMESPACE__ . '\remove_jpeg_compression', 10);

/**
 * https://github.com/WordPress/WordPress/commit/143fd4c1f71fe7d5f6bd7b64c491d9644d861355
 * Remove classic theme styles.
 *
 * @return void
 */
function remove_classic_theme_styles(): void {
    wp_dequeue_style('classic-theme-styles');
}
add_action('wp_enqueue_scripts', __NAMESPACE__ . '\remove_classic_theme_styles');

/**
 * Remove ?ver= query from styles and scripts.
 *
 * @param string $url The URL of the script or style.
 * @return string Modified URL without the version query.
 */
function remove_script_version(string $url): string {
    if (is_admin()) {
        return $url;
    }

    if ($url) {
        return esc_url(remove_query_arg('ver', $url));
    }

    return $url;
}
add_filter('script_loader_src', __NAMESPACE__ . '\remove_script_version', 15, 1);
add_filter('style_loader_src', __NAMESPACE__ . '\remove_script_version', 15, 1);

/**
 * Disable attachment template loading and redirect to 404.
 *
 * @return string The path to the template file.
 */
function attachment_redirect_not_found(): string {
    if (is_attachment()) {
        global $wp_query;
        $wp_query->set_404();
        status_header(404);
    }

    return '';
}
add_filter('template_redirect', __NAMESPACE__ . '\attachment_redirect_not_found', 10);

/**
 * Disable attachment canonical redirect links.
 *
 * @param string $url The canonical URL.
 * @return string The modified URL.
 */
function disable_attachment_canonical_redirect_url(string $url): string {
    attachment_redirect_not_found();

    return $url;
}
add_filter('redirect_canonical', __NAMESPACE__ . '\disable_attachment_canonical_redirect_url', 0, 1);

/**
 * Disable attachment links.
 *
 * @param string $url The attachment URL.
 * @param int $id The attachment ID.
 * @return string The modified attachment URL.
 */
function disable_attachment_link($url, $id): string {
    if ($attachment_url = wp_get_attachment_url($id)) {
        return $attachment_url;
    }

    return $url;
}
add_filter('attachment_link', __NAMESPACE__ . '\disable_attachment_link', 10, 2);

/**
 * Discourage search engines from indexing in non-production environments.
 *
 * @return void
 */
function disable_indexing(): void {
    if (wp_get_environment_type() !== 'production') {
        add_filter('pre_option_blog_public', '__return_false');
    }
}
add_action('init', __NAMESPACE__ . '\disable_indexing');

/*
* ================================ END Headache
*/
