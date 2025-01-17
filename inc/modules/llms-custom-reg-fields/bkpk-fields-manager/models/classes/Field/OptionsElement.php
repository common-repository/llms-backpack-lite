<?php
namespace BPKPFieldManager\Field;

use BPKPFieldManager\Html\Html;

/**
 * Handle select, radio, checkbox and multiselect field.
 * Direct chield: Role
 *
 * @author Dennis Hall
 *        
 * @since 1.2.0
 */
class OptionsElement extends Base
{

    /**
     * Options containing array.
     */
    protected $options = [];

    /**
     * Pre configure, set select as inputType.
     */
    protected function _configure_select()
    {
        $this->inputType = 'select';
    }

    /**
     * Pre configure, set radio as inputType.
     */
    protected function _configure_radio()
    {
        $this->inputType = 'radio';
    }

    /**
     * Pre configure, set checkbox as inputType.
     */
    protected function _configure_checkbox()
    {
        $this->inputType = 'checkbox';
        if (! empty($this->field['field_name']))
            $this->field['field_name'] .= '[]';
    }

    /**
     * Pre configure, set multiselect as inputType.
     */
    protected function _configure_multiselect()
    {
        $this->inputType = 'multiselect';
        if (! empty($this->field['field_name']))
            $this->field['field_name'] .= '[]';
    }

    /**
     * Post configure for radio.
     */
    protected function configure_radio_()
    {
        if (! empty($this->field['line_break'])) {
            $this->inputAttr['_option_after'] = '<br />';
        }
    }

    /**
     * Post configure for checkbox.
     */
    protected function configure_checkbox_()
    {
        if (! empty($this->field['line_break'])) {
            $this->inputAttr['_option_after'] = '<br />';
        }
    }

    /**
     * Post configure for multiselect.
     */
    protected function configure_multiselect_()
    {
        if (! empty($this->field['placeholder'])) {
            if (! isset($field['field_options'])) {
                $field['field_options'] = array();
            }
            $field['field_options']['placeholder'] = $field['placeholder'];
        }
        
        $json = ! empty($field['field_options']) ? json_encode($field['field_options']) : '';
        $this->javascript .= 'jQuery("#' . $this->inputID . '").multipleSelect(' . $json . ');';
    }

    /**
     * Post configure for all options element.
     */
    protected function configure_()
    {
        $this->setOptions();
    }

    /**
     * Set $this->options based on $this->field['options'].
     */
    protected function setOptions()
    {
        global $bkpkFM;
        
        if (! empty($this->field['options'])) {
            if (! is_array($this->field['options'])) {
                $this->fieldSeparator = ! empty($this->field['field_separator']) ? $this->field['field_separator'] : ',';
                $keySeparator = ! empty($this->field['key_separator']) ? $this->field['key_separator'] : '=';
                $this->options = $bkpkFM->toArray(esc_attr($this->field['options']), $this->fieldSeparator, $keySeparator);
            } else {
                $this->options = $this->field['options'];
            }
        }
    }

    /**
     * Override parent _setRequired() method for checkbox.
     */
    protected function _setRequired_checkbox()
    {
        if (! empty($this->field['required'])) {
            $this->addValidation('minCheckbox[1]');
        }
    }

    protected function _setInputAttr()
    {
        parent::_setInputAttr();
        $this->setDisabled();
    }

    /**
     * Rendering input for select, radio, checkbox and multiselect.
     *
     * @return string html
     */
    protected function renderInput()
    {
        $methodName = $this->inputType;
        
        return Html::$methodName($this->fieldValue, $this->inputAttr, $this->options);
    }
}
