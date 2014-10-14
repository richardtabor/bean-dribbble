<?php 
/**
 * Plugin Name: Bean Dribbble
 * Plugin URI: http://themebeans.com/plugins/bean-dribbble
 * Description: Enables a Dribbble widget and includes support for a custom Dribbble feed template, if built into the theme package.
 * Version: 1.0
 * Author: Themebeans
 * Author URI: http://themebeans.com
 */


if ( ! function_exists( 'add_action' ) ) {
  echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
  exit;
}

define('BEAN_DRIBBBLE_PATH', plugin_dir_url( __FILE__ ));
define('BEAN_DRIBBBLE_PLUGIN_FILE', __FILE__ );


/*===================================================================*/
/*
/* BEGIN BEAN DRIBBBLE PLUGIN
/*
/*===================================================================*/

// INCLUDE DRIBBBLE INTERFACE CLASS
require_once('inc/dribbble-api-interface.php');

// INCLUDE DRIBBBLE SETTINGS PAGE
require_once('bean-dribbble-settings.php');

// INCLUDE WIDGET
require_once('bean-dribbble-widget.php');


// GLOBAL ALIASES FOR THE INTERFACE FUNCTIONS
/**
 * Hit the user's shots endpoint to retrieve the shots and return them
 * wrapped in a PHP array
 * 
 * @param  string $username    The username to retrieve the shots of
 * @param  string $accessToken The access token to use for the request
 * @param  int    $shotsCount  Number of shots to retrieve
 * @return mixed               Array in case the request was successful;
 *                             false, otherwise
 */
function bean_get_dribbble_feed() {
  $args = func_get_args();

  return call_user_func_array(
    array( "Bean_Dribbble_API_Interface", "retrieveShots" ),
    $args);
}

/**
 * Ping the shots endpoint with the provided access token to check
 * to see if the response code is 200
 * 
 * @param  string $accessToken The access token
 * @return boolean             true or false
 */
function bean_verify_dribbble_access_token() {
  $args = func_get_args();

  return call_user_func_array(
    array( "Bean_Dribbble_API_Interface", "verifyAccessToken" ),
    $args);
}

/*===================================================================*/
/* ADD SETTINGS LINK TO PLUGINS PAGE
/*===================================================================*/
define( 'BEANDRIBBBLE_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

add_filter( 'plugin_action_links', 'beandribbble_plugin_action_links', 10, 2 );

function beandribbble_plugin_action_links( $links, $file ) {
  if ( $file != BEANDRIBBBLE_PLUGIN_BASENAME )
    return $links;

  $settings_link = '<a href="' . menu_page_url( 'bean-dribbble', false ) . '">'
    . esc_html( __( 'Settings', 'bean-dribbble' ) ) . '</a>';

  array_unshift( $links, $settings_link );

  return $links;
}
?>