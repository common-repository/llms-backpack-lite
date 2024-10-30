<?php
// add an action to the hook
add_action( 'lifterlms_user_registered', 'save_my_custom_lifterlms_registration_fields', 10, 1 );
add_action( 'lifterlms_user_edited', 'my_custom_lifterlms_edit_user_fields', 10, 1 );
// create the function that add_action will call
// $user_id is going to be the WP User ID of the newly created user

function save_my_custom_lifterlms_registration_fields( $user_id ) {
	// check to see if the field was submitted before proceeding
	if( isset( $_POST['my_new_reg_menu'] ) ) {
		// save the data to the user meta table, but you could do whatever you want at this point
		update_user_meta( $user_id, 'my_new_reg_menu', esc_html( $_POST['my_new_reg_menu'] ) );
	}
}
?>