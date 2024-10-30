<?php
namespace llms_bkpk;

class LifterlmsWidgets extends Config implements RequiredFunctions {
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
			if(in_array('llms_bkpk\LifterlmsWidgets',$llms_bkpk_active_classes_opt)){
				//if active in dashboard ticked
				include_once( dirname( __FILE__ ) . '/lifterlms-widgets/lifterlms-widgets.php' );
			} 
		}
	}
	/**
	 * Description of class in Admin View
	 *
	 * @return array
	 */
	public static function get_details() {
		$class_title       = esc_html__( 'Widgets', 'lifterlms-bkpk' );
		$customizer_link   = null;
		$class_description = esc_html__( 'A set of widgets that includes a Dynamic Widget Creator in your WordPress editor page so you can create unlimited widgets specific to your course or lesson without using custom sidebars. If you own a copy of LifterLMS xAPI, you get a Related Competencies widget as well.', 'lifterlms-bkpk' );
		$class_icon        = '<i class="lt_icon_fa fa fa-link-o"></i>';
		$tags              = 'general';
		$type              = 'free';
		return array(
			'title'            => $class_title,
			'type'             => $type,
			'tags'             => $tags,
			'customizer_link'  => $customizer_link, // OR set as null not to display
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
				'type'       => 'html',
				'inner_html' => "<strong><h2>Add custom LifterLMS widgets/shortcodes to your website.</h2></strong>This feature provides you a set of widgets that you can use from your WordPress Admin Dashboard > Appearance > Widgets page. The widgets and their equivalent shortcodes are as follows:<ul><li style='width:100%;'><b>Achievements widget and shortcode <code>[lifterlms_my_achievements]</code></b>: lets you display your students achievement badges anywhere you want in your site.</li><li style='width:100%;'><b>Certificates widget</b>: lets you display your students list of certificates anywhere you want in your site.</li><li style='width:100%;'><b>Related Courses widget</b>: lets you display a gallery of courses in the same Course Category as the current course. Use this in your Course and Lesson posts.</li><li style='width:100%;'><b>Resume Lesson widget</b>: Enter your <b>RESUME MY COURSE</b> text and display this button anywhere you want. This button, when clicked, will send your student to their next incomplete lesson in their last course entered.</li><li style='width:100%;'><b>If you own LifterLMS xAPI, you also have a Competencies widget</b>: This widget will list the competencies earned by the student.</li></ul>This feature also provides you a <b>Dynamic Widget Generator</b> for use in any of your LifterLMS courses and lessons. The generator lets you create your own widgets specific to that course or lesson post using a rich-text editor so your course and lesson sidebars now become dynamicly populated.",
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