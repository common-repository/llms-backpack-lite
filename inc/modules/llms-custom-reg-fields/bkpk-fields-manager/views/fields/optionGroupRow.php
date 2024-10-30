<?php
global $bkpkFM;

use BPKPFieldManager\Html\Form;
/**
 * Expected: $hidden
 */
?>

<div class="bkpk_plusminus_row bkpk_advanced bkpk_optgroup form-inline" style="<?php echo $hidden ?>margin-bottom:10px;">
	<div class="input-group">
		<div class="input-group-addon">Group</div>
    <?php
    /*
     * $input = new bkpkFM\Input\Text(array(
     * 'value' => isset( $option['label'] ) ? $option['label'] : null,
     * 'class' => 'bkpk_option_group form-control',
     * 'placeholder' => 'Group Label',
     * ));
     * echo $input->render();
     */
    
    echo Form::text(Form::get('label', $option), [
        'class' => 'bkpk_option_group form-control',
        'placeholder' => 'Group Label'
    ]);
    
    ?>
    </div>

	<button type="button" class="btn btn-default bkpk_row_button_plus">
		<i class="fa fa-plus"></i>
	</button>
	<button type="button" class="btn btn-default bkpk_row_button_minus">
		<i class="fa fa-minus"></i>
	</button>
</div>
