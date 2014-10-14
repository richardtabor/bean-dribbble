<?php 
/**
 * The base class for building plugin settings pages
 * 
 */


if ( ! class_exists( 'Bean_Plugin_Settings_Page' ) ) :

class Bean_Plugin_Settings_Page {

  /**
   * The unique slug to use for the options page and for the Settings API
   *
   */
  protected $options_page_slug;


  protected $page_title;
  protected $menu_title;

  protected $section_id;
  protected $section_title;

  /**
   * The settings tree to build the settings page for
   * 
   */
  private $settings = array();




  /**
   * Hook onto WP
   */
  function __construct($page_title, $menu_title, $options_page_slug) {
    $this->page_title = $page_title;
    $this->menu_title = $menu_title;
    $this->options_page_slug = $options_page_slug;

    $this->settings = $this->build_settings_tree();

    add_action( 'admin_menu', array($this, 'add_options_page') );
    add_action( 'admin_init', array($this, 'register_settings') );
  }



  /**
   * Construct the settings tree
   *
   */
  protected function build_settings_tree() {
    return array();
  }




  /**
   * Add the options page
   *
   * hooked to `admin_menu`
   */
  public function add_options_page() 
  {
    add_options_page(
      $this->page_title,
      $this->menu_title,
      'manage_options',
      $this->options_page_slug,
      array($this, 'output_admin_page')
    );

  }




  /**
   * Traverse through the settings tree and add everything to one section
   * through the WP Settings API
   * 
   * @see http://codex.wordpress.org/Settings_API
   */
  public function register_settings() {

    /* Register the section */
    add_settings_section(
      $this->section_id,
      $this->section_title ? 
        $this->section_title : 
        $this->page_title,
      null,
      $this->options_page_slug
    );


    /* Register all the setting fields */
    foreach( $this->settings as $key => $value ) {
      $title = $value['title'];
      $type = $value['type'];
      $class = $value['class'];
      $default_value = $value['default_value'];

      add_settings_field(
        "$key",
        "$title",
        array($this, "output_options_page_field"),
        $this->options_page_slug,
        $this->section_id,
        array(
          "name"          => $key,
          "id"            => $key,
          "label_for"     => $key,
          "type"          => $type,
          "class"         => $class,
          "default_value" => $default_value
        )
      );
    }

    /* Register the single actual setting for all the plugin settings */
    register_setting(
      $this->options_page_slug,
      $this->options_page_slug,
      array($this, 'sanitize_input_values')
    );
  }




  /**
   * Sanitize the registered settings; all the plugin settings are passed as 
   * an array
   *
   * @param array $input An array of all the settings to be sanitized
   * @return array An array of all the settings sanitized
   *
   */

  public function sanitize_input_values( $input ) {
    $output = array();

    if ( isset( $_REQUEST['reset'] ) ) return $output;

    foreach($input as $key => $value) {
      if ( isset( $value ) ) {
        /* Default sanitization that applies to all fields */
        $output[$key] = strip_tags( stripslashes ( $value ) );

        if ( isset( $this->settings[$key]['sanitizer'] ) ) {
          $sanitize_output = call_user_func_array(
            $this->settings[$key]['sanitizer'],
            array(
              $output[$key]
            )
          );

          if (is_array($sanitize_output)) {
            if (isset($sanitize_output['errcode'])) {
              add_settings_error(
                $key,
                $key . $sanitize_output['errcode'],
                $sanitize_output['message'],
                $sanitize_output['type']
              );
            }
          }
          else {
            $output[$key] = $sanitize_output;
          }
        }
      }
    }

    return $output;
  }




  /**
   * Callback function for the Settings API "field" to render the field markup
   *
   * @param array $args Extra arguments given to add_settings_field()
   * @return void
   */

  public function output_options_page_field($args) {
    extract( $args );

    $values = $this->get_plugin_settings();
    $value = isset( $values[ $name ] ) ? $values[ $name ] : $default_value;
    $value = isset( $force_value ) ? $force_value : $value;

    echo "<input " . 
      "class='$class' " . 
      "type='$type' " . 
      "id='$id' " . 
      "name='$this->options_page_slug[$name]' " . 
      "value='$value'>";
  }




  /**
   * A helper function to retrieve the saved plugin settings via the Options API
   *
   * Since all the plugin settings are stored in a single key in the database
   * as an array, the function accepts an optional $key parameter to only
   * retrieve a specific value from within that array
   *
   * @param string $key An optional paramter to return only the specific plugin
   *                    setting instead of returning the entire array
   * @return mixed Returns the specific plugin setting
   */

  private function get_plugin_settings( $key = "" ) {
    $settings = get_option( $this->options_page_slug );

    if ( !empty ( $key ) ) {
      if ( isset ( $settings[ $key ] ) )
        return $settings[ $key ];

      return NULL;
    }

    return $settings;
  }




  /**
   * Output the options page HTML
   *
   * Callback for the add_options_page call
   */
  public function output_admin_page() {
    throw new Exception("Not implemented");
  }
}

endif; // if ( ! class_exists( 'Bean_Plugin_Settings_Page' ) ) :

?>