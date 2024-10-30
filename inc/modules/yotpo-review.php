<?php
namespace llms_bkpk;

class YotpoReview extends Config implements RequiredFunctions {
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
			if(in_array('llms_bkpk\YotpoReview',$llms_bkpk_active_classes_opt)){
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
		$class_title       = esc_html__( 'Yotpo Social Reviews', 'lifterlms-bkpk' );
		$customizer_link           = '#';
		$class_description = esc_html__( 'Yotpo Social Reviews helps Woocommerce store owners with LifterLMS generate a ton of reviews for their products and courses. Yotpo is the only solution which makes it easy to share your reviews automatically to your social networks to gain a boost in traffic and an increase in sales.', 'lifterlms-bkpk' );
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
			//'settings'         => false,
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
				'inner_html'  => '<center><strong><h2><a href="https://learning-templates.com/product/lifterlms-backpack/" target="_blank" class="go-pro">Subscribe to BackPack to enable this feature</a></h2></strong></center><br/><strong><h2>Replace the LifterLMS Course Review feature with this far more advanced feature.</h2></strong>Connect/combine LifterLMS and WooCommerce products reveiws into one monetized platform.<br/>This feature also replaces the LifterLMS Mailchimp plugin letting you use YotPo for all your communication and reveiws in one place.<br/>Styling of the Reviews area is performed in the YotPo platform.',
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