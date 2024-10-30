<?php
global $bkpkFM;
// Expected: $general

$html = null;

// Start Profile Page Selection
$html .= "<h4>" . __('Profile Page Selection', $bkpkFM->name) . "</h4>";
$html .= wp_dropdown_pages(array(
    'name' => 'general[profile_page]',
    'selected' => isset($general['profile_page']) ? $general['profile_page'] : null,
    'echo' => 0,
    'show_option_none' => 'None '
));
$html .= $bkpkFM->createInput("general[profile_in_admin]", "checkbox", array(
    'value' => isset($general['profile_in_admin']) ? $general['profile_in_admin'] : null,
    'id' => 'bkpk_general_profile_in_admin',
    'label' => sprintf(__('Show profile link to <a href="%s">Users</a> administration page.', $bkpkFM->name), admin_url('users.php'))
));
$html .= "<p>" . sprintf(__("Profile page should contain shortcode like: %s", $bkpkFM->name), "[llms-bkpk-profile form=\"your_form_name\"]") . "</p>";
// End Profile Page Selection

$html .= "<div class='pf_divider'></div>";

$emailFormat = array(
    '' => null,
    'text/plain' => __('Plain Text', $bkpkFM->name),
    'text/html' => __('HTML', $bkpkFM->name)
);

$html .= "<h4>" . __('E-mail Sender Setting', $bkpkFM->name) . "</h4>";

$html .= "<p>" . __('Set default email sender information', $bkpkFM->name) . "</p>";

$html .= $bkpkFM->createInput("general[mail_from_name]", "text", array(
    'label' => __('From Name:', $bkpkFM->name),
    'value' => @$general['mail_from_name'],
    'enclose' => "p",
    'after' => ' <em>' . __('(Leave blank to use default)', $bkpkFM->name) . '</em>',
    'style' => 'width:300px;'
));

$html .= $bkpkFM->createInput("general[mail_from_email]", "text", array(
    'label' => __('From Email:', $bkpkFM->name),
    'value' => @$general['mail_from_email'],
    'enclose' => "p",
    'after' => ' <em>' . __('(Leave blank to use default)', $bkpkFM->name) . '</em>',
    'style' => 'width:300px;'
));

$html .= $bkpkFM->createInput("general[mail_content_type]", "select", array(
    'label' => __('Email Format:', $bkpkFM->name),
    'value' => @$general['mail_content_type'], // !empty( $general[ 'mail_content_type' ] ) ? $general[ 'mail_content_type' ] : apply_filters( 'wp_mail_content_type', 'text/plain' ),
    'enclose' => 'p',
    'by_key' => true
), $emailFormat);

// echo $bkpkFM->metaBox( "General Settings", $html );
?>