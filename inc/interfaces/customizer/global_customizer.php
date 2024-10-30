<?php
/* Add Customizer settings */
// Remove panels and sections
if(!function_exists('remove_customizer_settings')){
function remove_customizer_settings( $wp_customize ){
	$wp_customize->remove_panel('themes');
	$wp_customize->remove_panel('nav_menus');
	$wp_customize->remove_panel( 'widgets' );
	$wp_customize->remove_panel( 'woocommerce' );
	$wp_customize->remove_section( 'title_tagline');
	$wp_customize->remove_section( 'colors');
	$wp_customize->remove_section( 'header_image');
	$wp_customize->remove_section( 'background_image');
	$wp_customize->remove_section( 'static_front_page');
	$wp_customize->remove_section( 'custom_css');
	
	//get all panel
	$bkpk_panels = $wp_customize->panels();
	$bkpk_current_panel = $_REQUEST['bkpk_current_panel'];
	foreach($bkpk_panels as $name => $data){
		//remove panel other than current panel 
		if($bkpk_current_panel != $name ){
			$wp_customize->remove_panel($name);
		}
		
	}
	
	//test panel 
	if (!defined('WP_DEBUG') or false == WP_DEBUG) {
		$wp_customize->remove_panel( 'bkpkk');
	}
}
}

/* class for all the other panels to extend */

/**
 * Contains methods for customizing the theme customization screen.
 * 
 * @link http://codex.wordpress.org/Theme_Customization_API
 * @since plugin 1.0
 */
if (!class_exists('bkpkp_global_customizer')){
class bkpkp_global_customizer {
	 // Singleton instance
    private static $instance = false;

    /**
     * Singleton control
     */
    public static function get_instance() {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
	 public function __construct() {
        // Add settings
        add_action('customize_register', array($this, 'settings'));
		if (!$this->is_own_customizer_request()) {
            return;
        }
        add_action('customize_register', array($this, 'controls'));
		//remove by method 
		add_action( 'customize_register', 'remove_customizer_settings', 30 );
	}
   
	public static function settings ( $wp_customize ) {
		$wp_customize->add_setting( 'button_text_not_started', array(
			'default' => 'Get Started',
		) );

		// Main panel
		$wp_customize->add_panel( 'panel_bkpkk', array(
			'title'       => _x( 'Test Panel Options', 'backend', 'llms-bkpk' ),
			'description' => _x( 'All Contnue Button Styling.', 'backend', 'llms-bkpk' ),
			'priority'    => 1
		) );

		// individual sections
		$wp_customize->add_section( 'bkpk_test_inpt_field', array(
			'title'       => _x( 'Test Section Label', 'backend', 'llms-bkpk' ),
			'description' => _x( 'Edit the Button Text and select a font.', 'backend', 'llms-bkpk' ),
			'priority'    => 1,
			'panel'       => 'panel_bkpkk'
		) );
	}
	public static function controls ( $wp_customize ) {
		$wp_customize->add_control( new WP_Customize_Control(
			$wp_customize,
			'button_text_not_started',
			array(
				'priority' => 1,
				'label'    => _x( 'Test text field', 'backend', 'llms-bkpk' ),
				'section'  => 'bkpk_test_inpt_field',
			)
		) );
	}
	public static function is_own_component($component) {
		return false;
	}
	public static function is_own_section($key) {

		// Iterate over own sections
			if ($key === 'bkpk_test_inpt_field') {
				return true;
			}
		// Section not found
		return false;
	}
	public static function is_own_customizer_request() {
		return isset($_REQUEST['bkpk_customizer']) && $_REQUEST['bkpk_customizer'] === 'on';
	}
	public function remove_unrelated_components($components, $wp_customize) {
		// Iterate over components
		foreach ($components as $component_key => $component) {

			// Check if current component is own component
			if (!$this->is_own_component($component)) {
				unset($components[$component_key]);
			}
		}

		// Return remaining components
		return $components;
	}
	public function remove_unrelated_sections($active, $section) {
		// Check if current section is own section
		if (!$this->is_own_section($section->id)) {
			return false;
		}

		// We can override $active completely since this runs only on own Customizer requests
		return true;
	}
	public function remove_unrelated_controls($active, $control) {
		// Check if current control belongs to own section
		if (!$this->is_own_section($control->section)) {
			return false;
		}

		// We can override $active completely since this runs only on own Customizer requests
		return true;
	}
}
}
//bkpkp_global_customizer::get_instance();

// Setup the Theme Customizer settings and controls...
//add_action( 'customize_register' , array( 'MyTheme_Customize' , 'register' ) );
?>