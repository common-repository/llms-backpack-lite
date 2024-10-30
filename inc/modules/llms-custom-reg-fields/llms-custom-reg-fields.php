<?php

defined( 'ABSPATH' ) or die( 'Plugin file cannot be accessed directly.' );

define('LLMSREGFIELDS_ROOTDIR', plugin_dir_path(__FILE__));
require_once(LLMSREGFIELDS_ROOTDIR . 'inc/lifterlms-custom-reg-fields.php');
require_once(LLMSREGFIELDS_ROOTDIR . 'inc/lifterlms-custom-reg-fields-handler.php');
include_once(LLMSREGFIELDS_ROOTDIR . 'inc/form-edit-account.php');

// Add LifterLMS registration settings additions
function registration_general_section() {  
	add_settings_section( 
		'registration_settings_section', 'Registration Menu Selections', 'registration_general_section_options_callback', 'general' 
	);
	add_settings_field(
		'dd_selection_name', 'Registration Menu Item', 'registration_general_textbox_callback', 'general', 'registration_settings_section',
		array( 'dd_selection_name' )  
	); 
	register_setting('general','dd_selection_name', 'esc_attr');
}

function registration_general_section_options_callback() { // Section Callback
}

function registration_general_textbox_callback($args) {  // Textbox Callback
	$option = get_option($args[0]);
	echo '<input type="text" class="regular-text" id="'. $args[0] .'" name="'. $args[0] .'" value="" /><br/>';
	$html_table ="<br/><style>#dd_selection_name_list th,#dd_selection_name_list td{padding:0px;}</style>";
	$html_table .= "<table class='form-table' style='width: 400px;' id='dd_selection_name_list'>";
	$html_table .= "<thead><tr style='border-bottom-color: darkgrey; border-bottom-style: solid;'>";
	$html_table .= "<th style='width: 300px;'>";
	$html_table .= "Registration Menu Item";
	$html_table .= "</th>";
	$html_table .= "<th style='width: 100px; text-align: right;'>";
	$html_table .= "Action";
	$html_table .= "</th>";
	$html_table .= "</tr></thead>";
	
	$dd_selection_names = get_option('dd_selection_name_list',array());
	foreach($dd_selection_names as $dd_selection_name){
		$html_table .= "<tr>";
		$html_table .= "<td>";
		$html_table .= $dd_selection_name['name'];
		$html_table .= "</td>";
		$html_table .= "<td style='text-align: right;'>";
		$html_table .= "<a href='" . admin_url( 'options-general.php?action=delete_menuitem&id=' . $dd_selection_name['id'] ) . "'>Delete</a>";
		$html_table .= "</td>";
		$html_table .= "</tr>";
	}
	$html_table .= "</table>";
	echo $html_table;
}
add_filter( 'pre_update_option_dd_selection_name', 'llms_update_field_dd_selection_name', 10, 2 );

function llms_update_field_dd_selection_name( $new_value, $old_value ) {
	if(!empty($new_value)){
		$new_dealer = array("id"=>time(),"name"=>trim($new_value));
		$dd_selection_name_list = get_option('dd_selection_name_list',array());
		array_push($dd_selection_name_list,$new_dealer);
		update_option( 'dd_selection_name_list', $dd_selection_name_list );
	}
	return $new_value;
}

function llms_delete_menuitem_name(){
	if(isset($_GET['action']) && $_GET['action'] == 'delete_menuitem' && isset($_GET['id']) && $_GET['id'] != ''){
		$dd_selection_name_list = get_option('dd_selection_name_list',array());
		$index = array_search($_GET['id'], array_column($dd_selection_name_list, 'id'));
		array_splice($dd_selection_name_list, $index, 1);
		update_option( 'dd_selection_name_list', $dd_selection_name_list );
		wp_redirect(admin_url( 'options-general.php'));
	}
}
if(!function_exists("array_column")){
	function array_column($array,$column_name){
		return array_map(function($element) use($column_name){return $element[$column_name];}, $array);
	}
}

add_action('init','llms_delete_menuitem_name');

//show college field 
function llms_extra_user_fields( $user ) 
{ ?>
	<h3>Extra Student Information</h3>
	<table class="form-table">
		<tr>
			<th><label for="my_new_reg_menu">College Name</label></th>
			<td>
				<select class="input-text" name="my_new_reg_menu">
					<optgroup>
						<?php
						 $my_new_reg_menu = get_the_author_meta( 'my_new_reg_menu', $user->ID ); 
							$dd_selection_names = get_option('dd_selection_name_list',array());
							foreach($dd_selection_names as $dd_selection_name){
								echo '<option value="' . $dd_selection_name['id'] . '" ' . (($my_new_reg_menu == $dd_selection_name['id']) ? "selected":"") . '>' . $dd_selection_name['name'] . '</option>';
							}
						?>
					</optgroup>
				</select>
			</td>
		</tr>
	</table>
<?php }
add_action( 'personal_options_update', 'save_my_extra_user_fields' );

add_action( 'edit_user_profile_update', 'save_my_extra_user_fields' );
function save_my_extra_user_fields( $user_id ){
	if ( !current_user_can( 'edit_user', $user_id ) ) {
		return false;
	}else{
		if(isset($_POST['my_new_reg_menu']) && $_POST['my_new_reg_menu'] != ""){
			update_usermeta( $user_id, 'my_new_reg_menu', $_POST['my_new_reg_menu'] );
		}
	}
}
//save info on my edit page 
add_action( 'template_redirect', 'save_extra_fields_rhna' );

function save_extra_fields_rhna(){
	 
	 $user =  $current_user = wp_get_current_user();
	//update
	if(isset($_POST['my_new_reg_menu']) && $_POST['my_new_reg_menu'] != ""){
				update_usermeta( $user->ID, 'my_new_reg_menu', $_POST['my_new_reg_menu'] );
	}
}