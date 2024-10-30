<?php 
 /**
 * Adds Related Courses widget.
 */
class Related_Courses_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'Related_Courses_Widget', // Base ID
			esc_html__( 'LifterLMS : Related Courses', 'lifterlms-widgets' ), // Name
			array( 'description' => esc_html__( 'This is a widget that lists the Related Courses.', 'lifterlms-widgets' ),
			'classname'   => 'widget-llms-related-courses',
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
		//echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}
		
		
		//get current user's all get_achievements 
		$user_id = get_current_user_id();
		if($user_id){
			if (class_exists('LLMS_Lesson')) {
				
				
			$llms_lesson_id = get_the_ID(); //assuming this widget used only on lesson pages
			$llMS_lesson = new LLMS_Lesson($llms_lesson_id);
			$llms_crs_id = $llMS_lesson->get_parent_course($llms_lesson_id);
			
			$lms_std =  new LLMS_Course($llms_crs_id);
			$all_cats = $lms_std->get_categories();
			
			
			//make cat array 
			$cat_array = array();
			foreach($all_cats as $key => $single_cat){
				$cat_array[] = $single_cat->term_id;
			}
			$cat_ids =  implode(',',$cat_array);
		
			
				/*****/
				// The Query
				$args = array(
						'post_type' => array( 'course' ),
						/* 'post__not_in' => array( $llms_crs_id ), 
						//'category__in' => array( $cat_ids )
						'tax_query' => array(
											array(
												'taxonomy' => 'course_cat',
												'field'    => 'term_id',
												'terms'    => array( $cat_ids ),
												'operator' => 'IN',
											),
										),*/
					);
				$the_query = new WP_Query( $args );

				// The Loop
				if ( $the_query->have_posts() ) {
					//echo '<ul>';
					?>
						<div class="col-lg-12">
					<?php 
					while ( $the_query->have_posts() ) {
						$the_query->the_post();
						?>
						<div class="col-lg-3 left single-box-btm">
						 
						 <p class="img-box">
							<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_post_thumbnail('small'); ?></a>
						 </p>
						<h4> <?php the_title(); ?> </h4>
						<?php 
							
							//echo ' - '.get_the_ID();
							//progress
							
							//$lms_prg =  new LLMS_Course(get_the_ID());
							//$lms_prg_data = $lms_prg->get_student_progress();
							/* echo "<pre>";
							print_r($lms_prg_data);
							echo "</pre>";  */
							?>
							<p>
								<a class="btn primary button" href="<?php the_permalink(); ?>">View Course</a>
							</p>
							
							<?php	
							$go_down =1;
								$current_crs_ID = get_the_ID();
								if ( ! llms_is_user_enrolled( get_current_user_id(), $current_crs_ID ) ) {
									//return;
										$go_down =0;
								}
								if(	$go_down ){

								$student = new LLMS_Student();

								$progress = $student->get_progress( $current_crs_ID, 'course' );
								?>

								<div class="llms-course-progress">

									<?php if ( apply_filters( 'lifterlms_display_course_progress_bar', true ) ) : ?>

										<?php lifterlms_course_progress_bar( $progress, false, false ); ?>

									<?php endif; ?>

									<?php if ( 100 == $progress ) : ?>

										<p><?php _e( 'Course Complete', 'lifterlms' ); ?></p>

									<?php else : ?>

										<?php $lesson = $student->get_next_lesson( $current_crs_ID);
										if ( $lesson ) : ?>

											<a class="llms-button-primary" href="<?php echo get_permalink( $lesson ); ?>">

												<?php if ( 0 == $progress ) : ?>

													<?php _e( 'Get Started', 'lifterlms' ); ?>

												<?php else : ?>

													<?php _e( 'Continue', 'lifterlms' ); ?>

												<?php endif; ?>

											</a>


										<?php endif; ?>

									<?php endif; ?>
								</div>
							<?php } // endo of go down ?>
						</div>
						<?php 
					}
					?>
					</div> <!-- end col-12-->
					<?php 
					/* Restore original Post Data */
					wp_reset_postdata();
				} else {
					// no posts found
					echo "no course found";
				}
				/*****/
			
			
			
			}
		
		
		
		}else{
			//echo "please login.";
		}
		
		
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

} // class Related_Courses_Widget

// register Related_Courses_Widget widget
function register_Related_Courses_Widget() {
    register_widget( 'Related_Courses_Widget' );
}
add_action( 'widgets_init', 'register_Related_Courses_Widget' );