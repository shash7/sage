<?php
/**
 * Sage includes
 *
 * The $sage_includes array determines the code library included in your theme.
 * Add or remove files to the array as needed. Supports child theme overrides.
 *
 * Please note that missing files will produce a fatal error.
 *
 * @link https://github.com/roots/sage/pull/1042
 */
$sage_includes = [
  'lib/assets.php',     // Scripts and stylesheets
  'lib/extras.php',     // Custom functions
  'lib/setup.php',      // Theme setup
  'lib/titles.php',     // Page titles
  'lib/wrapper.php',    // Theme wrapper class
  'lib/customizer.php', // Theme customizer
  'lib/whitelabel.php', // Whitelabel admin area
  'lib/cpt/articles.php'// Sample CPT, commented out for now
];

foreach ($sage_includes as $file) {
  if (!$filepath = locate_template($file)) {
    trigger_error(sprintf(__('Error locating %s for inclusion', 'sage'), $file), E_USER_ERROR);
  }

  require_once $filepath;
}
unset($file, $filepath);

/**
 * Use this instead of isset
 * http://joehallenbeck.com/my-favorite-php-helper-function-a-better-isset/
 */
function getvar(&$var, $default = null) {
  if(isset($var)) {
    if(!$var) {
      return $default;
    } else {
      return $var;
    }
  } else {
    return $default;
  }
}

/**
 * Generate responsive image
 * @param  [type] $image_id  [post_id of the image]
 * @param  string $src_size  [default size of the image]
 * @param  string $img_sizes [image sizes]
 * @param  string $class     [Optional class to be added to the img]
 * @return [type]            [image html]
 */
function responsive_image( $image_id, $src_size = 'full', $img_sizes = '100vw', $class = '' ) {

  $alt        = '';
  $mime       = '';
  $image_src  = array();
  $src        = '';
  $wp_sizes   = array();
  $wp_size    = '';
  $srcset_arr = array();
  $srcset     = '';
  $image      = '';

  if( $image_id ) {
    $alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
    $alt = !empty($alt) ? $alt : '';
    $mime = get_post_mime_type( $image_id );

    if( $mime === 'image/gif' ) { // GIFs require full image size for 'src' and no 'srcset'
      $image_src = wp_get_attachment_image_src( $image_id, 'full' );
      $src       = $image_src[0];
    } elseif( $mime === 'image/jpeg' || $mime === 'image/png' ) { // JPGs and PNGs allowed 'srcset'
      $image_src = wp_get_attachment_image_src( $image_id, $src_size );
      $src       = $image_src[0];
      $wp_sizes  = get_intermediate_image_sizes();
      foreach( $wp_sizes as $wp_size ) {
        $size_src = wp_get_attachment_image_src($image_id, $wp_size);
        if ( !empty( $size_src ) ) {
          $width = $size_src[1];
          $url = $size_src[0];
          $srcset_arr[$width] = $url;
        }
      }
      if( !empty( $srcset_arr ) ) {
        ksort( $srcset_arr );
        foreach( $srcset_arr as $width => $size ) { 
          $srcset .= $size . ' ' . $width . 'w, ';  
        }                
      }
      $srcset = !empty( $srcset ) ? ' srcset="' . trim( $srcset, ', ' ) . '"' . ' sizes="' . $img_sizes . '"' : '';
    }

    $image = !empty( $src ) ? '<img class="' . $class . '" alt="' . $alt . '"' . $srcset . ' src="' . $src . '">' : ''; 
  }
  return $image;
}