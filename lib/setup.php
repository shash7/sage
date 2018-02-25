<?php

namespace Roots\Sage\Setup;

use Roots\Sage\Assets;

/**
 * Theme setup
 */
function setup() {
  // Enable features from Soil when plugin is activated
  // https://roots.io/plugins/soil/
  add_theme_support('soil-clean-up');
  add_theme_support('soil-nav-walker');
  add_theme_support('soil-nice-search');
  add_theme_support('soil-jquery-cdn');
  add_theme_support('soil-relative-urls');

  // Make theme available for translation
  // Community translations can be found at https://github.com/roots/sage-translations
  load_theme_textdomain('sage', get_template_directory() . '/lang');

  // Enable plugins to manage the document title
  // http://codex.wordpress.org/Function_Reference/add_theme_support#Title_Tag
  add_theme_support('title-tag');

  // Register wp_nav_menu() menus
  // http://codex.wordpress.org/Function_Reference/register_nav_menus
  register_nav_menus([
    'primary_navigation' => __('Primary Navigation', 'sage')
  ]);

  // Enable post thumbnails
  // http://codex.wordpress.org/Post_Thumbnails
  // http://codex.wordpress.org/Function_Reference/set_post_thumbnail_size
  // http://codex.wordpress.org/Function_Reference/add_image_size
  add_theme_support('post-thumbnails');

  // Enable post formats
  // http://codex.wordpress.org/Post_Formats
  add_theme_support('post-formats', ['aside', 'gallery', 'link', 'image', 'quote', 'video', 'audio']);

  // Enable HTML5 markup support
  // http://codex.wordpress.org/Function_Reference/add_theme_support#HTML5
  add_theme_support('html5', ['caption', 'comment-form', 'comment-list', 'gallery', 'search-form']);

  // Use main stylesheet for visual editor
  // To add custom styles edit /assets/styles/layouts/_tinymce.scss
  add_editor_style(Assets\asset_path('styles/main.css'));
}
add_action('after_setup_theme', __NAMESPACE__ . '\\setup');

/**
 * Theme assets
 */
function assets() {
  wp_enqueue_style('sage/css', Assets\asset_path('styles/main.css'), false, null);

  if (is_single() && comments_open() && get_option('thread_comments')) {
    wp_enqueue_script('comment-reply');
  }

  wp_enqueue_script('sage/js', Assets\asset_path('scripts/main.js'), ['jquery'], null, true);
}
add_action('wp_enqueue_scripts', __NAMESPACE__ . '\\assets', 100);

/**
 * Enable svg uploads
 * https://www.leighton.com/blog/enable-upload-of-svg-to-wordpress-media-library/
 */
function enable_svg_upload( $m ){
  $m['svg']  = 'image/svg+xml';
  $m['svgz'] = 'image/svg+xml';
  return $m;
}
add_filter( 'upload_mimes',  __NAMESPACE__ . '\\enable_svg_upload');

/**
 * Remove emoji support
 */
function remove_emojis() {

  // all actions related to emojis
  remove_action( 'admin_print_styles', 'print_emoji_styles' );
  remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
  remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
  remove_action( 'wp_print_styles', 'print_emoji_styles' );
  remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
  remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
  remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );

  // filter to remove TinyMCE emojis
  add_filter( 'tiny_mce_plugins',  __NAMESPACE__ . '\\disable_emojis_tinymce' );
}
add_action( 'init',  __NAMESPACE__ . '\\remove_emojis');
// Also remove emojis from tinymce
function disable_emojis_tinymce( $plugins ) {
  if ( is_array( $plugins ) ) {
    return array_diff( $plugins, array( 'wpemoji' ) );
  } else {
    return array();
  }
}

/**
 * Remove meta-name-"generator" tag
 */
remove_action('wp_head',  __NAMESPACE__ . '\\wp_generator');


/**
 * Remove dns prefetch
 */
add_filter( 'emoji_svg_url', '__return_false' );

/**
 * Removes jquery from the frontend
 */
function remove_core_jquery() {
  wp_deregister_script( 'jquery' );
  // Change the URL if you want to load a local copy of jQuery from your own server.
  //wp_register_script( 'jquery', "https://code.jquery.com/jquery-3.1.1.min.js", array(), '3.1.1' );
}
add_action( 'wp_enqueue_scripts',  __NAMESPACE__ . '\\remove_core_jquery' );

/**
 * Remove wp api tags
 */
remove_action( 'wp_head', 'rest_output_link_wp_head');
remove_action( 'wp_head', 'wp_oembed_add_discovery_links');
remove_action( 'template_redirect', 'rest_output_link_header', 11, 0 );

/**
 * Remove linkUri tag
 */
remove_action ('wp_head', 'rsd_link');
remove_action ('wp_head', 'rsd_link');

/**
 * Remove wlw tag
 */
remove_action( 'wp_head', 'wlwmanifest_link');

/**
 * Remove wp-embed by default
 */
function remove_wpembed(){
  wp_deregister_script( 'wp-embed' );
}
add_action( 'wp_footer',  __NAMESPACE__ . '\\remove_wpembed' );
