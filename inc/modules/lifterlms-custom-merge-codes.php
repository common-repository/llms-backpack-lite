<?php
namespace llms_bkpk;

class LifterlmsCustomMergeCodes extends Config implements RequiredFunctions {
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
			if(in_array('llms_bkpk\LifterlmsCustomMergeCodes',$llms_bkpk_active_classes_opt)){
				//if active in dashboard ticked
			} 
		}
	}
	/**
	 * Description of class in Admin View
	 *
	 * @return array
	 */
	public static function get_details() {
		$class_title       = esc_html__( 'Custom Merge Codes', 'lifterlms-bkpk' );
		$customizer_link   = 'http://learning-templates.com/lifterlms-student-notes-plugin';
		$class_description = esc_html__( 'Create your own Custom email Merge Codes for your LifterLMS memberships. Upload and monitor the codes.', 'lifterlms-bkpk' );
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
	/**
	 * Does the plugin rely on another function or plugin
	 *
	 * @return boolean || string Return either true or name of function or plugin
	 *
	 */
	public static function dependants_exist() {
		// Return true if no dependency or dependency is available
		return true;
	}
	/**
	 * HTML for modal to create settings
	 *
	 * @param $class_title
	 *
	 * @return bool | string Return either false or settings html modal
	 *
	 */
	public static function get_class_settings( $class_title ) {
		// Create options
		$options = array(
			array(
				'type'       => 'html',
				'inner_html' => '<center><strong><h2><a href="https://learning-templates.com/product/lifterlms-backpack/" target="_blank" class="go-pro">Subscribe to BackPack to enable this feature</a></h2></strong></center><br/><strong><h2>How to use your custom merge code:</h2></strong><br/>Once you enable this feature, visit your WordPress Dashboard to upload and manage your codes.<br/><br/>To use a custom merge code in a LifterLMS email, paste this <code>{cm_code}</code> (include the parenthesis) into any email.<br/><br/>This code will pick up the next available code in your uploaded list.<br/><br/>This feature will also email you, the site administrator, when you have less than 10 unused codes left.<br/><br/>You can pretty much upload any text-based information you want as codes.<br/><br/>The CSV file you upload will be a single column file with no header row. Simply enter your codes into column A, starting on line 1 and add more codes in column A on line 2 and below. Once you upload the codes, you will be able to apply them globally to a membership, or individually to any memberships.<br/><br/>You can also re-upload the codes to change the membership each is assigned to. Every uploaded, or changed code will have a status message associated with it after upload, or reupload.<br/><br/>Once uploaded, you can add the email merge code to an email and it can be used as you wish.<br/><br/>An example usage may be to provide Woocommerce Coupon codes exclusinvely to students upon registration.',
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