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
if ( !defined( 'ABSPATH' ) ) exit;


/****
Create wpHotelReservation class if already not exists.
****/
if( !class_exists( 'wpHotelReservation' ) ) {

	class wpHotelReservation{
		/**
		 * Constructor
		 * Defines constants
		 *
		 * @return void
		*/
		function __construct(){

			define( 'WPHR_PLUGIN', __FILE__ );
			define( 'WPHR_PLUGIN_BASENAME', plugin_basename( WPHR_PLUGIN ) );
			define( 'WPHR_PLUGIN_NAME', trim( dirname( WPHR_PLUGIN_BASENAME ), '/' ) );
			define( 'WPHR_PLUGIN_DIR', untrailingslashit( dirname( WPHR_PLUGIN ) ) );
			define( 'WPHR_PLUGIN_URL', untrailingslashit( plugins_url( '', WPHR_PLUGIN ) ) );
			global $wpdb;
			define('WPHR_TABLE', $wpdb->prefix.'reservations');
		}

		/**
		 * Creates reservation table
		 * @return void
		*/
		static function createWPHRTables(){
			
			$sql = "CREATE TABLE IF NOT EXISTS ".WPHR_TABLE." (
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
			PRIMARY KEY  (id)		
			)";
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
		}

		/**
		 * Creates reservation table
		 * @return void
		*/
		public function init(){
			add_action('admin_menu', array($this,'wphrAdminOptions' ));
		}

		/**
		 * Adds menu page
		 * prints style for menu page
		 * @return void
		*/
		public function wphrAdminOptions(){
			$page = add_menu_page('Hotel Reservations', 'Hotel Reservatons', 'manage_options', 'wphr-admin-options', array($this,'wphrAdminOptionsFun' ), 'dashicons-admin-home', 5);
			add_action( "admin_print_styles-{$page}", array($this, 'includeWphrTableStyles' ) );
		}

		/**
		 * enqueus style in admin menu page
		 * @return void
		*/
		public function includeWphrTableStyles(){
			wp_enqueue_style('wphr-styels', WPHR_PLUGIN_URL.'/css/wphr-admin.css');
		}

		/**
		 * Renders table with saved data
		*/
		public function wphrAdminOptionsFun(){
			$output = '<div class="wrap">
				<h2>Reservations</h2>';
				include_once('inc/wphr-list-table.php');
				wphrRenderListTable();
			$output .= '</div>';
			return $output;
		}

		static function deleteWPHRTables(){
	        // Drop table 
			$wpdb->query("DROP TABLE IF EXISTS ".WPHR_TABLE);
		}
	}
}


if( class_exists( 'wpHotelReservation' ) ) {
	$wphr = new wpHotelReservation();
	register_activation_hook( __FILE__, array('wpHotelReservation', 'createWPHRTables' ));

	register_deactivation_hook( __FILE__, 'deleteWPHRTables' );
	$wphr->init();
}

require('inc/wphr-widgets.php');