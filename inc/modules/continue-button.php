<?php
namespace llms_bkpk;

class ContinueButton extends Config implements RequiredFunctions {

	/* Class constructor */
	public function __construct() {
		add_action( 'plugins_loaded', array( __CLASS__, 'run_frontend_hooks' ) );
	}

	/* Initialize frontend actions and filters */
	public static function run_frontend_hooks() {
		if ( true === self::dependants_exist() ) {
			$llms_bkpk_active_classes_opt = get_option('llms_bkpk_active_classes');
			if(in_array('llms_bkpk\ContinueButton',$llms_bkpk_active_classes_opt)){
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
			'continue-button-customizer'=>
			'true',
			'bkpk_customizer' => 'on'),
			$url
		);
		
		//get value using options method
//		$mydata = get_option('ContinueButton');
//		$option_value = $mydata[0]['value']; 
		
		//get value using toolkit method 
		//get student dashboard slug
		$bkpk_stdbslg = \llms_bkpk\Config::get_settings_value( 'llms-bkpk-student-dashboard-slug','ContinueButton');
		
		//assign to option value 
		$option_value =  $bkpk_stdbslg;
		$panelname = 'panel_continue_button_bkpk';
		
		$target_url = add_query_arg(array(
                'bkpk_customizer'  => 'on',
                'bkpk_current_panel'  => $panelname,
                'url'                   => urlencode(add_query_arg(array('bkpk_customizer' => 'on','bkpk_current_panel'  => $panelname,), site_url()."/".$option_value."/")),
                'return'                => urlencode(
				add_query_arg(
					array(
						'page' => 'bkpk_settings',
					),
					admin_url('admin.php')
				)
			),
		), admin_url('customize.php')); */
		
		$class_title		= esc_html__( 'Restore Continue Button', 'lifterlms-bkpk' );
		$customizer_link	= '#';
		$class_description	= esc_html__( 'Brings back the Course Continue button to the course catalogue, student dashboard, course start page and memberships.', 'lifterlms-bkpk' );
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
				'type'        => 'html',
				'inner_html'  => '<center><strong><h2><a href="https://learning-templates.com/product/lifterlms-backpack/" target="_blank" class="go-pro">Subscribe to BackPack to enable this feature</a></h2></strong></center><br/><strong><h2>Restore that course Continue button</h2></strong>Once activated, you can then style the course Continue button in the WordPress Customizer using the available styling controls.<br/><br/>To open the WordPress customizer on your course catalogue, student dashboard, or even a course start page and active the styling controls, copy the slug of the URL from your website and paste it into the field below. Your slug can be a simple one like <b>my-courses</b> or can be more complex like <b>course-catalogue/my-course-name</b>',
			),
			array(
				'type'        => 'text',
				'label'       => esc_html__( 'Student Dashboard slug', 'llms-bkpk' ),
				'placeholder'       => esc_html__( 'my-courses', 'llms-bkpk' ),
				'option_name' => 'llms-bkpk-student-dashboard-slug',
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