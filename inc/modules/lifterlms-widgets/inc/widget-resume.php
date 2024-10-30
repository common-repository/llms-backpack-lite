<?php 
 /**
 * Adds X_Resume_Widget widget.
 */
class X_Resume_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'X_Resume_Widget', // Base ID
			esc_html__( 'LifterLMS : Resume Lesson', 'lifterlms-widgets' ), // Name
			array( 'description' => esc_html__( 'To Resume last uncompleted lesson.', 'lifterlms-widgets' ),
			'classname'   => 'widget-llms-resume-button',
			) // Args
		);
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
			//echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}
		
		//get current user all course 
		$user_id = get_current_user_id();
		
		if($user_id){
			if (class_exists('LLMS_Student')) {
			$lms_std =  new LLMS_Student($user_id);
			$all_courses = $lms_std->get_courses();
			
			//check if enrolled to any course 
			if($all_courses['results']){
					foreach($all_courses['results'] as $key => $course_id){
						//get all lessons
						$lms_lesson = new LLMS_Course($course_id);
						$lms_lessons = $lms_lesson->get_lessons('ids');
						//loop throught every lesson 
						foreach($lms_lessons as $k => $lms_lesson_id){
							
							//check if lesson complete 
							if($lms_std->is_complete($lms_lesson_id,'lesson')){
								//echo $rsm_lnk = get_the_permalink($lms_lesson_id);
							}else{
								$rsm_lnk = get_the_permalink($lms_lesson_id);
								break;
							}
						}
					}
					?>
					<div class="col-lg-6">
					 <?php if(!empty($rsm_lnk)){ ?>
						<a class="primary btn button" href="<?php echo $rsm_lnk; ?>">
							<?php //_e('Resume','lifterlms-widgets');?>
							<?php echo $instance['title']; ?>
						</a>
					 <?php }else{ ?>
						<a class="primary btn button" href="#">
							<?php _e('No Lesson to Resume','lifterlms-widgets');?>
						</a>
					 <?php } ?>
					</div>
					<?php 
			}else{
				//echo "please enroll to any course";
			}
			}
		
		
		
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
		$title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'New title', 'text_domain' );
		?>
		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'text_domain' ); ?></label> 
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

} // class X_Resume_Widget

// register X_Resume_Widget widget
function register_X_Resume_Widget() {
    register_widget( 'X_Resume_Widget' );
}
add_action( 'widgets_init', 'register_X_Resume_Widget' );