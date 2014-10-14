<?php 
/**
 * Plugin Name: Bean Dribbble
 * Plugin URI: http://themebeans.com/plugin/bean-dribbble-plugin/?ref=plugin_bean_dribbble
 * Description: Enables a Dribbble widget. Also enables support for Themebeans themes to display Dribbble posts in special page templates (only available in Spaces). You must register a <a href="https://dribbble.com/account/applications" target="_blank">Dribbble application</a> to retrieve your client access token.
 * Version: 1.0
 * Author: Kamil Waheed / Themebeans
 * Author URI: http://themebeans.com/?ref=plugin_bean_dribbble
 */


if ( ! function_exists( 'add_action' ) ) {
  echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
  exit;
}


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
?>