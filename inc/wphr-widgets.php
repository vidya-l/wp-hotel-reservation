<?php
// Don't call the file directly
if ( ! defined( 'ABSPATH' ) ) exit; 

/**
 * WP Hotel Reservation sidebar widget.
 */
class WP_Hotel_Reservation_Widget extends WP_Widget {
   /**
     * Sets up the widgets name, description etc
     */
    function WP_Hotel_Reservation_Widget() {
        $opts = array( 'description' => __( 'Reservation form', 'wphr' ) );
        parent::__construct( false, $name = __('WP Hotel Reservation', 'wphr' ), $opts );
    }
    /**
     * Outputs the content of the widget
     *
     * @param array $args
     * @param array $instance
     */
    function widget( $args, $instance ) {    
        global $post;
        extract( $args );
        $title = empty( $instance[ 'title' ] ) ? '' : apply_filters( 'widget_title', $instance[ 'title' ] );  
        echo $before_widget;
        echo $before_title . $title . $after_title;
        echo  $this->display_form();
        $this->enqueue_sripts();
        echo $after_widget;
    }
  
    /**
     * Update the options
     *
     * @param  array $new_instance
     * @param  array $old_instance
     * @return array
     */
    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance[ 'title' ] = strip_tags( $new_instance[ 'title' ] );
        return $instance;
    }

    /**
     * The widget configuration form back end.
     *
     * @param  array $instance
     * @return void
     */
    function form( $instance ) {
        $default = array ( 'title' => '' );
        $instance = wp_parse_args( ( array ) $instance, $default );
        $title = strip_tags( $instance[ 'title' ] );
        ?>
        <p>
          <label for="<?php echo $this->get_field_id('title'); ?>">
            <?php _e("Title", 'wphr')?>
          </label>
          <br/>
          <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
            name="<?php echo $this->get_field_name('title'); ?>" type="text"
            value="<?php echo esc_attr($title); ?>" />
        </p>

    <?php
    }

    /**
     * Enqueues scripts/styles to front end
     * @return void
     */
    function enqueue_sripts() {
        wp_enqueue_script( 'wphr-plugin', WP_HR_PLUGIN_URL . '/js/plugins.js' );
        wp_enqueue_script( 'form-validator', WP_HR_PLUGIN_URL . '/js/jquery.validate.min.js' );
        wp_enqueue_script( 'wphr-main', WP_HR_PLUGIN_URL . '/js/main.js', array( 'wphr-plugin', 'form-validator' ) );
        wp_enqueue_style( 'font-awesome', WP_HR_PLUGIN_URL . '/css/font-awesome/css/font-awesome.min.css' );   
        wp_enqueue_style( 'wphr-main', WP_HR_PLUGIN_URL . '/css/main.css' );   
        wp_localize_script( 'wphr-main', 'ajax_object',
          array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
    }

    /**
     * Returns the booking form
     * @return html
     */
    function display_form() { 
        ob_start();
        $html = load_template( WP_HR_PLUGIN_DIR . '/templates/widget-form.html' );
        return $html;
        ob_end_clean();
    }
}

add_action( 'widgets_init', function(){ register_widget( 'WP_Hotel_Reservation_Widget' );
});
require WP_HR_PLUGIN_DIR . '/ajax/wphr-functions.php';