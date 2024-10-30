<?php
namespace BPKPFieldManager;

global $bkpkFM;
/**
 * Expected: $form
 * $form['fields'] : array containing field's id
 * $form['disable_ajax'] : bool (default false)
 * $form['onsubmit'] : full js code for onsubmit
 * $form['form_id'] : Name of the form id
 * $form['form_class'] : Name of the form class
 * $form['form_action'] : Action name for form
 * $form['form_start'] : Just after form starting tag
 * $form['form_end'] : Just before form ending tag
 */
// get veriable by render: $actionType, $fields, $form

if (empty($form))
    return $html = $bkpkFM->ShowError(__('Form is not found.', $bkpkFM->name));

if (empty($form['fields']))
    return $html = $bkpkFM->ShowError(__('Fields are not found.', $bkpkFM->name));

if (! is_array($form['fields']))
    return $html = $bkpkFM->ShowError(__('Fields were saved incorrectly.', $bkpkFM->name));

$fields = $form['fields'];
if (empty($userID))
    $userID = 0;

$pageCount = ! empty($form['page_count']) ? $form['page_count'] : 0;

/**
 * Applying filter hook.
 * Accept two arg: $form, $formName
 * return $form
 */
$form = apply_filters('llms_bkpk_form_config', $form, $form['form_key'], $userID);
$form = apply_filters('llms_bkpk_form_config_render', $form, $form['form_key'], $userID);

$uniqueID = sanitize_key($form['form_key']);

$formID = ! empty($form['form_id']) ? $form['form_id'] : "bkpk_form_$uniqueID";
$formClass = ! empty($form['form_class']) ? $form['form_class'] : null;
$formAction = ! empty($form['form_action']) ? "action=\"" . $form['form_action'] . "\"" : null;
$onsubmit = ! empty($form['onsubmit']) ? "onsubmit=\"" . $form['onsubmit'] . "return false;\"" : null;

$html = null;

if ($actionType == 'registration' && $bkpkFM->isHookEnable('login_form_register', array(
    'form' => $form['form_key']
)))
    do_action('login_form_register');

if (@$_REQUEST['form_key'] == $form['form_key'] && @$_REQUEST['action_type'] == $actionType) {
    if (isset($bkpkFM->bkpk_post_method_status->$methodName))
        $html .= $bkpkFM->bkpk_post_method_status->$methodName;
}

$formClass .= ' bkpk_generated_form';

do_action('llms_bkpk_before_form', $form['form_key']);
$html .= "<form method=\"post\" $formAction id=\"$formID\" class=\"$formClass\" $onsubmit enctype=\"multipart/form-data\" > ";
if (! empty($form['form_start']))
    $html .= $form['form_start'];

if (is_array($fields)) {
    
    $inPage = false;
    $inSection = false;
    $isNext = false;
    $isPrevious = false;
    $currentPage = 0;
    foreach ($fields as $fieldID => $field) {
        
        if ($field['field_type'] == 'page_heading') {
            $currentPage ++;
            $isNext = $currentPage >= 2 ? true : false;
            $isPrevious = $currentPage > 2 ? true : false;
        }
        
        $html .= (new Field(null, $field, [
            'action_type' => $actionType,
            'unique_id' => $uniqueID,
            'user_id' => $userID,
            'form' => $form,
            'in_page' => $inPage,
            'in_section' => $inSection,
            'is_next' => $isNext,
            'is_previous' => $isPrevious,
            'current_page' => $currentPage
        ]))->render();
        
        if ($field['field_type'] == 'page_heading') {
            $inPage = true;
            $inSection = false;
        }
        
        if ($field['field_type'] == 'section_heading')
            $inSection = true;
    }
    
    // Similar to generateField: field_type==page_heading
    if ($inSection)
        $html .= "</div>";
    if ($pageCount >= 2) {
        $previousPage = $currentPage - 1;
        $html .= $bkpkFM->createInput("", "button", array(
            "value" => __('Previous', $bkpkFM->name),
            "onclick" => "umPageNavi($previousPage, false, this)",
            "class" => "previous_button " . ! empty($form['button_class']) ? $form['button_class'] : ""
        ));
        // $html .= "<input type='button' onclick='umPageNavi($previousPage)' value='" . __( 'Previous', $bkpkFM->name ) . "'>";
    }
    // End
    
    if ($actionType == 'registration' && $bkpkFM->isHookEnable('register_form', array(
        'form' => $form['form_key']
    ))) {
        ob_start();
        do_action('register_form');
        $html .= ob_get_contents();
        ob_end_clean();
    }
    
    $html .= $bkpkFM->createInput("form_key", "hidden", array(
        "value" => $form['form_key']
    ));
    $html .= $bkpkFM->createInput("action_type", "hidden", array(
        "value" => $actionType
    ));
    $html .= $bkpkFM->createInput("user_id", "hidden", array(
        "value" => $userID
    ));
    
    /*
     * $html .= $bkpkFM->nonceField();
     * $html .= $bkpkFM->methodName( $methodName );
     * $html .= $bkpkFM->wp_nonce_field( $methodName, 'bkpk_post_method_nonce', false, false );
     */
    
    $html .= $bkpkFM->methodPack($methodName);
    
    if ($actionType == 'profile')
        $buttonValue = __('Update', $bkpkFM->name);
    elseif ($actionType == 'registration')
        $buttonValue = __('Register', $bkpkFM->name);
    elseif ($actionType == 'login')
        $buttonValue = __('Login', $bkpkFM->name);
    
    if (! empty($form['button_title']))
        $buttonValue = $form['button_title'];
        
        // $html .= "<script>user_id=$userID</script>";
    
    if (isset($buttonValue)) {
        $html .= "<div class=\"bkpk_clear\"></div>";
        $html .= $bkpkFM->createInput("bkpk_submit_button", "submit", array(
            "value" => $buttonValue,
            "id" => "insert_user",
            "class" => ! empty($form['button_class']) ? $form['button_class'] : 'bkpk_button',
            'enclose' => 'div'
        ));
    }
    
    if ($inPage)
        $html .= "</div>";
}

if (! empty($form['form_end']))
    $html .= $form['form_end'];

$html .= "</form>";

do_action('llms_bkpk_after_form', $form['form_key'], $userID);

$js = "umPageNavi(1, false, '$formID');";
addFooterJs($js);

footerJs();

$html = apply_filters('llms_bkpk_form_display', $html, $form['form_key'], $form, $userID);
