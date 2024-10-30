<?php
namespace llms_bkpk;

class MemberComments extends Config implements RequiredFunctions {
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
			if(in_array('llms_bkpk\MemberComments',$llms_bkpk_active_classes_opt)){
				//if active in dashboard ticked
						include_once( dirname( __FILE__ ) . '/member-comments-for-lifterlms/llms_member_comments.php' );
				} 
		}
	}
	/**
	 * Description of class in Admin View
	 *
	 * @return array
	 */
	public static function get_details() {
		$class_title       = esc_html__( 'Member Comments', 'lifterlms-bkpk' );
		$customizer_link   = null;
		$class_description = esc_html__( 'Replaces bbPress and BuddyPress for LifterLMS courses and lessons. Turns WordPress comments into advanced chat areas.', 'lifterlms-bkpk' );
		$class_icon        = '<i class="lt_icon_fa fa fa-member"></i>';
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
		// Create options
		$options = array(
			array(
				'type'       => 'html',
				'inner_html' => "<strong><h2>This feature replaces BBPress and BuddyPress</h2></strong><strong><h2>How to use your Member Comments shortcodes:</h2></strong>Once you enable this feature, visit any LifterLMS post that you want to place the comments into and add the shortcode into the post.<br/>This shortcode has the following options:<ul><li style='width:100%;'><code>tb=1</code> turns ON the WordPress editor toolbar for this instance of the comments</li><li style='width:100%;'><code>tb=0</code> turns OFF the WordPress editor toolbar for this instance of the comments</li><li style='width:100%;'><code>inst=#</code> identifies the instance number (must be unique per post) of the comments shortcode</li><li style='width:100%;'><code>height=#</code> controls the height of the Comments History area above the comment form</li></ul>If you want to have multiple instances of comments in your post, you would have multiple shortcodes in the post.<br/><br/>Examples:<br/><br/><code>[llms_comments tb=1 inst=1 height=100]</code>This instance of the shortcode has the editor toolbar turned on and a history area of 100 pixels.<br/><br/><code>[llms_comments tb=0 inst=2 height=200]</code>This instance of the shortcode has the editor toolbar turned off and a history area of 100 pixels in the same post.<br/><br/>Instances of the shortcode can have any number assigned to them in any post. If you clone a course, the instance number will be unique to that cloned post, so no need to worry about duplicating comments across courses, lessons or quizzes.<h2>Member comments follows all the WordPress rules defined in your Dashboard > Settings > Discussion page.</h2>",
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