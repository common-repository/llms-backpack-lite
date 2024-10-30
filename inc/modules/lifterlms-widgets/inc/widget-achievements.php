<?php 
 /**
 * Adds X_Achievementss_Widget widget.
 */
class X_Achievementss_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'X_Achievementss_Widget', // Base ID
			esc_html__( 'LifterLMS : Achievements', 'lifterlms-widgets' ), // Name
			array( 'description' => esc_html__( 'This is a widget that lists the achievements.', 'lifterlms-widgets' ),
			'classname'   => 'widget-llms-achievements',
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
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}
		
		
		//get current user's all get_achievements 
		$user_id = get_current_user_id();
		if($user_id){
			if (class_exists('LLMS_Person')) {
			$lms_std =  new LLMS_Person($user_id);
			$all_achievements = $lms_std->get_user_achievements();
				if($all_achievements && (!empty($all_achievements))){
					foreach($all_achievements as $key => $ach_array){
						
						$ach_title = $ach_array['title'];
						$ach_image = $ach_array['image'];
					
						?>
					<div class="col-lg-1 left">
						<img src="<?php echo $ach_image; ?>" title="<?php echo $ach_title; ?>" >					
							
						</img>
					</div>
					<?php 
						
					}
					
			}else{ 
				_e('No achievement earned.','lifterlms-widgets');
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

} // class X_Achievementss_Widget

// register X_Achievementss_Widget widget
function register_X_Achievementss_Widget() {
    register_widget( 'X_Achievementss_Widget' );
}
add_action( 'widgets_init', 'register_X_Achievementss_Widget' );