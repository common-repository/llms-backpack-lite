<?php

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if ( !is_plugin_active( 'lifterlms-integration-xapi/lifterlms-integration-xapi.php' ) ) {
	$path = plugin_basename( __FILE__ );
	add_action("after_plugin_row_{$path}", function( $plugin_file, $plugin_data, $status ) {
		echo '<tr class="active"><td>&nbsp;</td><td>&nbsp;</td><td>
				The <b>"Related Competencies"</b> widget was not installed because the LifterLMS xAPI plugin is not active. <b>Click <a href="https://lifterlms.com/product/lifterlms-xapi/" target="_blank">HERE</a> to learn more.</b>
			</td></tr>';
		}, 10, 3
	);
}

define( 'LLMS_WGT_DIR', plugin_dir_path( __FILE__ ) );
define( 'LLMS_WGT_URL', plugins_url( '/', __FILE__  ) );
$llms_wft_license_key = get_option('llms_wgt_license_key');
 
	require_once( LLMS_WGT_DIR . 'inc/widget-achievements.php' );
	require_once( LLMS_WGT_DIR . 'inc/widget-certificates.php' );
	require_once( LLMS_WGT_DIR . 'inc/widget-related-courses.php' );
	require_once( LLMS_WGT_DIR . 'inc/widget-resume.php' );
	require_once( LLMS_WGT_DIR . 'inc/llms-metaboxes.php' );
	if ( is_plugin_active( 'lifterlms-integration-xapi/lifterlms-integration-xapi.php' ) ) {
		require_once( 'inc/widget-competencies.php' );
	}
/**
 * Should not clones widgets to course and any lesson under it
 *
 * @action: llms_generator_new_lesson ,llms_generator_new_course
 * @param post obj
 * it will get both cousre and lesson 
 */
function bkpk_dont_clone_widgets($post_obj){
	
	$post_id = $post_obj->get( 'id' );
	if($post_id ){
		//delete widgets
		delete_post_meta($post_id, '_dynamic_widgets');
	}
}
 add_action( 'llms_generator_new_lesson', 'bkpk_dont_clone_widgets',  10, 1 );
 add_action( 'llms_generator_new_course', 'bkpk_dont_clone_widgets',  10, 1 );
