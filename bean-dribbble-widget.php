<?php 
/**
 * Widget Name: Bean Dribbble Widget
 * Description: Displays your Dribbble shots
 * Version: 1.0
 * Author: Kamil Waheed / Themebeans
 */




/**
 * Preventing to use `Bean_Dribbble_Widget` for the class name to avoid the
 * collisions with the class packaged with some themes
 */
class Bean_Dribbble_Plugin_Widget extends WP_Widget {

  /**
   * Initialize the widget
   */
  public function __construct() {
    parent::__construct(
      '_bean_dribbble',
      'Bean Dribbble',
      array( 'description' => __( 'Displays your Dribbble shots.', 'bean' ) )
    );
  }



  /**
   * Output the markup of the widget
   * 
   * @param  array $args
   * @param  array $instance
   */
  public function widget( $args, $instance ) { 
      // WIDGET VARIABLES
    extract( $args );

    $plugin_settings = get_option('bean-dribbble-plugin-settings');

    $title = apply_filters( 'widget_title', $instance['title'] );
    $account = $plugin_settings['account_name'];
    $access_token = $plugin_settings['access_token'];
    $shots = $instance['shots'];
    $desc = $instance['desc'];
  
    echo $before_widget;
      
    if ( !empty( $title ) ) echo $before_title . $title . $after_title;
    
    if($desc != '') : ?><p><?php echo $desc; ?></p><?php endif;
    
    $dribbble_shots = Bean_Dribbble_API_Interface::retrieveShots(
                        $account,
                        $access_token,
                        $shots
                      );

    $output = '<div class="bean-dribbble-shots">';

    if ($dribbble_shots) {      
      foreach( $dribbble_shots as $dribbble_shot ) {        
        $output .= '<div class="bean-shot">';

        $image_src = $dribbble_shot->images->normal;
  
        $output .= '<a href="' . $dribbble_shot->html_url . '" target="blank">';
        $output .= '<img height="' . $dribbble_shot->height . '" ' . 
                        'width="' . $dribbble_shot->width . '" ' . 
                        'src="' . $image_src . '" ' . 
                        'alt="" />';
        $output .= '</a>';
        $output .= '</div>';
        
        $i++;
      }
  
      $output .= '</div>';
    } else {
      $output .= __('Womp. Could not connect to Dribbble.', 'bean') . '</div>';
    }

    echo $output;
    
    echo $after_widget;
  }



  /**
   * Output the markup for the options on admin
   *
   * @param array $instance The widget options
   */
  public function form( $instance ) {

    // WIDGET DEFAULTS
    $defaults = array(
      'title' => 'Dribbble',
      'desc' => 'Use our Dribbble widget to display your shots.',
      'shots' => 4
    );
  
    $instance = wp_parse_args( (array) $instance, $defaults );
    $title = $instance['title'];
    $shots = $instance['shots'];

    ?>
    
    <p>
      <label for="<?php echo $this->get_field_id('title'); ?>"><?php
        _e('Title/Intro:', 'bean'); ?>
      </label>

      <input class="widefat"
             id="<?php echo $this->get_field_id('title'); ?>"
             name="<?php echo $this->get_field_name('title'); ?>"
             type="text" value="<?php echo esc_attr( $title ); ?>" />
    </p>

    
    <p style="margin-top: -8px;">
      <textarea class="widefat"
                rows="5"
                cols="15"
                id="<?php echo $this->get_field_id( 'desc' ); ?>"
                name="<?php echo $this->get_field_name( 'desc' ); ?>"
                ><?php
          echo $instance['desc'];
         ?></textarea>
    </p>
    
    <p>
      <label for="<?php echo $this->get_field_id('shots'); ?>">
        <?php _e('Number of Shots:', 'bean'); ?>
      </label>

      <select name="<?php echo $this->get_field_name('shots'); ?>">
        <?php for( $i = 1; $i <= 12; $i++ ) { ?>
          <option value="<?php echo $i; ?>" <?php selected( $i, $shots ); ?>>
            <?php echo $i; ?>
          </option>
        <?php } ?>
      </select>
    </p>
  
  <?php
  }



  /**
   * Filter the widget options before saving
   * 
   * @param  array $new_instance The new options
   * @param  array $old_instance The old options
   */
  public function update( $new_instance, $old_instance ) {
    // STRIP TAGS TO REMOVE HTML - IMPORTANT FOR TEXT INPUTS
    $instance = $old_instance;
    $instance['title'] = strip_tags($new_instance['title']);
    $instance['desc'] = strip_tags($new_instance['desc']);
    
    $instance['shots'] = trim($new_instance['shots']);
    
    return $instance;
  }
}

// Register the widget
function register_bean_dribbble_widget() {
    register_widget( 'Bean_Dribbble_Plugin_Widget' );
}
add_action( 'widgets_init', 'register_bean_dribbble_widget', 11 );

?>