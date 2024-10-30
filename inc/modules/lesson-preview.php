<?php
namespace llms_bkpk;

class LessonPreview extends Config implements RequiredFunctions {
	public function __construct() {
		add_action( 'plugins_loaded', array( __CLASS__, 'run_frontend_hooks' ) );
	}
	public static function run_frontend_hooks() {
		if ( true === self::dependants_exist() ) {
			$llms_bkpk_active_classes_opt = get_option('llms_bkpk_active_classes');
			if(in_array('llms_bkpk\LessonPreview',$llms_bkpk_active_classes_opt)){
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

		$class_title       = esc_html__( 'Lessons Preview', 'lifterlms-bkpk' );
		$customizer_link   = '#';
		$class_description = esc_html__( 'Add Tags to LifterLMS Lessons. Add a reserved word so guest users can preview / use your lessons in isolation of the course and reports. Present your lessons in an animated grid gallery.', 'lifterlms-bkpk' );
		$class_icon        = '<i class="lt_icon_fa fa fa-minus-square-o"></i>';
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
				'type'       => 'html',
				'inner_html' => '<center><strong><h2><a href="https://learning-templates.com/product/lifterlms-backpack/" target="_blank" class="go-pro">Subscribe to BackPack to enable this feature</a></h2></strong></center><br/><strong><h2>Add a searchable library of lessons using a shortcode.</h2></strong>This feature adds tags to your LifterLMS Lessons with a reserved word (tag) named "Free".<br/>The reserved word tag, when added to a lesson, will let your Guest (not logged in users) users use that lesson in isolation of the course.<br/>Adding any other tag will let registered users use a lesson in isolation of the course. so guests will see, and search for, Free lessons and registered users will see, and search for, any tagged lessons.<br/><br/>Adding the <code>[llms_lessons_preview]</code> shortcode to any page or post will display an animated searchable grid of the lessons you have tagged and all lessons, including H5P and e-learning content based lessons will be able to be used in full, but willnot be recorded for the user.'
			),
			array(
				'type'        => 'text',
				'label'       => esc_html__( 'Number of columns', 'llms-bkpk' ),
				'placeholder'       => esc_html__( '', 'llms-bkpk' ),
				'option_name' => 'llms-bkpk-lesson-prev-cols',
			),
		);
/*		global $wp_roles;
		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new \WP_Roles();
			$roles    = $wp_roles->get_names();
		} else {
			$roles = $wp_roles->get_names();
		}
		$options = array();
		foreach ( $roles as $role_value => $role_name ) {
			array_push( $options, array( 'type' => 'checkbox', 'label' => $role_name, 'option_name' => $role_value ) );
		} */
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