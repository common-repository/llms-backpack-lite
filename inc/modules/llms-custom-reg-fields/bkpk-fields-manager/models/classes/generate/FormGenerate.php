<?php
namespace BPKPFieldManager;

/**
 * Generate UM Form
 * $this->data['fields'] should not changed
 *
 * @author Dennis Hall
 */
class FormGenerate extends FormBase
{

    /**
     *
     * @var (array) Fields inside form.
     */
    protected $formFields;

    protected $isAdmin;

    protected $actionType;

    protected $userID;

    /**
     * Assign: field_name, read_only, field_value & is_hide.
     *
     * @param (string) $formName            
     * @param (string) $actionType            
     * @param (int) $userID            
     */
    function __construct($formName, $actionType = '', $userID = 0)
    {
        global $bkpkFM;
        
        parent::__construct($formName);
        
        $this->formFields = isset($this->data['fields']) && is_array($this->data['fields']) ? $this->data['fields'] : array();
        
        $this->isAdmin = $bkpkFM->isAdmin() ? true : false;
        
        $this->actionType = $actionType;
        
        $this->userID = $userID;
        
        /**
         * Assign: field_name, read_only
         */
        $this->_sanitizeForm();
        
        /**
         * Assign: field_value
         */
        $this->_populate();
        
        /**
         * Assign: is_hide
         */
        $this->_addConditionalResult();
    }

    /**
     * Determine & assign: field_name, read_only
     * to each field in $this->formFields
     */
    private function _sanitizeForm()
    {
        global $bkpkFM;
        
        $fieldsTypes = $bkpkFM->umFields();
        
        $this->data['page_count'] = 0;
        
        foreach ($this->formFields as $id => $field) {
            
            if ($field['field_type'] == 'page_heading')
                $this->data['page_count'] ++;
            
            $typeData = $fieldsTypes[$field['field_type']];
            $field['field_group'] = $typeData['field_group'];
            
            /**
             * Determine Field Name
             */
            $fieldName = null;
            if ($field['field_group'] == 'wp_default') {
                $fieldName = $field['field_type'];
            } else {
                if (! empty($field['meta_key']))
                    $fieldName = $field['meta_key'];
            }
            
            $field['field_name'] = $fieldName;
            
            /**
             * Set readonly in case of read_only_non_admin
             */
            if (empty($field['read_only']) && ! empty($field['read_only_non_admin']) && ! $this->isAdmin)
                $field['read_only'] = true;
            
            $this->formFields[$id] = $field;
        }
    }

    /**
     * Determine & assign: field_value
     * to each field in $this->formFields
     */
    private function _populate()
    {
        global $bkpkFM;
        
        $user = new \WP_User($this->userID);
        $savedValues = in_array($this->actionType, array(
            'profile',
            'public'
        )) ? $user : null;
        
        foreach ($this->formFields as $id => $field) {
            
            /**
             * Determine Field Value
             */
            $fieldValue = null;
            if (isset($field['default_value'])) {
                $fieldValue = $bkpkFM->convertUserContent($user, $field['default_value']);
            }
            
            $fieldName = $field['field_name'];
            
            if (isset($savedValues->$fieldName))
                $fieldValue = $savedValues->$fieldName;
            
            if (empty($bkpkFM->showDataFromDB)) {
                if (isset($_POST[$fieldName]))
                    $fieldValue = $_POST[$fieldName];
            }
            
            $field['field_value'] = $fieldValue;
            
            $this->formFields[$id] = $field;
        }
    }

    /**
     * Determine & assign: is_hide
     * to each field in $this->formFields
     */
    private function _addConditionalResult()
    {
        foreach ($this->formFields as $id => $field) {
            if (empty($field['condition']['rules']))
                continue;
            if (! is_array($field['condition']['rules']))
                continue;
            
            $evals = array();
            foreach ($field['condition']['rules'] as $rule) {
                if (empty($rule['field_id']))
                    continue;
                
                $this->formFields[$rule['field_id']]['is_parent'] = true;
                $target = $this->formFields[$rule['field_id']]['field_value'];
                switch ($rule['condition']) {
                    case 'is':
                        $evals[] = $target == $rule['value'] ? true : false;
                        break;
                    
                    case 'is_not':
                        $evals[] = $target != $rule['value'] ? true : false;
                        break;
                }
            }
            
            $result = reset($evals);
            
            $count = count($evals);
            if ($count > 1) {
                for ($i = 1; $i < $count; $i ++) {
                    if ('and' == $field['condition']['relation'])
                        $result = $result && $evals[$i];
                    else
                        $result = $result || $evals[$i];
                }
            }
            
            $visibility = $field['condition']['visibility'];
            
            if ((('show' == $visibility) && ! $result) || (('hide' == $visibility) && $result)) {
                $this->formFields[$id]['is_hide'] = true;
            }
        }
    }

    /**
     * Get Form data including populated fields.
     *
     * @return (array)
     */
    function getForm()
    {
        $data = $this->data;
        $data['fields'] = $this->formFields;
        return $data;
    }

    /**
     * Get sanitized field by field_id
     *
     * @param (int) $id            
     * @return (array)
     */
    function getField($id)
    {
        return ! empty($this->formFields[$id]) ? $this->formFields[$id] : array();
    }

    /**
     * Valid form's fields for insert/update.
     * Return field_name as array key.
     *
     * Reject field when: there is no field_name, or set: read_only, is_hide
     *
     * @return (array): field_name->(array) $field
     */
    function validInputFields()
    {
        $validFields = array();
        
        foreach ($this->formFields as $field) {
            if (empty($field['field_name']))
                continue;
            if (! empty($field['read_only']))
                continue;
            if (! empty($field['is_hide']))
                continue;
            
            if (! empty($field['admin_only']) && ! $this->isAdmin)
                continue;
            if (! empty($field['non_admin_only']) && $this->isAdmin)
                continue;
            if (! empty($field['read_only_non_admin']) && ! $this->isAdmin)
                continue;
            
            if (isset($field['condition']))
                unset($field['condition']);
            
            $validFields[$field['field_name']] = $field;
        }
        
        return $validFields;
    }

    /**
     * Check if captcha field is available on form
     *
     * @return boolean
     */
    function hasCaptcha()
    {
        if (empty($this->data['fields']))
            return false;
        if (! is_array($this->data['fields']))
            return false;
        
        $captcha = null;
        foreach ($this->data['fields'] as $field) {
            if (isset($field['field_type']) && $field['field_type'] == 'captcha') {
                $captcha = $field;
                break;
            }
        }
        
        if ($captcha) {
            if (! empty($captcha['registration_only']) && $this->actionType != 'registration')
                return false;
            
            return true;
        }
    }
}
