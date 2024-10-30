<?php
namespace llms_bkpk;
class Boot extends Config {
	public function __construct() {
		global $llms_bkpk;
		if ( ! isset( $llms_bkpk ) ) {
			$llms_bkpk = new \stdClass();
		}
		// We need to check if spl auto loading is available when activating plugin
		// Plugin will not activate if SPL extension is not enabled by throwing error
		if ( ! extension_loaded( 'SPL' ) ) {
			$spl_error = esc_html__( 'Please contact your hosting company to update to php version 5.6+ and enable spl extensions.', 'learningtemplates-lifterlms-backpack' );
			trigger_error( $spl_error, E_USER_ERROR );
		}
		spl_autoload_register( array( __CLASS__, 'auto_loader' ) );
		$llms_bkpk->admin_menu = new AdminMenu;
		// Add admin menu ajax class to load and save settings
		add_action( 'wp_ajax_settings_save', array(
			get_parent_class(),
			'ajax_settings_save',
		) );// parent class is Config
		add_action( 'wp_ajax_settings_load', array(
			get_parent_class(),
			'ajax_settings_load',
		) );// parent class is Config
		add_action( 'wp_ajax_activate_deactivate_module', array(
			get_parent_class(),
			'ajax_activate_deactivate_module',
		) );// parent class is Config
		
		// Class Details:  Add Class to Admin Menu page
		$classes = self::get_active_classes();
		if ( $classes ) {
			foreach ( self::get_active_classes() as $class ) {
				// Some wp installs remove slashes during db calls, being extra safe when comparing DB vs php values
				if ( strpos( $class, '\\' ) === false ) {
					$class = str_replace( 'backpack', 'backpack\\', $class );
				}
				$class_namespace = explode( '\\', $class );
				if ( class_exists( $class ) && __NAMESPACE__ === $class_namespace[0] ) {
					new $class;
				}
			}
		}
	}
	public static function auto_loader( $class ) {
		// Remove Class's namespace eg: my_namespace/MyClassName to MyClassName
		$class = str_replace( self::get_namespace(), '', $class );
		$class = str_replace( '\\', '', $class );
		// First Character of class name to lowercase eg: MyClassName to myClassName
		$class_to_filename = lcfirst( $class );
		// Split class name on upper case letter eg: myClassName to array( 'my', 'Class', 'Name')
		$split_class_to_filename = preg_split( '#([A-Z][^A-Z]*)#', $class_to_filename, null, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY );
		if ( 1 <= count( $split_class_to_filename ) ) {
			// Split class name to hyphenated name eg: array( 'my', 'Class', 'Name') to my-Class-Name
			$class_to_filename = implode( '-', $split_class_to_filename );
		}
		$file_name = 'interfaces/' . strtolower( $class_to_filename ) . '.php';
		if ( file_exists( dirname( __FILE__ ) . '/' . $file_name ) ) {
			include_once $file_name;
		}
		// Create file name that will be loaded from the classes directory eg: my-Class-Name to my-class-name.php
		$file_name = 'classes/' . strtolower( $class_to_filename ) . '.php';
		if ( file_exists( dirname( __FILE__ ) . '/' . $file_name ) ) {
			include_once $file_name;
		}
		$file_name = strtolower( $class_to_filename ) . '.php';
		if ( file_exists( dirname( __FILE__ ) . '/' . $file_name ) ) {
			include_once $file_name;
		}
		$file_name = 'modules/' . strtolower( $class_to_filename ) . '.php';
		if ( file_exists( dirname( __FILE__ ) . '/' . $file_name ) ) {
			include_once $file_name;
		}
	}
}