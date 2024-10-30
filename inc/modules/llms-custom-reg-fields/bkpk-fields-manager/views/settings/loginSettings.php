<?php
/**
 * Expected: $login
 * field slug: login
 */
global $bkpkFM;
$html = null;

/**
 * Login Page
 */
$html .= "<h4>" . __('Login Page', $bkpkFM->name) . "</h4>";
$html .= wp_dropdown_pages([
    'name' => 'login[login_page]',
    'id' => 'bkpk_login_login_page',
    'selected' => @$login['login_page'],
    'echo' => 0,
    'show_option_none' => 'None '
]);

$createPageUrl = admin_url('admin-ajax.php');
$createPageUrl = add_query_arg([
    'page' => 'login',
    'method_name' => 'generatePage',
    'action' => 'pf_ajax_request'
], $createPageUrl);
$createPageUrl = wp_nonce_url($createPageUrl, 'generate_page');
$html .= "<a href='$createPageUrl' id='bkpk_login_login_page_create' class='button-secondary'>Create Page</a>";

$html .= "<p>" . sprintf(__('Login page should contain shortcode like: %s', $bkpkFM->name), "[llms-bkpk-login]") . "</p>";

$html2 = $bkpkFM->createInput("login[disable_wp_login_php]", "checkbox", [
    "label" => sprintf(__('Disable default login url (%s)', $bkpkFM->name), site_url('wp-login.php')),
    "value" => @$login['disable_wp_login_php'],
    "id" => "bkpk_login_disable_wp_login_php",
    "onchange" => "umSettingsToggleError()",
    "enclose" => "p"
]);

$loginUrl = ! empty($login['login_page']) ? get_permalink($login['login_page']) : null;
$html2 .= '<p><em>' . sprintf(__('Disable wp-login.php and redirect to front-end login page %s', $bkpkFM->name), $loginUrl) . '</em></p>';

$html .= "<div id=\"bkpk_login_disable_wp_login_php_block\">$html2</div>";

$html .= "<div class='pf_divider'></div>";

/**
 * Login Form
 */
$html .= "<h4>" . __('Login Form', $bkpkFM->name) . "</h4>";
$html .= $bkpkFM->createInput("login[disable_lostpassword]", "checkbox", [
    "value" => @$login['disable_lostpassword'],
    "id" => "bkpk_login_disable_lostpassword",
    "label" => __('Disable lost password feature', $bkpkFM->name),
    "enclose" => "p"
]);

$html .= $bkpkFM->createInput("login[disable_registration_link]", "checkbox", [
    "value" => @$login['disable_registration_link'],
    "id" => "bkpk_login_disable_registration_link",
    "label" => __('Hide registration link', $bkpkFM->name),
    "enclose" => "p"
]);

$html .= $bkpkFM->createInput("login[disable_ajax]", "checkbox", [
    "value" => @$login['disable_ajax'],
    "id" => "bkpk_login_disable_ajax",
    "label" => __('Disable AJAX submit', $bkpkFM->name),
    "enclose" => "p"
]);

$html .= $bkpkFM->renderPro("loginSettingsPro", [
    'login' => $login
], "settings");