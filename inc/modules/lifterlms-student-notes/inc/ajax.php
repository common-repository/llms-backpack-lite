<?php 
/**
 * student notes ajax functions
 *
 */

add_action( 'wp_ajax_student_notes_ajax', 'student_notes_ajax_receiver' );
//receive ajax 
function student_notes_ajax_receiver(){
	global $wpdb;
	$whatever = intval( $_POST['whatever'] );
	$related_post_id = intval( $_POST['related_post_id'] );
	//insert note 
	if(isset($_REQUEST['note'])){
					$llms_note_text	=	$_REQUEST['note'];
					$related_post_id			=	$_REQUEST['related_post_id'];
					$related_post_type			=	$_REQUEST['related_post_type'];
					$llms_notify_admin ='';
					$llms_notify_admin			=	isset($_REQUEST['llms_notify_admin']) ? $_REQUEST['llms_notify_admin'] : '0';
					if($llms_notify_admin=='true'){
						$llms_notify_admin = 1;
						
					}

					$note_id = 0;

					if($llms_note_text !=''){
						$user_id = get_current_user_id();

						$v=array(
						'post_title'    => __('notes default title ','lifterlms_student_notes'),
						'post_content'  => $llms_note_text,
						'post_status'   => 'publish',
						'post_type'   => 'llms_student_notes',
						'post_author'   => $user_id
						//'post_category' => array( 8,39 )
						);
						//insert note 
						$note_id	=	wp_insert_post( $v );
						//add post meta 
						add_post_meta($note_id, 'related_post_id' , $related_post_id);
						add_post_meta($note_id, 'related_post_type' , $related_post_type);
						add_post_meta($note_id, 'related_user_id' , $user_id);
						add_post_meta($note_id, 'llms_notify_admin' , $llms_notify_admin);
						add_post_meta($note_id, 'admin_response' , '');
						
						//notify inst 
						if($llms_notify_admin==1){
						
						
						//email to admin/instructor 
						
								$site	=	get_bloginfo('name');
								$from	=	get_bloginfo('admin_email');
								$to		=	get_bloginfo('admin_email');
								//check for teacher role 
								$is_teacher_role_exists = get_role('author');
								if($is_teacher_role_exists){
									
									$inst_user 	= 	get_post($related_post_id);
									$inst_id  	= 	$inst_user->post_author;
									$inst_data 	= 	get_userdata($inst_id);
									$inst_email	=	$inst_data->user_email; 
									$to 		= 	$inst_email;
								}
								$subject = 'A student has left you a note on '.$site;
								$edit_link = get_edit_post_link( $note_id );
								$body = "Hello Instructor <br/>	
							A student has left a note for you.<br/><br/>
							You can view and respond to the note <a href='$edit_link'>here</a><br/><br/>
							Thanks,<br/>	$site ";
								$headers[] = "From: $site <$from>";
								$headers[] = 'Content-Type: text/html; charset=UTF-8';
								 
								wp_mail( $to, $subject, $body, $headers );
								
					}
					
						//success message
						if($note_id){
						//$success_msg =	include_once(LLMS_S_N_TEMPLATE_DIR . 'notes/success_message.php');
						 

						}
					}
			}//end of insert 
			
	$return = array(
			'llms_notify_admin'	=>  $llms_notify_admin,
			'list'	=> llms_student_notes_list_ajax($related_post_id),
			'post'		=> $_POST,
			'cuser_id' => get_current_user_id() ,
			'note_id' => $note_id,
			'success_msg' => llms_student_notes_success_message(),
	);

	echo json_encode($return);
	wp_die();
	
}
//notes list 

//note list of current user 
function llms_student_notes_list_ajax($x){
	ob_start(); 
	
	$user_id = get_current_user_id();
	if($user_id){
		//loop 
		
		$post_id_del = isset($_REQUEST['post_id_del']) ? $_REQUEST['post_id_del'] : '';
		$current_user 	= get_current_user_id(); 
 		if($post_id_del !=''){
			$c_user = get_post($post_id_del);
			if($current_user == $c_user->post_author){
			//wp_delete_post( $post_id_del );
			//move to trash so admin can still check note 
			$trashed = wp_trash_post($post_id_del);
				if($trashed){
					echo "<div class='alert alert-danger'>Note deleted.</div>";
				}
			}
		}
	//set arguments 
	  $args = array(
		'posts_per_page'   => -1,
		'offset'           => 0,
		'category'         => '',
		'orderby'          => 'meta_value_num',
		'order'            => 'DESC',
		'include'          => '',
		'author'          => $user_id,
		'exclude'          => '',
		'meta_key'         => '',
		'meta_value'       => '',
		'post_type'        => 'llms_student_notes',
		'post_mime_type'   => '',
		'post_parent'      => '',
		'post_status'      => 'publish',
		 'meta_query' => array(
									array(
									'key' => 'related_post_id',
									'value' => $x 
									)
								), 
		'suppress_filters' => true );

		
		
		$posts_array = get_posts( $args );
	
		include_once(LLMS_S_N_TEMPLATE_DIR . 'ajax/loop.php');
	}
	
	 return ob_get_clean(); 
}

function llms_student_notes_success_message(){
	ob_start();
	include_once(LLMS_S_N_TEMPLATE_DIR . 'notes/success_message.php');
	 return ob_get_clean(); 
}
function llms_student_notes_success_del_message(){
	ob_start();
	include_once(LLMS_S_N_TEMPLATE_DIR . 'notes/success_delete_message.php');
	 return ob_get_clean(); 
}

//delete notes using ajax 
add_action( 'wp_ajax_student_notes_ajax_del', 'student_notes_ajax_receiver_del' );
//receive ajax 
function student_notes_ajax_receiver_del(){
	global $wpdb;
	 $return = array();
	$note_id = intval( $_POST['note_id'] );
	$post_id_del = isset($_REQUEST['note_id']) ? $_REQUEST['note_id'] : '';
		$current_user 	= get_current_user_id(); 
 		if($post_id_del !=''){
			$c_user = get_post($post_id_del);
			if($current_user == $c_user->post_author){
			//wp_delete_post( $post_id_del );
			//move to trash so admin can still check note 
			$trashed = wp_trash_post($post_id_del);
				if($trashed){
					$msg = "<div class='alert alert-danger'>Note deleted.</div>";
				}
			}
		}
		$return = array(
			'trashed'	=> $trashed,
			'success_msg' => llms_student_notes_success_del_message(),
	);

	echo json_encode($return);
	wp_die();
}












