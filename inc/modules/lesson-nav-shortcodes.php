<?php
namespace llms_bkpk;

class LessonNavShortcodes extends Config implements RequiredFunctions {
	public function __construct() {
		add_action( 'plugins_loaded', array( __CLASS__, 'run_frontend_hooks' ) );
	}
	public static function run_frontend_hooks() {
		if ( true === self::dependants_exist() ) {
			$llms_bkpk_active_classes_opt = get_option('llms_bkpk_active_classes');
			if(in_array('llms_bkpk\LessonNavShortcodes',$llms_bkpk_active_classes_opt)){
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

		$class_title       = esc_html__( 'Lesson Navigation Shortcodes', 'lifterlms-bkpk' );
		$customizer_link   = '#';
		$class_description = esc_html__( 'Create customizable lesson navigation buttons.', 'lifterlms-bkpk' );
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
				'type'       => 'html',
				'inner_html' => '<center><strong><h2><a href="https://learning-templates.com/product/lifterlms-backpack/" target="_blank" class="go-pro">Subscribe to BackPack to enable this feature</a></h2></strong></center><br/><strong><h2>Replace the LifterLMS Lesson Navigation blocks with any of the ones listed here.</h2></strong>The [llms_show_all_nav] shortcode contains both Next and Previous buttons.<br/>The [llms_show_next_nav] and [llms_show_prev_nav] shortcodes show the related Navigation Blocks.<br>Show both Next and Previous Lesson blocks just as in LifterLMS, but put them anywhere you want <code>[llms_show_all_nav]</code><br>Show individual Lesson Navigation blocks <code>[llms_show_next_nav]</code> and <code>[llms_show_prev_nav]</code>'
			),
			array(
				'type'        => 'checkbox',
				'label'       => esc_html__( 'Remove Lesson titles in buttons', 'llms-bkpk' ),
				'option_name' => 'llms-bkpk-lesson-nav-alt-text',
			),
			array(
				'type'        => 'text',
				'label'       => esc_html__( 'Alternate Previous Button Text', 'llms-bkpk' ),
				'placeholder'       => esc_html__( 'Previous Lesson', 'llms-bkpk' ),
				'option_name' => 'llms-bkpk-lesson-nav-prev-text',
			),
			array(
				'type'        => 'text',
				'label'       => esc_html__( 'Alternate Next Button Text', 'llms-bkpk' ),
				'placeholder'       => esc_html__( 'Next Lesson', 'llms-bkpk' ),
				'option_name' => 'llms-bkpk-lesson-nav-next-text',
			),
			array(
				'type'       => 'html',
				'inner_html' => "Visit the Font-Awesome webpage to find the Font-Awesome icons you wish to use with your navigation panels:</strong> <a href='https://fontawesome.bootstrapcheatsheets.com/' target='_blank'>https://fontawesome.bootstrapcheatsheets.com/</a><br/><br/>Copy the Font-Awesome class of your preferred icon (less the '.' before it) and paste that code into the related field below.<br/>If you do not want to use any icons, do not paste any class names into the fields."
			),
			array(
				'type'        => 'text',
				'label'       => esc_html__( 'Font-Awesome Previous icon class', 'llms-bkpk' ),
				'placeholder'       => esc_html__( 'fa-angle-left', 'llms-bkpk' ),
				'option_name' => 'llms-bkpk-lesson-nav-prev',
			),
			array(
				'type'        => 'text',
				'label'       => esc_html__( 'Font-Awesome Next icon class', 'llms-bkpk' ),
				'placeholder'       => esc_html__( 'fa-angle-right', 'llms-bkpk' ),
				'option_name' => 'llms-bkpk-lesson-nav-next',
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