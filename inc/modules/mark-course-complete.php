<?php
namespace llms_bkpk;

class MarkCourseComplete extends Config implements RequiredFunctions {

	/* Class constructor */
	public function __construct() {
		add_action( 'plugins_loaded', array( __CLASS__, 'run_frontend_hooks' ) );
	}

	/* Initialize frontend actions and filters */
	public static function run_frontend_hooks() {
		if ( true === self::dependants_exist() ) {
			$llms_bkpk_active_classes_opt = get_option('llms_bkpk_active_classes');
			if(in_array('llms_bkpk\MarkCourseComplete',$llms_bkpk_active_classes_opt)){

				//if active in dashboard ticked
			} 
		}
	}

	/* Theme Customizer Redirect */
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
		
		$class_title		= esc_html__( 'Mark a Users Course Complete/Reset', 'lifterlms-bkpk' );
		$customizer_link	= null;
		$class_description	= esc_html__( 'Mark a course a completed for a student in their user profile. Also Reset full course.', 'lifterlms-bkpk' );
		$class_icon			= '<i class="lt_icon_fa fa fa-link-o"></i>';
		$tags				= 'general';
		$type				= 'inactive';
		return array(
			'title'				=> $class_title,
			'type'				=> $type,
			'tags'				=> $tags,
			'customizer_link'	=> (!empty($customizer_link)) ? $customizer_link : null,
			'description'		=> $class_description,
			'dependants_exist'	=> self::dependants_exist(),
			'settings'			=> self::get_class_settings( $class_title ),
			'icon'				=> $class_icon,
		);
	}

	/* Does the plugin rely on another function or plugin */
	public static function dependants_exist() {
		// Return true if no dependency or dependency is available
		return true;
	}

	/* HTML for modal to create settings */
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
		$options = array(
			array(
				'type'       => 'html',
				'inner_html' => '<center><strong><h2><a href="https://learning-templates.com/product/lifterlms-backpack/" target="_blank" class="go-pro">Subscribe to BackPack to enable this feature</a></h2></strong></center><br/><strong><h2>Complete or reset a student in a course.</h2></strong>Adds a new section to each users profile.<br/>This new section contains 2 buttons:<ul><li style="width:100%;">One button to complete a student in a course</li><li style="width:100%;">One button to reset a student in a course</li></ul>When reseting a student in a course, you can optionally clear their quiz attempts by checking the box below and saving these settings.<br/>You can revisit this panel to check or uncheck this checkbox any time you want to change this selection.<br/>Once saved, visit a students User Profile to complete or incomplete them in a course.',
			),
			array(
				'type'        => 'checkbox',
				'label'       => esc_html__( 'Reset Quiz Attempts', 'llms-bkpk' ),
				'option_name' => 'llms-bkpk-reset-quiz-attempts',
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