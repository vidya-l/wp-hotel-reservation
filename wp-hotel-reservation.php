<?php
/*
  Plugin Name: WP Hotel Reservation widget
  Description: A widget for reserving hotel rooms.
  Version: 1.0.0
  Text Domain:   wp-hotel-reservation
  Author: Vidya L
  License: GPL2
*/

 // Don't call the file directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Create WP_Hotel_Reservation class if already not exists.
 */
if( ! class_exists( 'WP_Hotel_Reservation' ) ) {

	class WP_Hotel_Reservation {
		
		/**
		 * Sets up constants
		 */
		function __construct() {
			define( 'WP_HR_PLUGIN', __FILE__ );
			define( 'WP_HR_PLUGIN_BASENAME', plugin_basename( WP_HR_PLUGIN ) );
			define( 'WP_HR_PLUGIN_NAME', trim( dirname( WP_HR_PLUGIN_BASENAME ), '/' ) );
			define( 'WP_HR_PLUGIN_DIR', untrailingslashit( dirname( WP_HR_PLUGIN ) ) );
			define( 'WP_HR_PLUGIN_URL', untrailingslashit( plugins_url( '', WP_HR_PLUGIN ) ) );
			global $wpdb;
			define('WP_HR_TABLE', $wpdb->prefix . 'reservations' );
		}

		/**
		 * Creates reservation table
		 * @return void
		 */
		static function create_tables() {			
			$sql = "CREATE TABLE IF NOT EXISTS ".WP_HR_TABLE." (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			reserved_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
			from_date date NOT NULL,
			to_date date NOT NULL,
			room_type text NOT NULL,
			room_requirements text NOT NULL,
			adults tinyint(4) NOT NULL,
			children tinyint(4),
			name VARCHAR(200) NOT NULL,
			email VARCHAR(200) NOT NULL,
			phone int NOT NULL,
			special_requirements text DEFAULT '',
			status tinyint(2) DEFAULT 0 NOT NULL,
			PRIMARY KEY (id)		
			)";
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
		}

		/**
		 * Creates reservation table
		 * @return void
		 */
		public function init() {
			add_action( 'admin_menu', array( $this, 'create_admin_menu' ) );
		}

		/**
		 * Adds menu page
		 * prints style for menu page
		 * @return void
		 */
		public function create_admin_menu(){
			$page = add_menu_page( 'Hotel Reservations', 'Hotel Reservatons', 'manage_options', 'wphr-admin-options', array( $this,'listing_page' ), 'dashicons-admin-home', 5 );
			add_action( "admin_print_styles-{$page}", array( $this, 'enqueue_admin_style' ) );
		}

		/**
		 * enqueus style in admin menu page
		 * @return void
		 */
		public function enqueue_admin_style() {
			wp_enqueue_style( 'wphr-styels', WP_HR_PLUGIN_URL . '/css/wphr-admin.css' );
		}

		/**
		 * Renders table with saved data
		 */
		public function listing_page() {
			$output = 
				'<div class="wrap">
					<h2>Reservations</h2>';
					include_once( 'inc/wphr-list-table.php' );
					render_list_table();
			$output .= '</div>';
			return $output;
		}
		/**
		 * Drop reservation table on deactivation
		 * @return void
		 */
		static function delete_tables() {
			$wpdb->query( "DROP TABLE IF EXISTS ".WP_HR_TABLE );
		}
	}
}

/**
 * Create object of class WP_Hotel_Reservation
 */
if( class_exists( 'WP_Hotel_Reservation' ) ) {
	$wphr = new WP_Hotel_Reservation();
	register_activation_hook( __FILE__, array( 'WP_Hotel_Reservation', 'create_tables' ) );

	register_deactivation_hook( __FILE__, 'delete_tables' );
	$wphr->init();
}

require( 'inc/wphr-widgets.php' );