<?php
global $bkpkFM;
/**
 * *
 * Expected: $data
 */

/**
 * $data: $title_position, $meta_key, $advanced_mode, $field_type, $id, $options, $default_value
 */
extract($data);

$defaultOptionType = 'radio';
if (! empty($field_type)) {
    if (in_array($field_type, array(
        'checkbox',
        'multiselect'
    )))
        $defaultOptionType = 'checkbox';
}

/**
 * Backward Compatibility for v1.1.7 and earlier
 */
if (! empty($options) && ! is_array($options)) {
    $f = apply_filters('llms_bkpk_field_config', $data, $id, '', 0);
    $fieldSeparator = ! empty($f['field_separator']) ? $f['field_separator'] : ',';
    $keySeparator = ! empty($f['key_separator']) ? $f['key_separator'] : '=';
    $opts = $bkpkFM->toArray(esc_attr($options), $fieldSeparator, $keySeparator);
    $options = array();
    foreach ($opts as $k => $v) {
        $options[] = array(
            'value' => $k,
            'label' => $v
        );
    }
}

// bkpk_hidden class is not working for input-group
$hidden = empty($advanced_mode) ? 'display:none;' : '';
?>

<div class="bkpk_selector_options">
	<div class="form-group">
		<label class="col-sm-3 control-label"><?php _e( 'Options', $bkpkFM->name ) ?></label>
		<div class="bkpk_plusminus_rows_holder col-sm-9">
            <?php
            
            $countRow = $countGroup = 0;
            if (! empty($options) && is_array($options)) {
                foreach ($options as $option) {
                    if (isset($option['type']) && $option['type'] == 'optgroup') {
                        echo $bkpkFM->renderPro('optionGroupRow', array(
                            'hidden' => $hidden,
                            'option' => $option
                        ), 'fields', true);
                        $countGroup ++;
                    } else {
                        echo $bkpkFM->renderPro('optionRow', array(
                            'defaultValue' => isset($default_value) ? $default_value : null,
                            'defaultOptionType' => $defaultOptionType,
                            'hidden' => $hidden,
                            'option' => $option
                        ), 'fields', true);
                        $countRow ++;
                    }
                }
            }
            
            if ($countRow == 0) {
                echo $bkpkFM->renderPro('optionRow', array(
                    'defaultValue' => isset($default_value) ? $default_value : null,
                    'defaultOptionType' => $defaultOptionType,
                    'hidden' => $hidden,
                    'option' => array()
                ), 'fields', true);
            }
            
            if ($countGroup == 0) {
                echo $bkpkFM->renderPro('optionGroupRow', array(
                    'hidden' => $hidden,
                    'option' => array()
                ), 'fields', true);
            }
            ?>
        </div>
	</div>
</div>
