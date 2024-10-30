<?php
 /**
 * Adds llms_student_notes widget.
 */
class llms_student_notes extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'llms_student_notes', // Base ID
			esc_html__( 'LLMS : Student Notes', 'lifterlms_student_notes' ), // Name
			array( 'description' => esc_html__( 'Add notes to course lesson and quiz.', 'lifterlms_student_notes' ), ) // Args
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
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}
		
		//get current user 
		$user_id = get_current_user_id();
		
		if($user_id){
				//check for post type only allow on course,lesson and quiz 
				$post_type = get_post_type(get_the_ID());			
						if($post_type == 'course' or $post_type == 'lesson' or $post_type == 'llms_quiz'){
							?>
							<div class="llms-note-body">
								
								
									<?php echo do_shortcode('[llms_add_new_note]'); ?>
									<?php echo do_shortcode('[llms_notes_list]'); ?>
									
								
							</div>
						<?php 
						}else{
							?>
							<div class="llms-note-body">
								
									<span class="alert alert-warning">
									<?php 
											_e('Notes can be added only to a course, lesson and quiz.','lifterlms_student_notes');
										?>
										</span>
								
							</div>
							<?php }
		}else{
			?>
			<div class="llms-note-body">
			
					<span class="alert alert-warning">
					<?php 
						_e('Please login to see notes.','lifterlms_student_notes');
					?>
					</span>
				
			</div>
			<?php 
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
		$title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Notes:', 'lifterlms_student_notes' );
		?>
		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'lifterlms_student_notes' ); ?></label> 
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

} // class llms_student_notes

// register llms_student_notes widget
function register_llms_student_notes() {
    register_widget( 'llms_student_notes' );
}
add_action( 'widgets_init', 'register_llms_student_notes' );