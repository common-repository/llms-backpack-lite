<?php
#https://formidableforms.com/knowledgebase/formidable-hooks/ hooks list 

//add hide mark complete on forms
add_filter( 'frm_filter_final_form', 'bkpk_hide_mark_complete_css' );
function bkpk_hide_mark_complete_css( $form ) {
	$hide_style = '<b id="iamhide">hide will be here</b><style>#iamhide,#llms_mark_complete{display:none;}</style>';
	return $form.$hide_style;
}

//mark complete 
add_action('frm_after_create_entry', 'bkpk_after_form_success', 30, 3);
function bkpk_after_form_success($entry_id, $form_id,$args){
 //echo '<b> form submitted !</b>';
	$style = '<style>#llms_mark_complete{display:block !important;}</style>';
	
	echo $style;
	if ( is_user_logged_in() ) {
		
		
		//there is not post id stored yet so get it form stored referrer
		
/****************** start ************/
		global $wpdb;
	$table1	=	$wpdb->prefix.'frm_items';
	$rows	=	$wpdb->get_results( "SELECT * FROM $table1 WHERE id = $entry_id" ,ARRAY_A);
	
	if($rows){
		foreach($rows as $row){
			$desc	=	$row['description'];
			$ref = unserialize($desc);
			
			$ref_page_url = $ref['referrer']; 
			$postid = url_to_postid( $ref_page_url );
			
			$lesson_id = $postid;
	}
	}
/******************end************/
	
	$user_id = get_current_user_id();

	//mark complete current lesson 
	$object_id 	=	$lesson_id;
	$object_type = 'lesson'; 
	$trigger = 'admin_formidable_success'; 
	llms_mark_complete( $user_id, $object_id, $object_type, $trigger );
							
	}
	// wp_enqueue_script('jquery');

	//get setting value 
	$bkpk_time = \llms_bkpk\Config::get_settings_value( 'llms-bkpk-formidable-advance-user-delay','FormidableCompletion');
	$bkpk_time = isset($bkpk_time) ? $bkpk_time : 1000;
	$bkpk_time = $bkpk_time/1000; //change to seconds 
	
	//next lesson url
	if (class_exists('LLMS_Student')) {
		global $course ,$student; 
			$student =  new LLMS_Student($user_id);
			$course_id = get_post_meta($lesson_id,'_llms_parent_course',true);
	
	
	$lesson_next = apply_filters( 'llms_course_continue_button_next_lesson', $student->get_next_lesson( $course_id ), $course_id, $student );
	//get url
	$nxt_lesson_url = get_permalink($lesson_next);
	
	}
	//if not last lesson 
	if($lesson_next !== $lesson_id){
		//sent user to next lesson
		?>
		<meta http-equiv="refresh" content="<?php echo $bkpk_time; ?>;URL='<?php echo $nxt_lesson_url; ?>'" />  
		<?php 
	}
}

// redirect or not to next lesson
add_action( 'after_llms_mark_complete', 'bkpk_custom_complete_redirect', 10, 4 );

function bkpk_custom_complete_redirect( $student_id, $object_id, $object_type, $trigger ) {

	// this action will run when courses, lessons, sections, etc... are completed
	// since we're only dealing with lessons we'll skip the rest of the function
	// if the object isn't a lesson
	if ( 'lesson' !== $object_type ) {
		return;
	}
	
	//get setting value
	$bkpk_advace = \llms_bkpk\Config::get_settings_value( 'llms-bkpk-formidable-advance-user','FormidableCompletion');
	$redirectValue = get_permalink( $object_id);
	//only run according to setting
	if(!$bkpk_advace){
		if ($redirectValue) {   // if so,  go there!
			//conflict with 'course mark complete' so not run when at wp-admin 
			if(!is_admin()){
				wp_redirect( $redirectValue);
				exit;
			}
		}
	}
}
?>