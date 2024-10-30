<?php
namespace llms_bkpk;

class ThirdPartyReg extends Config implements RequiredFunctions {
	public function __construct() {
		add_action( 'plugins_loaded', array( __CLASS__, 'run_frontend_hooks' ) );
	}
	public static function run_frontend_hooks() {
		if ( true === self::dependants_exist() ) {
			$llms_bkpk_active_classes_opt = get_option('llms_bkpk_active_classes');
			if(in_array('llms_bkpk\ThirdPartyReg',$llms_bkpk_active_classes_opt)){
				//if active in dashboard ticked
			} 
		}
	}
	public static function get_details() {
		$class_title       = esc_html__( '3rd. Party Registration', 'lifterlms-bkpk' );
		$customizer_link   = null;
		$class_description = esc_html__( 'Let a 3rd party purchase a course for someone.', 'lifterlms-bkpk' );
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
		// Create options
		$options = array(
			array(
				'type'        => 'html',
				'inner_html'  => '<center><strong><h2><a href="https://learning-templates.com/product/lifterlms-backpack/" target="_blank" class="go-pro">Subscribe to BackPack to enable this feature</a></h2></strong></center><br/><strong><h2>This feature adds an area for a person to enter their payment information while purchasing a course for someone else.</h2></strong>Once the purchase process is completed, the buyers information is no longer retained on the server except in a LifterLMS Order so any recurring payments will continue with this information.<br/>The students user is created, a WordPress Welcome and Password email and any LifterLMS engagement emails are sent to the students email address and the student is registered to the access plan.',
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