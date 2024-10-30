<?php
// add an action to the hook
add_action( 'lifterlms_before_registration_button', 'my_custom_lifterlms_registration_fields' );
// create the function that add_action will call
// this function should echo or output html
// the HTML below follows the format of the other fields LifterLMS outputs by default
function my_custom_lifterlms_registration_fields() {
?>
	<div class="llms-form-field llms-cols-6">
		<label for="my_new_reg_menu"><?php _e( 'Select your college', 'llms-reg-items' ); ?><span class="llms-required">*</span></label>
		<select class="llms-field-select" name="my_new_reg_menu" required>
			<optgroup>
				<?php
					$dd_selection_names = get_option('dd_selection_name_list',array());
					foreach($dd_selection_names as $dd_selection_name){
						echo '<option value="' . $dd_selection_name['id'] . '">' . $dd_selection_name['name'] . '</option>';
					}
				?>
			</optgroup>
		</select>
	</div>
</div>
<?php
}
?>