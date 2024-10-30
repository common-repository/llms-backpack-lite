<?php
namespace llms_bkpk;

class ReenrollUsers extends Config implements RequiredFunctions {
	public function __construct() {
		add_action( 'plugins_loaded', array( __CLASS__, 'run_frontend_hooks' ) );
	}
	public static function run_frontend_hooks() {
		if ( true === self::dependants_exist() ) {
			$llms_bkpk_active_classes_opt = get_option('llms_bkpk_active_classes');
			if(in_array('llms_bkpk\ReenrollUsers',$llms_bkpk_active_classes_opt)){
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

		$class_title       = esc_html__( 'Upsell Enrollments', 'lifterlms-bkpk' );
		$customizer_link           = '';
		$class_description = esc_html__( 'When upselling a user from a course to a membership, keep the user enrolled in the course as long as the membership is active.', 'lifterlms-bkpk' );
		$class_icon        = '<i class="lt_icon_fa fa fa-link-o"></i>';
		$tags              = 'general';
		$type              = 'inactive';
		return array(
			'title'            => $class_title,
			'type'             => $type,
			'tags'             => $tags,
			'customizer_link'  => null, // OR set as null not to display
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
		$pages[]   = array( 'value' => 0, 'text' => 'Select a Course' );
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
				'inner_html'  => '<center><strong><h2><a href="https://learning-templates.com/product/lifterlms-backpack/" target="_blank" class="go-pro">Subscribe to BackPack to enable this feature</a></h2></strong></center><br/><strong><h2>This feature will remove the need to clone a course when upsold into a membership.</h2></strong>You can already add the same course into a membership with LifterLMS. You can also sell the course on its own and sell it in the membership but when that course access plan ends, it also ends in the membership, so the student looses access to that course when the original access plan requires either a recurring purchase, or terminates.<br/>Your only alternative, until now, is to clone the course and use the cloned course in the membership. The problem, however, is the cloned course now needs to be completed again by the student.<br/>This is a complex scenario that will not work when the other courses in the membership require this course be completed as a prerequisite.<br/>This feature overcomes that issue.<br/><br/>This feature allows for the following use cases:<ol><li>Students can buy the course standalone, then upgrade to the membership before the course expires, or</li><li>Students can purchase the membership which includes the course, or</li><li>Students can purchase the course, let it expire, then later purchase the membership and the course will un-expire for them as well, or</li><li>When used in conjunction with the End User Access feature, course access can be extended beyond the default access plan settings.</li></ol>Select up to 2 memberships you want to include exisiting enrollments into.',
			),
			array(
				'type'        => 'select',
				'label'       => esc_html__( 'Course ending later', 'llms-bkpk' ),
				'placeholder'       => esc_html__( 'Select a course with a later end date', 'llms-bkpk' ),
				'select_name' => 'llms-bkpk-upsell-course1',
				'options'     => $pages,
			),
			array(
				'type'        => 'select',
				'label'       => esc_html__( 'Course ending later', 'llms-bkpk' ),
				'placeholder'       => esc_html__( 'Select a course with a later end date', 'llms-bkpk' ),
				'select_name' => 'llms-bkpk-upsell-course2',
				'options'     => $pages,
			),
		);
		// Build html
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