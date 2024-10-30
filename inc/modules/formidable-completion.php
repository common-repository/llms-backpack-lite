<?php
namespace llms_bkpk;

class formidableCompletion extends Config implements RequiredFunctions {

	/* Class constructor */
	public function __construct() {
		add_action( 'plugins_loaded', array( __CLASS__, 'run_frontend_hooks' ) );
	}

	/* Initialize frontend actions and filters */
	public static function run_frontend_hooks() {
		if ( true === self::dependants_exist() ) {
			$llms_bkpk_active_classes_opt = get_option('llms_bkpk_active_classes');
			if(in_array('llms_bkpk\FormidableCompletion',$llms_bkpk_active_classes_opt)){

				//if active in dashboard ticked
				include_once( dirname( __FILE__ ) . '/formidable-completion/formidable-completion.php' );
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
			'formidable-completion-customizer'=>
			'true',
			'bkpk_customizer' => 'on'),
			$url
		); */
		
		//get value using options method
//		$mydata = get_option('ContinueButton');
//		$option_value = $mydata[0]['value']; 
		
		//get value using toolkit method 
		//get student dashboard slug
/*		$bkpk_stdbslg = \llms_bkpk\Config::get_settings_value( 'llms-bkpk-student-dashboard-slug','FormidableCompletion');
		
		//assign to option value 
		$option_value =  $bkpk_stdbslg;
		
		$target_url = add_query_arg(array(
                'bkpk_customizer'  => 'on',
                'url'                   => urlencode(add_query_arg(array('bkpk_customizer' => 'on'), site_url()."/".$option_value."/")),
                'return'                => urlencode(
				add_query_arg(
					array(
						'page' => 'bkpk_settings',
					),
					admin_url('admin.php')
				)
			),
		), admin_url('customize.php')); */
		
		$class_title		= esc_html__( 'Form Completes Lesson', 'lifterlms-bkpk' );
		$customizer_link	= null;
		$class_description	= esc_html__( 'Have your Form Submit button complete a Lesson.', 'lifterlms-bkpk' );
		$class_icon			= '<i class="lt_icon_fa fa fa-link-o"></i>';
		$tags				= 'general';
		$type				= 'free';
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
	$pages[]   = array( 'value' => 0, 'text' => 'Select Form Plugin' );
	$pages[] = array( 'value' => 1, 'text' => 'Formidable' );
		$options = array(
			array(
				'type'        => 'html',
				'inner_html'  => '<strong><h2>Have your Form complete your Lesson</h2></strong><br/>With this feature, you can keep your LifterLMS Mark Complete button, or even remove it totally and the lesson will still be completed by the forms Submit button.<br/><br/>You can also choose to keep the user in the same lesson, or advance them to the next lesson after they click on the forms Submit button.<br/>A delay period is also provided in case you want your form to show a success or fail message before jumping to the next lesson.<br/><br/>Why is there a drop-down menu for the form selection when there is only Formidable available?<br/><br/>This feature will have other form systems added to it. Next-up is Ninja forms.<br/><br/><b>IMPORTANT NOTE:</b> If you check the <b>Advance user to next lesson</b> checkbox, your form MUST NOT be set to use AJAX to submit the form.',
			),
			array(
				'type'        => 'select',
				'label'       => esc_html__( 'Pick your form plugin (Formidable is only available in this Free version)', 'llms-bkpk' ),
				'select_name' => 'llms-bkpk-form-type-select',
				'options'     => $pages,

			),
			array(
				'type'        => 'checkbox',
				'label'       => esc_html__( 'Advance user to next lesson after submitting form', 'llms-bkpk' ),
//				'placeholder'       => esc_html__( 'my-courses', 'llms-bkpk' ),
				'option_name' => 'llms-bkpk-formidable-advance-user',
			),
			array(
				'type'        => 'text',
				'label'       => esc_html__( 'Delay (in milliseconds) before jumping to next lesson', 'llms-bkpk' ),
				'placeholder'       => esc_html__( '5000', 'llms-bkpk' ),
				'option_name' => 'llms-bkpk-formidable-advance-user-delay',
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
?>