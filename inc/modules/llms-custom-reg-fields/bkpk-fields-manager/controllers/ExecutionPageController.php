<?php
namespace BPKPFieldManager;

class ExecutionPageController
{

    function __construct()
    {
        add_filter('wp_list_pages_excludes', array(
            $this,
            'excludeExecutionPage'
        ));
        add_action('wp', array(
            $this,
            'executionPage'
        ));
        add_filter('register_url', array(
            $this,
            'registerUrl'
        ), 30);
        add_filter('logout_url', array(
            $this,
            'logoutUrl'
        ), 30, 2);
        add_filter('lostpassword_url', array(
            $this,
            'lostpasswordUrl'
        ), 30, 2);
        add_filter('allowed_redirect_hosts', array(
            $this,
            'addRedirectHosts'
        ), 30, 2);
    }

    function excludeExecutionPage($ids)
    {
        global $bkpkFM;
        if (! is_array($ids))
            $ids = array();
        
        $settings = $bkpkFM->getData('settings');
        
        if (! empty($settings['login']['resetpass_page']))
            array_push($ids, (int) $settings['login']['resetpass_page']);
        
        if (! empty($settings['registration']['email_verification_page']))
            array_push($ids, (int) $settings['registration']['email_verification_page']);
        
        return $ids;
    }

    function executionPage()
    {
        global $bkpkFM, $post;
        if (! is_page())
            return false;
        
        $settings = $bkpkFM->getData('settings');
        
        if (! empty($settings['login']['login_page'])) {
            if ($post->ID == (int) $settings['login']['login_page'])
                self::executeLogout();
        }
        
        if (! empty($settings['login']['resetpass_page'])) {
            if ($post->ID == (int) $settings['login']['resetpass_page'])
                self::executeResetpassPage($settings['login']);
        }
        
        if (! empty($settings['registration']['email_verification_page'])) {
            if ($post->ID == (int) $settings['registration']['email_verification_page'])
                self::executeEmailVerificationPage($settings['registration']);
        }
    }

    private function executeLogout()
    {
        global $bkpkFM;
        $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
        if ($action != 'logout')
            return false;
        
        if ($bkpkFM->isHookEnable('login_form_logout'))
            do_action('login_form_logout');
        
        check_admin_referer('log-out');
        wp_logout();
        
        $redirect_to = ! empty($_REQUEST['redirect_to']) ? $_REQUEST['redirect_to'] : home_url();
        wp_safe_redirect($redirect_to);
        exit();
    }

    private function executeResetpassPage($settings)
    {
        global $bkpkFM, $post;
        
        $bkpkFM->enqueueScripts(array(
            'llms-bkpk',
            'validationEngine',
            'password_strength'
        ));
        $bkpkFM->runLocalization();
        
        $password = new ResetPassword();
        $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
        switch ($action) {
            case 'resetpass':
            case 'rp':
                $config = $bkpkFM->getExecutionPageConfig('resetpass');
                $post->post_title = isset($config['title']) ? $config['title'] : '';
                $post->post_content .= $password->resetPassword($config);
                break;
            
            default:
                $config = $bkpkFM->getExecutionPageConfig('lostpassword');
                $config['only_lost_pass_form'] = true;
                $post->post_title = isset($config['title']) ? $config['title'] : '';
                $post->post_content .= $password->lostPasswordForm($config);
                break;
        }
    }

    private function executeEmailVerificationPage($settings)
    {
        global $bkpkFM, $post;
        
        if (! in_array(@$settings['user_activation'], array(
            'email_verification',
            'both_email_admin'
        )))
            return;
        
        $bkpkFM->enqueueScripts(array(
            'llms-bkpk',
            'validationEngine',
            'password_strength'
        ));
        $bkpkFM->runLocalization();
        
        $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
        switch ($action) {
            case 'email_verification':
            case 'ev':
                $config = $bkpkFM->getExecutionPageConfig('email_verification');
                $post->post_title = isset($config['title']) ? $config['title'] : '';
                $post->post_content .= $bkpkFM->emailVerification($config);
                break;
        }
    }

    function registerUrl($url)
    {
        global $bkpkFM;
        
        $registration = $bkpkFM->getSettings('registration');
        if (! empty($registration['user_registration_page'])) {
            $url = get_permalink($registration['user_registration_page']);
        }
        
        return $url;
    }

    function logoutUrl($logout_url, $redirect)
    {
        global $bkpkFM;
        
        $args = array(
            'action' => 'logout'
        );
        
        $login = $bkpkFM->getSettings('login');
        
        // Set default login page to frontend login page
        if (! empty($login['login_page']) && empty($redirect))
            $redirect = get_permalink($login['login_page']);
        
        if ($bkpkFM->isPro())
            $redirect = $bkpkFM->getRedirectionUrl($redirect, 'logout');
        
        $redirect = apply_filters('llms_bkpk_logout_redirect', $redirect, wp_get_current_user());
        
        if (! empty($redirect)) {
            $args['redirect_to'] = urlencode($redirect);
            $logout_url = add_query_arg($args, $logout_url);
        }
        
        if (! empty($login['disable_wp_login_php'])) {
            $pageID = ! empty($login['login_page']) ? $login['login_page'] : 0;
            if ($pageID) {
                $logout_url = add_query_arg($args, get_permalink($pageID));
                $logout_url = wp_nonce_url($logout_url, 'log-out');
            }
        }
        
        return $logout_url;
    }

    function lostpasswordUrl($lostpassword_url, $redirect)
    {
        global $bkpkFM;
        
        $settings = $bkpkFM->getSettings('login');
        if (empty($settings['resetpass_page']))
            return $lostpassword_url;
        
        $pageID = (int) $settings['resetpass_page'];
        $permalink = get_permalink($pageID);
        if (empty($permalink))
            return $lostpassword_url;
        
        $args = array(
            'action' => 'lostpassword'
        );
        if (! empty($redirect))
            $args['redirect_to'] = $redirect;
        
        $lostpassword_url = add_query_arg($args, $permalink);
        
        return $lostpassword_url;
    }

    /**
     * Add allowed redirection host for logout
     */
    function addRedirectHosts($allowed)
    {
        global $bkpkFM;
        
        $redirection = $bkpkFM->getSettings('redirection');
        if (! empty($redirection['logout_url'])) {
            foreach ($redirection['logout_url'] as $url) {
                $parse = parse_url(trim($url));
                $allowed[] = $parse['host'];
            }
        }
        
        return $allowed;
    }
}