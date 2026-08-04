<?php

//// Add RSS links to <head> section
//automatic_feed_links();

if (!function_exists('tuankhang_setup')) :
    /**
     * Sets up theme defaults and registers support for various WordPress features.
     *
     * Note that this function is hooked into the after_setup_theme hook, which
     * runs before the init hook. The init hook is too late for some features, such
     * as indicating support for post thumbnails.
     */
    function tuankhang_setup()
    {
        /*
         * Make theme available for translation.
         * Translations can be filed in the /languages/ directory.
         * If you're building a theme based on tuankhang, use a find and replace
         * to change 'tuankhang' to the name of your theme in all the template files.
         */
        load_theme_textdomain('tuankhang', get_template_directory() . '/languages');

        // Add default posts and comments RSS feed links to head.
        add_theme_support('automatic-feed-links');

        /*
         * Let WordPress manage the document title.
         * By adding theme support, we declare that this theme does not use a
         * hard-coded <title> tag in the document head, and expect WordPress to
         * provide it for us.
         */
        add_theme_support('title-tag');

        /*
         * Enable support for Post Thumbnails on posts and pages.
         *
         * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
         */
        add_theme_support('post-thumbnails');


        /*
         * Switch default core markup for search form, comment form, and comments
         * to output valid HTML5.
         */
        add_theme_support('html5', array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
        ));
    }
endif;
add_action('after_setup_theme', 'tuankhang_setup');

require_once get_theme_file_path('/inc/home-tailwind.php');
require_once get_theme_file_path('/inc/home-components.php');
require_once get_theme_file_path('/inc/content-tailwind.php');
require_once get_theme_file_path('/inc/product-tailwind.php');
require_once get_theme_file_path('/inc/seo-tailwind.php');
require_once get_theme_file_path('/inc/performance-tailwind.php');

function tk_is_product_search()
{
    if (!is_search()) {
        return false;
    }
    $post_type = get_query_var('post_type');
    return $post_type === 'san-pham' || (is_array($post_type) && in_array('san-pham', $post_type, true));
}

function tk_is_product_tailwind_context()
{
    return is_post_type_archive('san-pham')
        || is_tax('danh-muc')
        || is_singular('san-pham')
        || tk_is_product_search();
}

function tk_is_tailwind_context()
{
    return !is_admin();
}

function tk_get_frontend_context()
{
    if (is_front_page()) {
        return 'home';
    }
    if (tk_is_product_tailwind_context()) {
        return 'products';
    }
    return 'content';
}

function tk_enqueue_built_style($handle, $relative, $dependencies = array())
{
    $path = get_theme_file_path($relative);
    if (is_file($path)) {
        wp_enqueue_style($handle, get_theme_file_uri($relative), $dependencies, (string) filemtime($path));
    }
}

function tk_enqueue_built_script($handle, $relative, $dependencies = array())
{
    $path = get_theme_file_path($relative);
    if (is_file($path)) {
        wp_enqueue_script($handle, get_theme_file_uri($relative), $dependencies, (string) filemtime($path), array('in_footer' => true, 'strategy' => 'defer'));
    }
}

function tuankhang_enqueue_frontend_assets()
{
    if (is_admin()) {
        return;
    }

    $context = tk_get_frontend_context();
    $style_context = is_singular('san-pham') ? 'product-detail' : $context;
    tk_enqueue_built_style('tuankhang-site', '/assets/dist/site.min.css');
    tk_enqueue_built_style('tuankhang-' . $style_context, '/assets/dist/' . $style_context . '.min.css', array('tuankhang-site'));
    tk_enqueue_built_script('tuankhang-site', '/assets/dist/site.min.js');
    tk_enqueue_built_script('tuankhang-' . $context, '/assets/dist/' . $context . '.min.js', array('tuankhang-site'));
}
add_action('wp_enqueue_scripts', 'tuankhang_enqueue_frontend_assets', 1);

function tuankhang_preload_interface_fonts()
{
    if (is_admin()) {
        return;
    }

    $language = tk_home_language();
    $files = array('manrope-latin.woff2', 'source-serif-4-latin.woff2');
    if ($language !== 'en') {
        array_unshift($files, 'manrope-vietnamese.woff2', 'source-serif-4-vietnamese.woff2');
    }

    foreach ($files as $file) {
        printf(
            '<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
            esc_url(get_theme_file_uri('/assets/dist/fonts/' . $file))
        );
    }
}
add_action('wp_head', 'tuankhang_preload_interface_fonts', 2);

function tuankhang_prune_tailwind_assets()
{
    if (is_admin()) {
        return;
    }

    $style_handles = array('contact-form-7', 'wp-pagenavi');
    $script_handles = array('jquery', 'jquery-core', 'jquery-migrate');
    if (!is_singular('san-pham')) {
        $script_handles = array_merge($script_handles, array('contact-form-7', 'wpcf7-recaptcha'));
    }
    foreach ($style_handles as $handle) {
        wp_dequeue_style($handle);
        wp_deregister_style($handle);
    }
    foreach ($script_handles as $handle) {
        wp_dequeue_script($handle);
        wp_deregister_script($handle);
    }
}
add_action('wp_enqueue_scripts', 'tuankhang_prune_tailwind_assets', 999);
add_image_size('image-cat-trangchu', 274, 240, true);
function remove_max_srcset_image_width($max_width)
{
    return $max_width;
}

function removeHeadLinks()
{
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wlwmanifest_link');
}

add_action('init', 'removeHeadLinks');
remove_action('wp_head', 'wp_generator');

// Declare sidebar widget zone
if (function_exists('register_sidebar')) {
    register_sidebar(array(
        'name' => 'Sidebar Widgets',
        'id' => 'sidebar-widgets',
        'description' => 'These are widgets for the sidebar.',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h2>',
        'after_title' => '</h2>'
    ));
}
add_filter('use_block_editor_for_post', '__return_false');
function mySearchFilter($query)
{
    $post_type = isset($_GET['type']) && is_string($_GET['type'])
        ? sanitize_key(wp_unslash($_GET['type']))
        : '';
    if (!$post_type || !post_type_exists($post_type)) {
        $post_type = 'san-pham';
    }
    if ($query->is_search) {
        $query->set('post_type', $post_type);
    };
    return $query;
}

;
add_filter('pre_get_posts', 'mySearchFilter');
function remove_cpt_slug($post_link, $post, $leavename)
{
    //Get all the Custom Post Types
    $args = array(
        'public' => true, //Only the Public CPTs
        '_builtin' => false); //Exclude Built-in Post Types (Posts and Pages)
    $cpts = get_post_types($args, 'objects');
    foreach ($cpts as $cpt) {
        //Replace the slug
        $post_link = str_replace('/' . $post->post_type . '/', '/', $post_link . '.html');
    }
    return $post_link;
}

//add_filter('post_type_link', 'remove_cpt_slug', 10, 3);
function include_cpt_query($query)
{
    // Only loop the main query
    if (!$query->is_main_query())
        return;
    // Only loop our very specific rewrite rule match
    if (2 != count($query->query) || !isset($query->query['page'])) {
        return;
    }
    // 'name' will be set if post permalinks are just post_name, otherwise the page rule will match
    if (!empty($query->query['name'])) {
        $query->set('post_type', get_post_types());
    }
}

add_action('pre_get_posts', 'include_cpt_query');

function attachment_image_link_remove_filter($content)
{
    $content =
        preg_replace(
            array('{<a[^>]*><img}', '{/></a>}'),
            array('<img', '/>'),
            $content
        );
    return $content;
}

add_filter('the_content', 'attachment_image_link_remove_filter');
add_filter('max_srcset_image_width', 'remove_max_srcset_image_width');
function wdo_disable_srcset($sources)
{
    return $sources;
}

add_filter('wp_calculate_image_srcset', 'wdo_disable_srcset');

function tk_disable_emoji_assets_for_tailwind()
{
    if (is_admin()) {
        return;
    }
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('wp_print_styles', 'wp_enqueue_emoji_styles');
}
add_action('wp', 'tk_disable_emoji_assets_for_tailwind', 0);

add_action('admin_init', 'hide_editor');
function hide_editor()
{
    // Get the Post ID.
    $post_id = $_GET['post'] ? $_GET['post'] : $_POST['post_ID'];
    if (!isset($post_id)) return;
    // Hide the editor on a page with a specific page template
    // Get the name of the Page Template file.
    $template_file = get_post_meta($post_id, '_wp_page_template', true);
    if ($template_file == 'page-caidat.php') { // the filename of the page template
        remove_post_type_support('page', 'editor');
    }
}

function custom_admin_css_dinh()
{
    echo '<style type="text/css">
           body.wp-admin .wpt-image {margin-top: 70px !important;overflow: hidden;}
           body.wp-admin .wpt-image .wpt-form-item-textfield {float: right;width: 72%;margin-top:20px}
           body.wp-admin .wpt-image .wpt-file-preview {float: left; width: 25%;}
           body.wp-admin .wpt-image .wpt-file-preview img {height: auto;width: 100%;}
           .postbox-header .handle-actions {display: none !important;}
         </style>';
}

add_action('admin_head', 'custom_admin_css_dinh');
