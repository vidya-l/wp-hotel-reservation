<?php
add_action( 'wp_ajax_save_reservation_details', 'save_reservation_details' );
add_action( 'wp_ajax_nopriv_save_reservation_details', 'save_reservation_details' );
function save_reservation_details() {
	global $wpdb;
	$date_from = wp_checkdate( $_POST[ 'date_from' ] );
	$date_to = wp_checkdate( $_POST[ 'date_to' ] );
	$adults = $_POST[ 'adults' ];
	$children = $_POST[ 'children' ];
	$email = sanitize_email( $_POST[ 'email' ] );
	$name = sanitize_text_field( $_POST[ 'txtName' ] );
	$phone = sanitize_text_field( $_POST[ 'phone' ] );
	$room_requirements = $_POST[ 'room_requirements' ];
	$room_type = $_POST[ 'room_type' ];
	$special_requirements = sanitize_text_field( $_POST[ 'special_requirements' ] );
	
	$status = $wpdb->insert( WP_HR_TABLE, array( 'from_date' => $date_from, 'to_date' => $date_to, 'adults' => $adults,	'children' => $children, 'email' => $email, 'name' => $name, 'phone' => $phone, 'room_requirements' => $room_requirements, 'room_type' => $room_type, 'special_requirements' => $special_requirements ) );
	if( $status ) {
		//wp_mail();
	}
	$output = json_encode( array( 'status' => $status ) );
	echo $output;
	wp_die();
}