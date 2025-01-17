<?php
namespace BPKPFieldManager;

/**
 * Without setting $fromName parameter, it is possible to get shared fields by calling getSharedFields method.
 * When $formName is set, $this->data contains form's data and $this->data['fields'] contains populated fields.
 *
 * @author Dennis Hall
 */
class FormBase
{

    /**
     *
     * @var (string) Form Name
     */
    protected $name;

    /**
     *
     * @var (array) Form Data including populated fields.
     */
    protected $data = array();

    /**
     *
     * @var (bool) Is form found in DB?
     */
    protected $found;

    /**
     *
     * @var (array) All raw shared fields from DB.
     */
    protected $sharedFields = array();

    /**
     *
     * @var (array) All raw forms from DB.
     */
    private $rawForms = array();

    /**
     *
     * @param (string) $formName            
     */
    function __construct($formName = null)
    {
        $this->name = $formName;
        
        /**
         * get all shared fields from db and set to $this->allFields.
         */
        $this->_loadAllFields();
        
        /**
         * Populate: $this->found, $this->data, Sanitize: $this->data['fields']
         */
        if (! empty($this->name)) {
            $this->_loadForm();
            $this->_initForm();
        }
    }

    /**
     * get all shared fields from db and set to $this->allFields.
     */
    private function _loadAllFields()
    {
        global $bkpkFM;
		
		//add switch 
		/*
		1= reg
		*/
        $form_id = 0;
		
		//get form id 
		$form_id_from_url = isset($_REQUEST['form']) ?  $_REQUEST['form'] : 0;
		if($form_id_from_url){
			$form_id = $form_id_from_url;
		}
        $allFields = $bkpkFM->getData('fields_'.$form_id);
		
		//print_r($_REQUEST);
        $this->sharedFields = is_array($allFields) ? $allFields : array();
    }

    /**
     * Load raw form and form's fields from DB.
     */
    private function _loadForm()
    {
        global $bkpkFM;
        
        if (empty($this->name))
            return;
        
        if ('wp_backend_profile' == $this->name) {
            $backendProfile = $bkpkFM->getSettings('backend_profile');
            $this->data['fields'] = isset($backendProfile['fields']) ? $backendProfile['fields'] : array();
        } else {
            $forms = $bkpkFM->getData('forms');
            if (isset($forms[$this->name])) {
                $this->found = true;
                $this->data = $forms[$this->name];
            }
        }
    }

    /**
     * Populate: $this->found, $this->data.
     * Sanitize: $this->data['fields']: Merge by $this->allFields.
     * Set: $field['is_shared'] in case of shared field.
     */
    private function _initForm()
    {
        global $bkpkFM;
        
        $formFields = array();
        if (! empty($this->data['fields']) && is_array($this->data['fields'])) {
            foreach ($this->data['fields'] as $id => $field) {
                $id = is_array($field) ? $id : $field;
                $field = is_array($field) ? $field : array();
                if (! empty($this->sharedFields[$id]) && is_array($this->sharedFields[$id])) {
                    $field = array_merge($this->sharedFields[$id], $field);
                    $field['is_shared'] = true;
                }
                
                $field['id'] = $id;
                
                if (! empty($field['field_type']))
                    $formFields[$id] = $field;
            }
        }
        
        $this->data['fields'] = $formFields;
    }

    /**
     * Is form found in DB?
     *
     * @return (bool)
     */
    function isFound()
    {
        return $this->found ? true : false;
    }

    /**
     * Get raw shared fields from DB.
     *
     * @return (array)
     */
    function getSharedFields()
    {
        return $this->sharedFields;
    }

    /**
     * Get all shared and form's field together.
     * Shared fields getting preference than form' fields.
     *
     * @return (array)
     */
    function getAllFields()
    {
        $fields = $this->getSharedFields();
        $formsFields = $this->getRawForms();
        foreach ($formsFields as $form) {
            if (! empty($form['fields']) && is_array($form['fields'])) {
                foreach ($form['fields'] as $key => $val) {
                    if (! isset($fields[$key])) {
                        $fields[$key] = $val;
                    }
                }
            }
        }
        
        return $fields;
    }

    /**
     * Get raw forms from DB.
     *
     * @return (array)
     */
    function getRawForms()
    {
        global $bkpkFM;
        if (! empty($this->rawForms))
            return $this->rawForms;
        
        $forms = $bkpkFM->getData('forms');
        return $this->rawForms = is_array($forms) ? $forms : array();
    }

    /**
     * This method will be deleted.
     *
     * @return \BPKPFieldManager\(array)
     */
    function getDataDep()
    {
        return $this->data;
    }
}
