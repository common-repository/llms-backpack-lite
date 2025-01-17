<?php
namespace BPKPFieldManager\Field;

use BPKPFieldManager\Html\Html;

/**
 * Handle role field.
 *
 * @author Dennis Hall
 *        
 * @since 1.2.0
 */
class Role extends OptionsElement
{

    protected $options = [];

    protected function _configure()
    {
        global $bkpkFM;
        $this->inputType = ! empty($this->field['role_selection_type']) ? $this->field['role_selection_type'] : 'select';
        if (is_user_logged_in() && 'registration' != $this->actionType) {
            $this->field['field_value'] = $bkpkFM->getUserRole($this->userID);
        }
        if (empty($this->field['field_value'])) {
            $this->field['field_value'] = 'none';
        }
    }

    protected function configure_()
    {
        global $bkpkFM;
        $allowedRoles = $bkpkFM->allowedRoles(@$this->field['allowed_roles']);
        if (is_array($allowedRoles) && 'select' == $this->inputType) {
            $allowedRoles = array_merge([
                '' => null
            ], $allowedRoles);
        }
        if (! empty($allowedRoles)) {
            $this->options = $allowedRoles;
        }
        if ('radio' == $this->inputType) {
            $this->inputAttr['_option_after'] = '<br />';
        }
    }

    protected function renderInput()
    {
        $methodName = $this->inputType;
        $html = Html::$methodName($this->fieldValue, $this->inputAttr, $this->options);
        $html .= Html::input('hidden', $this->field['id'], [
            'name' => 'role_field_id'
        ]);
        
        return $html;
    }
}