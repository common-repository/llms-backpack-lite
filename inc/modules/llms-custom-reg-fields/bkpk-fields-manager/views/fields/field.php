<?php
global $bkpkFM;

$roles = $bkpkFM->getRoleList(true);
$emptyFirstRoles = $roles;
array_unshift($emptyFirstRoles, null);

$field_type_data = $bkpkFM->getFields('key', $field_type);
$field_type_title = $field_type_data['title'];
$field_group = $field_type_data['field_group'];
$field_types_options = $bkpkFM->getFields('field_group', $field_group, 'title', ! $bkpkFM->isPro);

if ($field_group == 'wp_default')
    $field_title = isset($field_title) ? $field_title : $field_types_options[$field_type];

/**
 * Defining $fieldXXX variable to populate back-end field options.
 *
 * Available Variable:
 *
 * $fieldBlank,
 * $fieldDivider
 *
 * $fieldTitle,
 * $fieldTypes,
 * $fieldTitlePosition
 * $fieldDescription,
 * $fieldMetaKey,
 * $fieldDefaultValue,
 * $fieldOptions,
 *
 * $fieldRequired,
 * $fieldAdminOnly,
 * $fieldNonAdminOnly,
 * $fieldReadOnly,
 * $fieldUnique,
 * $fieldNonAdminOnly,
 * $fieldRegistrationOnly,
 * $fieldDisableAjax,
 * $fieldHideDefaultAvatar,
 * $fieldCropImage,
 *
 * $fieldDefaultRole
 * $fieldRoleSelectionType,
 * $fieldSelectedRoles,
 *
 * $fieldCssClass,
 * $fieldCssStyle,
 * $fieldSize,
 * $fieldMaxChar,
 *
 * $fieldForceUsername,
 * $fieldRetypeEmail,
 * $fieldRetypePassword,
 * $fieldPasswordStrength,
 *
 * $fieldShowDivider,
 * $fieldRichText,
 * $fieldAllowedExtension,
 * $fieldDateTimeSelection,
 * $fieldDateFormat,
 * $fieldCountrySelectionType,
 *
 * $fieldMaxNumber,
 * $fieldMinNumber,
 * $fieldMaxFileSize,
 *
 * $fieldImageWidth,
 * $fieldImageHeight,
 *
 * $fieldCaptchaTheme,
 */

$fieldBlank = "<div class=\"bkpk_segment\"></div>";
$fieldDivider = "<div class=\"pf_divider\"></div>";

$fieldTitle = $bkpkFM->createInput("field_title", "text", array(
    "value" => isset($field_title) ? $field_title : null,
    "label" => __('Field Title', $bkpkFM->name),
    "id" => "field_title_$id",
    "class" => "bkpk_input bkpk_field_title_editor",
    "label_class" => "pf_label",
    "onkeyup" => "umChangeFieldTitle(this)",
    "onblur" => "umUpdateMetaKey(this)",
    "enclose" => "div class='bkpk_segment'"
));

$fieldTypes = $bkpkFM->createInput("field_type", "select", array(
    "value" => isset($field_type) ? $field_type : null,
    "label" => __('Field Type', $bkpkFM->name),
    "class" => "bkpk_input",
    "label_class" => "pf_label",
    "enclose" => "div class='bkpk_segment'",
    "onchange" => "umChangeField(this, $id)",
    "by_key" => true
), $field_types_options);

$fieldTitlePosition = $bkpkFM->createInput("title_position", "select", array(
    "value" => isset($title_position) ? $title_position : null,
    "label" => __('Title Position', $bkpkFM->name),
    "class" => "bkpk_input",
    "label_class" => "pf_label",
    "enclose" => "div class='bkpk_segment'",
    "by_key" => true
), array(
    'top' => __('Top', $bkpkFM->name),
    'left' => __('Left', $bkpkFM->name),
    'right' => __('Right', $bkpkFM->name),
    'inline' => __('Inline', $bkpkFM->name),
    'placeholder' => __('Placeholder', $bkpkFM->name),
    'hidden' => __('Hidden', $bkpkFM->name)
));

$fieldDescription = $bkpkFM->createInput("description", "textarea", array(
    "value" => isset($description) ? $description : null,
    "label" => __('Field Description', $bkpkFM->name),
    "class" => "bkpk_input",
    "label_class" => "pf_label",
    "enclose" => "div class='bkpk_segment'"
));

$fieldMetaKey = $bkpkFM->createInput("meta_key", "text", array(
    "value" => isset($meta_key) ? $meta_key : null,
    "label" => __('Meta Key', $bkpkFM->name),
    "class" => "bkpk_input bkpk_meta_key_editor",
    "label_class" => "pf_label",
    "onblur" => "umUpdateMetaKey(this)",
    "after" => "<div style='margin-right:20px;'><span class='bkpk_required'>Required Field.</span> Field data will save by metakey. Without defining metakey, field data will not be saved. e.g country_name (unique and no space)</div>",
    "enclose" => "div class='bkpk_segment'"
));

$fieldDefaultValue = $bkpkFM->createInput("default_value", "textarea", array(
    "value" => isset($default_value) ? $default_value : null,
    "label" => __('Default Value', $bkpkFM->name),
    "class" => "bkpk_input",
    "label_class" => "pf_label",
    "enclose" => "div class='bkpk_segment'"
));

$fieldOptions = $bkpkFM->createInput("options", "textarea", array(
    "value" => isset($options) ? $options : null,
    "label" => __('Field Options', $bkpkFM->name),
    "class" => "bkpk_input",
    "label_class" => "pf_label",
    "after" => "<div style='margin-right:20px;'><span class='bkpk_required'>Required Field.</span> (e.g itm1, itm2) for Key Value: itm1=Item 1, itm2=Item 2</div>",
    "enclose" => "div class='bkpk_segment'"
));

$fieldRequired = $bkpkFM->createInput("required", "checkbox", array(
    "value" => isset($required) ? $required : null,
    "label" => __('Required', $bkpkFM->name) . "<br />",
    "id" => "bkpk_fields_{$id}_required"
));

$fieldAdminOnly = $bkpkFM->createInput("admin_only", "checkbox", array(
    "value" => isset($admin_only) ? $admin_only : null,
    "label" => __('Admin Only', $bkpkFM->name) . "<br />",
    "id" => "bkpk_fields_{$id}_admin_only"
));

$fieldNonAdminOnly = $bkpkFM->createInput("non_admin_only", "checkbox", array(
    "value" => isset($non_admin_only) ? $non_admin_only : null,
    "label" => __('Non-Admin Only', $bkpkFM->name) . "<br />",
    "id" => "bkpk_fields_{$id}_non_admin_only"
));

$fieldReadOnly = $bkpkFM->createInput("read_only", "checkbox", array(
    "value" => isset($read_only) ? $read_only : null,
    "label" => __('Read Only for all user', $bkpkFM->name) . "<br />",
    "id" => "bkpk_fields_{$id}_read_only"
));

$fieldReadOnly .= $bkpkFM->createInput("read_only_non_admin", "checkbox", array(
    "value" => isset($read_only_non_admin) ? $read_only_non_admin : null,
    "label" => __('Read Only for non admin', $bkpkFM->name) . "<br />",
    "id" => "bkpk_fields_{$id}_read_only_non_admin"
));

$fieldUnique = $bkpkFM->createInput("unique", "checkbox", array(
    "value" => isset($unique) ? $unique : null,
    "label" => __('Unique', $bkpkFM->name) . "<br />",
    "id" => "bkpk_fields_{$id}_unique"
));

$fieldNonAdminOnly = $bkpkFM->createInput("non_admin_only", "checkbox", array(
    "value" => isset($non_admin_only) ? $non_admin_only : null,
    "label" => __('Non-Admin Only', $bkpkFM->name) . "<br />",
    "id" => "bkpk_fields_{$id}_non_admin_only"
));

$fieldRegistrationOnly = $bkpkFM->createInput("registration_only", "checkbox", array(
    "value" => isset($registration_only) ? $registration_only : null,
    "label" => __('Only on Registration Page', $bkpkFM->name) . "<br />",
    "id" => "bkpk_fields_{$id}_registration_only"
));

$fieldDisableAjax = $bkpkFM->createInput("disable_ajax", "checkbox", array(
    "value" => isset($disable_ajax) ? $disable_ajax : null,
    "label" => __('Disable AJAX upload', $bkpkFM->name) . "<br />",
    "id" => "bkpk_fields_{$id}_disable_ajax"
));

$fieldHideDefaultAvatar = $bkpkFM->createInput("hide_default_avatar", "checkbox", array(
    "value" => isset($hide_default_avatar) ? $hide_default_avatar : null,
    "label" => __('Hide default avatar', $bkpkFM->name) . "<br />",
    "id" => "bkpk_fields_{$id}_hide_default_avatar"
));

$fieldCropImage = $bkpkFM->createInput("crop_image", "checkbox", array(
    "value" => isset($crop_image) ? $crop_image : null,
    "label" => __('Crop Image', $bkpkFM->name) . "<br />",
    "id" => "bkpk_fields_{$id}_crop_image"
));

$fieldLineBreak = $bkpkFM->createInput("line_break", "checkbox", array(
    "value" => isset($line_break) ? $line_break : null,
    "label" => __('Line Break') . "<br />",
    "id" => "bkpk_fields_{$id}_line_break"
));

$fieldIntegerOnly = $bkpkFM->createInput("integer_only", "checkbox", array(
    "value" => isset($integer_only) ? $integer_only : null,
    "label" => __('Allow integer only') . "<br />",
    "id" => "bkpk_fields_{$id}_integer_only"
));

$fieldDefaultRole = $bkpkFM->createInput("default_value", "select", array(
    "value" => isset($default_value) ? $default_value : null,
    "label" => __('Default Role', $bkpkFM->name),
    "class" => "bkpk_input",
    "label_class" => "pf_label",
    "enclose" => "div class='bkpk_segment'",
    "option_after" => "<br/>",
    "after" => '<div>(' . __('Should be one of the Allowed Roles', $bkpkFM->name) . ')</div>',
    "by_key" => true,
    "combind" => true
), $emptyFirstRoles);

$fieldRoleSelectionType = $bkpkFM->createInput("role_selection_type", "radio", array(
    "value" => isset($role_selection_type) ? $role_selection_type : 'select',
    "label" => __('Role Selection Type', $bkpkFM->name),
    "id" => "bkpk_fields_{$id}_role_selection_type",
    "class" => "bkpk_input",
    "label_class" => "pf_label",
    "enclose" => "div class='bkpk_segment'",
    "option_after" => "<br/>",
    "by_key" => true
), array(
    'select' => 'Dropdown',
    'radio' => 'Select One (radio)',
    'hidden' => 'Hidden'
));

$fieldAllowedRoles = $bkpkFM->createInput("allowed_roles", "checkbox", array(
    "value" => isset($allowed_roles) ? $allowed_roles : array_flip($roles),
    "label" => __('Allowed Roles', $bkpkFM->name),
    "id" => "bkpk_fields_{$id}_allowed_roles",
    "class" => "bkpk_input",
    "label_class" => "pf_label",
    "enclose" => "div class='bkpk_segment'",
    "option_after" => "<br/>",
    "by_key" => true,
    "combind" => true
), $roles);

$fieldCssClass = $bkpkFM->createInput("css_class", "text", array(
    "value" => isset($css_class) ? $css_class : null,
    "label" => __('CSS Class', $bkpkFM->name),
    "class" => "bkpk_input",
    "label_class" => "pf_label",
    "enclose" => "div class='bkpk_segment'",
    "after" => "<div>(Specify custom class name)</div>"
));

$fieldCssStyle = $bkpkFM->createInput("css_style", "textarea", array(
    "value" => isset($css_style) ? $css_style : null,
    "label" => __('CSS Style', $bkpkFM->name),
    "class" => "bkpk_input",
    "label_class" => "pf_label",
    "enclose" => "div class='bkpk_segment'"
));

$fieldSize = $bkpkFM->createInput("field_size", "text", array(
    "value" => isset($field_size) ? $field_size : null,
    "label" => __('Field Size', $bkpkFM->name),
    "class" => "bkpk_input",
    "label_class" => "pf_label",
    "after" => "<div>(e.g. 200px;)</div>",
    "enclose" => "div class='bkpk_segment'"
));

$fieldHeight = $bkpkFM->createInput("field_height", "text", array(
    "value" => isset($field_height) ? $field_height : null,
    "label" => __('Field Height', $bkpkFM->name),
    "class" => "bkpk_input",
    "label_class" => "pf_label",
    "after" => "<div>(e.g. 200px;)</div>",
    "enclose" => "div class='bkpk_segment'"
));

$fieldMaxChar = $bkpkFM->createInput("max_char", "text", array(
    "value" => isset($max_char) ? $max_char : null,
    "label" => __('Max Char', $bkpkFM->name),
    "class" => "bkpk_input",
    "label_class" => "pf_label",
    "after" => "<div>(Maximum allowed character)</div>",
    "enclose" => "div class='bkpk_segment'"
));

// For wp_default fields
$fieldForceUsername = $bkpkFM->createInput("force_username", "checkbox", array(
    "value" => isset($force_username) ? $force_username : null,
    "label" => __('Force to change username', $bkpkFM->name) . "<br />",
    "id" => "bkpk_fields_{$id}_force_username"
));
$fieldRetypeEmail = $bkpkFM->createInput("retype_email", "checkbox", array(
    "value" => isset($retype_email) ? $retype_email : null,
    "label" => __('Retype Email', $bkpkFM->name) . "<br />",
    "id" => "bkpk_fields_{$id}_retype_email"
));
$fieldRetypePassword = $bkpkFM->createInput("retype_password", "checkbox", array(
    "value" => isset($retype_password) ? $retype_password : null,
    "label" => __('Retype Password', $bkpkFM->name) . "<br />",
    "id" => "bkpk_fields_{$id}_retype_password"
));
$fieldPasswordStrength = $bkpkFM->createInput("password_strength", "checkbox", array(
    "value" => isset($password_strength) ? $password_strength : null,
    "label" => __('Show password strength meter', $bkpkFM->name) . "<br />",
    "id" => "bkpk_fields_{$id}_password_strength"
));
$fieldRequiredCurrentPassword = $bkpkFM->createInput("required_current_password", "checkbox", array(
    "value" => isset($required_current_password) ? $required_current_password : null,
    "label" => __('Current password is required', $bkpkFM->name) . "<br />",
    "id" => "bkpk_fields_{$id}_current_password"
));

$fieldShowDivider = $bkpkFM->createInput("show_divider", "checkbox", array(
    "value" => isset($show_divider) ? $show_divider : null,
    "label" => __('Show Divider', $bkpkFM->name) . "<br />",
    "id" => "bkpk_fields_{$id}_show_divider"
));

$fieldRichText = $bkpkFM->createInput("rich_text", "checkbox", array(
    "value" => isset($rich_text) ? $rich_text : null,
    "label" => __('Use Rich Text', $bkpkFM->name) . "<br />",
    "id" => "bkpk_fields_{$id}_rich_text"
));

/*
 * $fieldNameFormat = $bkpkFM->createInput( "fields[$id][name_format]", "select", array(
 * "value" => isset($name_format) ? $name_format : null,
 * "label" => __( 'Name Format', $bkpkFM->name ),
 * "class" => "bkpk_input",
 * "label_class" => "pf_label",
 * "enclose" => "div class='bkpk_segment'",
 * "by_key" => true,
 * ), array( 'name'=>'Full Name', 'first_last'=>'First and Last Name', 'first_middle_last'=>'First, Middle and Last Name' ) );
 */

$fieldAllowedExtension = $bkpkFM->createInput("allowed_extension", "text", array(
    "value" => isset($allowed_extension) ? $allowed_extension : null,
    "label" => __('Allowed Extension', $bkpkFM->name),
    "class" => "bkpk_input",
    "label_class" => "pf_label",
    "after" => "<div>(default: jpg,png,gif)</div>",
    "enclose" => "div class='bkpk_segment'"
));

$fieldDateTimeSelection = $bkpkFM->createInput("datetime_selection", "select", array(
    "value" => isset($datetime_selection) ? $datetime_selection : null,
    "label" => __('Type Selection', $bkpkFM->name),
    "class" => "bkpk_input",
    "label_class" => "pf_label",
    "after" => "<div>(" . __('Date, Time or Date & Time', $bkpkFM->name) . ")</div>",
    "enclose" => "div class='bkpk_segment'",
    "by_key" => true
), array(
    'date' => __('Date', $bkpkFM->name),
    'time' => __('Time', $bkpkFM->name),
    'datetime' => __('Date and Time', $bkpkFM->name)
));

$fieldDateFormat = $bkpkFM->createInput("date_format", "text", array(
    "value" => isset($date_format) ? $date_format : null,
    "label" => __('Date Format', $bkpkFM->name),
    "class" => "bkpk_input",
    "label_class" => "pf_label",
    "after" => "<div>(Default: yy-mm-dd)</div>",
    "enclose" => "div class='bkpk_segment'"
));

$fieldCountrySelectionType = $bkpkFM->createInput("country_selection_type", "select", array(
    "value" => isset($country_selection_type) ? $country_selection_type : null,
    "label" => __('Save meta value by', $bkpkFM->name),
    "class" => "bkpk_input",
    "label_class" => "pf_label",
    "enclose" => "div class='bkpk_segment'",
    "by_key" => true
), array(
    'by_country_code' => __('Country Code', $bkpkFM->name),
    'by_country_name' => __('Country Name', $bkpkFM->name)
));

$fieldMaxNumber = $bkpkFM->createInput("max_number", "text", array(
    "value" => isset($max_number) ? $max_number : null,
    "label" => __('Maximum Number', $bkpkFM->name),
    "class" => "bkpk_input",
    "label_class" => "pf_label",
    "enclose" => "div class='bkpk_segment'"
));

$fieldMinNumber = $bkpkFM->createInput("min_number", "text", array(
    "value" => isset($min_number) ? $min_number : null,
    "label" => __('Minimum Number', $bkpkFM->name),
    "class" => "bkpk_input",
    "label_class" => "pf_label",
    "enclose" => "div class='bkpk_segment'"
));

$fieldMaxFileSize = $bkpkFM->createInput("max_file_size", "text", array(
    "value" => isset($max_file_size) ? $max_file_size : null,
    "label" => __('Max File Size', $bkpkFM->name),
    "class" => "bkpk_input",
    "label_class" => "pf_label",
    "after" => "<div>" . __('(in KB. Default: 1024KB)', $bkpkFM->name) . "</div>",
    "enclose" => "div class='bkpk_segment'"
));

$fieldImageWidth = $bkpkFM->createInput("image_width", "text", array(
    "value" => isset($image_width) ? $image_width : null,
    "label" => __('Image Width (px)', $bkpkFM->name),
    "class" => "bkpk_input",
    "label_class" => "pf_label",
    "after" => "<div>" . __('(For Image Only. e.g. 640)', $bkpkFM->name) . "</div>",
    "enclose" => "div class='bkpk_segment'"
));

$fieldImageHeight = $bkpkFM->createInput("image_height", "text", array(
    "value" => isset($image_height) ? $image_height : null,
    "label" => __('Image Height (px)', $bkpkFM->name),
    "class" => "bkpk_input",
    "label_class" => "pf_label",
    "after" => "<div>" . __('(For Image Only. e.g. 480)', $bkpkFM->name) . "</div>",
    "enclose" => "div class='bkpk_segment'"
));

$fieldImageSize = $bkpkFM->createInput("image_size", "text", array(
    "value" => isset($image_size) ? $image_size : null,
    "label" => __('Image Size (px) width/height', $bkpkFM->name),
    "class" => "bkpk_input",
    "label_class" => "pf_label",
    "after" => "<div>" . __('(Default 96)', $bkpkFM->name) . "</div>",
    "enclose" => "div class='bkpk_segment'"
));

$fieldRegex = $bkpkFM->createInput("regex", "text", array(
    "value" => isset($regex) ? $regex : null,
    "label" => __('Regex', $bkpkFM->name),
    "class" => "bkpk_input",
    "label_class" => "pf_label",
    "after" => "<div>" . sprintf(__('e.g.: %s', $bkpkFM->name), '^[A-za-z]$') . "</div>",
    "enclose" => "div class='bkpk_segment'"
));

$fieldErrorText = $bkpkFM->createInput("error_text", "text", array(
    "value" => isset($error_text) ? $error_text : null,
    "label" => __('Error Text', $bkpkFM->name),
    "class" => "bkpk_input",
    "label_class" => "pf_label",
    "after" => "<div>" . __('Default: Invalid field', $bkpkFM->name) . "</div>",
    "enclose" => "div class='bkpk_segment'"
));

/*
 * $fieldCaptchaPublicKey = $bkpkFM->createInput( "fields[$id][captcha_public_key]", "text", array(
 * "value" => isset($captcha_public_key) ? $captcha_public_key : null,
 * "label" => __( 'reCaptcha Public Key', $bkpkFM->name ),
 * "class" => "bkpk_input",
 * "label_class" => "pf_label",
 * "enclose" => "div class='bkpk_segment'",
 * ) );
 *
 * $fieldCaptchaPrivateKey = $bkpkFM->createInput( "fields[$id][captcha_private_key]", "text", array(
 * "value" => isset($captcha_private_key) ? $captcha_private_key : null,
 * "label" => __( 'reCaptcha Private Key', $bkpkFM->name ),
 * "class" => "bkpk_input",
 * "label_class" => "pf_label",
 * "enclose" => "div class='bkpk_segment'",
 * ) );
 */

$fieldCaptchaTheme = $bkpkFM->createInput("captcha_theme", "select", array(
    "value" => isset($captcha_theme) ? $captcha_theme : null,
    "label" => __('reCaptcha Theme', $bkpkFM->name),
    "class" => "bkpk_input",
    "label_class" => "pf_label",
    "enclose" => "div class='bkpk_segment'",
    "by_key" => true
), array(
    '' => __('Red', $bkpkFM->name),
    'white' => __('White', $bkpkFM->name),
    'blackglass' => __('Black Glass', $bkpkFM->name),
    'clean' => __('Clean', $bkpkFM->name)
));

/**
 * Combined fields conditionally
 */

$html = "$fieldTitle $fieldTypes $fieldTitlePosition $fieldDivider";

// Single Field
if ($field_type == 'user_login') :
    $html .= "$fieldDescription $fieldMaxChar";
    $html .= "<div class='bkpk_segment'>$fieldAdminOnly</div>";
    $html .= "$fieldDivider $fieldSize $fieldCssClass $fieldCssStyle";
    $html .= "<div class='bkpk_segment'><p>" . __('By default, <strong>Required</strong> and <strong>Unique</strong> validation will be will be applied on this field. <strong>Read Only</strong> will be applied conditionally.', $bkpkFM->name) . "</p></div>";
 

elseif ($field_type == 'user_email') :
    $html .= "$fieldDescription $fieldMaxChar";
    $html .= "<div class='bkpk_segment'>$fieldRetypeEmail $fieldAdminOnly $fieldReadOnly</div>";
    $html .= "$fieldDivider $fieldSize $fieldCssClass $fieldCssStyle";
    $html .= "<div class='bkpk_segment'><p>" . __('By default, <strong>Required</strong> and <strong>Unique</strong> validation will be applied on this field.', $bkpkFM->name) . "</p></div>";
 

elseif ($field_type == 'user_pass') :
    $html .= "$fieldDescription $fieldMaxChar";
    $html .= "<div class='bkpk_segment'>$fieldRequiredCurrentPassword $fieldRetypePassword $fieldPasswordStrength $fieldAdminOnly $fieldReadOnly</div>";
    $html .= "$fieldDivider $fieldSize $fieldCssClass $fieldCssStyle";
    $html .= "<div class='bkpk_segment'><p>" . __('<strong>Required</strong> validation will be applied automatically when password field is being used for registration.', $bkpkFM->name) . "</p></div>";


// elseif( $field_type == 'user_nicename' ):
// elseif( $field_type == 'user_url' ):
// elseif( $field_type == 'user_registered' ):
// elseif( $field_type == 'display_name' ):
// elseif( $field_type == 'first_name' OR $field_type == 'last_name' ):

elseif ($field_type == 'description') :
    $html .= "$fieldDescription $fieldMaxChar";
    $html .= "<div class='bkpk_segment'>$fieldRichText $fieldRequired $fieldAdminOnly $fieldReadOnly $fieldUnique</div>";
    $html .= "$fieldHeight";
    $html .= "$fieldDivider $fieldSize $fieldCssClass $fieldCssStyle";
 

elseif ($field_type == 'role') :
    $html .= "$fieldDescription $fieldDefaultRole";
    $html .= "<div class='bkpk_segment'>$fieldRequired $fieldAdminOnly $fieldNonAdminOnly $fieldReadOnly</div>";
    $html .= "$fieldAllowedRoles $fieldRoleSelectionType $fieldBlank";
    $html .= "$fieldDivider $fieldSize $fieldCssClass $fieldCssStyle";
 

elseif ($field_type == 'user_avatar') :
    $html .= "$fieldDescription $fieldAllowedExtension";
    $html .= "<div class='bkpk_segment'>$fieldRequired $fieldAdminOnly $fieldReadOnly $fieldDisableAjax $fieldCropImage $fieldHideDefaultAvatar</div>";
    $html .= "$fieldImageSize ";
    $html .= "$fieldDivider $fieldMaxFileSize $fieldCssClass $fieldCssStyle";
 

elseif ($field_type == 'hidden') :
    $html .= "$fieldMetaKey $fieldDefaultValue";
    $html .= "<div class='bkpk_segment'>$fieldAdminOnly</div>";
 

elseif ($field_type == 'select' || $field_type == 'radio' || $field_type == 'checkbox') :
    $html .= "$fieldDescription $fieldMetaKey";
    $html .= "<div class='bkpk_segment'>$fieldRequired $fieldAdminOnly $fieldReadOnly $fieldLineBreak</div>";
    $html .= "$fieldDefaultValue $fieldOptions";
    $html .= "$fieldDivider $fieldSize $fieldCssClass $fieldCssStyle";


// Rendering Pro field
// elseif( ( $field_group == 'standard' && !@$field_type_data['is_free'] ) || ( $field_group == 'formatting' ) ) :
elseif (! $field_type_data['is_free']) :
    $html .= $bkpkFM->renderPro('fieldPro', array(
        'field_type' => $field_type,
        'fieldDescription' => $fieldDescription,
        'fieldMetaKey' => $fieldMetaKey,
        'fieldRequired' => $fieldRequired,
        'fieldAdminOnly' => $fieldAdminOnly,
        'fieldReadOnly' => $fieldReadOnly,
        'fieldUnique' => $fieldUnique,
        'fieldDefaultValue' => $fieldDefaultValue,
        'fieldOptions' => $fieldOptions,
        'fieldSize' => $fieldSize,
        'fieldMaxChar' => $fieldMaxChar,
        'fieldCssClass' => $fieldCssClass,
        'fieldCssStyle' => $fieldCssStyle,
        'fieldNonAdminOnly' => $fieldNonAdminOnly,
        'fieldRegistrationOnly' => $fieldRegistrationOnly,
        'fieldDisableAjax' => $fieldDisableAjax,
        'fieldCropImage' => $fieldCropImage,
        'fieldIntegerOnly' => $fieldIntegerOnly,
        'fieldDivider' => $fieldDivider,
        
        'fieldDateTimeSelection' => $fieldDateTimeSelection,
        'fieldDateFormat' => $fieldDateFormat,
        'fieldRetypePassword' => $fieldRetypePassword,
        'fieldPasswordStrength' => $fieldPasswordStrength,
        'fieldRetypeEmail' => $fieldRetypeEmail,
        'fieldAllowedExtension' => $fieldAllowedExtension,
        'fieldImageWidth' => $fieldImageWidth,
        'fieldImageHeight' => $fieldImageHeight,
        'fieldMaxFileSize' => $fieldMaxFileSize,
        'fieldMinNumber' => $fieldMinNumber,
        'fieldMaxNumber' => $fieldMaxNumber,
        'fieldCountrySelectionType' => $fieldCountrySelectionType,
        'fieldShowDivider' => $fieldShowDivider,
        'fieldRegex' => $fieldRegex,
        'fieldErrorText' => $fieldErrorText,
        'fieldCaptchaTheme' => $fieldCaptchaTheme
    ));


// Default property for fields. if no single settings are found
elseif ($field_group == 'wp_default') :
    $html .= "$fieldDescription $fieldMaxChar";
    $html .= "<div class='bkpk_segment'>$fieldRequired $fieldAdminOnly $fieldReadOnly $fieldUnique</div>";
    $html .= "$fieldDivider $fieldSize $fieldCssClass $fieldCssStyle";
 

elseif ($field_group == 'standard') :
    $html .= "$fieldDescription $fieldMetaKey";
    $html .= "<div class='bkpk_segment'>$fieldRequired $fieldAdminOnly $fieldReadOnly $fieldUnique</div>";
    $html .= "$fieldDefaultValue $fieldHeight $fieldMaxChar";
    $html .= "$fieldDivider $fieldSize $fieldCssClass $fieldCssStyle";


endif;

/**
 * * Start conditional login **
 */
$html .= $bkpkFM->renderPro('conditionalPanel', array(
    'id' => $id,
    'conditional' => ! empty($conditions) && is_array($conditions) ? $conditions : array(),
    'fieldList' => $bkpkFM->fieldListforDropdown
), 'fields', true);
/**
 * * End conditional login **
 */

$html = "<div id='field_$id'>$html</div>";

$field_title = isset($field_title) ? $field_title : __('New Field', $bkpkFM->name);
$metaBoxTitle = "<div class='bkpk_admin_field_single'><span class='bkpk_admin_field_title'>$field_title</span> (<span class='bkpk_admin_field_type'>$field_type_title</span>) ID:<span class='bkpk_field_id'>$id</span></div>";

$metaBoxOpen = true;
if (isset($n))
    if (! ($n == 1))
        $metaBoxOpen = false;

$html = $bkpkFM->metaBox($metaBoxTitle, $html, true, $metaBoxOpen);

echo "<div class=\"bkpk_field_single\">$html</div>";