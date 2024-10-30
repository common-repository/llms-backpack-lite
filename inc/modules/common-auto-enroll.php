<?php
namespace llms_bkpk;

class CommonAutoEnroll extends Config implements RequiredFunctions {
	public function __construct() {
		add_action( 'plugins_loaded', array( __CLASS__, 'run_frontend_hooks' ) );
	}
	public static function run_frontend_hooks() {
		if ( true === self::dependants_exist() ) {
			$llms_bkpk_active_classes_opt = get_option('llms_bkpk_active_classes');
			if(in_array('llms_bkpk\CommonAutoEnroll',$llms_bkpk_active_classes_opt)){
				//if active in dashboard ticked
			} 
		}
	}
	public static function get_details() {
		$class_title       = esc_html__( 'Common Auto-Enroll', 'lifterlms-bkpk' );
		$customizer_link   = null;
		$class_description = esc_html__( 'Without using Memberships, Auto-enroll new students to a common course if they are enrolled in any other course.', 'lifterlms-bkpk' );
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
		$pages[]   = array( 'value' => 0, 'text' => 'Select Course' );
		$get_pages = get_posts(
			array(
				'post_type' => array( 'course' ),
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
				'inner_html'  => '<center><strong><h2><a href="https://learning-templates.com/product/lifterlms-backpack/" target="_blank" class="go-pro">Subscribe to BackPack to enable this feature</a></h2></strong></center><br/><strong><h2>This feature will auto-enroll newly registered students into a common course.</h2></strong>You do not need to use memberships with this feature, however, if a course is in an existing membership, you can still select it.<br/>Existing users will not be auto-enrolled into the common course.<br/>Select the course you want to automatically enroll new users into below.',
			),
			array(
				'type'        => 'select',
				'label'       => esc_html__( 'Common Course', 'llms-bkpk' ),
				'placeholder'       => esc_html__( 'Select the common course', 'llms-bkpk' ),
				'select_name' => 'llms-bkpk-common-course-list',
				'options'     => $pages,
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