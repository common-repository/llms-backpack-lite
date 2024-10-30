<?php

/**
 * Notes Post Table Columns
 *
 * @since    3.9.6
 * @version  3.9.6
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class LLMS_Admin_Post_Table_Notes {

	/**
	 * Constructor
	 * @return  void
	 * @since    3.9.6
	 * @version  3.9.6
	 */
	public function __construct() {
	
		add_filter( 'init', array( $this, 'delete' ), 10, 1 );
		add_filter( 'manage_llms_student_notes_posts_columns', array( $this, 'add_columns' ), 10, 1 );
		add_action( 'manage_llms_student_notes_posts_custom_column', array( $this, 'manage_columns' ), 10, 2 );
		add_action( 'admin_notices', array( $this, 'admin_notice_success' ), 10, 1 );

		//add course filter 
		//add_action( 'restrict_manage_posts', array( $this, 'filters' ), 10 );

		//change query
		//add_action( 'pre_get_posts', array( $this, 'query_posts_filter' ), 10,1 );

		//disable default date 
		//add_filter( 'months_dropdown_results', array( $this, 'default_date_filter' ), 10 ,2);

	}

	/**
	 * Add Custom lesson Columns
	 * @param   array  $columns  array of default columns
	 * @return  array
	 * @since    3.9.6
	 * @version  3.9.6
	 */
	public function add_columns( $columns ) {

		$columns = array(
			'cb' => '<input type="checkbox" />',
			'excerpt' => __( 'Note Excerpt', 'lifterlms_student_notes' ),
			//'title' => __( 'Lesson Title', 'lifterlms_student_notes' ),
			'author' => __( 'Student Name', 'lifterlms_student_notes' ),
			'related_post_type' => __( 'Type', 'lifterlms_student_notes' ),
			'related_post' => __( 'Title', 'lifterlms_student_notes' ),
			//'prereq' => __( 'Prerequisite', 'lifterlms_student_notes' ),
			'date' => __( 'Date', 'lifterlms_student_notes' ),
			'actions' => __( 'Actions', 'lifterlms_student_notes' ),
		);

		return $columns;
	}


	/**
	 * Manage content of custom lesson columns
	 * @param  string $column   column key/name
	 * @param  int    $post_id  WP Post ID of the lesson for the row
	 * @return void
	 * @since   3.9.6
	 * @version  3.9.6
	 */
	public function manage_columns( $column, $post_id ) {
		/* echo $column;
		echo "<br/>";
		echo $post_id;
		//break;
 */
		$l = new LLMS_Lesson( $post_id );

		switch ( $column ) {
			case 'excerpt':
			
			echo wp_trim_words( get_the_content($post_id), 40, '...' );
			break;
			case 'related_post_type' :
					$related_post_type =  get_post_meta( $post_id, 'related_post_type', true );
					echo $related_post_type;
			break;
			case 'related_post' :
			$related_post_id = absint( get_post_meta( $post_id, 'related_post_id', true ) );
			$edit_link = get_the_permalink( $related_post_id );

			if ( ! empty( $related_post_id ) ) {
				printf( __( '<a href="%1$s">%2$s</a>' ), $edit_link, get_the_title( $related_post_id ) );
			}
			break;
			case 'actions':
			$edit_link ='#';
			$edit_link = get_edit_post_link( $post_id );
			//$edit_link = get_the_permalink( $post_id );
			$edit_link .= "#Respond";
			#http://edbooks.learning-templates.com/wp-admin/post.php?post=1149&action=trash&_wpnonce=7798586085
			$edit_link2 = admin_url('post.php?post_type=llms_student_notes&post=');
			$nonce = wp_create_nonce( '_wpnonce' );
			$extra_data = $post_id.'&action=note-trash&_wpnonce='.$nonce;
			$d_link = $edit_link2.$extra_data;
			
			printf( __( '<a class="button primary" href="%1$s">%2$s</a>' ), $edit_link, __('Respond','lifterlms_student_notes') );
			printf( __( '<a id="del-post-'.$post_id.'" class="button primary" href="%1$s">%2$s</a>' ), $d_link, __('Delete','lifterlms_student_notes') );
			?>
			<script>
			jQuery( function($) {
					
				//$( "a#del-post-<?php echo $post_id; ?>" ).click(function(e){
					//console.log('clicked');
					//e.preventDefault();
					//$( "#pos-<?php echo $post_id; ?>  .trash a" ).trigger('click');
				//});
				
			});
			</script>
			<?php 
			break;


		}// End switch().

	}
	/**
	 * Add  filters
	 * 
	 * @return string/html
	 * @since 3.9.6
	 */
	 public function filters($post_type){
		
		//only add filter to post type you want
		if ('llms_student_notes' == $post_type){
				
			?>
			<?php $selected_course_id = isset($_GET['filter_course_id'])? sanitize_text_field($_GET['filter_course_id']):''; ?>
			<select name="filter_course_id" id="filter_course_id">
				<option value=""><?php _e('All Courses ', 'lifterlms'); ?></option>
				<?php foreach($this->get_posts() as $course_id) { ?>
					<option value="<?php echo $course_id; ?>" <?php selected( $course_id,$selected_course_id ); ?> ><?php echo get_the_title($course_id); ?></option>
				<?php } ?>
			</select>
			<script>
			/* auto submit on course filter change */
			jQuery( document ).ready(function($) {
				$('#filter_course_id').change(function(){
					$('#filter_lesson_id').val('');
					$('#posts-filter').submit();
				});
			});
			</script>
			<?php 
			//get all lessons of course 
			//TO DO: use clasess :issue arise after submitting using classes 
			//$crs_obj 			= 	new LLMS_Course($selected_course_id);
			//$filter_all_lessons = 	$crs_obj->get_lesson_ids();
			
			$all_less 	=	$this->get_posts('lesson');
			foreach($all_less  as $lesson_id) {
				 $parent_id = absint( get_post_meta( $lesson_id, '_llms_parent_course', true ) );
				 if($selected_course_id==$parent_id){
									 
					  $filter_all_lessons[] = $lesson_id;
					
				 }
				 
			} 
			
			?>
			<?php $selected_lesson_id = isset($_GET['filter_lesson_id'])? sanitize_text_field($_GET['filter_lesson_id']):''; ?>
			<select name="filter_lesson_id" id="filter_lesson_id">
				<option value=""><?php _e('All Lessons ', 'lifterlms'); ?></option>
				<?php foreach($filter_all_lessons  as $lesson_id) { ?>
					<option value="<?php echo $lesson_id; ?>" <?php selected( $lesson_id,$selected_lesson_id ); ?> ><?php echo get_the_title($lesson_id); ?></option>
				<?php } ?>
			</select>
			<?php
			//date filter 
			global $wpdb ,$wp_locale;
			$extra_checks = "AND post_status != 'auto-draft'";
			$months = $wpdb->get_results( $wpdb->prepare( "
				SELECT DISTINCT YEAR( post_date ) AS year, MONTH( post_date ) AS month
				FROM $wpdb->posts
				WHERE post_type = %s
				$extra_checks
				ORDER BY post_date DESC
			", $post_type ) );
			
		
			$month_count = count( $months );
			if ( !$month_count || ( 1 == $month_count && 0 == $months[0]->month ) )
			return;
			
			$m = isset( $_GET['m'] ) ? (int) $_GET['m'] : 0;
			?>
					<label for="filter-by-date" class="screen-reader-text"><?php _e( 'Filter by date' ); ?></label>
					<select name="m" id="filter-by-date">
						<option <?php selected( $m, 0 ); ?> value="0"><?php _e( 'All dates' ); ?></option>
			<?php
					foreach ( $months as $arc_row ) {
						if ( 0 == $arc_row->year )
							continue;
						$month = zeroise( $arc_row->month, 2 );
						$year = $arc_row->year;
						printf( "<option %s value='%s'>%s</option>\n",
							selected( $m, $year . $month, false ),
							esc_attr( $arc_row->year . $month ),
							
							sprintf( __( '%1$s %2$d' ), $wp_locale->get_month( $month ), $year )
						);
					}
			?>
					</select>
			<?php
		
		}
	}
	/**
	 * Get posts 
	 * 
	 * @arg: post type 
	 * @return array
	 * @since 3.9.6
	 */
	 public function get_posts($post_type = 'course'){
		 global $wpdb;
			/** Grab  posts from  DB */
			$query = $wpdb->prepare('
				SELECT  * FROM %1$s 
				WHERE post_status = "%2$s" 
				AND post_type = "%3$s"
				ORDER BY ID DESC',
				$wpdb->posts,
				'publish',         
				''.$post_type.''
			);
			return $wpdb->get_col($query);
	 }
	 
	/**
	 * Change query on filter submit
	 * 
	 * @return Void
	 * @Since 3.9.6
	 */
	public function query_posts_filter( $query ){
		global $pagenow;
		$type = 'post';
		if (isset($_GET['post_type'])) {
			$type = $_GET['post_type'];
		}
		if ( 'llms_student_notes' == $type && is_admin() && $pagenow=='edit.php' && isset($_GET['filter_course_id']) && $_GET['filter_course_id'] != '') {
			
			$selected_course_id = isset($_GET['filter_course_id'])? sanitize_text_field($_GET['filter_course_id']):''; 
			 
			$selected_lesson_id = isset($_GET['filter_lesson_id'])? sanitize_text_field($_GET['filter_lesson_id']):'';
			
			//get all lessons of course 
			/* if(class_exists('LLMS_Lesson')){
			$lesson	=	new LLMS_Lesson($selected_lesson_id);
			$l_id	=	$lesson->get( 'assigned_quiz' );
			}else{ 
				echo 'no lesson class'; 
			} */
			$all_less 	=	$this->get_posts('lesson');
			if($selected_lesson_id){
				//to check if single lesson is set then no need for all lesson 
				$all_less = array($selected_lesson_id);
			}
			
			 foreach($all_less  as $lesson_id) {
				 $parent_id = absint( get_post_meta( $lesson_id, '_llms_parent_course', true ) );
				 if($selected_course_id==$parent_id){
									 
					  $quiz_ids[] = absint( get_post_meta( $lesson_id, '_llms_assigned_quiz', true ) );
					
				 }
				 
			} 
				 
		if(!empty($quiz_ids)){	
			// array unique 
			$quiz_ids = array_unique($quiz_ids);
			//remove 0 value array 
			 if(!$selected_lesson_id){
				$quiz_ids = array_diff($quiz_ids, array(0));
			} 
			  

			$l_id ='novalue';
			if(is_array($quiz_ids)){
				$l_id	=	implode(',',$quiz_ids);
			}
			
			
			if($l_id){
				
				//set query var these quizes will show 
				$query->query_vars['post__in'] = array($l_id);
			}
			
			if($l_id == 0){
				
				//set query var these quizes will show 
				$query->query_vars['post__in'] = array(0);
			}
		}else{
			//if no lesson on course 
			//set to no quiz found
			$query->query_vars['post__in'] = array(0);
		}
			
		}
	}
	/**
	 * Hide default date filter  only on llms_student_notes post types 
	 * 
	 * @return empty array | months array 
	 * @Since 3.9.6
	 */
	public function default_date_filter( $months, $post_type){
		
		if($post_type=='llms_student_notes'){
			
			return array();
		}
		return $months;
		
	}
	//delete
	public function delete($data){
		#http://edbooks.learning-templates.com/wp-admin/post.php?post_type=llms_student_notes&post=1149&action=note-trash&_wpnonce=66186362ec
			$user_id = get_current_user_id();
		if($user_id){
			
				
				$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
			
			if($action !=''){
			if($action =='note-trash'){
				$post = isset($_REQUEST['post']) ? $_REQUEST['post'] : '';
				//wp_delete_post( $post_id_del );
				//move to trash so admin can still check note 
				$trashed = wp_trash_post($post);
					if($trashed){
						echo "<div class='alert alert-danger'>Note deleted.</div>";
						wp_redirect(admin_url('edit.php?post_type=llms_student_notes&notic=1'));
						exit('re');
					}
					}
				
			}
			
			
		}
	}
	public function admin_notice_success($a) {
		
		$notic = isset($_REQUEST['notic']) ? $_REQUEST['notic'] : '';
		if($notic ==1){
			?>
			<div class="notice notice-success is-dismissible">
				<p><?php _e( 'Note Deleted !', 'llms_student_notes' ); ?></p>
			</div>
			<?php
		}
	}

}
return new LLMS_Admin_Post_Table_Notes();
