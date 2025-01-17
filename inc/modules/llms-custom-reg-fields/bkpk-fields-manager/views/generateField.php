<?php
use BPKPFieldManager\File;

global $bkpkFM, $user_ID;
/*
 * Required: $field, $userID
 * Expect by rander: $field, $form $actionType, $userID, $inPage, $inSection, $isNext, $isPrevious, $currentPage, $uniqueID
 */

/**
 * *** Initialiaze default value ****
 */
$fieldType = "text";
$class = "bkpk_field_{$field['id']} bkpk_input bkpk_single_field ";
$divClass = "bkpk_field_container llms-form-field type-text llms-cols-12";
$divStyle = "";
$inputStyle = "";
$fieldOptions = "";
$html = "";
$validation = "";
$maxlength = "";
$by_key = false;
$label_class = "";
$fieldTitle = "";
$fieldReadOnly = "";
$showInputField = true;
$attr = array();

$inputID = empty($field['input_id']) ? "bkpk_field_{$field['id']}_$uniqueID" : $field['input_id'];
$labelID = empty($field['label_id']) ? $inputID . '_label' : $field['label_id'];
$descriptionID = empty($field['description_id']) ? "id=\"{$inputID}_description\"" : "id=\"{$field['description_id']}\"";

$fieldBefore = ! empty($field['before']) ? $field['before'] : "";
$fieldAfter = ! empty($field['after']) ? $field['after'] : "";

if (! empty($field['field_class']))
    $class .= "{$field['field_class']} test2 ";

if (! empty($field['label_class']))
    $label_class .= "{$field['label_class']} ";

if (! empty($field['field_style']))
    $inputStyle .= $field['field_style'];

/**
 * *** Conditions ****
 */

if (! empty($field['admin_only'])) :
    if (! $bkpkFM->isAdmin())
        return;

endif;

if (! empty($field['non_admin_only'])) :
    if ($bkpkFM->isAdmin($userID))
        return;

endif;

if (! empty($field['read_only_non_admin'])) :
    if (! ($bkpkFM->isAdmin()))
        $fieldReadOnly = 'readonly';
 
endif;

if (! empty($field['read_only']))
    $fieldReadOnly = 'readonly';

if (! empty($field['required'])) {
    $attr['required'] = 'required';
    $validation .= 'required,';
}

if (! empty($field['unique'])) {
    // $validation .= "ajax[ajaxValidateUniqueField],";
    // $class .= "bkpk_unique ";
}

if (! empty($field['title_position'])) {
    // if( $field['title_position'] <> 'hidden' AND isset($field['field_title']) )
    if (isset($field['field_title']) && (! in_array($field['title_position'], array(
        'hidden',
        'placeholder'
    ))))
        $fieldTitle = $field['field_title'];
}

if (! empty($field['title_position'])) {
    if ($field['title_position'] == 'top')
        $label_class .= 'bkpk_label_top';
    elseif ($field['title_position'] == 'left')
        $label_class .= 'bkpk_label_left';
    elseif ($field['title_position'] == 'right')
        $label_class .= 'bkpk_label_right';
    elseif ($field['title_position'] == 'inline') {
        $label_class .= 'bkpk_label_inline ';
        $divClass .= ' bkpk_inline';
    } elseif ($field['title_position'] == 'placeholder') {
        $field['placeholder'] = isset($field['placeholder']) ? $field['placeholder'] : $field['field_title'];
    }
}

if (! empty($field['field_size'])) {
    $inputStyle .= "width:{$field['field_size']}; ";
}

if (! empty($field['field_height'])) {
    $inputStyle .= "height:{$field['field_height']}; ";
}

if (! empty($field['max_char'])) {
    $maxlength = $field['max_char'];
}

if (isset($field['css_class'])) {
    $divClass .= " {$field['css_class']} ";
}

if (isset($field['css_style'])) {
    $divStyle .= "{$field['css_style']} ";
}

if (isset($field['options'])) {
    $by_key = true;
    if (! is_array($field['options'])) {
        $fieldSeparator = ! empty($field['field_separator']) ? $field['field_separator'] : ',';
        $keySeparator = ! empty($field['key_separator']) ? $field['key_separator'] : '=';
        $fieldOptions = $bkpkFM->toArray(esc_attr($field['options']), $fieldSeparator, $keySeparator);
    } else
        $fieldOptions = $field['options'];
}

/**
 * *** Fields Condition ****
 */

switch ($field['field_type']) {
    
    // WP Default Fields
    case 'user_login':
        if ($actionType == 'profile') :
            $fieldReadOnly = 'readonly';
        
        endif;
        
        $attr['required'] = 'required';
        // $validation .= "required,ajax[ajaxValidateUniqueField],";
        $validation .= "required";
        break;
    
    case 'user_email':
    case 'email':
        $fieldType = 'email';
        
        if ($field['field_type'] == 'user_email') {
            $attr['required'] = 'required';
            $validation .= "required,";
        }
        
        $validation .= "custom[email],";
        
        // $validation .= "required,custom[email],ajax[ajaxValidateUniqueField],";
        if (isset($field['retype_email'])) :
            $field2['field_name'] = $field['field_name'] . "_retype";
            $isRequired = $actionType == 'registration' ? 'required,' : '';
            $field2['class'] = $class . "validate[{$isRequired}equals[$inputID]]";
            $field2['fieldID'] = $inputID . "_retype";
            if (! empty($fieldTitle)) {
                $field2['fieldTitle'] = ! empty($field['retype_label']) ? $field['retype_label'] : sprintf(__('Retype %s', $bkpkFM->name), $fieldTitle);
                $field2['fieldTitle'] = isset($field['retype_field_title']) ? $field['retype_field_title'] : $field2['fieldTitle'];
            }
            $field2['placeholder'] = isset($field['placeholder']) ? sprintf(__('Retype %s', $bkpkFM->name), $field['field_title']) : '';
            $field2['placeholder'] = isset($field['retype_placeholder']) ? $field['retype_placeholder'] : $field2['placeholder'];
        
        endif;
        break;
    
    case 'user_pass':
    case 'password':
        $fieldType = 'password';
        $field['field_value'] = "";
        
        if ($actionType == 'registration') {
            $attr['required'] = 'required';
            $validation .= 'required,';
        }
        
        if (! empty($field['regex'])) {
            $attr['pattern'] = $field['regex'];
            $attr['oninput'] = "setCustomValidity('')";
        }
        
        if (! empty($field['error_text']))
            $attr['oninvalid'] = "setCustomValidity('{$field['error_text']}')";
        
        if (! empty($field['password_strength'])) {
            $moreContent = "<script type=\"text/javascript\">jQuery(document).ready(function(){jQuery(\"#$inputID\").password_strength();});</script>";
            // $class .= "pass_strength ";
        }
        
        if (! empty($field['retype_password'])) :
            $field2['field_name'] = $field['field_name'] . "_retype";
            $isRequired = $actionType == 'registration' ? 'required,' : '';
            $field2['class'] = str_replace("pass_strength", "", $class) . "validate[{$isRequired}equals[$inputID]]";
            $field2['fieldID'] = $inputID . "_retype";
            if (! empty($fieldTitle)) {
                $field2['fieldTitle'] = ! empty($field['retype_label']) ? $field['retype_label'] : sprintf(__('Retype %s', $bkpkFM->name), $fieldTitle);
                $field2['fieldTitle'] = isset($field['retype_field_title']) ? $field['retype_field_title'] : $field2['fieldTitle'];
            }
            $field2['placeholder'] = isset($field['placeholder']) ? sprintf(__('Retype %s', $bkpkFM->name), $field['field_title']) : '';
            $field2['placeholder'] = isset($field['retype_placeholder']) ? $field['retype_placeholder'] : $field2['placeholder'];
        
        endif;
        
        if (! empty($field['required_current_password']) && ('profile' == $actionType)) :
            if (! empty($fieldTitle)) {
                $currentPassTitle = isset($field['current_pass_title']) ? $field['current_pass_title'] : sprintf(__("Current %s", $bkpkFM->name), $fieldTitle);
                $fieldTitle = isset($field['new_pass_title']) ? $field['new_pass_title'] : sprintf(__("New %s", $bkpkFM->name), $fieldTitle);
            }
            
            $currentPassPlaceholder = isset($field['placeholder']) ? sprintf(__('Current %s', $bkpkFM->name), $field['field_title']) : '';
            $currentPassPlaceholder = isset($field['current_pass_placeholder']) ? $field['current_pass_placeholder'] : $currentPassPlaceholder;
            if (isset($field['placeholder'])) {
                $field['placeholder'] = isset($field['new_pass_placeholder']) ? $field['new_pass_placeholder'] : sprintf(__("New %s", $bkpkFM->name), $field['field_title']);
            }
            
            $html .= $bkpkFM->createInput($field['field_name'] . "_current", $fieldType, array(
                "value" => "",
                "label" => isset($currentPassTitle) ? $currentPassTitle : '',
                "id" => $inputID . "_current",
                "class" => $class . " validate[funcCall[umConditionalRequired]",
                "style" => isset($inputStyle) ? $inputStyle : "",
                "maxlength" => $maxlength,
                "label_id" => $labelID . "_current",
                "label_class" => $label_class ? $label_class : 'pf_label',
                "placeholder" => $currentPassPlaceholder,
                "enclose" => 'p'
            ));
        
        endif;
        break;
    
    // case 'user_nicename' :
    
    case 'user_url':
        $validation .= "custom[url],";
        break;
    
    // case 'display_name' :
    // case 'nickname' :
    // case 'first_name' :
    // case 'last_name' :
    
    case 'user_registered':
        $validation .= "custom[datetime],";
        
        if ($fieldReadOnly == 'readonly')
            $isDisabled = true;
        $fieldReadOnly = 'readonly';
        
        if (empty($field['field_options']['dateFormat']))
            $field['field_options']['dateFormat'] = 'yy-mm-dd';
        if (empty($field['field_options']['timeFormat']))
            $field['field_options']['timeFormat'] = 'hh:mm:ss';
        if (! isset($field['field_options']['changeYear']))
            $field['field_options']['changeYear'] = true;
        $jsMethod = '.datetimepicker(' . json_encode($field['field_options']) . ');';
        
        $moreContent = '<script type="text/javascript">jQuery(document).ready(function(){jQuery("#' . $inputID . '")' . $jsMethod . '});</script>';
        break;
    
    case 'role':
        if ($user_ID && ($actionType != 'registration'))
            $field['field_value'] = $bkpkFM->getUserRole($userID);
        
        if (empty($field['field_value']))
            $field['field_value'] = 'none';
        
        $fieldType = @$field['role_selection_type'];
        if ($fieldType == 'radio')
            $option_after = "<br />";
        
        $allowedRoles = $bkpkFM->allowedRoles(@$field['allowed_roles']);
        if (is_array($allowedRoles) && $fieldType == 'select')
            $allowedRoles = array_merge(array(
                '' => null
            ), $allowedRoles);
        
        $fieldOptions = $allowedRoles;
        $by_key = true;
        $combind = true;
        
        $html .= $bkpkFM->createInput('role_field_id', 'hidden', array(
            'value' => @$field['id']
        ));
        break;
    
    // case 'jabber' :
    // case 'aim' :
    // case 'yim' :
    
    // Standard Fields
    // case 'text' :
    
    case 'textarea':
        $fieldType = 'textarea';
        break;
    
    case 'select':
        $fieldType = 'select';
        break;
    
    case 'checkbox':
        $fieldType = 'checkbox';
        $field['field_value'] = $bkpkFM->toArray($field['field_value'], ',');
        $combind = true;
        if (isset($field['required'])) :
            $validation = 'minCheckbox[1],';
          
        endif;
        
        if (! empty($field['line_break'])) :
            $option_after = "<br />";
        
        endif;
        break;
    
    case 'radio':
        $fieldType = 'radio';
        if (! empty($field['line_break'])) :
            $option_after = "<br />";
        
        endif;
        break;
    
    case 'hidden':
        $fieldType = 'hidden';
        break;
    
    case 'file':
    case 'user_avatar':
        $userAvatar = false;
        if ($field['field_type'] == 'user_avatar') {
            if (empty($field['field_value'])) {
                if (empty($field['hide_default_avatar']))
                    $userAvatar = (@$actionType == 'registration') ? get_avatar('nobody@noemail') : get_avatar($userID);
            }
            
            /*
             * if ( ! empty( $field[ 'field_value' ] ) ){
             * $size = ! empty( $field['image_size'] ) ? $field['image_size'] : 96;
             * $userAvatar = get_avatar( $userID, $size );
             * } else {
             * if ( empty( $field['hide_default_avatar'] ) )
             * $userAvatar = ( @$actionType == 'registration' ) ? get_avatar( 'nobody@noemail' ) : get_avatar( $userID );
             * }
             */
        }
        
        if ($userAvatar) {
            $fieldResultContent = $userAvatar;
        } else {
            $umFile = new File($field);
            $fieldResultContent = $umFile->showFile();
        }
        
        /*
         * $fieldResultContent = $bkpkFM->render( 'showFile', array(
         * 'filepath' => $field['field_value'],
         * 'field_name' => $field['field_name'],
         * 'avatar' => $userAvatar,
         * 'width' => @$field['image_width'],
         * 'height' => @$field['image_height'],
         * 'crop' => !empty( $field['crop_image'] ) ? true : false,
         * 'readonly' => $fieldReadOnly,
         * ) );
         */
        
        if (@$field['title_position'] == 'left') {
            $fieldResultContent = "<div class=\"bkpk_left_margin\">$fieldResultContent</div>";
        }
        
        $fieldResultDiv = true;
        
        if (@$field['disable_ajax']) {
            $fieldType = 'file';
            // $validation = str_replace( 'required,', '', $validation );
        } else {
            $showInputField = false;
            $extension = null;
            $maxsize = null;
            if (isset($field['allowed_extension']))
                $extension = $field['allowed_extension'];
            if (isset($field['max_file_size']))
                $maxsize = $field['max_file_size'] * 1024;
            $html .= $bkpkFM->createInput(null, 'label', array(
                'value' => $fieldTitle,
                'id' => $labelID,
                'class' => $label_class,
                'for' => $inputID
            ));
            if (! $fieldReadOnly) :
                $uploadButtonLeftClass = @$field['title_position'] == 'left' ? 'bkpk_left_margin' : '';
                $html .= "<div id=\"$inputID\" bkpk_field_id=\"bkpk_field_{$field['id']}\" bkpk_form_key=\"{$form['form_key']}\" name=\"{$field['field_name']}\" class=\"bkpk_file_uploader_field $uploadButtonLeftClass\" extension=\"$extension\" maxsize=\"$maxsize\"></div>";
             
            endif;
        }
        break;
    
    case 'description':
    case 'rich_text':
        
        if (('description' == $field['field_type']) && empty($field['rich_text'])) {
            $fieldType = 'textarea';
            break;
        }
        
        if (@$field['title_position'] == 'left') {
            $fieldBefore .= "<div class=\"bkpk_left_margin\">";
            $fieldAfter .= "</div>";
        }
        
        if (! empty($field['use_previous_editor'])) {
            $fieldType = 'textarea';
            $class .= "bkpk_rich_text ";
        } else {
            $showInputField = false;
            $html .= $bkpkFM->createInput(null, 'label', array(
                'value' => $fieldTitle,
                'id' => $labelID,
                'class' => $label_class,
                'for' => $inputID
            ));
            ob_start();
            $editorID = preg_replace("/[^a-z0-9 ]/", '', strtolower($inputID));
            wp_editor(@$field['field_value'], $editorID, array(
                'textarea_name' => $field['field_name'],
                'editor_height' => ! empty($field['field_height']) ? str_replace('px', '', $field['field_height']) : null,
                'editor_class' => ! empty($field['field_class']) ? $field['field_class'] : null,
                'editor_css' => ! empty($field['field_style']) ? $field['field_style'] : null
            ));
            $editorOutput = $fieldBefore . ob_get_clean() . $fieldAfter;
            $html .= ! empty($field['field_size']) ? "<div style=\"width:{$field['field_size']}\">$editorOutput</div>" : $editorOutput;
        }
        break;
}

$label_class = $label_class ? $label_class : 'pf_label';

if (isset($field['description'])) {
    $descriptionClass = ! empty($field['description_class']) ? $field['description_class'] : 'bkpk_description';
    $descriptionStyle = ! empty($field['description_style']) ? "style=\"{$field['description_style']}\"" : "";
    if (@$field['title_position'] == 'left')
        $descriptionClass .= ' bkpk_left_margin';
}

if ($bkpkFM->isPro())
    include ($bkpkFM->viewsPath . 'pro/generateProField.php');

if (! empty($doReturn))
    return $html;

/**
 * As we did not added support translation of html5 required validation message, so we move only to old style js validation.
 */
if ($validation) {
    // $validation = str_replace( 'required,', '', $validation );
    $class .= "validate[" . rtrim($validation, ',') . "]";
}
unset($attr['required']);

if (! empty($field['is_parent']))
    $class .= ' bkpk_parent';

if (empty($noMore)) {
    
    if (! empty($field['events']) && is_array($field['events']))
        $attr = array_merge($attr, $field['events']);
    
    $attr = array_merge(array(
        "value" => isset($field['field_value']) ? $field['field_value'] : "",
        "label" => __($fieldTitle, $bkpkFM->name),
        "readonly" => ! empty($fieldReadOnly) ? $fieldReadOnly : "",
        "disabled" => ! empty($isDisabled) ? true : false,
        "id" => $inputID,
        "class" => $class,
        "style" => @$inputStyle ? $inputStyle : "",
        "maxlength" => $maxlength,
        "option_after" => isset($option_after) ? $option_after : "",
        "by_key" => $by_key,
        "label_id" => $labelID,
        "label_class" => $label_class,
        "onblur" => isset($onBlur) ? $onBlur : "",
        "combind" => isset($combind) ? $combind : false,
        "before" => $fieldBefore,
        "after" => $fieldAfter,
        "placeholder" => isset($field['placeholder']) ? $field['placeholder'] : ""
    )
    // "data-um-conditions" => ! empty( $field['conditions'] ) ? urlencode(json_encode( $field['conditions'] )) : "",
    // "enclose" => ! empty( $enclose ) ? $enclose : false,
    , $attr);
    
    if ($showInputField) {
        $html .= $bkpkFM->createInput($field['field_name'], $fieldType, $attr, $fieldOptions);
    }
    
    if (isset($field2)) {
        extract($field2);
		
		 
        $html .= $bkpkFM->createInput($field2['field_name'], $fieldType, array(
            "value" => isset($field['field_value']) ? $field['field_value'] : "",
            "label" => __($fieldTitle, $bkpkFM->name),
            "readonly" => ! empty($fieldReadOnly) ? $fieldReadOnly : "",
            "id" => $field2['fieldID'],
            "class" => $class,
            "style" => isset($inputStyle) ? $inputStyle : "",
            "maxlength" => $maxlength,
            "label_id" => $labelID,
            "label_class" => $label_class ? $label_class : 'pf_label',
            "placeholder" => isset($field2['placeholder']) ? $field2['placeholder'] : "",
            "enclose" => 'p'
        ));
    }
    
    if (isset($field['description'])) {
        $html .= "<p $descriptionID class=\"$descriptionClass\" $descriptionStyle>" . __($field['description'], $bkpkFM->name) . "</p>";
    }
}

$fieldResultContent = isset($fieldResultContent) ? $fieldResultContent : "";
$fieldResultDiv = isset($fieldResultDiv) ? "<div id=\"{$inputID}_result\" class=\"bkpk_field_result\">$fieldResultContent</div>" : "";
$moreContent = isset($moreContent) ? $moreContent : "";

$divStyle = $divStyle ? "style=\"$divStyle\"" : "";

if (! empty($field['is_hide']))
    $divClass .= ' bkpk_hidden';

if (! empty($field['condition']))
    $moreContent .= '<script type="text/json" class="bkpk_condition">' . json_encode($field['condition']) . '</script>';

$html = "<div class=\"$divClass\" $divStyle>$html $fieldResultDiv $moreContent</div>";
 
