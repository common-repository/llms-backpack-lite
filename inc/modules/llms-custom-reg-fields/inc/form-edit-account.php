<?php
//get edit account form 





// create the function that add_action will call
// this function should echo or output html
// the HTML below follows the format of the other fields LifterLMS outputs by default
function my_custom_lifterlms_edit_user_fields() {
?>
	<!--<div class="llms-form-field llms-cols-6">
		<label for="my_new_reg_menu"><?php _e( 'Select your college 3', 'llms-reg-items' ); ?></label>
		<select class="llms-field-select" name="my_new_reg_menu2">
			<option>1</option>
			<option>2</option>
			<option>3</option>
			<option>4</option>
		</select>
	</div>-->
 
<?php
global $inputs_orig_array;
	//get from saved in fields 2 
	$bkpk_reg_fields = get_option('bkpk_llms_fields_2');
	

foreach($bkpk_reg_fields as $field_id1 =>$field1){
	 $field_type_bkpk = $field1['field_type'];
	 
	 $real_type  = isset($inputs_orig_array[$field_type_bkpk]['type']) ? $inputs_orig_array[$field_type_bkpk]['type'] : $field_type_bkpk;
	 
	 
	 $meta_key_name = 	'bkpk-field-'.$field1['field_type'].'-'.$field_id1; 
	 $bkpk_single_field_value = get_user_meta(get_current_user_id(),$meta_key_name,true);
	 //prnt value
	 
	 echo '<div class="llms-form-field llms-cols-6">';
	
	 ?>
	 <input type='hidden' name='llms-password-change-toggle' value='1' />
	<label for="<?php echo $meta_key_name; ?>"><?php echo $field1['field_title']; ?></label>
	 <?php 
	 
	 //print field 
	 $name_id_array = array('name' => $meta_key_name, 'id' => $meta_key_name, 'class' => 'Class');
	 $options_array = isset($field1['options']) ?  $field1['options'] : array();
	 echo BPKPFieldManager\Html\Form::$real_type($bkpk_single_field_value, $name_id_array,$options_array);
	 echo '</div>';
	   
}
  



}

add_action( 'lifterlms_before_user_update', function( $data, $screen ) {
	 //update value on save click
	if(isset($data['my_new_reg_menu'])){
				update_usermeta( get_current_user_id(), 'my_new_reg_menu2', $data['my_new_reg_menu2'] );
	}
	//2 for edit account 
	 $bkpk_reg_fields = get_option('bkpk_llms_fields_2');
		 
		foreach($bkpk_reg_fields as $field_id =>$field){
		 /* check base.php line ln 475 'bkpk-field-'.$this->inputName.'-'.$this->field['id'],*/
			$field_name  = 	'bkpk-field-'.$field['field_type'].'-'.$field_id; 
			
			if(isset($data[$field_name]) && $data[$field_name] != ""){
				update_user_meta(  get_current_user_id(), $field_name, $data[$field_name] );
			}
		}

}, 10, 2 );


/**
 * Show in csv export 
 *

 * Output data for a custom column on the student reporting table/export
 * @param    string     $value    default value being output
 * @param    string     $key      name of the custom field being output
 * @param    obj        $student  LLMS_Student for the row
 * @param    string     $context  output context "display" or "export"
 * @return   mixed
 */
function bkpk_acc_column_data( $value, $key, $data, $context ) {
	 
	global $inputs_orig_array;
	$bkpk_reg_fields = get_option('bkpk_llms_fields_2');
	
	foreach($bkpk_reg_fields as $field_id3 =>$field3){
	 $field_type_bkpk3 = $field3['field_type'];
	 
	 $real_type  = isset($inputs_orig_array[$field_type_bkpk3]['type']) ? $inputs_orig_array[$field_type_bkpk3]['type'] : $field_type_bkpk3;
	
	 $meta_key_name = 	'bkpk-field-'.$field3['field_type'].'-'.$field_id3; 
	
	 $field_title = $field3['field_title'];
	 if(isset($field3['bkpk_report_export']) and $field3['bkpk_report_export']==1){
	   if ( $meta_key_name === $key ) {
		$value = '';
		$value = get_user_meta( $data->get_id(), $meta_key_name, true );
		
		}
	 }
	
	}
	
	return $value;

}


/**
 * Add custom columns to the main students reporting table
 * @param    [type]     $cols  [description]
 * @return   [type]
 * @since    [version]
 * @version  [version]
 */
function bkpk_acc_columns_titles( $cols ) {
	
	global $inputs_orig_array;
	$bkpk_reg_fields = get_option('bkpk_llms_fields_2');
	
	foreach($bkpk_reg_fields as $field_id3 =>$field3){
	 $field_type_bkpk3 = $field3['field_type'];
	 
	 $real_type  = isset($inputs_orig_array[$field_type_bkpk3]['type']) ? $inputs_orig_array[$field_type_bkpk3]['type'] : $field_type_bkpk3;
	
	 $meta_key_name = 	'bkpk-field-'.$field3['field_type'].'-'.$field_id3; 
	
	 $field_title = $field3['field_title'];
	 if(isset($field3['bkpk_report_export']) and $field3['bkpk_report_export']==1){
	  $cols[$meta_key_name] =  array('title' =>  $field_title,'export_only' => true,'exportable' =>true);
	 }
	}

	return $cols;
}
/**
 * get setting from model
 * run hook according to setting
 */
	$bkpk_active_modules = get_option('llms_bkpk_active_classes');
	// module enabled
	if(in_array('llms_bkpk\FieldManager',$bkpk_active_modules)){
		//acc enabled in modal 
		$fields_acc_enabled = \llms_bkpk\Config::get_settings_value( 'llms-bkpk-forms-enable-acc','FieldManager');		
		if($fields_acc_enabled){
			//front 
						
			// add an action to the hook
			//add_action( 'lifterlms_save_account_details', 'my_custom_lifterlms_edit_user_fields' );
			add_action( 'lifterlms_after_update_fields', 'my_custom_lifterlms_edit_user_fields' );
			
			//add in report and export 
			add_filter( 'llms_table_get_data_students', 'bkpk_acc_column_data', 10, 4 );
			add_filter( 'llms_table_get_students_columns', 'bkpk_acc_columns_titles', 10 );		
		}}