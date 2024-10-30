<?php
namespace BPKPFieldManager;

global $bkpkFM;

$formBuilder = new FormBuilder('fields_editor');
?>

<div class="wrap">
	<h1><?php _e('LifterLMS - Fields Manager', $bkpkFM->name); ?></h1>
	
	<?php 
	//check if admin have enable field 
	$bkpk_active_modules = get_option('llms_bkpk_active_classes');
	if(!in_array('llms_bkpk\FieldManager',$bkpk_active_modules)){
		die('<p class="notice-error notice-warning">Please enable Fields Manager !</p>');
	}
	?>
	
	<p> Target Form : 
	<?php 
	$form_id = 0; //set to zero 
	 
	$form_id_from_url = isset($_REQUEST['form']) ?  $_REQUEST['form'] : 0;
		if($form_id_from_url){
			$form_id = $form_id_from_url;
		}
	
	//get setting from modal 
	$fields_reg_enabled = \llms_bkpk\Config::get_settings_value( 'llms-bkpk-forms-enable-reg','FieldManager');
	$fields_acc_enabled = \llms_bkpk\Config::get_settings_value( 'llms-bkpk-forms-enable-acc','FieldManager');
	$fields_wc_enabled = \llms_bkpk\Config::get_settings_value( 'llms-bkpk-forms-enable-wc','FieldManager');
		
		
	?>
		<select id='bkpk_forms_type'>
			<option value='admin.php?page=llms_bkpk-user-fields&form=0'>Select a form</option>
			<?php if($fields_reg_enabled){ ?>
			<option <?php if($form_id==1){ echo 'selected';} ?> value='admin.php?page=llms_bkpk-user-fields&form=1' >Registration</option>
			<?php } ?>
			
			<?php if($fields_acc_enabled){ ?>
			<option  <?php if($form_id==2){ echo 'selected';} ?> value='admin.php?page=llms_bkpk-user-fields&form=2'>Edit Account</option>
			<?php } ?>
			
			<?php if($fields_wc_enabled){ ?>
			<option <?php if($form_id==3){ echo 'selected';} ?> value='admin.php?page=llms_bkpk-user-fields&form=3'>WooCommerce</option>
			<?php } ?>
			
		</select>
	
	</p>
	<p><?php _e('Select a field type to add to the form', $bkpkFM->name); ?></p>
    <?php do_action( 'bkpk_admin_notice' ); ?>
    
    <div id="bkpk_fields_editor" class="row">

		<!--<div class="col-xs-12 ">
				<div id="bkpk_fields_selectors" class="panel-group">
                    <?php //$formBuilder->fieldsSelectorPanels(); ?>
                </div>
		</div>-->
		<div class="col-xs-12 col-sm-8 ">
			<div id="bkpk_fields_container" class="metabox-holder">
                <?php $formBuilder->displayAllFields(); ?>
            </div>
		</div>

		<div id="bkpk_steady_sidebar_holder" class="col-xs-12 col-sm-4 ">
			<div id="bkpk_steady_sidebar">
				<div id="bkpk_fields_selectors" class="panel-group">
                    <?php $formBuilder->fieldsSelectorPanels(); ?>
                </div>

				<div id="bkpk_additional_input" class="bkpk_hidden">
                    <?php echo $bkpkFM->methodName( 'updateFields', true ); ?>
                    <?php echo $formBuilder->maxFieldInput(); ?>
                    <?php echo $formBuilder->additional(); ?>            
                </div>

				<p class="">
					<button style="float: right" type="button"
						class="bkpk_save_button btn btn-primary"><?= __('Save Changes', $bkpkFM->name) ?></button>
				</p>
				<p class="bkpk_clear"></p>
				<p class="bkpk_error_msg"></p>

			</div>
		</div>

	</div>

</div>
<script>
    jQuery(function($){
      // bind change event to select
      $('#bkpk_forms_type').on('change', function () {
          var url = $(this).val(); // get selected value
          if (url) { // require a URL
              window.location = url; // redirect
          }
          return false;
      });
    });
</script>
