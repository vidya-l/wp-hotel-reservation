<?php
add_action('wp_ajax_saveReservationDetails', 'saveReservationDetails');
add_action('wp_ajax_nopriv_saveReservationDetails', 'saveReservationDetails');
function saveReservationDetails(){
	$date_from = $_POST['date_from'];
	$date_to = $_POST['date_to'];
	$adults = $_POST['adults'];
	$children = $_POST['children'];
	$email = $_POST['email'];
	$name = $_POST['txtName'];
	$phone = $_POST['phone'];
	$room_requirements = $_POST['room_requirements'];
	$room_type = $_POST['room_type'];
	$special_requirements = $_POST['special_requirements'];
	global $wpdb;
	$status = $wpdb->insert(WPHR_TABLE, array('from_date'=> $date_from, 'to_date'=>$date_to, 'adults'=> $adults,	'children' => $children, 'email' => $email, 'name' =>$name, 'phone' =>$phone, 'room_requirements'=>$room_requirements, 'room_type' => $room_type, 'special_requirements' => $special_requirements ));
	if($status){
		//wp_mail();
	}
	$output = json_encode(array('status' => $status));
	echo $output;
	wp_die();
}