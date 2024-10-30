<?php
namespace BPKPFieldManager;

class SupportArrayModel
{

    function controllersOrder()
    {
        return []; // This array causes to load the file twice.
        return array(
            'umPreloadsController',
            'umPreloadsProController',
            'umBackendProfileController',
            'umShortcodesController',
            'umFieldsController',
            'umFormsController',
            'umEmailNotificationController',
            'umExportImportController',
            'umSettingsController'
        );
    }

    function adminPages()
    {
        global $bkpkFM;
        
        $pages = array(
           /*  'forms' => array(
                'menu_title' => __('Forms', $bkpkFM->name),
                'page_title' => __('lifterlms Back-Pack Forms Editor', $bkpkFM->name),
                'menu_slug' => 'llmsbkpk',
                'position' => 0,
                'is_free' => true
            ), */
            'fields' => array(
                'menu_title' => __('Fields', $bkpkFM->name),
                'page_title' => __('Fields Editor', $bkpkFM->name),
                'menu_slug' => 'llms_bkpk-user-fields',
                'position' => 1,
                'is_free' => true,
                'show_menu' => false
            ),
			
          /*  'email_notification' => array(
                'menu_title' => __('Email Notifications', $bkpkFM->name),
                'page_title' => __('Email Notifications', $bkpkFM->name),
                'menu_slug' => 'llms-bkpk-email',
                'position' => 2,
                'is_free' => false
            ),
            'export_import' => array(
                'menu_title' => __('Export & Import', $bkpkFM->name),
                'page_title' => __('Export & Import', $bkpkFM->name),
                'menu_slug' => 'llms-bkpk-import-export',
                'position' => 3,
                'is_free' => false
            ),
             'settings' => array(
                'menu_title' => __('Settings', $bkpkFM->name),
                'page_title' => __('lifterlms Back-Pack Settings', $bkpkFM->name),
                'menu_slug' => 'llms-bkpk-settings',
                'position' => 4,
                'is_free' => true
            ),
            'pro_ads' => array(
                'menu_title' => __('Pro Features', $bkpkFM->name),
                'page_title' => __('lifterlms Back-Pack Pro Features', $bkpkFM->name),
                'menu_slug' => 'llms-bkpk-pro-ads',
                'position' => 5,
                'is_free' => true,
                'not_in_pro' => true
            ) */
        );
        
        $pages = apply_filters('llms_bkpk_admin_pages', $pages);
        uasort($pages, array(
            $bkpkFM,
            'sortByPosition'
        ));
        
        return $pages;
    }

    function hooksList()
    {
        return array(
            'group_login' => 'Login',
            'login_form_login' => false, // action
            'login_redirect' => false, // filter
            'wp_login_errors' => false, // filter
            'login_form' => false, // action
            'login_form_logout' => false, // action
            
            'group_lostpassword' => 'Lost Password',
            'login_form_lostpassword' => false, // action
            'login_form_retrievepassword' => false, // action
            'lostpassword_post' => false, // action
            'retrieve_password' => false, // action
            'allow_password_reset' => false, // filter
            'retrieve_password_key' => false, // action
            'retrieve_password_title' => false, // filter
            'retrieve_password_message' => false, // filter
            'lostpassword_redirect' => false, // filter
            'lost_password' => false, // action
            'lostpassword_form' => false, // action
            
            'group_resetpass' => 'Reset Password',
            'login_form_resetpass' => false, // action
            'login_form_rp' => false, // action
            'password_reset_key_expired' => false, // filter
            'validate_password_reset' => false, // action
            'password_reset' => false, // action
            'resetpass_form' => false, // action
            
            'group_register' => 'User Registration',
            'login_form_register' => false, // action
                                            // 'wp_signup_location' => false, //filter for multisite found in wp-login.php
            'registration_redirect' => false, // filter
            'register_form' => false, // action
            'user_registration_email' => false, // filter
            'register_post' => false, // action
            'registration_errors' => false
        ); // filter
    }

    /**
     * This method is not yet tested used anywhere.
     *
     * Removing action and filter hooks form wp.
     */
    function removeHooks()
    {
        $list = [
            'password_reset' => 'action'
        ];
        
        foreach ($list as $hook => $type) {
            if (! $this->isHookEnable()) {
                if ($type == 'action')
                    remove_all_actions($hook);
                elseif ($type == 'filter')
                    remove_all_filters($hook);
            }
        }
    }

    /**
     *
     * @param type $hookName            
     * @param type $hookType:
     *            action | filter
     * @return boolean
     */
    function isHookEnable($hookName, $args = array())
    {
        global $bkpkFMCache;
        
        if (empty($bkpkFMCache->hooksList))
            $bkpkFMCache->hooksList = self::hooksList();
        
        $enable = ! empty($bkpkFMCache->hooksList[$hookName]) ? true : false;
        
        return apply_filters("llms_bkpk_wp_hook", $enable, $hookName, $args);
    }
    
    // Not suppose to use since 1.1.6rc2
    function isFilterEnable($hookName)
    {
        $list = array(
            'login_redirect' => false, // Commit
            'logout_redirect' => true, // Commit changed to llms_bkpk_logout_redirect
            'registration_redirect' => true
        ); // commit
        
        $list = apply_filters('llms_bkpk_filter_list', $list);
        
        if (! empty($list[$hookName]))
            return true;
        return false;
    }

    function enqueueScripts($scripts = [])
    {
        global $bkpkFM;
        
        $jsUrl = $bkpkFM->assetsUrl . 'js/';
        $cssUrl = $bkpkFM->assetsUrl . 'css/';
        
        $list = array(
            'llms-bkpk' => array(
                'llms-bkpk.js' => '',
                'llms-bkpk.css' => ''
            ),
            'llms-bkpk-admin' => array(
                'llms-bkpk-admin.js' => ''
            ),
            'jquery-ui-all' => array(
                'jquery-ui.min.css' => 'jqueryui/'
            ),
            'fileuploader' => array(
                'fileuploader.js' => 'jquery/',
                'fileuploader.css' => 'jquery/'
            ),
            'wysiwyg' => array(
                'jquery.wysiwyg.js' => 'jquery/',
                'wysiwyg.image.js' => 'jquery/',
                'wysiwyg.link.js' => 'jquery/',
                'wysiwyg.table.js' => 'jquery/',
                'jquery.wysiwyg.css' => 'jquery/'
            ),
            'timepicker' => array(
                'jquery-ui-timepicker-addon.js' => 'jquery/'
            ),
            'validationEngine' => array(
                'jquery.validationEngine-en.js' => 'jquery/',
                'jquery.validationEngine.js' => 'jquery/',
                'validationEngine.jquery.css' => 'jquery/'
            ),
            'password_strength' => array(
                'jquery.password_strength.js' => 'jquery/'
            ),
            'placeholder' => array(
                'jquery.placeholder.js' => 'jquery/'
            ),
            'multiple-select' => array(
                'jquery.multiple.select.js' => 'jquery/',
                'multiple-select.css' => 'jquery/'
            ),
            'opentip' => array(
                'opentip-jquery.min.js' => 'jquery/',
                'opentip.css' => 'jquery/'
            ),
            'bootstrap' => array(
                'bootstrap.css' => 'bootstrap/',
                'bootstrap.min.js' => ''
            ),
            'font-awesome' => array(
                'font-awesome.min.css' => 'font-awesome/css/'
            ),
            'bootstrap-multiselect' => array(
                'bootstrap-multiselect.css' => 'jquery/',
                'bootstrap-multiselect.js' => 'jquery/'
            )
        );
        
        $list = apply_filters('llms_bkpk_scripts', $list);
        
        $version = $bkpkFM->version;
        
        foreach ($scripts as $script) {
            if (isset($list[$script])) {
                foreach ($list[$script] as $key => $val) {
                    $file = $bkpkFM->fileinfo($key);
                    if ($file->ext == 'js')
                        wp_enqueue_script($file->name, $jsUrl . $val . $key, array(
                            'jquery'
                        ), $version, true);
                    elseif ($file->ext == 'css')
                        wp_enqueue_style($file->name, $cssUrl . $val . $key, array(), $version);
                }
            } else
                wp_enqueue_script($script);
        }
    }

    function loadAllScripts()
    {
        $this->enqueueScripts(array(
            'llms-bkpk',
            'jquery-ui-all',
            'fileuploader',
            'wysiwyg',
            'jquery-ui-datepicker',
            'jquery-ui-slider',
            'timepicker',
            'validationEngine',
            'password_strength',
            'placeholder',
            'multiple-select'
        ));
        $this->runLocalization();
    }

    function umFields($name = '')
    {
        global $bkpkFM;
        
        $fieldsList = array(
            
            // WP default fields
            'user_login' => array(
                'title' => __('Username', $bkpkFM->name),
                'field_group' => 'wp_default',
                'is_free' => false
            ),
            'user_email' => array(
                'title' => __('Email', $bkpkFM->name),
                'field_group' => 'wp_default',
                'is_free' => false
            ),
            'user_pass' => array(
                'title' => __('Password', $bkpkFM->name),
                'field_group' => 'wp_default',
                'is_free' => false
            ),   
            /*'user_nicename' => array(
                'title'         => 'Nicename',
                'field_group'     => 'wp_default', 
            ), */            
            'user_url' => array(
                'title' => __('Website', $bkpkFM->name),
                'field_group' => 'wp_default',
                'is_free' => false
            ),
            'display_name' => array(
                'title' => __('Display Name', $bkpkFM->name),
                'field_group' => 'wp_default',
                'is_free' => true
            ),
            'nickname' => array(
                'title' => __('Nickname', $bkpkFM->name),
                'field_group' => 'wp_default',
                'is_free' => true
            ),
            'first_name' => array(
                'title' => __('First Name', $bkpkFM->name),
                'field_group' => 'wp_default',
                'is_free' => true
            ),
            'last_name' => array(
                'title' => __('Last Name', $bkpkFM->name),
                'field_group' => 'wp_default',
                'is_free' => true
            ),
            'description' => array(
                'title' => __('Bio', $bkpkFM->name),
                'field_group' => 'wp_default',
                'is_free' => true
            ),
            'user_registered' => array(
                'title' => __('Registered Date', $bkpkFM->name),
                'field_group' => 'wp_default',
                'is_free' => true
            ),
            'role' => array(
                'title' => __('Role', $bkpkFM->name),
                'field_group' => 'wp_default',
                'is_free' => false
            ),
            'jabber' => array(
                'title' => __('Jabber', $bkpkFM->name),
                'field_group' => 'wp_default',
                'is_free' => false
            ),
            'aim' => array(
                'title' => __('Aim', $bkpkFM->name),
                'field_group' => 'wp_default',
                'is_free' => false
            ),
            'yim' => array(
                'title' => __('Yim', $bkpkFM->name),
                'field_group' => 'wp_default',
                'is_free' => false
            ),
            'user_avatar' => array(
                'title' => __('Avatar', $bkpkFM->name),
                'field_group' => 'wp_default',
                'is_free' => true
            ),
            
            'blogname' => is_multisite() ? array(
                'title' => __('New Blog', $bkpkFM->name),
                'field_group' => 'wp_default',
                'is_free' => false
            ) : null,
            
            // Standard Fields
            'text' => array(
                'title' => __('Textbox', $bkpkFM->name),
                'field_group' => 'wp_default',
                'is_free' => true
            ),
            'textarea' => array(
                'title' => __('Paragraph', $bkpkFM->name),
                'field_group' => 'wp_default',
                'is_free' => true
            ),
            'rich_text' => array(
                'title' => __('Rich Text', $bkpkFM->name),
                'field_group' => 'wp_default',
                'is_free' => true
            ),
            'hidden' => array(
                'title' => __('Hidden Field', $bkpkFM->name),
                'field_group' => 'wp_default',
                'is_free' => true
            ),
            'select' => array(
                'title' => __('Drop Down', $bkpkFM->name),
                'field_group' => 'wp_default',
                'is_free' => true
            ),
            'radio' => array(
                'title' => __('Radio', $bkpkFM->name),
                'field_group' => 'wp_default',
                'is_free' => true
            ),
            'checkbox' => array(
                'title' => __('Checkbox', $bkpkFM->name),
                'field_group' => 'wp_default',
                'is_free' => true
            ),
            'multiselect' => array(
                'title' => __('Multi Select', $bkpkFM->name),
                'field_group' => 'wp_default',
                'is_free' => false
            ),
            'datetime' => array(
                'title' => __('Date / Time', $bkpkFM->name),
                'field_group' => 'standard',
                'is_free' => false
            ),                      
            /*'password' => array(
                'title'         => __( 'Password', $bkpkFM->name ),
                'field_group'   => 'standard', 
                'is_free'       => false,
            ),    
            'email' => array(
                'title'         => __( 'Email', $bkpkFM->name ),
                'field_group'   => 'standard',
                'is_free'       => false,
            ),*/
            'file' => array(
                'title' => __('File Upload', $bkpkFM->name),
                'field_group' => 'standard',
                'is_free' => false
            ),
            'image_url' => array(
                'title' => __('Image URL', $bkpkFM->name),
                'field_group' => 'standard',
                'is_free' => false
            ),
            'phone' => array(
                'title' => __('Phone Number', $bkpkFM->name),
                'field_group' => 'standard',
                'is_free' => false
            ),
            'number' => array(
                'title' => __('Number', $bkpkFM->name),
                'field_group' => 'standard',
                'is_free' => false
            ),
            'url' => array(
                'title' => __('URL', $bkpkFM->name),
                'field_group' => 'standard',
                'is_free' => false
            ),
            'country' => array(
                'title' => __('Country', $bkpkFM->name),
                'field_group' => 'standard',
                'is_free' => false
            ),
            'custom' => array(
                'title' => 'Custom Field',
                'field_group' => 'standard',
                'is_free' => false
            ),
            
            // Formating Fields
            'page_heading' => array(
                'title' => __('Page Heading', $bkpkFM->name),
                'field_group' => 'formatting',
                'is_free' => false
            ),
            'section_heading' => array(
                'title' => __('Section Heading', $bkpkFM->name),
                'field_group' => 'formatting',
                'is_free' => false
            ),
            'html' => array(
                'title' => __('HTML', $bkpkFM->name),
                'field_group' => 'formatting',
                'is_free' => false
            ),
            'captcha' => array(
                'title' => __('Captcha', $bkpkFM->name),
                'field_group' => 'formatting',
                'is_free' => false
            )
        );
        
        if (! empty($name)) {
            return isset($fieldsList[$name]) ? $fieldsList[$name] : array();
        }
        
        return $fieldsList;
    }

    /**
     * Supported action type
     *
     * @return (array) if type=null || (bool) check for valid action type
     */
    function validActionType($type = null)
    {
        $types = array(
            'profile',
            'registration',
            'profile-registration',
            'public',
            'login'
        );
        
        if (empty($type))
            return $types;
        
        return in_array($type, $types) ? true : false;
    }

    function loginByArray()
    {
        global $bkpkFM;
        return array(
            'user_login' => __('Username', $bkpkFM->name),
            'user_email' => __('Email', $bkpkFM->name),
            'user_login_or_email' => __('Username or Email', $bkpkFM->name)
        );
    }

    function defaultSettingsArray($key = null)
    {
        $settings = array(
            
            'general' => array(),
            
            'login' => array(
                'login_by' => 'user_login',
                'login_form' => "%login_form%\n%lostpassword_form%",
                'loggedin_profile' => array(
                    'administrator' => "<p>Hello %user_login%</p>\n<p>%avatar%</p>\n<p><a href=\"%admin_url%\">Admin Section</a></p>\n<p><a href=\"%logout_url%\">Logout</a></p>",
                    'subscriber' => "<p>Hello %user_login%</p>\n<p>%avatar%</p>\n<p><a href=\"%logout_url%\">Logout</a></p>"
                )
            ),
            
            'registration' => array(
                'user_activation' => 'auto_active'
            ),
            
            'redirection' => array(
                'login' => array(
                    'administrator' => 'dashboard',
                    'subscriber' => 'default'
                ),
                'logout' => array(
                    'administrator' => 'default',
                    'subscriber' => 'default'
                ),
                'registration' => array(
                    'administrator' => 'default',
                    'subscriber' => 'default'
                )
            ),
            
            'backend_profile' => array(),
            
            'misc' => array()
        );
        
        if ($key)
            return @$settings[$key];
        return $settings;
    }

    function defaultEmailsArray($key = null)
    {
        global $bkpkFM;
        
        $emails = array(
            
            'registration' => array(
                'user_email' => array(
                    'subject' => '[%site_title%] Your account details',
                    'body' => "Username: %user_login% \r\nE-mail: %user_email% \r\n\r\nLogin Url: %login_url%"
                ),
                'admin_email' => array(
                    'subject' => '[%site_title%] New User Registration',
                    'body' => "Username: %user_login% \r\nEmail: %user_email% \r\n"
                )
            ),
            
            'activation' => array(
                'user_email' => array(
                    'subject' => '[%site_title%] User Activated',
                    'body' => "Congratulations! \r\n\r\nYour account is activated. You can login with your username and password. \r\n\r\nLogin Url: %login_url%"
                )
            ),
            
            'deactivation' => array(
                'user_email' => array(
                    'subject' => '[%site_title%] User Deactivated',
                    'body' => "Your account is deactivated by administrator. You can not login anymore to [%site_url%]."
                )
            ),
            
            'email_verification' => array(
                'user_email' => array(
                    'subject' => '[%site_title%] Email verified',
                    'body' => "Your email %user_email% is successfully verified on [%site_url%]."
                ),
                'admin_email' => array(
                    'subject' => '[%site_title%] Email verified',
                    'body' => "Email %user_email% for user %user_login% is successfully verified on [%site_url%]."
                )
            ),
            
            'admin_approval' => array(
                'user_email' => array(
                    'subject' => '[%site_title%] Account Approves',
                    'body' => "Your account has been approved on [%site_url%]."
                )
            ),
            
            'lostpassword' => array(
                'user_email' => array(
                    'subject' => "[%site_title%] Password Reset",
                    'body' => "Someone requested that the password be reset for the following account:\r\n\r\n%site_url% \r\n\r\nUsername: %user_login% \r\n\r\nIf this was a mistake, just ignore this email and nothing will happen. \r\n\r\nTo reset your password, visit the following address: \r\n\r\n%reset_password_link% \r\n"
                )
            ),
            
            'reset_password' => array(
                // 'user_email' => array(
                // 'subject' => '[%site_title%] Password Lost/Changed',
                // 'body' => "Password Lost and Changed for user: %user_login% \r\n",
                // 'bkpk_disable'=> true,
                // ),
                'admin_email' => array(
                    'subject' => '[%site_title%] Password Lost/Changed',
                    'body' => "Password Lost and Changed for user: %user_login% \r\n",
                    'bkpk_disable' => false
                )
            ),
            
            'profile_update' => array(
                'user_email' => array(
                    'subject' => '[%site_title%] Profile Updated',
                    'body' => "Hi %display_name%,\r\n\r\nYour profile have updated on site: %site_url%",
                    'bkpk_disable' => true
                ),
                'admin_email' => array(
                    'subject' => '[%site_title%] Profile Updated',
                    'body' => "Profile updated for Username: %user_login% ",
                    'bkpk_disable' => true
                )
            )
        );
        
        if ($key)
            return @$emails[$key];
        return $emails;
    }

    function runLocalization()
    {
        global $bkpkFM, $bkpkFMCache;
        
        if (empty($bkpkFMCache->localizedStrings)) {
            $bkpkFMCache->localizedStrings = array(
                'llms-bkpk' => array(
                    'get_link' => $bkpkFM->isPro ? 'Please validate your license to use this feature.' : "Get pro version from {$bkpkFM->website} to use this feature.",
                    'please_wait' => __('Please Wait...', $bkpkFM->name),
                    'saving' => __('Saving', $bkpkFM->name),
                    'saved' => __('Saved', $bkpkFM->name),
                    'not_saved' => __('Not Saved', $bkpkFM->name)
                ),
                'fileuploader' => array(
                    'upload' => __('Upload', $bkpkFM->name),
                    'drop' => __('Drop files here to upload', $bkpkFM->name),
                    'cancel' => __('Cancel', $bkpkFM->name),
                    'failed' => __('Failed', $bkpkFM->name),
                    'invalid_extension' => sprintf(__('%1$s has invalid extension. Only %2$s are allowed.', $bkpkFM->name), '{file}', '{extensions}'),
                    'too_large' => sprintf(__('%1$s is too large, maximum file size is %2$s.', $bkpkFM->name), '{file}', '{sizeLimit}'),
                    'empty_file' => sprintf(__('%s is empty, please select files again without it.', $bkpkFM->name), '{file}'),
                    'confirm_remove' => __('Confirm to remove?', $bkpkFM->name)
                ),
                'jquery.password_strength' => array(
                    'too_weak' => __('Too weak', $bkpkFM->name),
                    'weak' => __('Weak password', $bkpkFM->name),
                    'normal' => __('Normal strength', $bkpkFM->name),
                    'strong' => __('Strong password', $bkpkFM->name),
                    'very_strong' => __('Very strong password', $bkpkFM->name)
                ),
                'jquery.validationEngine-en' => array(
                    'required_field' => __('* This field is required', $bkpkFM->name),
                    'required_option' => __('* Please select an option', $bkpkFM->name),
                    'required_checkbox' => __('* This checkbox is required', $bkpkFM->name),
                    'min' => __('* Minimum ', $bkpkFM->name),
                    'max' => __('* Maximum ', $bkpkFM->name),
                    'char_allowed' => __(' characters allowed', $bkpkFM->name),
                    'min_val' => __('* Minimum value is ', $bkpkFM->name),
                    'max_val' => __('* Maximum value is ', $bkpkFM->name),
                    'past' => __('* Date prior to ', $bkpkFM->name),
                    'future' => __('* Date past ', $bkpkFM->name),
                    'options_allowed' => __(' options allowed', $bkpkFM->name),
                    'please_select' => __('* Please select ', $bkpkFM->name),
                    'options' => __(' options', $bkpkFM->name),
                    'not_equals' => __('* Fields do not match', $bkpkFM->name),
                    'invalid_phone' => __('* Invalid phone number', $bkpkFM->name),
                    'invalid_email' => __('* Invalid email address', $bkpkFM->name),
                    'invalid_integer' => __('* Not a valid integer', $bkpkFM->name),
                    'invalid_number' => __('* Not a valid number', $bkpkFM->name),
                    'invalid_date' => __('* Invalid date, must be in YYYY-MM-DD format', $bkpkFM->name),
                    'invalid_time' => __('* Invalid time, must be in hh:mm:ss format', $bkpkFM->name),
                    'invalid_datetime' => __('* Invalid datetime, must be in YYYY-MM-DD hh:mm:ss format', $bkpkFM->name),
                    'invalid_ip' => __('* Invalid IP address', $bkpkFM->name),
                    'invalid_url' => __('* Invalid URL', $bkpkFM->name),
                    'invalid_field' => __('* Invalid field', $bkpkFM->name),
                    'numbers_only' => __('* Numbers only', $bkpkFM->name),
                    'letters_only' => __('* Letters only', $bkpkFM->name),
                    'no_special_char' => __('* No special characters allowed', $bkpkFM->name),
                    'user_exists' => __('* This user is already taken', $bkpkFM->name),
                    
                    'customRules' => $bkpkFM->getCustomFieldRegex()
                )
            );
        }
        
        foreach ($bkpkFMCache->localizedStrings as $scriptName => $data) {
            $objectName = str_replace(array(
                '.',
                '-'
            ), '_', $scriptName);
            wp_localize_script($scriptName, $objectName, $data);
        }
    }

    function getMsg($key, $arg1 = null, $arg2 = null)
    {
        global $bkpkFM;
        
        $msgs = self::msgs();
        
        if (isset($msgs[$key])) {
            $msg = __($msgs[$key], $bkpkFM->name);
            
            if (! (strpos($msg, '%s') === false))
                $msg = sprintf($msg, $arg1);
                // elseif( ! (strpos($msg, '%2$s') === false ) )
                // $msg = sprintf( $msg, $arg1, $arg2 );
            
            return apply_filters('llms_bkpk_msg', $msg, $key, $arg1, $arg2);
        }
        
        return false;
    }

    function msgs()
    {
        global $bkpkFM;
        
        $msgs = array(
            
            'group_1' => __('Login', $bkpkFM->name),
            'login_pass_label' => __('Password', $bkpkFM->name),
            'login_remember_label' => __('Remember me', $bkpkFM->name),
            'login_button' => __('Login', $bkpkFM->name),
            'login_email_required' => __('Both username and email are required', $bkpkFM->name),
            'invalid_login' => __('<strong>ERROR</strong>: Invalid %s.', $bkpkFM->name),
            'login_success' => __('Login successfuly', $bkpkFM->name),
            'registration_link' => __("Don't have an account? <a href=\"%s\">Sign up</a>", $bkpkFM->name),
            
            'group_2' => __('Lost Password', $bkpkFM->name),
            'lostpassword_link' => __('Lost your password?', $bkpkFM->name),
            'lostpassword_intro' => __('Please enter your username or email address. You will receive a link to reset your password via email.', $bkpkFM->name),
            'lostpassword_label' => __('Username or E-mail', $bkpkFM->name),
            'lostpassword_button' => __('Get New Password', $bkpkFM->name),
            
            'group_3' => __('Reset Password', $bkpkFM->name),
            'resetpassword_heading' => __('Reset Password', $bkpkFM->name),
            'resetpassword_intro' => __('Enter your new password below.', $bkpkFM->name),
            'resetpassword_pass1_label' => __('New password', $bkpkFM->name),
            'resetpassword_pass2_label' => __('Confirm new password', $bkpkFM->name),
            'resetpassword_button' => __('Reset Password', $bkpkFM->name),
            'password_reset_mismatch' => __('The passwords do not match.', $bkpkFM->name),
            'password_reseted' => __('Your password has been reset.', $bkpkFM->name),
            
            'group_4' => __('Profile', $bkpkFM->name),
            'profile_required_loggedin' => __('Please login to access your profile.', $bkpkFM->name),
            'public_non_lggedin_msg' => __('Please login to access your profile.', $bkpkFM->name),
            'profile_updated' => __('Profile successfully updated.', $bkpkFM->name),
            
            'group_5' => __('Registration', $bkpkFM->name),
            'sent_verification_link' => __('We have sent you a verification link to your email. Please complete your registration by clicking the link.', $bkpkFM->name),
            'sent_link_wait_for_admin' => __('We have sent you a verification link to your email. Please verify your email by clicking the link and wait for admin approval.', $bkpkFM->name),
            'email_verified_pending_admin' => __('Your email is successfully verified. Please wait for admin approval.', $bkpkFM->name),
            'wait_for_admin_approval' => __('Please wait until an admin approves your account.', $bkpkFM->name),
            'email_verified' => __('Your email is successfully verified and the account has been activated. <a href="%s">Login now</a>', $bkpkFM->name),
            'registration_completed' => __('Registration successfully completed.', $bkpkFM->name),
            
            'group_6' => __('Validation', $bkpkFM->name),
            'validate_default' => __('Invalid %s', $bkpkFM->name),
            'validate_required' => __('%s is required', $bkpkFM->name),
            'validate_email' => __('Invalid email address', $bkpkFM->name),
            'validate_equals' => __('%s does not match', $bkpkFM->name),
            'validate_current_password' => __('Please provide valid current password', $bkpkFM->name),
            'validate_current_required' => __('Current %s is required', $bkpkFM->name),
            // 'validate_unique' => __( '%1$s: "%2$s" already takenM', $bkpkFM->name ),
            
            'group_7' => __('Misc', $bkpkFM->name),
            'not_member_of_blog' => __('User is not member of this site.', $bkpkFM->name),
            'user_already_activated' => __('User already activated', $bkpkFM->name),
            'account_inactive' => __('<strong>ERROR:</strong> your account is inactive', $bkpkFM->name),
            'account_pending' => __('<strong>ERROR:</strong> your account is not yet activated.', $bkpkFM->name),
            'verify_email' => __('Please verify your email address.', $bkpkFM->name),
            'check_email_for_link' => __('Check your e-mail for the confirmation link.', $bkpkFM->name),
            'email_not_found' => __('Email not found', $bkpkFM->name),
            'incorrect_captcha' => __('Incorrect captcha code', $bkpkFM->name),
            'invalid_key' => __('Sorry, that key does not appear to be valid.', $bkpkFM->name),
            'expired_key' => __('Sorry, that key has expired. Please try again.', $bkpkFM->name),
            'invalid_parameter' => __('Invalid parameter', $bkpkFM->name)
        );
        
        $text = $bkpkFM->getSettings('text');
        if (is_array($text)) {
            foreach ($msgs as $key => $msg) {
                if (! empty($text[$key]))
                    $msgs[$key] = $text[$key];
            }
        }
        
        return apply_filters('llms_bkpk_messages', $msgs);
    }

    function getExecutionPageConfig($key)
    {
        global $bkpkFM;
        
        $settings = $bkpkFM->getData('settings');
        
        $lostPassTitle = 'Lost password';
        if (! empty($settings['login']['resetpass_page']))
            $lostPassTitle = get_the_title((int) $settings['login']['resetpass_page']);
        
        $emailVerifyTitle = 'Email verification';
        if (! empty($settings['registration']['email_verification_page'])) {
            $emailVerifyTitle = get_the_title((int) $settings['registration']['email_verification_page']);
            if ($emailVerifyTitle == 'Lost password')
                $emailVerifyTitle = 'Email verification';
        }
        
        $configs = array(
            'lostpassword' => array(
                'title' => __($lostPassTitle, $bkpkFM->name)
            ),
            'resetpass' => array(
                'title' => __('Reset password', $bkpkFM->name)
            ),
            'email_verification' => array(
                'title' => __($emailVerifyTitle, $bkpkFM->name)
            )
        );
        
        if (! empty($configs[$key]))
            $config = apply_filters('llms_bkpk_execution_page_config', $configs[$key], $key);
        
        switch ($key) {
            case 'lostpassword':
                return apply_filters('llms_bkpk_lostpassword_form', $config);
            case 'resetpass':
                return apply_filters('llms_bkpk_resetpass_form', $config);
            case 'email_verification':
                return apply_filters('llms_bkpk_email_verification_form', $config);
        }
        
        return false;
    }
}