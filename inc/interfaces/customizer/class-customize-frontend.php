<?php
/**
 * Class which handles the output of the WP customizer on the frontend.
 * Meaning that this stuff loads always, no matter if the global $wp_customize
 * variable is present or not.
 */

class BKPK_Customizer_Frontend {

	/**
	 * Add actions to load the right staff at the right places (header, footer).
	 */
	function __construct() {
//		add_action( 'wp_enqueue_scripts' , array( $this, 'customizer_css' ), 20 );
//		add_action( 'wp_head' , array( $this, 'head_output' ) );
	}

	/**
	* This will output the custom WordPress settings to the live theme's WP head.
	*
	* Used by hook: 'wp_head'
	*
	* @see add_action( 'wp_head' , array( $this, 'head_output' ) );
	*/
	public static function customizer_css() {
		// customizer settings
		$cached_css = get_plugin_data( 'cached_css', '' );
		$user_css   = get_plugin_data( 'custom_css', '' );

		ob_start();

		echo '/* WP Customizer start */' . PHP_EOL;
		echo apply_filters( 'atd/cached_css', $cached_css );
		echo '/* WP Customizer end */';

		if ( strlen( $user_css ) ) {
			echo PHP_EOL . "/* User custom CSS start */" . PHP_EOL;
			echo $user_css . PHP_EOL; // no need to filter this, because it is 100% custom code
			echo PHP_EOL . "/* User custom CSS end */" . PHP_EOL;
		}

		wp_add_inline_style( 'atd-main', ob_get_clean() );
	}
}