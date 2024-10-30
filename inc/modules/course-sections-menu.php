<?php
namespace llms_bkpk;

class CourseSectionsMenu extends Config implements RequiredFunctions {
	/**
	 * Class constructor
	 */
	public function __construct() {
		add_action( 'plugins_loaded', array( __CLASS__, 'run_frontend_hooks' ) );
	}
	/*
	 * Initialize frontend actions and filters
	 */
	public static function run_frontend_hooks() {
		if ( true === self::dependants_exist() ) {
			$llms_bkpk_active_classes_opt = get_option('llms_bkpk_active_classes');
			if(in_array('llms_bkpk\CourseSectionsMenu',$llms_bkpk_active_classes_opt)){
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
			'dashboard-customizer'=>
			'true',
			'bkpk_customizer' => 'on'),
			$url
		);
		
		$bkpk_stdbslg = \llms_bkpk\Config::get_settings_value( 'llms-bkpk-dashboard-slug','Dashboard');
		
		//assign to option value 
		$option_value =  $bkpk_stdbslg;
		$panelname = 'panel_course_outline_bkpk';
		
		$target_url = add_query_arg(array(
                'bkpk_customizer'  => 'on',
				'bkpk_current_panel'  => $panelname,
                'url'                   => urlencode(add_query_arg(array('bkpk_customizer' => 'on', 'bkpk_current_panel'  => $panelname,), site_url()."/".$option_value."/")),
                'return'                => urlencode(
				add_query_arg(
					array(
						'page' => 'bkpk_settings',
					),
					admin_url('admin.php')
				)
			),
		), admin_url('customize.php')); */

		$class_title       = esc_html__( 'Course Sections Menu', 'lifterlms-bkpk' );
		$customizer_link   = '#';
		$class_description = esc_html__( 'Show the Sections of the current course in a customizable outline', 'lifterlms-bkpk' );
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
				'inner_html'  => '<center><strong><h2><a href="https://learning-templates.com/product/lifterlms-backpack/" target="_blank" class="go-pro">Subscribe to BackPack to enable this feature</a></h2></strong></center><br/><strong><h2>Show a menu with the section names of the current course.</h2></strong>Once activated, use this <code>[llms_course_menu]</code> shortcode to display the current courses sections in a menu.<br/>When the student clicks on a menu item, it will take them to the first lesson in that section of the current course.</b>',
			),
			array(
				'type'        => 'text',
				'label'       => esc_html__( 'Font-Awesome Completed Icon', 'llms-bkpk' ),
				'placeholder'       => esc_html__( '', 'llms-bkpk' ),
				'option_name' => 'llms-bkpk-course-completed-icon',
			),
			array(
				'type'        => 'text',
				'label'       => esc_html__( 'Font-Awesome Incomplete Icon', 'llms-bkpk' ),
				'placeholder'       => esc_html__( '', 'llms-bkpk' ),
				'option_name' => 'llms-bkpk-course-incomplete-icon',
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