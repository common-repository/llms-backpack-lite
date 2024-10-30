<?php 
/**
 * Adds LLMS_Competencies_widget widget.
 */
class LLMS_Competencies_widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'LLMS_Competencies_widget', // Base ID
			esc_html__( 'LifterLMS xAPI : Competencies ', 'lifterlms-widgets' ), // Name
			array( 'description' => esc_html__( 'This is a widget that lists the Competencies.', 'lifterlms-widgets' ),
			'classname'   => 'widget-llms-competencies',
			) // Args
		);
		
		add_action( 'wp_footer', array( $this, 'add_modal' ));
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}
		$collapse	= $toggles	=	1;
		// get course details 
		$course_id = $this->get_course_id();
		$course = new LLMS_Course( $course_id );
		$student = new LLMS_Student();
		$sections = $course->get_sections();
		
		?>
		<article>
		
<div class="llms-widget-syllabus<?php echo ( $collapse ) ? ' llms-widget-syllabus--collapsible' : ''; ?> llms-widget-competencies--collapsible">

	<?php do_action( 'lifterlms_outline_before' ); ?>
		
		
		 <!-- check for course competency s--->
	  <?php 
							  //get data from competencies
							  //dynamic values  
							global $wpdb;	
							$table_name		= 	$wpdb->prefix.'tincan_llms';
							
							$cmp_details1 	= 	$wpdb->get_results("SELECT * FROM $table_name WHERE id = $course_id ", ARRAY_A );

							$cmp_details1 	= 	$cmp_details1[0];
							
							if($cmp_details1['activityid']){
							
								$cmp_id = $this->getIDfromGUID($cmp_details1['activityid']);
								
								$cmp_post_type 	=	get_post_type($cmp_id);
								
								//post type check 
								if($cmp_post_type == 'competencies'){
									$is_complete = $student->is_complete( $course_id, 'course' ); 
									
									?>
									<li class="course-competency" style="list-style:none;"> 
									

										<span class="llms-lesson-complete <?php echo ( $is_complete ? 'done' : '' ); ?>">
											<i class="fa fa-check-circle"></i>
										</span>

									

										<span class="course-title <?php echo ( $is_complete ? 'done' : '' ); ?>">
										  <?php //echo  $lesson->get( 'id' ) ; ?> 
										  
												<a class="open-modal" modal_id="<?php echo 'compt_modal_id_'.$course_id; ?>" href="<?php echo get_permalink( $cmp_id ); ?>" title="completency for '<?php echo get_the_title($course_id); ?>'">
													<?php echo get_the_title($cmp_id); ?>
												</a>
												
												
												<?php 
											
											
											?>
											</span>
											<!----dialog start s---->
									<div id="<?php echo 'compt_modal_id_'.$course_id; ?>" class="modal_competencies1" title="<?php echo get_the_title($cmp_id); ?>" >
									<article>
									<?php 									
									$post_7 = get_post( $cmp_id); 
									$post_content = $post_7->post_content;
									 echo  $post_content;
									 ?>
										</article>
									</div>
											<script>
									jQuery(document).ready(function($){
										
										  $('#<?php echo 'compt_modal_id_'.$course_id; ?>').dialog({
											   autoOpen: false,  modal: true
										  });
										  
										  $('a.open-modal').click(function(e){
											   e.preventDefault();
											  var modal_id = $(this).attr('modal_id');
											   $('#'+modal_id+'').dialog( "open" );
										  });
										});
									</script>
									<!----dialog ends e---->
</li>
									

									
							<?php } } 
							?>
								
	  <!-- check for course competency e--->
		
		
		
	<ul class="llms-course-outline">
	 

		<?php foreach ( $sections as $section ) : ?>
		<?php 
		//set count to zero for each section 
		$cmp_count 	=	0; 
		
		?>
			<li class="llms-section<?php echo ( $collapse ) ? ( $section->get( 'id' ) == $current_section ) ? ' llms-section--opened' : ' llms-section--closed' : ''; ?>">

				<div class="section-header">

					<?php do_action( 'lifterlms_outline_before_header' ); ?>

					<?php if ( $collapse ) : ?>

						<span class="llms-collapse-caret">
							<i class="fa fa-caret-down"></i>
							<i class="fa fa-caret-right"></i>
						</span>

					<?php endif; ?>

					<span class="section-title"><?php echo apply_filters( 'llms_widget_syllabus_section_title', $section->get( 'title' ), $section ); ?></span>

					<?php do_action( 'lifterlms_outline_after_header' ); ?>

				</div>

				<?php foreach ( $section->get_lessons() as $lesson ) :
					$is_complete = $student->is_complete( $lesson->get( 'id' ), 'lesson' ); ?>

					<ul class="llms-lesson">

							
							  <?php 
							  //get data from competencies
							  //dynamic values  
							global $wpdb;	
							$table_name		= 	$wpdb->prefix.'tincan_llms';
							$l_id 			= 	$lesson->get( 'id' ) ;
							$cmp_details 	= 	$wpdb->get_results("SELECT * FROM $table_name WHERE id = $l_id ", ARRAY_A );

							$cmp_details 	= 	$cmp_details[0];
							
							if($cmp_details['activityid']){
							
								$cmp_id = $this->getIDfromGUID($cmp_details['activityid']);
								
								$cmp_post_type 	=	get_post_type($cmp_id);
								
								//post type check 
								if($cmp_post_type == 'competencies'){
									
									?>
									
									<li>

										<span class="llms-lesson-complete <?php echo ( $is_complete ? 'done' : '' ); ?>">
											<i class="fa fa-check-circle"></i>
										</span>

									

										<span class="lesson-title <?php echo ( $is_complete ? 'done' : '' ); ?>">
										  <?php //echo  $lesson->get( 'id' ) ; ?> 
										  
												<a class="open-modal" modal_id="<?php echo 'compt_modal_id_'.$l_id; ?>" href="<?php echo get_permalink( $cmp_id ); ?>" title="completency for '<?php echo apply_filters( 'llms_widget_syllabus_section_title', $lesson->get( 'title' ) ); ?>'">
													<?php echo get_the_title($cmp_id); ?>
												</a>
												<?php 
											
											
											?>
											</span>

									<!----dialog start s---->
									<div id="<?php echo 'compt_modal_id_'.$l_id; ?>" class="modal_competencies1" title="<?php echo get_the_title($cmp_id); ?>" >
									<article>
									<?php 									
									$post_7 = get_post( $cmp_id); 
									$post_content = $post_7->post_content;
									 echo  $post_content;
									 ?>
									 </article>
									</div>
									<script>
									
									jQuery(document).ready(function($){
										  $('#<?php echo 'compt_modal_id_'.$l_id; ?>').dialog({
											   autoOpen: false, 											  
											   modal: true
										  });
										  
										  $('a.open-modal').click(function(e){
											   e.preventDefault();
											  var modal_id = $(this).attr('modal_id');
											   $('#'+modal_id+'').dialog( "open" );  
										  });
										});
									</script>
									<!----dialog ends e---->

									</li>
								<?php 
								
								//increase count if there is competence in this section 
								$cmp_count++;
							}
							}
							
							  ?>
							 

								

							

					</ul>

				<?php endforeach; ?>
				<?php 
				//show no competeny messeage for section 
							if($cmp_count==0 ){
								_e('There are no competency for this section.','lifterlms-widgets');
							}
				?>

			</li>
			

		<?php endforeach; ?>

		<?php if ( $collapse && $toggles ) : ?>

			<li class="llms-section llms-syllabus-footer">

				<?php do_action( 'lifterlms_outline_before_footer' ); ?>

				<a class="llms-button-text llms-collapse-toggle" data-action="open" href="#"><?php _e( 'Open All', 'lifterlms' ); ?></a>
				<span>&middot;</span>
				<a class="llms-button-text llms-collapse-toggle" data-action="close" href="#"><?php _e( 'Close All', 'lifterlms' ); ?></a>

				<?php do_action( 'lifterlms_outline_after_footer' ); ?>

			</li>

		<?php endif; ?>

	</ul>

	<?php do_action( 'lifterlms_outline_after' ); ?>

</div>

		</article>
		
		
		<?php 
		//get current user's all get_achievements 
		$user_id = get_current_user_id();
		if($user_id){
		
		}else{
			//echo "please login.";
		}
		
		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Competencies', 'lifterlms-widgets' );
		?>
		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'lifterlms-widgets' ); ?></label> 
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<?php 
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

		return $instance;
	}
	
	//get post id form guid 
	function getIDfromGUID( $guid ){
		//global $wpdb;
		//return $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE guid=%s", $guid ) );
		$p_id = 0;
		//
		$args =  array(
								'posts_per_page' => -1,
								'post_type' => 'competencies',
								
							);
						$custom_posts = get_posts($args);
						foreach($custom_posts as $posts) :
							
							if( $guid== get_the_permalink($posts->ID)){
								
								$p_id		=	$posts->ID;
							}
							
						endforeach;
						wp_reset_query();
						
		return $p_id;				
						

	}
//get course id 
private function get_course_id() {

		global $post;
		$post_id = isset( $post->ID ) ? $post->ID : null;

		$course_id = null;

		if ( $post_id ) {

			switch ( $post->post_type ) {

				case 'course':
					$course_id = $post_id;
				break;

				case 'lesson':
					$lesson = llms_get_post( $post_id );
					$course_id = $lesson->get( 'parent_course' );
				break;

				case 'llms_quiz':
					$quiz = llms_get_post( $post_id );
					$lesson_id = $quiz->get_assoc_lesson( get_current_user_id() );
					if ( ! $lesson_id ) {
						$session = LLMS()->session->get( 'llms_quiz' );
						$lesson_id = ( $session && isset( $session->assoc_lesson ) ) ? $session->assoc_lesson : false;
					}
					if ( $lesson_id ) {
						$lesson = llms_get_post( $lesson_id );
						$course_id = $lesson->get( 'parent_course' );
					}
				break;

			}
		}

		return $course_id;

}

// add modal box 
public function add_modal(){
	//include js 
	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-ui-dialog');
	
	
}

} // class LLMS_Competencies_widget

// register LLMS_Competencies_widget widget
function register_LLMS_Competencies_widget() {
    register_widget( 'LLMS_Competencies_widget' );
}
add_action( 'widgets_init', 'register_LLMS_Competencies_widget' );
