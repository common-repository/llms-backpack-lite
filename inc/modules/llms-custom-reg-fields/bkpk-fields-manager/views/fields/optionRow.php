<?php
global $bkpkFM;

use BPKPFieldManager\Html\Form;

/**
 * Expected: $defaultOptionType, $hidden, $option, $defaultValue
 */

$value = isset($option['value']) ? $option['value'] : null;
?>

<div class="bkpk_plusminus_row bkpk_option form-inline"
	style="margin-bottom: 10px">

    <?php
    /*
     * $defaultOptionClass = 'bkpkFM\Input\\' . $defaultOptionType;
     * $input = new $defaultOptionClass(array(
     * 'stored' => $defaultValue,
     * 'value' => $value,
     * 'class' => 'bkpk_option_default form-control'
     * ));
     * echo $input->render();
     */
    
    echo Form::$defaultOptionType($defaultValue, [
        'value' => $value,
        'class' => 'bkpk_option_default form-control'
    ]);
    
    ?>

    <div class="input-group" style="width: 30%">
		<div class="input-group-addon">L</div>
        <?php
        /*
         * $input = new bkpkFM\Input\Text(array(
         * 'value' => isset( $option['label'] ) ? $option['label'] : null,
         * 'class' => 'bkpk_option_label form-control',
         * 'placeholder' => 'Label',
         * ));
         * echo $input->render();
         */
        
        echo Form::text(Form::get('label', $option), [
            'class' => 'bkpk_option_label form-control',
            'placeholder' => 'Label'
        ]);
        ?>
    </div>

	<div class="input-group bkpk_advanced" style="<?php echo $hidden; ?>">
		<div class="input-group-addon">V</div>
        <?php
        /*
         * $input = new bkpkFM\Input\Text(array(
         * 'value' => $value,
         * 'class' => 'bkpk_option_value form-control',
         * 'placeholder' => 'Value',
         * ));
         * echo $input->render();
         */
        
        echo Form::text($value, [
            'class' => 'bkpk_option_value form-control',
            'placeholder' => 'Value'
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
