<?php
namespace llms_bkpk;

class LessonGrid extends Config implements RequiredFunctions {
	public function __construct() {
		add_action( 'plugins_loaded', array( __CLASS__, 'run_frontend_hooks' ) );
	}
	public static function run_frontend_hooks() {
		if ( true === self::dependants_exist() ) {
			$llms_bkpk_active_classes_opt = get_option('llms_bkpk_active_classes');
			if(in_array('llms_bkpk\LessonGrid',$llms_bkpk_active_classes_opt)){
				//if active in dashboard ticked 
			} 
		}
	}
	public static function get_details() {
/*		$url = '';
		$url = add_query_arg(
			'return',
			urlencode(
				add_query_arg(
					array(
						'page' => 'bkpk_settings',
					),
					admin_url('admin.php')
				)
			),
			$url
		);
		
		// Redirect to this panel
		$url = add_query_arg(array(
			'course-grid-customizer'=>
			'true',
			'bkpk_customizer' => 'on'),
			$url
		);
		$bkpk_stdbslg = \llms_bkpk\Config::get_settings_value( 'llms-bkpk-course-grid-slug','CourseGrid');
		
		//assign to option value 
		$option_value =  $bkpk_stdbslg;
		$panelname = 'panel_course_grid_bkpk';
		
		$target_url = add_query_arg(array(
                'bkpk_customizer' => 'on',
				'bkpk_current_panel' => $panelname,
                'url' => urlencode(add_query_arg(array('bkpk_customizer' => 'on','bkpk_current_panel'  => $panelname,), site_url()."/".$option_value."/")),
                'return' => urlencode(
				add_query_arg(
					array(
						'page' => 'bkpk_settings',
					),
					admin_url('admin.php')
				)
			),
		), admin_url('customize.php')); */

		$class_title       = esc_html__( 'Lesson Grid', 'lifterlms-bkpk' );
		$customizer_link   = '#';
		$class_description = esc_html__( 'Show your Lessons for the current Section in a stylish expandable grid.', 'lifterlms-bkpk' );
		$class_icon        = '<i class="lt_icon_fa fa fa-link-o"></i>';
		$tags              = 'general';
		$type              = 'inactive';
		return array(
			'title'            => $class_title,
			'type'             => $type,
			'tags'             => $tags,
			'customizer_link'  => $customizer_link, // OR set as null not to display
			'description'      => $class_description,
			'dependants_exist' => self::dependants_exist(),
			'settings'         => self::get_class_settings( $class_title ),
			'icon'             => $class_icon,
		);
	}
	public static function dependants_exist() {
		// Return true if no dependency or dependency is available
		return true;
	}
	public static function get_class_settings( $class_title ) {
	$pages[]   = array( 'value' => 0, 'text' => '-- Select Page --' );
		$get_pages = get_pages(
			array(
				'sort_order'  => 'asc',
				'sort_column' => 'post_title',
			) );
		foreach ( $get_pages as $page ) {
			$pages[] = array( 'value' => $page->ID, 'text' => get_the_title( $page->ID ) );
		}
		// Create options
		$options = array(
			array(
				'type'        => 'html',
				'inner_html'  => '<center><strong><h2><a href="https://learning-templates.com/product/lifterlms-backpack/" target="_blank" class="go-pro">Subscribe to BackPack to enable this feature</a></h2></strong></center><br/><strong><h2>This feature will add a Lessons block for your current section into your lesson content with a stylish expandable grid to be displayed in the lesson page.</h2></strong><br/>To add the lesson grid to your lesson page, copy this shortcode <code>[llms_lesson_grid]</code> and paste it into your lesson page anywhere you want.<br/><br/>Next, select the number of lessons you want to show in the grid before the "More" button is displayed.<br/><b>IMPORTANT NOTE:</b> The more lessons you display in the grid before the "More" button, the longer it will take to load the grid.',
			),
			array(
				'type'        => 'text',
				'label'       => esc_html__( 'Number of Lessons to show before loading more', 'llms-bkpk' ),
				'placeholder'       => esc_html__( 'Number of lessons to show', 'llms-bkpk' ),
				'option_name' => 'llms-bkpk-lesson-grid-lessons-to-show',
			),
			array(
				'type'        => 'text',
				'label'       => esc_html__( 'Course Post slug', 'llms-bkpk' ),
				'placeholder'       => esc_html__( 'course/how-to-build-a-learning-management-system-with-lifterlms', 'llms-bkpk' ),
				'option_name' => 'llms-bkpk-lesson-grid-slug',
			),
		);
		// Build html
		$html = self::settings_output(
			array(
				'class'   => __CLASS__,
				'title'   => $class_title,
				'options' => $options,
				'submit_button' => false,
			) );
		return $html;
	}
}
?>