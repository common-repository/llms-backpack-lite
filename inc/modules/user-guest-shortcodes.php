<?php
namespace llms_bkpk;

class UserGuestShortcodes extends Config implements RequiredFunctions {
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
			if(in_array('llms_bkpk\UserGuestShortcodes',$llms_bkpk_active_classes_opt)){
				//if active in dashboard ticked
						include_once( dirname( __FILE__ ) . '/user-guest-shortcodes/user-guest-shortcodes.php' );
				} 
			
		}
	}
	/**
	 * Description of class in Admin View
	 *
	 * @return array
	 */
	public static function get_details() {
		$class_title       = esc_html__( 'User/Guest/Role Shortcodes', 'lifterlms-bkpk' );
		$customizer_link   = null;
		$class_description = esc_html__( 'Show different information to users and guests.', 'lifterlms-bkpk' );
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
		$role_lis = '';
		foreach (get_editable_roles() as $role_name => $role_info){
		$role_lis .= "<li style='width:100%;'><b><code>[show_for role=".'"'.$role_name.'"'."]Anything you want goes here![/show_for]</code></b></li>";
		}
		// Create options
		$options = array(
			array(
				'type'        => 'html',
				'inner_html'  => "<strong><h2>Shortcodes to show content to your registered users or guest users</h2></strong>
				Registered users: are users who are logged into your website.<br/>
				Guest users: are users who are not logged into your website.<br/><br/>
				Use this <b><code>[user]Anything you want goes here![/user]</code></b> shortcode to show registered users anything you want.<br/>
				Use this <b><code>[quest]Anything you want goes here![/guest]</code></b> shortcode to show guest users anything you want.<br/><br/>
				Use this <b><code>[show_first_name]</code></b> shortcode to show a users first name.<br/><br/>
				Use this <b><code>[show_last_name]</code></b> shortcode to show a users last name.<br/><br/>
				Here is a list of shortcodes for the roles in your site:<ul>$role_lis</ul>Use any of these shortcodes to show anything you want to users with these roles.<br/>Any content type is supported between the opening and closing shortcode tags.",
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