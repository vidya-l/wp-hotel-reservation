<?php
// Don't call the file directly
if ( !defined( 'ABSPATH' ) ) exit; 

/**
 * WP Hotel Reservation sidebar widget.
 */
class wpHotelReservationWidget extends WP_Widget{
  /**
  * Sets up the widgets name, description etc
  */
  function wpHotelReservationWidget() {

    $opts = array('description' => __('Reservation form','wphr') );
    parent::__construct(false, $name = __('WP Hotel Reservation
      ','wphr'), $opts);

  }
  /**
   * Outputs the content of the widget
   *
   * @param array $args
   * @param array $instance
   */
  function widget($args, $instance) {
    
    global $post;
    extract( $args );
    $title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
  
    echo $before_widget;
    echo $before_title . $title . $after_title;
    echo  $this->displayForm();
    $this->enqueueWPHRSCripts();
    echo $after_widget;
  }
  
  /**
   * Update the options
   *
   * @param  array $new_instance
   * @param  array $old_instance
   * @return array
   */
  function update($new_instance, $old_instance) {
    $instance = $old_instance;
    $instance['title'] = strip_tags($new_instance['title']);

    return $instance;
  }

   /**
   * The widget configuration form back end.
   *
   * @param  array $instance
   * @return void
   */
  function form($instance) {
   
    $default = array ( 'title' => '' );
    $instance = wp_parse_args( (array) $instance, $default);
    $title = strip_tags($instance['title']);
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
  function enqueueWPHRSCripts(){
    wp_enqueue_script('jquery');
    wp_enqueue_script('wphr-plugin', WPHR_PLUGIN_URL.'/js/plugins.js');
    wp_enqueue_script('form-validator', WPHR_PLUGIN_URL.'/js/jquery.validate.min.js');
    wp_enqueue_script('wphr-main', WPHR_PLUGIN_URL.'/js/main.js');
    wp_enqueue_style('font-awesome', WPHR_PLUGIN_URL.'/css/font-awesome/css/font-awesome.min.css');   
    wp_enqueue_style('wphr-main', WPHR_PLUGIN_URL.'/css/main.css');   
    wp_enqueue_style('wphr-normalize', WPHR_PLUGIN_URL.'/css/normalize.css'); 
    

    wp_localize_script( 'wphr-main', 'ajax_object',
          array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
  }

  /**
   * Returns the booking form
   * @return html
   */

  function displayForm(){ 
    $html = '';
    $html .= '<form id="booking-form" class="booking-form" name="booking-form" method="post" action="">
            <div id="form-content">

                <div class="group">
                    <label for="date-from">From</label>
                    <div class="addon-right">
                        <input id="date-from" name="date_from" class="form-control" type="text" required>
                        <i class="fa fa-calendar"></i>
                    </div>
                </div>

                <div class="group">
                    <label for="date-to">To</label>
                    <div class="addon-right">
                        <input id="date-to" name="date_to" class="form-control" type="text" required>
                        <i class="fa fa-calendar"></i>
                    </div>
                </div>

                <div class="group">
                    <label for="room-type">Room type</label>
                    <div>
                        <select id="room-type" name="room_type" class="form-control" required>
                            <option value="Single room">Single room</option>
                            <option value="Double room">Double room</option>
                            <option value="Apartment">Apartment</option>
                        </select>
                    </div>
                </div>
                
                <div class="group">
                    <label for="room-requirements">Room <br/>requirements</label>
                    <div>
                        <select id="room-requirements" name="room_requirements" class="form-control">
                            <option value="No Preference">No Preference</option>
                            <option value="Non Smoking">Non Smoking</option>
                            <option value="Smoking">Smoking</option>
                        </select>
                    </div>
                </div>              
                
                <div class="group">
                    <label for="adults">Adults</label>
                    <div>
                        <select id="adults" name="adults" class="form-control">
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                        </select>
                    </div>
                </div>

                <div class="group">
                    <label for="children">Children</label>
                    <div>
                        <select id="children" name="children" class="form-control">
                            <option value="-">-</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                        </select>
                    </div>
                </div>

                <div class="group">
                    <label for="txtName" >Name</label>
                    <div><input id="name" name="txtName" class="form-control" type="text" placeholder="Name" required></div>
                </div>

                <div class="group">
                    <label for="email">Email</label>
                    <div><input id="email" name="email" class="form-control" type="email" placeholder="Email" required></div>
                </div>

                <div class="group">
                    <label for="phone">Phone</label>
                    <div><input id="phone" name="phone" class="form-control" type="text" placeholder="Phone" required></div>
                </div>

                <div class="group">
                    <label for="special-requirements">Special <br/>requirements</label>
                    <div><textarea id="special-requirements" name="special_requirements" class="form-control" cols="" rows="5" placeholder="Special requirements"></textarea></div>
                </div>

                <div class="group submit">
                    <label class="empty"></label>
                    <div><input name="submit" type="submit" value="Submit"/></div>
                </div>
            </div>
            <div id="form-loading" class="hide"><i class="fa fa-circle-o-notch fa-spin"></i></div>
            <div id="form-message" class="message hide">
                Thank you for your enquiry!
                We will get back to you soon
            </div>
        </form>';
    return $html;
  }
}

add_action('widgets_init', create_function('', 'return register_widget("wpHotelReservationWidget");'));

require WPHR_PLUGIN_DIR.'/ajax/wphr-functions.php';