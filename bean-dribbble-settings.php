<?php 
/**
 * Add the Bean Dribbble settings page under WP Admin > Settings
 *
 * The settings page is used for setting:
 *   – Dribbble account username
 *   – Dribbble access token
 * 
 */

// Bean_Dribbble_API_Interface
include_once('inc/dribbble-api-interface.php');

// Bean_Plugin_Settings_Page
include_once('inc/bean-plugin-settings-page.php');

/**
 * Wrap everything in the class to avoid global scope collisions
 * 
 */
class Bean_Dribbble_Settings extends Bean_Plugin_Settings_Page {
  /**
   * Hook onto WP
   */
  function __construct() {
    parent::__construct(
      "Dribbble Settings",
      "Bean Dribbble",
      "bean-dribbble-plugin-settings"
    );
  }



  /**
   * Construct the settings tree
   *
   * @return array The settings tree
   */
  protected function build_settings_tree() {
    return array(
      "account_name" =>
        array(
          "title"         => "Account Name",
          "type"          => "text",
          "default_value" => "",
          "class"         => "regular-text"
        ),
      "access_token" => 
        array(
          "title"         => "Consumer Access Token",
          "type"          => "text",
          "default_value" => "",
          "class"         => "regular-text",
          "sanitizer"     => array( $this, 'sanitize_access_token' )
        )
    );
  
  }



  /**
   * Sanitize the access token
   *
   * @param String $value The value to sanitize
   * @return String The sanitized value of the access token; FALSE if error
   */
  public function sanitize_access_token($value) {
    if ( Bean_Dribbble_API_Interface::verifyAccessToken( $value ) ) {
      return $value;
    } else {
      return array(
        'errcode' => 'invalid-access-token',
        'message' => "The access token doesn't look quite right.",
        'type'    => 'error'
      );
    }
  }



  /**
   * Output the Bean Dribbble options page HTML
   *
   * Callback for the add_options_page call
   */
  public function output_admin_page() 
  {
    if( !current_user_can('manage_options') ) {
      wp_die( __('Insufficient permissions', 'bean') );
    }

    echo '<div class="wrap">';
      screen_icon();
      echo '<h2>Bean Tweets Plugin</h2>';
      echo '<div class="wrap">'; 
      echo '<p>' . __('Display your most recent tweets throughout your theme with the Bean Tweets widget. In order to do this, you must first create a Twitter application and insert the required codes below. Then, simply add the Bean Tweets widget to a widget area within your Widgets Dashboard. If you need additional help, we wrote a detailed <strong><a href="http://themebeans.com/how-to-create-access-tokens-for-twitter-api-1-1/" target="_blank">OAuth Guide</a></strong> to help you along. Cheers!', 'bean' ) . '</p></br>';
      ?>
        <?php
        echo '<form method="post" action="options.php">';
          
          
          
          echo '<h4 style="font-size: 15px; font-weight: 600; color: #222; margin-bottom: 10px;">' . __('How To', 'bean' ) . '</h4>';
          echo '<ol>';
            echo '<li><a href="https://dev.twitter.com/apps/new" target="_blank">' . __( 'Create a Twitter application', 'bean' ) . '</a></li>';
            echo '<li>' . __( 'Fill in all fields on the create application page.', 'bean' ) . '</li>';
            echo '<li>' . __( 'Agree to rules, fill out captcha, and submit your application.', 'bean' ) . '</li>';
            echo '<li>' . __( 'Click the "Create my Access Tokens" button.', 'bean' ) . '</li>';
            echo '<li>' . __( 'Upon refresh, copy the Consumer Key, Consumer Secret, Access Token & Access Token Secret codes.', 'bean' ) . '</li>';
            echo '<li>' . __( "Paste each code into their respective fields below." ) . '</li>';
            echo '<li>' . __( "Click the 'Save Changes' button below." ) . '</li>';
            echo '<li>' . __( "Add the 'Bean Tweets' widget to a widget area in your <a href='widgets.php'>Widgets Dashboard</a>." ) . '</li>';
          echo '</ol></br>';
    
          settings_fields($this->options_page_slug);
          do_settings_sections($this->options_page_slug);
          submit_button();
    
        echo '</form>';
      echo '</div>';
    echo '</div>';
  } //END bean_dribbble_admin_page
}

new Bean_Dribbble_Settings;

?>