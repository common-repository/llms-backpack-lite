<?php
global $bkpkFM;

$isPro = $bkpkFM->isPro();

$html = null;

$html .= "<h4>$bkpkFM->title " . sprintf(__("Version: %s", $bkpkFM->name), $bkpkFM->version) . "</h4>";
$html .= "<div class='pf_divider'></div>";

if ($isPro) {
    $html .= "<p><strong>" . sprintf(__("Your license is validated. %s is Installed and active", $bkpkFM->name), $bkpkFM->title) . "</strong> ";
    $html .= $bkpkFM->createInput("", "button", array(
        "value" => __('Update Credentials', $bkpkFM->name),
        "id" => "bkpk_activation_link",
        "class" => "button"
    ));
    $html .= "</p>";
    
    $formStyle = 'style="display:none"';
    $formMsg = __('Enter your email and password for %s', $bkpkFM->name);
    $formMsg .= $bkpkFM->createInput("", "button", array(
        "value" => __('Withdraw License', $bkpkFM->name),
        "id" => "bkpk_deactivation_link",
        "class" => "button-secondary pf_right"
    ));
} else {
    $formStyle = '';
    if ($bkpkFM->isPro)
        $formMsg = __('Enter your email and password for %s to activate the pro version.', $bkpkFM->name);
    else
        $formMsg = __('Enter your email and password for %s for upgrade to pro version.', $bkpkFM->name);
}

$html .= "<form id=\"bkpk_activation_form\" method=\"post\" $formStyle onsubmit=\"umAuthorizePro(this); return false;\" >";

$html .= '<p> ' . sprintf($formMsg, make_clickable($bkpkFM->website)) . '</p>';

if (! $isPro)
    $html .= $getLicense = "<a href=\"{$bkpkFM->website}/faq/\" class=\"button-primary pf_right\">" . __('FAQ / Help', $bkpkFM->name) . '</a>';

$html .= $bkpkFM->createInput('account_email', 'text', array(
    'id' => 'account_email',
    'label' => '<strong>' . __('Email', $bkpkFM->name) . '</strong>',
    'class' => 'validate[required,custom[email]]',
    'label_class' => 'bkpk_label_left',
    'style' => 'width:200px;',
    'enclose' => 'p'
));

$html .= $bkpkFM->createInput('account_pass', 'password', array(
    'id' => 'account_pass',
    'label' => '<strong>' . __('Password', $bkpkFM->name) . '</strong>',
    'class' => 'validate[required]',
    'label_class' => 'bkpk_label_left',
    'style' => 'width:200px;',
    'enclose' => 'p'
));

$html .= "<input type=\"hidden\" name=\"action_type\" value=\"authorize_pro\">";

$html .= $bkpkFM->nonceField();

if ($isPro) {
    $html .= $bkpkFM->createInput("save_field", "button", array(
        "value" => __('Cancel', $bkpkFM->name),
        "id" => "bkpk_cancel_link",
        "class" => "button-secondary",
        "style" => "margin-left:150px;",
        "after" => "&nbsp;&nbsp;"
    ));
}

$html .= $bkpkFM->createInput("save_field", "submit", array(
    "value" => $isPro ? __('Update', $bkpkFM->name) : __('Validate', $bkpkFM->name),
    "id" => "authorize_pro",
    "class" => "button-secondary",
    "style" => ! $isPro ? "margin-left:150px;" : ""
));

if (! $bkpkFM->isPro && $bkpkFM->isLicenceValidated())
    $html .= " <strong><a href='" . $bkpkFM->pluginUpdateUrl() . "'>" . __('Click to upgrade to Pro!', $bkpkFM->name) . "</a></strong> ";

$html .= "</form>";

$confirmMsg = '';
if (is_multisite()) {
    if (is_super_admin())
        $confirmMsg = __('This will withdraw license from all sites under the network. Are you sure you want to withdraw pro license from all sites?', $bkpkFM->name);
} else
    $confirmMsg = __('Are you sure you want to withdraw pro license from this site?', $bkpkFM->name);

$html .= "\n\r" . '<script type="text/javascript">';
$html .= 'jQuery(document).ready(function(){';
$html .= 'jQuery("#bkpk_activation_link").click(function(){jQuery("#bkpk_activation_form").fadeToggle();});';
$html .= 'jQuery("#bkpk_cancel_link").click(function(){jQuery("#bkpk_activation_form").fadeOut();});';
$html .= 'jQuery("#bkpk_deactivation_link").click(function(){if(confirm("' . $confirmMsg . '")){umWithdrawLicense(this);}});';
$html .= '});';
$html .= '</script>' . "\n\r";

echo $bkpkFM->metaBox(__('lifterlms Back-Pack Pro Account Information', $bkpkFM->name), $html);
