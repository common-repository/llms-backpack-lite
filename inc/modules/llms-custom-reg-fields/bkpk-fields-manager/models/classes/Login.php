<?php
namespace BPKPFieldManager;

/**
 * Handle all login processes
 *
 * @since 1.2.1
 *       
 * @author Dennis Hall
 */
class Login
{

    private $methodName = 'Login';

    /**
     * Login settings
     *
     * @var array
     */
    private $settings = [];

    private $config = [];

    function __construct()
    {
        global $bkpkFM;
        $this->settings = $bkpkFM->getSettings('login');
    }

    /**
     * Generate html login form
     *
     * @param string $formName            
     * @return string html
     */
    public function loginForm($formName)
    {
        global $bkpkFM;
        if (is_user_logged_in())
            return $this->loginResponse();
        
        $html = null;
        $html .= getHookHtml('login_form_login');
        $formHtml = ! empty($formName) ? $this->customLoginForm($formName) : $this->defaultLoginForm();
        if (is_wp_error($formHtml))
            return $bkpkFM->ShowError($formHtml);
        
        $html .= $formHtml;
        
        if (empty($this->settings['disable_lostpassword'])) {
            $html .= (new ResetPassword())->lostPasswordForm();
        }
        if (empty($this->settings['disable_registration_link'])) {
            $html .= $bkpkFM->renderPro('registrationLink', array(
                'config' => $this->config
            ), 'login');
        }
        
        return $html;
    }

    /**
     * Genertae login form based on from_name
     *
     * @param string $formName            
     * @return string|WP_Error
     */
    private function customLoginForm($formName)
    {
        global $bkpkFM;
        $formBuilder = new FormGenerate($formName, 'login');
        if (! $formBuilder->isFound())
            return new \WP_Error('form_not_found', sprintf(__('Form "%s" is not found.', $bkpkFM->name), $formName));
        
        $form = $formBuilder->getForm();
        
        $form['form_class'] = 'bkpk_login_form ' . ! empty($form['form_class']) ? $form['form_class'] : null;
        if (empty($form['disable_ajax']))
            $form['onsubmit'] = "umLogin(this);";
        
        return $bkpkFM->renderPro('generateForm', [
            'form' => $form,
            'actionType' => 'login',
            'methodName' => $this->methodName
        ]);
    }

    /**
     * Generate default login form
     */
    private function defaultLoginForm()
    {
        global $bkpkFM;
        $html = null;
        $title = $bkpkFM->loginByArray();
        $methodName = $this->methodName;
        if (isset($bkpkFM->bkpk_post_method_status->$methodName))
            $html .= $bkpkFM->bkpk_post_method_status->$methodName;
        
        $this->config = apply_filters('llms_bkpk_default_login_form', $this->config);
        $html .= $bkpkFM->renderPro('loginForm', array(
            'config' => $this->config,
            'loginTitle' => @$title[$this->settings['login_by']],
            'disableAjax' => ! empty($this->loginSettings['disable_ajax']) ? true : false,
            'methodName' => $methodName
        ), 'login');
        
        return $html;
    }

    /**
     * Show logged-in user profile.
     */
    private function loginResponse($user = null)
    {
        global $bkpkFM;
        if (empty($user))
            $user = wp_get_current_user();
        
        $role = $bkpkFM->getUserRole($user->ID);
        
        return $bkpkFM->convertUserContent($user, @$this->settings['loggedin_profile'][$role]);
    }

    /**
     * Get login url
     * This method has been called by login_url filter
     *
     * @param string $login_url            
     * @param string $redirect            
     * @return string|false
     */
    public function loginUrl($login_url, $redirect)
    {
        $loginPage = ! empty($this->settings['login_page']) ? $this->settings['login_page'] : '';
        if ($loginPage) {
            $login_url = get_permalink($loginPage);
            if (! empty($redirect))
                $login_url = add_query_arg('redirect_to', urlencode($redirect), $login_url);
            // if ( $force_reauth )
            // $login_url = add_query_arg('reauth', '1', $login_url);
        }
        
        return $login_url;
    }

    /**
     * Disable wp-login.php and redirect to custom login page
     * This method has been called by login_init action hook
     */
    public function disableDefaultLoginPage()
    {
        if (isset($_REQUEST['log']) && isset($_REQUEST['pwd'])) {
            $user = wp_authenticate($_REQUEST['log'], $_REQUEST['pwd']);
            if (! is_wp_error($user))
                return;
        }
        if (! empty($_REQUEST['action']) && in_array($_REQUEST['action'], [
            'logout',
            'postpass'
        ]))
            return;
        
        if (! empty($this->settings['login_page']) && ! empty($this->settings['disable_wp_login_php'])) {
            wp_redirect(get_permalink($this->settings['login_page']));
            exit();
        }
    }

    /**
     * This method has been called by authenticate filter.
     *
     * @param WP_User $user            
     * @param string $username            
     * @param string $password            
     * @return WP_User|WP_Error
     */
    public function changeLoginErrorMessage($user, $username, $password)
    {
        global $bkpkFM;
        if (! is_wp_error($user))
            return $user;
        
        if (! in_array($user->get_error_code(), array(
            'invalid_username',
            'incorrect_password'
        ))) {
            return $user;
        }
              
        if (in_array(@$this->settings['login_by'], array(
            'user_email',
            'user_login_or_email'
        ))) {
            $title = $bkpkFM->loginByArray();
            
            // can be commented since 1.1.3rc2
            if ($user->get_error_code() == 'invalid_username')
                return new \WP_Error('invalid_username', sprintf(__('<strong>ERROR</strong>: Invalid %s.', $bkpkFM->name), @$title[$login['login_by']]));
            
            if ($user->get_error_code() == 'incorrect_password')
                return new \WP_Error('incorrect_password', sprintf(__('<strong>ERROR</strong>: Incorrect Password. <a href="%s" title="Password Lost and Found">Lost your password</a>?', $bkpkFM->name), wp_lostpassword_url()));
        }
        
        return $user;
    }

    /**
     * This method will call by POST method in bkpk_login action
     * Called by umAjaxProModel::postLogin()
     */
    public function postLogin()
    {
        global $bkpkFM;
        $bkpkFM->verifyNonce();
        
        $output = null;
        if (! empty($_REQUEST['form_key'])) {
            $form = new FormGenerate($_REQUEST['form_key'], 'login');
            if ($form->hasCaptcha() && ! $bkpkFM->isValidCaptcha()) {
                $error = new \WP_Error('invalid_captcha', $bkpkFM->getMsg('incorrect_captcha'));
                $output = $bkpkFM->showError($error, false);
            }
        }
        if (! empty($output))
            return $bkpkFM->printAjaxOutput($output);
        
        $user = $this->login();
        if ($user) {
            if (! is_wp_error($user)) {
                if (empty($_REQUEST['is_ajax'])) {
                    wp_redirect($user->redirect_to);
                    exit();
                }
                $redirect = "redirect_to=\"$user->redirect_to\"";
                $output = "<div status=\"success\" $redirect ></div>";
            } else {
                if ($bkpkFM->isHookEnable('wp_login_errors'))
                    $user = apply_filters('wp_login_errors', $user, ''); // $errors = $user, $redirect_to = ''
                $output = $bkpkFM->showError($user->get_error_message() . $bkpkFM->reloadCaptcha(), false);
            }
        }
        
        return $bkpkFM->printAjaxOutput($output);
    }

    /**
     * Do login if user not logged on.
     *
     * @return onSuccess : redirect_url | onFailed : WP_Error or false
     */
    public function login($creds = [])
    {
        global $bkpkFM;
        if (is_user_logged_in())
            return false;
        
        if (empty($creds['user_login'])) {
            $user = $this->findUserForLogin();
            if (is_wp_error($user))
                return $user;
            $userName = $user->user_login;
        } else
            $userName = $creds['user_login'];
        
        if (empty($creds['user_pass'])) {
            if (isset($_REQUEST['pwd']))
                $userPass = $_REQUEST['pwd'];
            elseif (isset($_REQUEST['user_pass']))
                $userPass = $_REQUEST['user_pass'];
        } else
            $userPass = $creds['user_pass'];
        
        $remember = ! empty($creds['remember']) ? $creds['remember'] : @$_REQUEST['rememberme'];
        
        $user = wp_authenticate($userName, $userPass);
        if (is_wp_error($user))
            return $user;
        
        $prevent = $this->preventNonMemberLogin($userName);
        if (is_wp_error($prevent))
            return $prevent;
        
        $user = wp_signon(array(
            'user_login' => $userName,
            'user_password' => $userPass,
            'remember' => $remember ? true : false
        ), $this->secureCookie($user, $userName));
        
        if (is_wp_error($user))
            return $user;
        
        $this->addRedirectTo($user);
        
        return $user;
    }

    /**
     * if Prevent user login for non-member of blog is set.
     */
    private function preventNonMemberLogin($userName)
    {
        if (is_multisite()) {
            global $blog_id;
            if (! empty($this->settings['blog_member_only'])) {
                $userID = username_exists(sanitize_user($userName, true));
                if ($userID) {
                    if (! is_user_member_of_blog($userID))
                        return new \WP_Error('not_member_of_blog', $bkpkFM->getMsg('not_member_of_blog'));
                }
            }
        }
    }

    /**
     * Get secure-cookie
     *
     * @param WP_User $user            
     * @param string $userName            
     */
    private function secureCookie($user, $userName)
    {
        $secure_cookie = '';
        if (force_ssl_admin())
            $secure_cookie = true;
            
            // If the user wants ssl but the session is not ssl, force a secure cookie.
        if (! force_ssl_admin()) {
            if ($user = get_user_by('login', sanitize_user($userName))) {
                if (get_user_option('use_ssl', $user->ID)) {
                    $secure_cookie = true;
                    force_ssl_admin(true);
                }
            }
        }
        
        // if ( !$secure_cookie && is_ssl() && force_ssl_login() && !force_ssl_admin() && ( 0 !== strpos($redirect_to, 'https') ) && ( 0 === strpos($redirect_to, 'http') ) )
        // $secure_cookie = false;
        
        return apply_filters('llms_bkpk_login_secure_cookie', $secure_cookie, $user);
    }

    /**
     * Add redirect_to propert to $user object
     *
     * @param WP_User $user            
     */
    private function addRedirectTo(&$user)
    {
        global $bkpkFM;
        $role = $bkpkFM->getUserRole($user->ID);
        $redirect_to = $role == 'administrator' ? admin_url() : home_url();
        if ($bkpkFM->isPro())
            $redirect_to = $bkpkFM->getRedirectionUrl($redirect_to, 'login', $role);
        
        if ($bkpkFM->isHookEnable('login_redirect'))
            $redirect_to = apply_filters('login_redirect', $redirect_to, isset($_REQUEST['redirect_to']) ? $_REQUEST['redirect_to'] : '', $user);
        
        $user->redirect_to = $redirect_to;
    }

    /**
     * Find user_login form user_login or user_email
     */
    function findUserForLogin()
    {
        global $bkpkFM;
        $loginBy = isset($this->settings['login_by']) ? $this->settings['login_by'] : null;
        
        $userLogin = null;
        if (isset($_REQUEST['log']))
            $userLogin = $_REQUEST['log'];
        elseif (isset($_REQUEST['user_login']))
            $userLogin = $_REQUEST['user_login'];
        elseif (isset($_REQUEST['user_email']))
            $userLogin = $_REQUEST['user_email'];
        
        if ($loginBy == 'user_login_or_email') {
            $user = get_user_by('email', $userLogin);
            if ($user === false)
                $user = get_user_by('login', $userLogin);
        } elseif ($loginBy == 'user_email')
            $user = get_user_by('email', $userLogin);
        else
            $user = get_user_by('login', $userLogin);
        
        if ($user === false) {
            $title = $bkpkFM->loginByArray();
            return new \WP_Error('invalid_login', $bkpkFM->getMsg('invalid_login', @$title[$loginBy]));
        }
        
        return $user;
    }
}