<?php
global $bkpkFM;
// Expected: $registration
// field slug: registration

$html = null;

$html .= "<p><strong>" . __('User registration page', $bkpkFM->name) . "  </strong></p>";
$html .= wp_dropdown_pages(array(
    'name' => 'registration[user_registration_page]',
    'id' => 'bkpk_registration_user_registration_page',
    'selected' => @$registration['user_registration_page'],
    'echo' => 0,
    'show_option_none' => 'None '
));

$html .= '<p>Registration page should contain shortcode like: [llms-bkpk-registration form="your_form_name"]</p>';

$html .= $bkpkFM->renderPro("registrationSettingsPro", array(
    'registration' => $registration
), "settings");
