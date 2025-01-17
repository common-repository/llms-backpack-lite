<?php
namespace BPKPFieldManager;

/**
 * Handle everyting for user profile update and user registration.
 *
 * This class has been also usd by backend profile and users import features.
 *
 * @since 1.1.7
 *       
 * @author Dennis Hall
 */
class UserInsert
{

    /**
     *
     * @var WP_Error object
     */
    private $errors;

    private $actionType;

    private $formName;

    /**
     *
     * @var array key: FieldName, val: config array
     */
    private $fields;

    /**
     *
     * @var WP_User object
     */
    private $user;

    private $userID;

    /**
     *
     * @var FormGenerate object
     */
    private $form;

    /**
     * All user's data including meta data.
     *
     * @var array key: fieldName, val: FieldValue
     */
    private $userData;

    /**
     * Only meta data.
     *
     * @var array key: fieldName, val: FieldValue
     */
    private $metaData;

    public function __construct()
    {
        $this->errors = new \WP_Error();
    }

    /**
     * Set $this->actionType.
     */
    private function setActionType()
    {
        $this->actionType = ! empty($_REQUEST['action_type']) ? strtolower(esc_attr($_REQUEST['action_type'])) : '';
        
        if ($this->actionType == 'profile-registration') {
            if (is_user_logged_in()) {
                $this->actionType = 'profile';
            } else {
                $this->actionType = 'registration';
            }
        }
        
        if (empty($this->actionType)) {
            $this->errors->add('empty_action_type', __('Action type is empty', $bkpkFM->name));
        }
    }

    /**
     * Set $this->user and $this->userID.
     */
    private function setUser()
    {
        global $bkpkFM;
        
        $this->user = wp_get_current_user();
        
        switch ($this->actionType) {
            case 'profile':
                if (! empty($_REQUEST['user_id'])) {
                    if (current_user_can('edit_users')) {
                        $userID = (int) esc_attr($_REQUEST['user_id']);
                        $this->user = new \WP_User($userID);
                    }
                }
                
                if (! $this->user->exists()) {
                    $this->errors->add('invalid_user', __('Invalid user!', $bkpkFM->name));
                } else {
                    $this->userID = $this->user->ID;
                }
                
                break;
            
            case 'registration':
                // $cap = 'add_users' 'create_users';
                break;
        }
    }

    /**
     * Set $this->formName.
     */
    private function setFormName()
    {
        global $bkpkFM;
        
        if (! isset($_REQUEST['form_key'])) {
            $this->errors->add('empty_form_name', __('Form name is empty', $bkpkFM->name));
        }
        
        $this->formName = ! empty($_REQUEST['form_key']) ? esc_attr($_REQUEST['form_key']) : '';
    }

    /**
     * Set $this->form and $this->fields.
     */
    private function setForm()
    {
        $this->form = new FormGenerate($this->formName, $this->actionType, $this->userID);
        $this->fields = $this->form->validInputFields();
    }

    /**
     * Sanitize and validate user input.
     *
     * Assume $this->actionType, $this->fields, $this->formName and $this->user already set.
     * Call this function only after calling $this->setForm()
     */
    private function sanitizeFields()
    {
        global $bkpkFM;
        
        $userData = array();
        
        /**
         * Assign $fieldName, $field to $userData.
         * Also validating required and unique.
         */
        foreach ($this->fields as $fieldName => $field) {
            $field = apply_filters('llms_bkpk_field_config', $field, $field['id'], $this->formName, $this->userID);
            $field = apply_filters('llms_bkpk_field_config_before_update', $field, $field['id'], $this->formName, $this->userID);
            
            if ($this->actionType == 'profile') {
                if ($fieldName == 'user_login' || ($fieldName == 'user_pass' && empty($_REQUEST['user_pass']))) {
                    continue;
                }
            }
            
            if ($field['field_type'] == 'custom' && isset($field['input_type']) && $field['input_type'] == 'password') {
                if (empty($_REQUEST[$fieldName])) {
                    continue;
                }
            }
            
            // / Assigning data to $userData
            $userData[$fieldName] = ! empty($_POST[$fieldName]) ? $_POST[$fieldName] : '';
            
            if (is_array($userData[$fieldName]) && count($userData[$fieldName]) == 1 && ! empty($userData[$fieldName])) {
                $userData[$fieldName] = $userData[$fieldName][0];
            }
            
            // Comment out esc_attr which has been added since 1.1.7rc2. esc_attr causes encodin '<' '>' sign for links on rich text
            // if ( $userData[ $fieldName ] && ! is_array( $userData[ $fieldName ] ) )
            // $userData[ $fieldName ] = esc_attr( $userData[ $fieldName ] );
            
            // / Handle non-ajax file upload
            if (in_array($field['field_type'], array(
                'user_avatar',
                'file'
            ))) {
                if (isset($_FILES[$fieldName])) {
                    $extensions = ! empty($field['allowed_extension']) ? $field['allowed_extension'] : 'jpg,png,gif';
                    $maxSize = ! empty($field['max_file_size']) ? $field['max_file_size'] * 1024 : 1024 * 1024;
                    $file = $bkpkFM->fileUpload($fieldName, $extensions, $maxSize);
                    if (is_wp_error($file)) {
                        if ($file->get_error_code() != 'no_file') {
                            $errors->add($file->get_error_code(), $file->get_error_message());
                        }
                    } else {
                        if (is_string($file)) {
                            $umFile = new File($field);
                            $userData[$fieldName] = $file;
                        }
                    }
                }
                
                $bkpkFM->removeFromFileCache($userData[$fieldName]);
            }
            
            /*
             * Using Field Class
             */
            if (! isset($field['field_value'])) {
                $field['field_value'] = $userData[$fieldName];
            }
            
            $umField = new Field($field['id'], $field, array(
                'user_id' => $this->userID,
                'insert_type' => $this->actionType
            ));
            
            if ($fieldName == 'user_pass' && $this->actionType == 'registration') {
                $umField->addRule('required');
            }
            
            if ($fieldName == 'user_pass' && $this->actionType == 'profile') {
                if (! empty($field['required_current_password'])) {
                    $umField->addRule('current_password');
                }
            }
            
            if (isset($_REQUEST[$fieldName . '_retype'])) {
                $umField->addRule('equals');
            }
            
            if (! $umField->validate()) {
                foreach ($umField->getErrors() as $errKey => $errVal) {
                    $this->errors->add($errKey, $errVal);
                }
            }
        }
        
        $this->userData = $userData;
        
        $this->setMetaData();
    }

    /**
     * setMetaData.
     */
    private function setMetaData()
    {
        global $bkpkFM;
        
        $userdata = array();
        $metadata = array();
        $wpField = $bkpkFM->defaultUserFieldsArray();
        
        if (is_array($this->userData)) {
            foreach ($this->userData as $key => $val) {
                $key = is_string($key) ? trim($key) : $key;
                $val = is_string($val) ? trim($val) : $val;
                
                if (! $key) {
                    continue;
                }
                
                if (isset($wpField[$key])) {
                    $userdata[$key] = $val;
                } else {
                    $metadata[$key] = $val;
                }
            }
        }
        
        $this->metaData = $metadata;
    }

    /**
     * Validate captcha.
     */
    private function validateCaptcha()
    {
        global $bkpkFM;
        
        // / Run Captcha validation after completed all other validation
        if ($this->form->hasCaptcha() && ! $bkpkFM->isValidCaptcha()) {
            $this->errors->add('invalid_captcha', $bkpkFM->getMsg('incorrect_captcha'));
        }
    }

    /**
     * Check allowed role for security purpose.
     */
    private function validateRole()
    {
        if (isset($this->userData['role'])) {
            $ignoreRole = true;
            
            // $fieldData = $bkpkFM->getFieldData( @$_REQUEST['role_field_id'] );
            $field = $this->form->getField(@$_REQUEST['role_field_id']);
            if (is_array(@$field['allowed_roles'])) {
                if (in_array($this->userData['role'], $field['allowed_roles'])) {
                    $ignoreRole = false;
                }
            }
            
            if ($ignoreRole) {
                unset($this->userData['role']);
            }
        }
    }

    /**
     * TODO: Need to implement and convert it into object ***.
     *
     *
     * Add or update user
     *
     * @param array $data:
     *            data need to update, both userdata and metadata
     * @param int $userID:
     *            if not set, user will registered else user update
     */
    public function insertUser($data, $userID = null)
    {
        global $bkpkFM;
        $errors = new \WP_Error();
        
        $this->runHooks();
        
        // Determine Fields
        $userdata = array();
        $metadata = array();
        $wpField = $bkpkFM->defaultUserFieldsArray();
        
        if (is_array($data)) {
            foreach ($data as $key => $val) {
                $key = is_string($key) ? trim($key) : $key;
                $val = is_string($val) ? trim($val) : $val;
                
                if (! $key) {
                    continue;
                }
                
                if (isset($wpField[$key])) {
                    $userdata[$key] = $val;
                } else {
                    $metadata[$key] = $val;
                }
            }
        }
        
        // sanitize email and user
        if (! empty($userdata['user_email'])) {
            $userdata['user_email'] = sanitize_email($userdata['user_email']);
        }
        
        if (! empty($userdata['user_login'])) {
            $userdata['user_login'] = sanitize_user($userdata['user_login'], true);
        }
        
        // Case of registration
        if (! $userID) {
            if (! empty($userdata['user_email']) && empty($userdata['user_login'])) {
                $user_login = $userdata['user_email'];
                if (apply_filters('llms_bkpk_username_without_domain', false)) {
                    $user_login = explode('@', $userdata['user_email']);
                    $user_login = $user_login[0];
                    if (username_exists($user_login)) {
                        $user_login = $user_login . rand(1, 999);
                    }
                }
                $userdata['user_login'] = sanitize_user($user_login, true);
            } elseif (! empty($userdata['user_login']) && empty($userdata['user_email'])) {
                $userdata['user_email'] = is_email($userdata['user_login']) ? $userdata['user_login'] : '';
            } elseif (empty($userdata['user_login']) && empty($userdata['user_email'])) {
                $errors->add('empty_login_email', __('Cannot create a user with an empty login name and empty email', $bkpkFM->name));
            }
            
            if (empty($userdata['user_pass'])) {
                $userdata['user_pass'] = wp_generate_password(12, false);
                $passwordNag = true;
            }
            
            if ($bkpkFM->isHookEnable('user_registration_email')) {
                $userdata['user_email'] = apply_filters('user_registration_email', $userdata['user_email']);
            }
            
            if ($bkpkFM->isHookEnable('register_post')) {
                do_action('register_post', $userdata['user_login'], $userdata['user_email'], $errors);
            }
            
            if ($bkpkFM->isHookEnable('registration_errors')) {
                $errors = apply_filters('registration_errors', $errors, $userdata['user_login'], $userdata['user_email']);
            }
            
            if (is_wp_error($errors)) {
                if ($errors->get_error_code()) {
                    return $errors;
                }
            }
            
            $user_id = wp_insert_user($userdata);
            if (is_wp_error($user_id)) {
                return $user_id;
            }
            
            if (! empty($passwordNag)) {
                update_user_option($user_id, 'default_password_nag', true, true);
            } // Set up the Password change nag.
                  
            // Profile Update
        } else {
            $userdata['ID'] = $userID;
            $user_id = wp_update_user($userdata);
            if (is_wp_error($user_id)) {
                return $user_id;
            }
        }
        
        $userdata['ID'] = $user_id;
        
        return array_merge($userdata, $metadata);
    }

    /**
     * Run action hooks.
     */
    private function runHooks()
    {
        add_action('profile_update', array(
            $this,
            '_updateMetaData'
        ));
        add_action('user_register', array(
            $this,
            '_addMetaData'
        ));
    }

    /**
     * Update user meta data by using action hooks.
     *
     * This method is only called inside this class.
     * Using public visibility because of calling by action hook.
     */
    public function _updateMetaData($user_id)
    {
        if (! empty($this->metaData) && is_array($this->metaData)) {
            foreach ($this->metaData as $key => $val) {
                update_user_meta($user_id, $key, $val);
            }
        }
    }

    /**
     * Add user meta data by using action hooks.
     *
     * This method is only called inside this class.
     * Using public visibility because of calling by action hook.
     */
    public function _addMetaData($user_id)
    {
        if (! empty($this->metaData) && is_array($this->metaData)) {
            foreach ($this->metaData as $key => $val) {
                add_llms_bkpk($user_id, $key, $val);
            }
        }
    }

    /**
     * Update user's data.
     */
    private function userUpdate()
    {
        global $bkpkFM;
        
        $html = null;
        
        if (! is_user_logged_in()) {
            $this->errors->add('user_not_loggedin', __('User must be logged in to update profile', $bkpkFM->name));
        }
        
        $this->userData = apply_filters('llms_bkpk_pre_user_update', $this->userData, $this->userID, $this->formName);
        if (is_wp_error($this->userData)) {
            return $bkpkFM->showError($this->userData);
        }
        
        $response = $this->insertUser($this->userData, $this->userID);
        if (is_wp_error($response)) {
            return $bkpkFM->showError($response);
        }
        
        $bkpkFM->showDataFromDB = true;
        
        do_action('llms_bkpk_after_user_update', (object) $response, $this->formName);
        
        $message = $bkpkFM->getMsg('profile_updated');
        $html = "<div action_type='$this->actionType'>" . $bkpkFM->showMessage($message) . '</div>';
        
        return $bkpkFM->printAjaxOutput($html);
    }

    /**
     * Register a new user.
     */
    private function registerUser()
    {
        global $bkpkFM;
        
        // / $userData: array.
        $userData = apply_filters('llms_bkpk_pre_user_register', $this->userData);
        if (is_wp_error($userData)) {
            return $bkpkFM->showError($userData);
        }
        
        $blogData = $bkpkFM->validateBlogSignup();
        if (is_wp_error($blogData)) {
            return $bkpkFM->showError($blogData);
        }
        
        /*
         * // Commented since 1.1.8rc1, replace with bellow
         * if (is_multisite() && wp_verify_nonce(@$_POST ['bkpk_newblog'], 'blogname') && !empty($_POST ['blogname'])) {
         * $blogData = wpmu_validate_blog_signup($_POST ['blogname'], $_POST ['blog_title']);
         * if ($blogData ['errors']->get_error_code()) {
         * return $bkpkFM->showError($blogData ['errors']);
         * }
         * }
         */
        
        //
        // Replaced with addUserToBlog() since 1.1.8rc1
        /*
         * $userID = null;
         * if (is_multisite()) {
         * $registrationSettings = $bkpkFM->getSettings('registration');
         * if (!empty($registrationSettings ['add_user_to_blog'])) {
         * $user_login = sanitize_user($userData ['user_login'], true);
         * $userID = username_exists($user_login);
         * if ($userID) {
         * $blog_id = get_current_blog_id();
         * if (!is_user_member_of_blog($userID, $blog_id)) {
         * add_user_to_blog($blog_id, $userID, get_option('default_role'));
         * } else {
         * $userID = null;
         * }
         * }
         * }
         * }
         */
        
        $userID = $bkpkFM->addUserToBlog($userData);
        
        $response = $this->insertUser($userData, $userID);
        if (is_wp_error($response)) {
            return $bkpkFM->showError($response);
        }
        
        if (! empty($blogData)) {
            $responseBlog = $bkpkFM->registerBlog($blogData, $userData);
            if (is_wp_error($responseBlog)) {
                return $bkpkFM->showError($responseBlog);
            }
        }
        
        // / Allow to populate form data based on DB instead of $_REQUEST
        $bkpkFM->showDataFromDB = true;
        
        $registrationSettings = $bkpkFM->getSettings('registration');
        $activation = $registrationSettings['user_activation'];
        if ($activation == 'auto_active') {
            $msg = $bkpkFM->getMsg('registration_completed');
        } elseif ($activation == 'email_verification') {
            $msg = $bkpkFM->getMsg('sent_verification_link');
        } elseif ($activation == 'admin_approval') {
            $msg = $bkpkFM->getMsg('wait_for_admin_approval');
        } elseif ($activation == 'both_email_admin') {
            $msg = $bkpkFM->getMsg('sent_link_wait_for_admin');
        }
        
        if (! $bkpkFM->isPro()) {
            wp_new_user_notification($response['ID'], $response['user_pass']);
        }
        
        do_action('llms_bkpk_after_user_register', (object) $response);
        
        $html = $bkpkFM->showMessage($msg);
        
        if (isset($responseBlog)) {
            $html .= $bkpkFM->showMessage($responseBlog);
        }
        
        $role = $bkpkFM->getUserRole($response['ID']);
        $redirect_to = null;
        if ($bkpkFM->isPro())
            $redirect_to = $bkpkFM->getRedirectionUrl($redirect_to, 'registration', $role);
        
        if ($activation == 'auto_active') {
            if (! empty($registrationSettings['auto_login'])) {
                $customUser = (new Login())->login($response);
                // Commented since 1.2.1
                // $customUser = $bkpkFM->doLogin($response);
                if (empty($redirect_to) && ! empty($customUser->redirect_to))
                    $redirect_to = $customUser->redirect_to;
            }
        }
        
        if ($bkpkFM->isHookEnable('registration_redirect')) {
            $redirect_to = apply_filters('registration_redirect', $redirect_to, $response['ID']);
        }
        
        if ($redirect_to) {
            if (empty($_REQUEST['is_ajax'])) {
                wp_redirect($redirect_to);
                exit();
            }
            
            $timeout = $activation == 'auto_active' ? 3 : 5;
            $html .= $bkpkFM->jsRedirect($redirect_to, $timeout);
        }
        
        $html = '<div action_type="registration">' . $html . '</div>';
        
        return $bkpkFM->printAjaxOutput($html);
    }

    /**
     * Public method for http post processing, handle both registration and profile update.
     */
    public function postInsertUserProcess()
    {
        global $bkpkFM; // $bkpkFM->dump($_REQUEST);
        
        $this->setActionType();
        $this->setUser();
        $this->setFormName();
        $this->setForm();
        
        if ($this->formName && $this->form && ! $this->form->isFound()) {
            $this->errors->add('not_found', sprintf(__('Form "%s" is not found.', $bkpkFM->name), $this->formName));
        }
        
        if (! $this->fields) {
            $this->errors->add('empty_field', __('No field to update', $bkpkFM->name));
        }
        
        /*
         * Showing errors
         */
        if ($this->errors->get_error_code()) {
            return $bkpkFM->ShowError($this->errors);
        }
        
        $this->sanitizeFields();
        
        if ($this->actionType == 'registration')
            $bkpkFM->validateMultisiteRegistration($this->errors);
        
        if (empty($this->userData)) {
            $this->errors->add('empty_field_value', __('No data to update', $bkpkFM->name));
        }
        
        if ($this->errors->get_error_code()) {
            return $bkpkFM->ShowError($this->errors);
        }
        
        $this->validateCaptcha();
        $this->validateRole();
        
        if ($this->errors->get_error_code()) {
            return $bkpkFM->ShowError($this->errors);
        }
        
        if ($this->actionType == 'registration') {
            return $this->registerUser();
        } elseif ($this->actionType == 'profile') {
            return $this->userUpdate();
        }
    }

    /**
     * Handle users import by UserImportController
     *
     * @param unknown $userdata            
     * @param unknown $user_id            
     */
    public function importUsersProcess($userdata, $user_id = null)
    {
        $this->userData = $userdata;
        $this->setMetaData();
        
        return $this->insertUser($userdata, $user_id);
    }

    /**
     * Validate user's input.
     * Add error to $errors object.
     * Assign sanitized array to $bkpkFMCache->backend_profile_fields.
     */
    public function validateBackendFieldsProcess($user, &$errors)
    {
        $this->formName = 'wp_backend_profile';
        $this->actionType = 'profile';
        $this->user = $user;
        $this->userID = $user->ID;
        $this->errors = $errors;
        
        $this->setForm();
        $this->sanitizeFields();
        
        if (! $this->errors->get_error_codes()) {
            $this->runHooks();
        }
    }
}
