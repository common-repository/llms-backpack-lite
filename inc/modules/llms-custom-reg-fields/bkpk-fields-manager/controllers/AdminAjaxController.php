<?php
namespace BPKPFieldManager;

class AdminAjaxController
{

    function __construct()
    {
        add_action('wp_ajax_bkpk_add_field', array(
            $this,
            'ajaxAddField'
        ));
        add_action('wp_ajax_bkpk_add_form_field', array(
            $this,
            'ajaxAddFormField'
        ));
        add_action('wp_ajax_bkpk_change_field', array(
            $this,
            'ajaxChangeField'
        ));
        add_action('wp_ajax_bkpk_update_field', array(
            $this,
            'ajaxUpdateFields'
        ));
        
        add_action('wp_ajax_bkpk_add_form', array(
            $this,
            'ajaxAddForm'
        ));
        add_action('wp_ajax_bkpk_update_forms', array(
            $this,
            'ajaxUpdateForms'
        ));
    }

    function ajaxAddField()
    {
        global $bkpkFM;
        $bkpkFM->verifyAdminNonce('add_field');
        
        if (empty($_POST['id']))
            die();
        
        if (! empty($_POST['field_type'])) {
            $arg = $_POST;
            $arg['is_new'] = true;
            $fieldBuilder = new FieldBuilder($arg);
            $fieldBuilder->setEditor('fields_editor');
            echo $fieldBuilder->buildPanel();
        }
        
        die();
    }

    function ajaxAddFormField()
    {
        global $bkpkFM;
        $bkpkFM->verifyAdminNonce('add_field');
        
        if (empty($_POST['id']))
            die();
        
        if (! empty($_POST['is_shared'])) {
            
            $fields = $bkpkFM->getData('fields');
            
            if (isset($fields[$_POST['id']])) {
                $field = $fields[$_POST['id']];
                $field['id'] = $_POST['id'];
                $field['is_shared'] = true;
                $fieldBuilder = new FieldBuilder($field);
                $fieldBuilder->setEditor('form_editor');
                echo $fieldBuilder->buildPanel();
            } else {
                echo "<div class=\"alert alert-warning\" role=\"alert\">Field id {$_POST['id']} is not exists!</div>";
            }
        } elseif (! empty($_POST['field_type'])) {
            $arg = $_POST;
            $arg['is_new'] = true;
            $fieldBuilder = new FieldBuilder($arg);
            $fieldBuilder->setEditor('form_editor');
            echo $fieldBuilder->buildPanel();
        }
        
        die();
    }

    function ajaxChangeField()
    {
        global $bkpkFM;
        $bkpkFM->verifyNonce();
        
        if (isset($_POST['field_type']) && isset($_POST['id']) && $_POST['editor']) {
            $field = $_POST;
            $fieldBuilder = new FieldBuilder($field);
            $fieldBuilder->setEditor($_POST['editor']);
            echo $fieldBuilder->buildPanel();
        }
        
        die();
    }

    function ajaxUpdateFields()
    {
        global $bkpkFM;
        $bkpkFM->verifyAdminNonce('updateFields');
        
        $fields = array();
        if (isset($_POST['fields']))
            $fields = $bkpkFM->arrayRemoveEmptyValue($_POST['fields']);
        
        $formBuilder = new FormBuilder();
        
        $fields = $formBuilder->sanitizeFieldsIDs($fields);
        
        $fields = apply_filters('llms_bkpk_pre_configuration_update', $fields, 'fields_editor');
		
		//update fields according to type 
		/* 1 = woo commerce
		*/ 
		//print_r($_REQUEST);
		//default 
		$form_id = 0;
		//get form id 
		$ref_data= $_REQUEST['_wp_http_referer'];
		$form_data=  substr($ref_data, -6);
		//print_r (explode("=",$form_data));
		$form_data_exp = explode("=",$form_data);
		$form_id_from_url = $form_data_exp[1];
		if($form_id_from_url){
			$form_id = $form_id_from_url;
		}
        $bkpkFM->updateData('fields_'.$form_id , $fields);
        
        $formBuilder->setMaxFieldID();
        
        if (! empty($formBuilder->redirect_to)) {
            echo json_encode(array(
                'redirect_to' => $formBuilder->redirect_to
            ));
            die();
        }
        
        echo 1;
        die();
    }

    function ajaxAddForm()
    {
        global $bkpkFM;
        $bkpkFM->verifyNonce();
        
        $fields = $bkpkFM->getData('fields');
        $bkpkFM->render('form', array(
            'id' => $_POST['id'],
            'fields' => $fields
        ));
        die();
    }

    function ajaxUpdateForms()
    {
        global $bkpkFM; // $bkpkFM->dump($_REQUEST);die();
        $bkpkFM->verifyAdminNonce('formEditor');
        
        $parse = parse_url($_SERVER['HTTP_REFERER']);
        parse_str($parse['query'], $query);
        
        if (empty($query['action'])) {
            echo 'Something went wrong!';
            die();
        }
        
        if (! empty($_POST['form_key'])) {
            $formKey = $_POST['form_key'];
        } else {
            echo 'Form name is required.';
            die();
        }
        
        $forms = $bkpkFM->getData('forms');
        
        $formBuilder = new FormBuilder();
        
        if ('edit' == $query['action']) {
            if (empty($query['form']) || empty($_POST['form_key'])) {
                echo 'Something went wrong!';
                die();
            }
            
            if ($query['form'] != $_POST['form_key']) {
                if (isset($forms[$_POST['form_key']])) {
                    echo 'Form: "' . $_POST['form_key'] . '" already exists!';
                    die();
                }
                
                unset($forms[$query['form']]);
                $query['form'] = $_POST['form_key'];
                $formBuilder->redirect_to = $parse['scheme'] . '://' . $parse['host'] . $parse['path'] . '?' . http_build_query($query);
            }
        } elseif ('new' == $query['action']) {
            if (isset($forms[$_POST['form_key']])) {
                echo 'Form: "' . $_POST['form_key'] . '" already exists!';
                die();
            }
            
            $query['form'] = $_POST['form_key'];
            $query['action'] = 'edit';
            $formBuilder->redirect_to = $parse['scheme'] . '://' . $parse['host'] . $parse['path'] . '?' . http_build_query($query);
        }
        
        $fields = $formBuilder->getSharedFields();
        
        $form = $_POST;
        
        $form = stripslashes_deep($_POST);
        
        // $form = $bkpkFM->arrayRemoveEmptyValue( $_POST );
        
        $formFields = isset($form['fields']) ? $form['fields'] : array();
        
        $formFields = $formBuilder->sanitizeFieldsIDs($formFields);
        
        foreach ($formFields as $id => $field) {
            if (is_array($field)) {
                foreach ($field as $key => $val) {
                    // Process shared fields
                    if (isset($fields[$id][$key])) {
                        if ($fields[$id][$key] == $val)
                            unset($formFields[$id][$key]);
                    } else {
                        if (empty($val))
                            unset($formFields[$id][$key]);
                    }
                }
            }
            
            if (! empty($field['make_field_shared']) && ! isset($fields[$id])) {
                unset($formFields[$id]['make_field_shared']);
                $fields[$id] = $formFields[$id];
                $formFields[$id] = array();
                $triggerFieldsUpdate = true;
            }
        }
        
        $form['fields'] = $formFields;
        
        $form = $bkpkFM->removeAdditional($form);
        
        $forms[$formKey] = $form;
        
        $forms = apply_filters('llms_bkpk_pre_configuration_update', $forms, 'forms_editor');
        
        $bkpkFM->updateData('forms', $forms);
        
        // $bkpkFM->dump($fields);
        if (! empty($triggerFieldsUpdate)) {
            $bkpkFM->updateData('fields', $fields);
            if (empty($formBuilder->redirect_to))
                $formBuilder->redirect_to = $parse['scheme'] . '://' . $parse['host'] . $parse['path'] . '?' . $parse['query'];
        }
        
        $formBuilder->setMaxFieldID();
        
        if (! empty($formBuilder->redirect_to)) {
            echo json_encode(array(
                'redirect_to' => $formBuilder->redirect_to
            ));
            die();
        }
        
        echo 1;
        die();
    }
}
