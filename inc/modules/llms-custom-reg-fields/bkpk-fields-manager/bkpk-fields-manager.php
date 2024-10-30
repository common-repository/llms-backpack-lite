<?php
/*
 * backpack field manager 

 */
if (version_compare(PHP_VERSION, '5.4.0', '<')) {
    $code = "echo '<div class=\"error\"><p>BackPack plugin requires <strong>  PHP 5.4.0</strong> or above. Current PHP version: ' . PHP_VERSION . '</p></div>';";
    add_action('admin_notices', create_function(null, $code));
    return;
}

if (realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME'])) {
    exit('Please don\'t access this file directly.');
}

require __DIR__ . '/vendor/autoload.php';
//require __DIR__ . '/lib/Framework.php';

global $bkpkFramework, $bkpkFM;

if (! is_object($bkpkFramework)) {
    $bkpkFramework = new BPKPFieldManager\Frameworkbkpk();
}
$bkpkFramework->loadDirectory($bkpkFramework->controllersPath);

$bkpkFM = new BPKPFieldManager\BPKPFieldManager(__FILE__);
$bkpkFM->init();


/**
 * array to get field type
 */
 global $inputs_orig_array;
 /****************** input field array start ***************/
 global $bkpkFM;
 //got it from model/class/builder/fieldbuider.php 
 
        $inputs_orig_array = array(
            'field_title' => array(
                'label' => __('Field Label', $bkpkFM->name),
                'placeholder' => __('Field Label', $bkpkFM->name)
            ),
            'title_position' => array(
                'type' => 'select',
                'label' => __('Label Position', $bkpkFM->name),
                'options' => array(
                    'top' => __('Top', $bkpkFM->name),
                    'left' => __('Left', $bkpkFM->name),
                    'right' => __('Right', $bkpkFM->name),
                    'inline' => __('Inline', $bkpkFM->name),
                    // 'placeholder' => __( 'Placeholder', $bkpkFM->name ), // Commented since 1.1.8rc2
                    'hidden' => __('Hidden', $bkpkFM->name)
                )
            ),
            'placeholder' => array(
                'label' => 'Placeholder',
                'placeholder' => __('Placeholder', $bkpkFM->name)
            ),
            'description' => array(
                'type' => 'textarea',
                'label' => __('Description', $bkpkFM->name),
                'placeholder' => __("Field's Description", $bkpkFM->name)
            ),
            'meta_key' => array(
                'label' => 'Meta Key <span class="bkpk_required">*</span>',
                'placeholder' => __('Unique identification key for field', $bkpkFM->name),
                'info' => __('Unique identification key for field (required).', $bkpkFM->name)
            ),
            'default_value' => array(
                'label' => __('Default Value', $bkpkFM->name),
                'placeholder' => __('Default Value', $bkpkFM->name),
                'info' => __('Use this value when user doesn\'t have any stored value', $bkpkFM->name)
            ),
            'options' => array(
                'type' => 'textarea',
                'label' => __('Field Options', $bkpkFM->name) . ' <span class="bkpk_required">*</span>',
                'placeholder' => 'Available options. (e.g: Yes,No OR yes=Agree,no=Disagree'
            ),
            
            'field_class' => array(
                'label' => __('Input Class', $bkpkFM->name),
                'placeholder' => __('Specify custom class name for input', $bkpkFM->name)
            ),
            'css_class' => array(
                'label' => __('Field Container Class', $bkpkFM->name),
                'placeholder' => __('Custom class name for field container', $bkpkFM->name)
            ),
            'css_style' => array(
                'type' => 'textarea',
                'label' => __('Field Container Style', $bkpkFM->name),
                'placeholder' => __('Custom css style for field container', $bkpkFM->name)
            ),
            'field_size' => array(
                'label' => __('Field Size', $bkpkFM->name),
                'placeholder' => 'e.g. 200px;'
            ),
            'field_height' => array(
                'label' => __('Field Height', $bkpkFM->name),
                'placeholder' => 'e.g. 200px;'
            ),
            'max_char' => array(
                'label' => __('Max Char', $bkpkFM->name),
                'placeholder' => __('Maximum allowed character', $bkpkFM->name)
            ),
            
            'allowed_extension' => array(
                'label' => __('Allowed Extension', $bkpkFM->name),
                'placeholder' => 'Default: jpg,png,gif'
            ),
            'role_selection_type' => array(
                'type' => 'select',
                'label' => __('Role Selection Type', $bkpkFM->name),
                'options' => array(
                    'select' => 'Dropdown',
                    'radio' => 'Select One (radio)',
                    'hidden' => 'Hidden'
                )
            ),
            'datetime_selection' => array(
                'type' => 'select',
                'label' => __('Type Selection', $bkpkFM->name),
                'info' => 'Date, Time or Date & Time',
                'options' => array(
                    'date' => __('Date', $bkpkFM->name),
                    'time' => __('Time', $bkpkFM->name),
                    'datetime' => __('Date and Time', $bkpkFM->name)
                )
            ),
            'date_format' => array(
                'label' => __('Date Format', $bkpkFM->name),
                'placeholder' => 'Default: yy-mm-dd'
            ),
            'year_range' => array(
                'label' => __('Year Range', $bkpkFM->name),
                'placeholder' => 'Default: 1950:c'
            ),
            'country_selection_type' => array(
                'type' => 'select',
                'label' => __('Save meta value by', $bkpkFM->name),
                'options' => array(
                    'by_country_code' => __('Country Code', $bkpkFM->name),
                    'by_country_name' => __('Country Name', $bkpkFM->name)
                )
            ),
            
            'max_number' => array(
                'type' => 'number',
                'label' => __('Maximum Number', $bkpkFM->name)
            ),
            'min_number' => array(
                'type' => 'number',
                'label' => __('Minimum Number', $bkpkFM->name)
            ),
            'step' => array(
                'type' => 'number',
                'label' => 'Step',
                'info' => __('Intervals for number input', $bkpkFM->name)
            ),
            
			
            'max_file_size' => array(
                'type' => 'number',
                'min' => 0,
                'max' => 89,
                'label' => __('Maximum File Size (KB)', $bkpkFM->name),
                'placeholder' => 'Default: 1024KB',
                'info' => 'According to your server settings, allowed maximum is KB. ' . 'To increase the limit, increase value of post_max_size and upload_max_filesize on your server.'
            ),
            'image_width' => array(
                'type' => 'number',
                'min' => 0,
                'label' => 'Image Width (px)',
                'placeholder' => 'For Image Only. e.g. 640'
            ),
            'image_height' => array(
                'type' => 'number',
                'min' => 0,
                'label' => 'Image Height (px)',
                'placeholder' => 'For Image Only. e.g. 480'
            ),
            'image_size' => array(
                'type' => 'number',
                'min' => 0,
                'label' => 'Image Size (px) width/height',
                'placeholder' => 'Default: 96'
            ),
            'input_type' => array(
                'type' => 'select',
                'label' => 'HTML5 Input Type',
                'by_key' => true,
                'options' => array(
                    '' => '',
                    'email' => [
                        'Email',
                        'data-child' => 'retype_email,retype_label'
                    ],
                    'password' => [
                        'Password',
                        'data-child' => 'retype_password,retype_label'
                    ],
                    'tel' => 'Tel',
                    'month' => 'Month',
                    'week' => 'Week'
                )
            ),
            'regex' => array(
                'label' => 'Pattern',
                'placeholder' => 'e.g. (alpha-numeric): [a-zA-Z0-9]+'
            ),
            'error_text' => array(
                'label' => __('Error Text', $bkpkFM->name),
                'placeholder' => 'e.g. Invalid field'
            ),
            'retype_label' => array(
                'label' => __('Retype Label', $bkpkFM->name),
                'placeholder' => __('Label for retype field', $bkpkFM->name)
            ),
            'captcha_theme' => [
                'type' => 'select',
                'label' => __('reCaptcha Theme', $bkpkFM->name),
                'options' => [
                    '' => __('Light', $bkpkFM->name),
                    'dark' => __('Dark', $bkpkFM->name)
                ]
            ],
            'captcha_type' => [
                'type' => 'select',
                'label' => __('reCaptcha Type', $bkpkFM->name),
                'options' => [
                    '' => __('Image', $bkpkFM->name),
                    'audio' => __('Audio', $bkpkFM->name)
                ]
            ],
            'captcha_lang' => [
                'label' => __('reCaptcha Language', $bkpkFM->name),
                'placeholder' => __('(e.g. en) Leave blank for auto detection', $bkpkFM->name),
                'info' => __('(e.g. en) Leave blank for auto detection', $bkpkFM->name)
            ],
            'resize_image' => array(
                'type' => 'checkbox',
                'label' => __('Resize Image', $bkpkFM->name),
                'child' => 'crop_image'
            ),
            'retype_email' => array(
                'type' => 'checkbox',
                'label' => __('Retype Email', $bkpkFM->name),
                'child' => 'retype_label'
            ),
            'retype_password' => array(
                'type' => 'checkbox',
                'label' => __('Retype Password', $bkpkFM->name),
                'child' => 'retype_label'
            )
        );
/******************end ***************/
 
 
/** 
 * fields in front end reg 
 *
 */

	function bkpk_reg_fields() {
		?>
		<div class="clear"></div>
		
		<?php 
		//get setting from modal 
	$fields_reg_title_enabled = \llms_bkpk\Config::get_settings_value( 'llms-bkpk-forms-enable-reg-title','FieldManager');
	$fields_reg_sec_title = \llms_bkpk\Config::get_settings_value( 'llms-bkpk-forms-reg-section-title','FieldManager');
		if($fields_reg_title_enabled){
		?>
			<h4 class="llms-form-heading">
		
				<?php 
				if($fields_reg_sec_title){
					echo $fields_reg_sec_title;
				}else{
					_e('Extra fields','llms-bkpk'); 
				}
				?>
		
		
			</h4>
		<?php } ?>
		<!-- get current checkout form field and change name -->
		<div class="clear"></div>
		  
		<?php
		   

		$bkpk_reg_fields = get_option('bkpk_llms_fields_1');
		foreach($bkpk_reg_fields as $field_id =>$field){
			$umField = new BPKPFieldManager\Field($field_id);
			$umField->generateField();
			echo $umField->render();
			//echo $umField->displayValue();
			
		}
		
	}
	
	//filter add  meta on checkout 
	function bkpk_reg_fields_save( $insert_metas, $data, $action  ) {
		
		update_option('test_x_student_insert_metas',$insert_metas);
		update_option('test_x_student_insert_data',$data);
		update_option('test_x_student_insert_action',$action);
		update_option('test_x_student_insert_action_new','seems function is called');
		
		
		$bkpk_reg_fields = get_option('bkpk_llms_fields_1');
		foreach($bkpk_reg_fields as $field_id =>$field){
		 /* check base.php line ln 475 'bkpk-field-'.$this->inputName.'-'.$this->field['id'],*/
		 $field_name = 	'bkpk-field-'.$field['field_type'].'-'.$field_id; 
		 $insert_metas[$field_name] 		= $data[$field_name];
		}
		
		return $insert_metas;
	}
	
	/** 
	 * show in user edit profile 
	 */
	function bkpk_reg_user_profile_fields( $user ) 
{ ?>
	<h3><?php _e('Registration Information','llms-bkpk'); ?> </h3>
 
		<table class="form-table admin-reg-fields-table">
	<?php 
	global $inputs_orig_array;
	$bkpk_reg_fields = get_option('bkpk_llms_fields_1');
	

foreach($bkpk_reg_fields as $field_id1 =>$field1){
	 $field_type_bkpk = $field1['field_type'];
	 
	 $real_type  = isset($inputs_orig_array[$field_type_bkpk]['type']) ? $inputs_orig_array[$field_type_bkpk]['type'] : $field_type_bkpk;
	 
	 
	 $meta_key_name = 	'bkpk-field-'.$field1['field_type'].'-'.$field_id1; 
	 $bkpk_single_field_value = get_user_meta($user->ID,$meta_key_name,true);
	 //prnt value
	 
	 echo '<tr>'
	 ?>
	 <th><label for="<?php echo $meta_key_name; ?>"><?php echo $field1['field_title']; ?></label></th>
	 <?php 
	 echo '<td>';
	 //print field 
	 $name_id_array = array('name' => $meta_key_name, 'id' => $meta_key_name, 'class' => 'Class');
	 $options_array = isset($field1['options']) ?  $field1['options'] : array();
	 echo BPKPFieldManager\Html\Form::$real_type($bkpk_single_field_value, $name_id_array,$options_array);
	 echo '</tr></td>';
	   
}
  
		
		?>
		</table>
<?php }

/**
 * save registration fields
 * @ user edit screen 
 *
 */
 
function bkpk_reg_user_fields_update( $user_id ){
	
	
	if ( !current_user_can( 'edit_user', $user_id ) ) {
		return false;
	}else{
		
		
		$bkpk_reg_fields = get_option('bkpk_llms_fields_1');
		print_r($_POST);
		foreach($bkpk_reg_fields as $field_id =>$field){
		 /* check base.php line ln 475 'bkpk-field-'.$this->inputName.'-'.$this->field['id'],*/
			$field_name  = 	'bkpk-field-'.$field['field_type'].'-'.$field_id; 
			
			if(isset($_POST[$field_name]) && $_POST[$field_name] != ""){
				update_user_meta( $user_id, $field_name, $_POST[$field_name] );
			}
		}
		
		
	}
}

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
function bkpk_reg_column_data( $value, $key, $data, $context ) {
	 
	global $inputs_orig_array;
	$bkpk_reg_fields = get_option('bkpk_llms_fields_1');
	
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
function bkpk_reg_columns_titles( $cols ) {
	
	global $inputs_orig_array;
	$bkpk_reg_fields = get_option('bkpk_llms_fields_1');
	
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
	
	if(!is_array($bkpk_active_modules)){
		$bkpk_active_modules = array();
		}
	// module enabled
	if(in_array('llms_bkpk\FieldManager',$bkpk_active_modules)){
		//registration enabled in modal 
		$fields_reg_enabled = \llms_bkpk\Config::get_settings_value( 'llms-bkpk-forms-enable-reg','FieldManager');		
		if($fields_reg_enabled){
			//front
			add_action( 'lifterlms_checkout_after_billing_fields', 'bkpk_reg_fields' );
			
			//save 
			add_filter( 'lifterlms_user_update_insert_user_meta', 'bkpk_reg_fields_save', 10, 3 );
			add_filter( 'lifterlms_user_registration_insert_user_meta', 'bkpk_reg_fields_save', 10, 3 ); 
			
			// show profile in back end edit profile 
			add_action( 'show_user_profile', 'bkpk_reg_user_profile_fields' );
			add_action( 'edit_user_profile', 'bkpk_reg_user_profile_fields' );
			
			//update from edit user page 
			add_action( 'personal_options_update', 'bkpk_reg_user_fields_update' );
			add_action( 'edit_user_profile_update', 'bkpk_reg_user_fields_update' );
			 
			
			//add in report and export 
			add_filter( 'llms_table_get_data_students', 'bkpk_reg_column_data', 10, 4 );
			add_filter( 'llms_table_get_students_columns', 'bkpk_reg_columns_titles', 10 );
		}
	}
	
	
	
	