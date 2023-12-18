<?php

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

// Disable useless dashboard widgets
function simppplechild_disable_dashboard_widgets() {
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
add_action('wp_dashboard_setup', 'simppplechild_disable_dashboard_widgets', 999);

// Disable useless admin toolbar node
function simppplechild_remove_toolbar_node($wp_admin_bar) {
    $wp_admin_bar->remove_node('new-content');
    $wp_admin_bar->remove_node('comments');
    $wp_admin_bar->remove_node('search');
    $wp_admin_bar->remove_node('wpseo-menu');
    $wp_admin_bar->remove_node('duplicate-post');
    $wp_admin_bar->remove_node('wp-logo');
    $wp_admin_bar->remove_node('redis-cache');
}
add_action('admin_bar_menu', 'simppplechild_remove_toolbar_node', 999);

// Add current post slug to body class
function simppplechild_add_slug_body_class($classes) {
    global $post;
    if (isset($post)) {
        $classes[] = $post->post_type . '__' . $post->post_name;
    }

    return $classes;
}
add_filter('body_class', 'simppplechild_add_slug_body_class');

// Remove author page URL
function simppplechild_author_page_redirect($link) {
    $link = '';

    return $link;
}
add_action('author_link', 'simppplechild_author_page_redirect');

// Remove comment author URL
function simppplechild_remove_comment_author_link($return, $author, $comment_ID) {
    return $author;
}
add_filter('get_comment_author_link', 'simppplechild_remove_comment_author_link', 10, 3);

// Remove comment website URL
function simppplechild_disable_comment_url($fields) {
    unset($fields['url']);

    return $fields;
}
function simppplechild_add_comment_url_filter() {
    add_filter('comment_form_default_fields', 'simppplechild_disable_comment_url', 20);
}
add_action('after_setup_theme', 'simppplechild_add_comment_url_filter');

// Disable emoji's
//Filter function used to remove the tinymce emoji plugin.
function simppplechild_disable_emojis_tinymce($plugins) {
    if (is_array($plugins)) {
        return array_diff($plugins, ['wpemoji']);
    } else {
        return [];
    }
}

//Remove emoji CDN hostname from DNS prefetching hints.
function simppplechild_disable_emojis_remove_dns_prefetch($urls, $relation_type) {
    if ('dns-prefetch' == $relation_type) {
        $emoji_svg_url = apply_filters('emoji_svg_url', 'https://s.w.org/images/core/emoji/2/svg/');
        $urls = array_diff($urls, [$emoji_svg_url]);
    }

    return $urls;
}

function simppplechild_disable_emojis() {
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
    add_filter('tiny_mce_plugins', 'simppplechild_disable_emojis_tinymce');
    add_filter('wp_resource_hints', 'simppplechild_disable_emojis_remove_dns_prefetch', 10, 2);
}
add_action('init', 'simppplechild_disable_emojis');

// Disable Dashicons CSS for non-logged-in users
// Remove Dashicons from Admin Bar for non logged in users
function simppplechild_adminify_remove_dashicons() {
    if (!is_admin_bar_showing() && !is_customize_preview()) {
        wp_dequeue_style('dashicons');
        wp_deregister_style('dashicons');
    }
}
add_action('wp_print_styles', 'simppplechild_adminify_remove_dashicons', 100);

// Disable XML RPC
add_filter('xmlrpc_enabled', '__return_false');
add_filter('xmlrpc_methods', '__return_false');

// Disable jQuery migrate
function simppplechild_remove_jquery_migrate($scripts) {
    if (!is_admin() && isset($scripts->registered['jquery'])) {
        $script = $scripts->registered['jquery'];
        if ($script->deps) {
            // Check whether the script has any dependencies
            $script->deps = array_diff($script->deps, ['jquery-migrate']);
        }
    }
}
add_action('wp_default_scripts', 'simppplechild_remove_jquery_migrate');

// Hide WP version
function simppplechild_remove_version() {
    return '';
}
add_filter('the_generator', 'simppplechild_remove_version');
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

// Disable RSS Feeds
function simppplechild_disable_feed() {
    wp_die(__('No feed available, please visit the <a href="' . esc_url(home_url('/')) . '">homepage</a>!'));
}
add_action('do_feed', 'simppplechild_disable_feed', 1);
add_action('do_feed_rdf', 'simppplechild_disable_feed', 1);
add_action('do_feed_rss', 'simppplechild_disable_feed', 1);
add_action('do_feed_rss2', 'simppplechild_disable_feed', 1);
add_action('do_feed_atom', 'simppplechild_disable_feed', 1);
add_action('do_feed_rss2_comments', 'simppplechild_disable_feed', 1);
add_action('do_feed_atom_comments', 'simppplechild_disable_feed', 1);

// Remove RSS Feed links
remove_action('wp_head', 'feed_links_extra', 3);
remove_action('wp_head', 'feed_links', 2);

// Disable comments.
add_filter('comments_open', '__return_false');

// Remove meta rel=dns-prefetch href=//s.w.org
remove_action('wp_head', 'wp_resource_hints', 2);

// Remove relational links for the posts.
remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10);

// Disable Self Pingbacks
function simppplechild_no_self_ping(&$links) {
    $home = get_option('home');
    foreach ($links as $l => $link) {
        if (0 === strpos($link, $home)) {
            unset($links[$l]);
        }
    }
}
add_action('pre_ping', 'simppplechild_no_self_ping');

// Disable default users API endpoints for security.
// https://www.wp-tweaks.com/hackers-can-find-your-wordpress-username/
function simppplechild_disable_rest_endpoints(array $endpoints): array {
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
add_filter('rest_endpoints', 'simppplechild_disable_rest_endpoints');

// Condition access to REST API
add_filter('rest_authentication_errors', function ($errors) {
    $route = $GLOBALS['wp']->query_vars['rest_route'];

    if (str_contains($route, '/wp/v2/')) {
        if (!is_user_logged_in()) {
            if (!isset($_REQUEST['nonce'])) {
                return new WP_Error(
                    'rest_not_logged_in',
                    __('Nonce parameter missing from query', 'mainsdejardin'),
                    ['status' => 401]
                );
            } else {
                if (!wp_verify_nonce($_REQUEST['nonce'], 'wp_rest')) {
                    return new WP_Error(
                        'rest_not_logged_in',
                        __('Incorrect nonce parameter in query', 'mainsdejardin'),
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

// Remove JPEG compression.
function simppplechild_remove_jpeg_compression() {
    return 100;
}
add_filter('jpeg_quality', 'simppplechild_remove_jpeg_compression', 10, 2);

// Remove classic theme styles.
// https://github.com/WordPress/WordPress/commit/143fd4c1f71fe7d5f6bd7b64c491d9644d861355
function simppplechild_remove_classic_theme_styles() {
    wp_dequeue_style('classic-theme-styles');
}
add_action('wp_enqueue_scripts', 'simppplechild_remove_classic_theme_styles');

// Remove ?ver= query from styles and scripts.
function simppplechild_remove_script_version(string $url): string {
    if (is_admin()) {
        return $url;
    }

    if ($url) {
        return esc_url(remove_query_arg('ver', $url));
    }

    return $url;
}
add_filter('script_loader_src', 'simppplechild_remove_script_version', 15, 1);
add_filter('style_loader_src', 'simppplechild_remove_script_version', 15, 1);

// Disable attachment template loading and redirect to 404.
function simppplechild_attachment_redirect_not_found() {
    if (is_attachment()) {
        global $wp_query;
        $wp_query->set_404();
        status_header(404);
    }
}
add_filter('template_redirect', 'simppplechild_attachment_redirect_not_found');

// Disable attachment canonical redirect links.
function simppplechild_disable_attachment_canonical_redirect_url($url) {
    simppplechild_attachment_redirect_not_found();

    return $url;
}
add_filter('redirect_canonical', 'simppplechild_disable_attachment_canonical_redirect_url', 0, 2);

// Disable attachment links.
function simppplechild_disable_attachment_link($url, $id) {
    if ($attachment_url = wp_get_attachment_url($id)) {
        return $attachment_url;
    }

    return $url;
}
add_filter('attachment_link', 'simppplechild_disable_attachment_link', 10, 2);

// Discourage search engines from indexing in non-production environments.
function simppplechild_disable_indexing() {
    return wp_get_environment_type() === 'production' ? true : 0;
}
add_action('pre_option_blog_public', 'simppplechild_disable_indexing');

/*
* ================================ END Headache
*/