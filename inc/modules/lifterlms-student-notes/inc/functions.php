<?php 


//Student notes post type 
//'custom-fields'

add_action( 'init', 'create_llms_student_notes_post_type' );
function create_llms_student_notes_post_type() {
	register_post_type( 'llms_student_notes',
		array(
			'labels' => array(
				'name' => __( 'Student Notes' ),
				'singular_name' => __( 'llms_student_notes' )
			),
		'public' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'has_archive' => false,
		'menu_icon'  => 'dashicons-id-alt',
		'menu_position' => 52,
		'supports' => array( 'title', 'editor','author','thumbnail' ),
		)
	);
}

//include bootstrap 
function llms__student_notes_scripts_styles(){
	$url = 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css';
	#https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js
//wp_enqueue_style('bootstrap-3.3.7', $url, false, null);
}
 //stop bootstrap it's causing font issue in custmizer
//add_action('init', 'llms__student_notes_scripts_styles');

//tinyMCE 

// add function to the tinymce on init
add_filter( 'tiny_mce_before_init', 'my_tinymce_setup_function' );
function my_tinymce_setup_function( $initArray ) {
	  $post_type = get_post_type(get_the_ID());			
		//add only on student post types 
	  if($post_type == 'llms_student_notes' ){
	  
				$initArray['setup'] = 'function(ed) {
				ed.on("keyup", function(e) {
					editor_content = ed.getContent().replace(/(<[a-zA-Z\/][^<>]*>|\[([^\]]+)\])|(\s+)/ig,"");
					jQuery("#ilc_excerpt_counter").text(editor_content.length);
					if(editor_content.length>340){
						jQuery("#notes-submit-btn").attr("disabled","disabled");
						alert("Please check only 340 characters are allowed.");
					}else{
						jQuery("#notes-submit-btn").removeAttr("disabled");
					}
				});
			}';
		}
    return $initArray;
}

// add function to the tinymce on init
//add_filter( 'tiny_mce_before_init', 'live_selection_char_count' );
  function live_selection_char_count( $initArray ) {
    $initArray['setup'] = 'function(ed, index) {
    ed.on("click", function(e) {
    	// get the selection, and strip html
		 var selection = ed.selection.getContent().replace(/(<[a-zA-Z\/][^<>]*>|\[([^\]]+)\])|(\s+)/ig,"");
		// count the characters and write them in input field
       jQuery("#ilc_live_counter").text( "("+selection.length+")");
    });
}';
    return $initArray;
}

//download code 

if(isset($_GET['download_note'])){
	$csv = "<b>Downloading to file.";
	$note_id = $_GET['note_id'];
	if($note_id){
		$post1 = get_post($note_id);
		//$content = wpautop($post1->post_content);
		$content = $post1->post_content;
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private", false);
		header("Content-Type: application/octet-stream");
		header("Content-Disposition: attachment; filename=\"note-".$note_id.".doc\";" );
		header("Content-Transfer-Encoding: binary");
		echo $content;
		exit;
	}
}