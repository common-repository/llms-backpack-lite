<?php
//define plugin root directory 
define( 'LLMS_S_N_DIR', plugin_dir_path( __FILE__ ) );
define( 'LLMS_S_N_TEMPLATE_DIR', LLMS_S_N_DIR.'templates/' );

//include some scripts and styles 
define( 'LLMS_S_N_URL', plugins_url( '/', __FILE__  ) );

//include some files 
		 require_once( LLMS_S_N_DIR . 'inc/functions.php' );
		require_once( LLMS_S_N_DIR . 'inc/shortcodes.php' );
		require_once( LLMS_S_N_DIR . 'inc/widget-notes.php' );

		//columns and filters 
		require_once( LLMS_S_N_DIR . 'inc/columns-filters.php' );

		//meta boxes
		require_once( LLMS_S_N_DIR . 'inc/post-meta.php' );
		
		//FOR AJAX
		require_once( LLMS_S_N_DIR . 'inc/ajax.php' ); 

function llms_students_scripts_styles() {
	//include js 
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'jquery-ui-core' );
	wp_enqueue_script( 'jquery-ui-accordion' );
	wp_enqueue_script( 'jquery-effects-shake' );
	wp_register_script( 'LLMS_S_N_URL_js_j', LLMS_S_N_URL.'js/student-notes-script.js',array( 'jquery' ) );
	wp_enqueue_script( 'LLMS_S_N_URL_js_j' );
	wp_register_script( 'LLMS_S_N_ajax_js', LLMS_S_N_URL.'js/student-notes-script-ajax.js',array( 'jquery','jquery-effects-shake' ) );
	$translation_array = array(
			'ajax_url' => admin_url( 'admin-ajax.php' )
			 
		);
	wp_localize_script( 'LLMS_S_N_ajax_js', 'sn_object', $translation_array );
	wp_enqueue_script( 'LLMS_S_N_ajax_js' );
	wp_register_style( 'LLMS_S_N_URL-style', LLMS_S_N_URL. '/css/student-notes-style.css' );
	wp_enqueue_style( 'LLMS_S_N_URL-style' );

	//jquery ui style 
	 global $wp_scripts;

    // get registered script object for jquery-ui
    $ui = $wp_scripts->query( 'jquery-ui-core' );

    // tell WordPress to load the Smoothness theme from Google CDN
    $protocol = is_ssl() ? 'https' : 'http';
    $url = "$protocol://ajax.googleapis.com/ajax/libs/jqueryui/{$ui->ver}/themes/smoothness/jquery-ui.min.css";
    wp_enqueue_style( 'jquery-ui-smoothness', $url, false, null );
}
add_action( 'wp_head', 'llms_students_scripts_styles' );