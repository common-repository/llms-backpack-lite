<?php
namespace llms_bkpk;

class FieldManager extends Config implements RequiredFunctions {
	public function __construct() {
		add_action( 'plugins_loaded', array( __CLASS__, 'run_frontend_hooks' ) );
	}
	public static function run_frontend_hooks() {
		if ( true === self::dependants_exist() ) {
			$llms_bkpk_active_classes_opt = get_option('llms_bkpk_active_classes');
			if(in_array('llms_bkpk\FieldManager',$llms_bkpk_active_classes_opt)){
				//if active in dashboard ticked
						include_once( dirname( __FILE__ ) . '/llms-custom-reg-fields/llms-custom-reg-fields.php' );
				} 
		}
	}
	public static function get_details() {
		$class_title       = esc_html__( 'Field Manager', 'lifterlms-bkpk' );
		$customizer_link   = '#';
		$class_description = esc_html__( 'Create Drag and Drop form fields. Use these fields in the Checkout, Registration, Edit Account forms, as well as in Woocommerce Accounts and WordPress Users Profiles. Add the information to LifterLMS Reports and Exports.', 'lifterlms-bkpk' );
		$class_icon        = '<i class="lt_icon_fa fa fa-link-o"></i>';
		$tags              = 'general';
		$type              = 'free';
		return array(
			'title'            => $class_title,
			'type'             => $type,
			'tags'             => $tags,
			'customizer_link'  => null, // OR set as null not to display
			'fields_link'	   => admin_url('admin.php?page=llms_bkpk-user-fields'),
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
				'inner_html' => "<strong><h2>Enable the form types you use.</h2></strong>Using a visual form builder, Field Manager, lets you insert LifterLMS form fields.<br/><br/>By enabling these checkboxes, Your Fields page will let your select the form type your want to add your own fields into using our visual form builder.<br/><br/>the fields types are very expansive in the form builder; letting you add anything from Avatars to URLs for the student.<br/><br/>All fields are also added to the students dashbaord in a section named Extra student Details.<br/><br/>Finally, when creating a fields, you can choose to add the value into your LifterLMS Reports and CSV downloads.",
			),
			array(
				'type'        => 'checkbox',
				'label'       => esc_html__( 'Enable in Registration', 'llms-bkpk' ),
				'option_name' => 'llms-bkpk-forms-enable-reg',
			),
			array(
				'type'        => 'html',
				'inner_html'  => 'Enable in Edit Account checkbox is not available in this free version', 'llms-bkpk',
			),
			array(
				'type'        => 'html',
				'inner_html'  => 'Enable in Woocommerce Registration Account checkbox is not available in this free version', 'llms-bkpk',
			),
			array(
				'type'        => 'html',
				'inner_html'  => 'Enable in Woocommerce Edit Account checkbox is not available in this free version', 'llms-bkpk',
			),
			array(
				'type'        => 'checkbox',
				'label'       => esc_html__( 'Enable section title for all forms', 'llms-bkpk' ),
				'option_name' => 'llms-bkpk-forms-enable-reg-title',
			),
			array(
				'type'        => 'text',
				'label'       => esc_html__( 'Section title', 'llms-bkpk' ),
				'placeholder' => esc_html__( '', 'llms-bkpk' ),
				'option_name' => 'llms-bkpk-forms-reg-section-title',
			),
		);
		// Build html
		$html = self::settings_output(
			array(
				'class'   => __CLASS__,
				'title'   => $class_title,
				'options' => $options,
			) );
		return $html;
	}
	
}