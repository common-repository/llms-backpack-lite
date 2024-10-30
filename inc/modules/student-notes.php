<?php
namespace llms_bkpk;

class StudentNotes extends Config implements RequiredFunctions {
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
			if(in_array('llms_bkpk\StudentNotes',$llms_bkpk_active_classes_opt)){
				//if active in dashboard ticked
						include_once( dirname( __FILE__ ) . '/lifterlms-student-notes/lifterlms-student-notes.php' );
				} 
		}
	}
	/**
	 * Description of class in Admin View
	 *
	 * @return array
	 */
	public static function get_details() {
		$class_title       = esc_html__( 'Student Notes', 'lifterlms-bkpk' );
		$customizer_link   = null;
		$class_description = esc_html__( 'Add Student Notes to your courses, lesson and quizzes and optionally communicate with your students', 'lifterlms-bkpk' );
		$class_icon        = '<i class="lt_icon_fa fa fa-minus-square-o"></i>';
		$tags              = 'general';
		$type              = 'free';
		return array(
			'title'            => $class_title,
			'type'             => $type,
			'tags'             => $tags,
			'customizer_link'          => $customizer_link, // OR set as null not to display
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
				'inner_html' => "<strong><h2>LifterLMS Student Notes lets your students take notes and communicate with you.</h2></strong>Student notes has 3 components:<ol><li style='width:100%;'><b>The Add New Note shortcode <code>[llms_add_new_note]</code></b>: creates the note taking area that can be used anywhere in your site and saves a note specific to that page or post.</li><li style='width:100%;'><b>Historical Notes shortcode <code>[llms_notes_list]</code></b>: lets your student see any previous notes they had made in the page or post.</li><li style='width:100%;'><b>Full notes List shortcode <code>[llms_full_notes_list]</code></b>: lets you display a list of all the notes the student has placed in any of their notes instances. It also provides links to the posts and pages the notes where the student can jump right to them.</li></ol>Use the controls below to turn on or off the ability for a student to communicate with the instructor, change the message beside the related checkbox or to turn on or off the editor toolbar. These settings will effect all instances of student notes.",
			),
			array(
				'type'        => 'checkbox',
				'label'       => esc_html__( 'Enable Instructor notification', 'llms-bkpk' ),
				'option_name' => 'llms-bkpk-student-notes-notify-enable',
			),
			array(
				'type'        => 'text',
				'label'       => esc_html__( 'Notify Instructor Text', 'llms-bkpk' ),
				'placeholder'       => esc_html__( 'Notify Instructor', 'llms-bkpk' ),
				'option_name' => 'llms-bkpk-student-notes-notify-instructor-text',
			),
			array(
				'type'        => 'checkbox',
				'label'       => esc_html__( 'Hide Editor toolbar', 'llms-bkpk' ),
				'placeholder'       => esc_html__( 'unchecked', 'llms-bkpk' ),
				'option_name' => 'llms-bkpk-student-notes-hide-toolbar',
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