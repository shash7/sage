<?php

namespace Roots\Sage\Whitelabel;


/**
 * replace login logo url
 */
function logo_url($url) {
  return get_bloginfo('url');
}
add_filter( 'login_headerurl', __NAMESPACE__ . '\\logo_url' );

/**
 * Replace login logo image
 */
function login_logo() {
  ?>
    <style type="text/css">
      #login h1 a, .login h1 a {
        background-image: url(<?php echo get_stylesheet_directory_uri(); ?>/dist/images/MAB_ICONS_mab_logo.svg);
        /* Logo dimensions */
        height:35px;
        width:112px;
        background-size: 112px 35px;
        background-repeat: no-repeat;
        padding-bottom: 30px;
      }
    </style>
  <?php
}
add_action( 'login_enqueue_scripts', __NAMESPACE__ . '\\login_logo' );

/**
 * Replace login page logo title
 */
function login_title() {
    return 'Shash7';
}
add_filter('login_headertitle', __NAMESPACE__ . '\\login_title');

/**
 * Replace admin footer text
 * @return [type] [description]
 */
function admin_footer() {
  echo 'Built by <a href="https://shash7.com" target="_blank">shash7</a>. Running Sage 8.5.4';
}
add_filter('admin_footer_text', __NAMESPACE__ . '\\admin_footer');

/**
 * Remove unnecessary admin dashboard widgets
 * @return [type] [description]
 */
function remove_dashboard_widgets() {
  remove_meta_box('dashboard_quick_press','dashboard','side'); //Quick Press widget
  remove_meta_box('dashboard_recent_drafts','dashboard','side'); //Recent Drafts
  remove_meta_box('dashboard_primary','dashboard','side'); //WordPress.com Blog
  remove_meta_box('dashboard_secondary','dashboard','side'); //Other WordPress News
  remove_meta_box('dashboard_incoming_links','dashboard','normal'); //Incoming Links
  remove_meta_box('dashboard_plugins','dashboard','normal'); //Plugins
  remove_meta_box('dashboard_right_now','dashboard', 'normal'); //Right Now
  remove_meta_box('rg_forms_dashboard','dashboard','normal'); //Gravity Forms
  remove_meta_box('dashboard_recent_comments','dashboard','normal'); //Recent Comments
  remove_meta_box('icl_dashboard_widget','dashboard','normal'); //Multi Language Plugin
  remove_meta_box('dashboard_activity','dashboard', 'normal'); //Activity
  remove_action('welcome_panel','wp_welcome_panel');
}
add_action('wp_dashboard_setup', __NAMESPACE__ . '\\remove_dashboard_widgets' );


/**
 * Add ACF options page
 */
if( function_exists('acf_add_options_page') ) {
  acf_add_options_page(); 
}

/**
 * Set google maps api key for acf field
 */
function acf_google_map_key($api) {
  $api['key'] = '';
  return $api;
}
// add_filter('acf/fields/google_map/api', __NAMESPACE__ . '\\acf_google_map_key');
