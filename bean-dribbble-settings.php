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
		"bean-dribbble"
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
			"title"         => "Dribbble Username:",
			"type"          => "text",
			"default_value" => "",
			"class"         => "regular-text"
			),
		"access_token" => 
		array(
			"title"         => "Client Access Token:",
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
			'message' => "Your Client Access Token doesn't look quite right.",
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
	echo '<h2>Bean Dribbble Plugin</h2>';
	echo '<div class="wrap">'; 
	echo '<p>' . __('Display your most recent shots throughout your theme with the Bean Dribbble widget. Some of our themes include Dribbble Feed templates, which utilize your access token to output your recent shots. If you need additional help, we wrote a detailed <strong><a href="http://themebeans.com/tutorials/using-the-bean-dribbble-plugin" target="_blank">Access Token Guide</a></strong> to help you along. Cheers!.', 'bean' ) . '</p></br>';
	
	echo '<form method="post" action="options.php">';
	echo '<h4 style="font-size: 15px; font-weight: 600; color: #222; margin-bottom: 10px;">' . __('How To', 'bean' ) . '</h4>';
	echo '<ol>';
	echo '<li><a href="https://dribbble.com/session/new" target="_blank">' . __( 'Login', 'bean' ) . '</a> and <a href="https://dribbble.com/account/applications/new" target="_blank">' . __( 'Create a Dribbble application', 'bean' ) . '</a></li>';
	echo '<li>' . __( 'Fill in all fields on the create application page', 'bean' ) . '</li>';
	echo '<li>' . __( 'Use your URL for the "Website URL" and "Callback URL" fields', 'bean' ) . '</li>';
	echo '<li>' . __( 'Click the "Register Application" button', 'bean' ) . '</li>';
	echo '<li>' . __( 'Upon refresh you&#39ll see three codes. Copy only the "Client Access Token" code and return to this page', 'bean' ) . '</li>';
	echo '<li>' . __( 'Add your Dribbble username and paste the "Client Access Code" code into it&#39s respective field below' ) . '</li>';
	echo '<li>' . __( "Click the 'Save Changes' button" ) . '</li>';
	echo '<li>' . __( 'Add the "Bean Dribbble" widget to a widget area in your <a href="widgets.php">Widgets Dashboard</a>' ) . '</li>';
	echo '<li>' . __( 'Follow the steps below to add a Bean Dribbble Feed template' ) . '</li>';
	echo '</ol></br>';

	echo '<h4 style="font-size: 15px; font-weight: 600; color: #222; margin-bottom: 10px;">' . __('Dribbble Feed Template', 'bean' ) . '</h4>';
	echo '<ol>';
	echo '<li><a href="post-new.php?post_type=page">' . __( 'Create a new page', 'bean' ) . '</a></li>';
	echo '<li>' . __( 'Select a Bean Dribbble template from the Page Attributes module', 'bean' ) . '</a></li>';
	echo '<li>' . __( 'Publish your page', 'bean' ) . '</li>';
	echo '</ol></br>';

	settings_fields($this->options_page_slug);
	do_settings_sections($this->options_page_slug);
	submit_button();

	echo '</form>';
	echo '</div>';
	echo '</div>';
	echo '<style> form h3 {display:none;} label {font-size: 13px; font-weight: normal;line-height: 1.4em;} .form-table td, .form-table th {padding: 0; padding-bottom: 5px; margin: 0;} </style>';
} //END bean_dribbble_admin_page
}

new Bean_Dribbble_Settings;

?>