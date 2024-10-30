<?php 
add_shortcode('user','show_user_content');
function show_user_content($atts,$content = null){
	ob_start();
	global $post;
	if (is_user_logged_in()){
		$has_shortcode  = strpos($content ,'[');
		if($has_shortcode)
		$content = apply_filters('the_content',$content);
	
		return $content;
			}
	ob_end_clean();
	return '';
}

add_shortcode('guest','show_guest_content');
function show_guest_content($atts,$content){
	ob_start();
	if (!is_user_logged_in()){
		$has_shortcode  = strpos($content ,'[');
		if($has_shortcode)
		$content = apply_filters('the_content',$content);
	
		return $content;
	}
	ob_end_clean();
}

add_shortcode( 'show_for', 'get_user_role' );
function get_user_role($atts, $content) {
	extract( shortcode_atts( array(	'role' => 'role' ), $atts ) );
	ob_start();
	if( current_user_can( $role ) ) {
		$has_shortcode  = strpos($content ,'[');
		if($has_shortcode)
		$content = apply_filters('the_content',$content);
		
		return $content;
	}
	ob_get_clean();
}

add_shortcode( 'show_first_name', 'show_user_firstname_in_shortcode' );
function show_user_firstname_in_shortcode( $atts ) {
	global $current_user, $user_login;
	get_currentuserinfo();
	add_filter('widget_text', 'do_shortcode');
	ob_start();
	if ($user_login) {
		$content = $current_user->first_name;
		return $content;
	}
	ob_get_clean();
}

add_shortcode( 'show_last_name', 'show_user_lastname_in_shortcode' );
function show_user_lastname_in_shortcode( $atts ) {
	global $current_user, $user_login;
	get_currentuserinfo();
	add_filter('widget_text', 'do_shortcode');
	ob_start();
	if ($user_login) {
		$content = $current_user->last_name;
		return $content;
	}
	ob_get_clean();
}