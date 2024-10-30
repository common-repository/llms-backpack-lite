<?php
namespace BPKPFieldManager;

global $bkpkFM;
// Expected: $config, $loginTitle, $disableAjax, $methodName

$uniqueID = isset($config['unique_id']) ? $config['unique_id'] : rand(0, 99);

$onSubmit = $disableAjax ? null : "onsubmit=\"umLogin(this); return false;\"";

$html = null;

if (isset($config['before_form']))
    $html .= $config['before_form'];

$formID = ! empty($config['form_id']) ? $config['form_id'] : "bkpk_login_form$uniqueID";
$formClass = isset($config['form_class']) ? $config['form_class'] : 'bkpk_login_form';

$html .= "<form id=\"$formID\" class=\"$formClass\" method=\"post\" $onSubmit >";

$html .= $bkpkFM->createInput('log', 'text', array(
    'value' => isset($_REQUEST['log']) ? stripslashes($_REQUEST['log']) : '',
    'label' => ! empty($config['login_label']) ? $config['login_label'] : $loginTitle,
    'placeholder' => ! empty($config['login_placeholder']) ? $config['login_placeholder'] : '',
    'id' => ! empty($config['login_id']) ? $config['login_id'] : 'user_login' . $uniqueID,
    'class' => ! empty($config['login_class']) ? $config['login_class'] : 'bkpk_login_field bkpk_input',
    'label_class' => ! empty($config['login_label_class']) ? $config['login_label_class'] : 'pf_label',
    'enclose' => 'p'
));

$html .= $bkpkFM->createInput('pwd', 'password', array(
    'label' => ! empty($config['pass_label']) ? $config['pass_label'] : $bkpkFM->getMsg('login_pass_label'),
    'placeholder' => ! empty($config['pass_placeholder']) ? $config['pass_placeholder'] : '',
    'id' => ! empty($config['pass_id']) ? $config['pass_id'] : 'user_pass' . $uniqueID,
    'class' => ! empty($config['pass_class']) ? $config['pass_class'] : 'bkpk_pass_field bkpk_input',
    'label_class' => ! empty($config['pass_label_class']) ? $config['pass_label_class'] : 'pf_label',
    'enclose' => 'p'
));

if ($bkpkFM->isHookEnable('login_form')) {
    ob_start();
    do_action('login_form');
    $html .= ob_get_contents();
    ob_end_clean();
}

$html .= $bkpkFM->createInput('rememberme', 'checkbox', array(
    'value' => isset($_REQUEST['rememberme']) ? true : false,
    'label' => ! empty($config['remember_label']) ? $config['remember_label'] : $bkpkFM->getMsg('login_remember_label'),
    'id' => ! empty($config['remember_id']) ? $config['remember_id'] : 'remember' . $uniqueID,
    'class' => ! empty($config['remember_class']) ? $config['remember_class'] : 'bkpk_remember_field',
    'enclose' => 'p'
));

// $html .= "<input type='hidden' name='action' value='bkpk_login' />";
// $html .= "<input type='hidden' name='action_type' value='login' />";

$html .= $bkpkFM->methodPack($methodName);

if (! empty($_REQUEST['redirect_to'])) {
    $html .= $bkpkFM->createInput('redirect_to', 'hidden', array(
        'value' => $_REQUEST['redirect_to']
    ));
}

if (isset($config['before_button']))
    $html .= $config['before_button'];

$html .= $bkpkFM->createInput('wp-submit', 'submit', array(
    'value' => ! empty($config['button_value']) ? $config['button_value'] : $bkpkFM->getMsg('login_button'),
    'id' => ! empty($config['button_id']) ? $config['button_id'] : 'bkpk_login_button' . $uniqueID,
    'class' => ! empty($config['button_class']) ? $config['button_class'] : 'bkpk_login_button',
    'enclose' => 'p'
));

if (isset($config['after_button']))
    $html .= $config['after_button'];

$html .= "</form>";

if (isset($config['after_form']))
    $html .= $config['after_form'];

$js = "jQuery(\"input\").placeholder();";
addFooterJs($js);
footerJs();