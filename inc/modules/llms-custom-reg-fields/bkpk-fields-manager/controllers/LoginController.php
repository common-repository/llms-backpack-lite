<?php
namespace BPKPFieldManager;

class LoginController
{

    function __construct()
    {
        add_filter('login_url', array(
            $this,
            'loginUrl'
        ), 10, 2);
        add_action('login_init', array(
            $this,
            'disableDefaultLoginPage'
        ));
        add_filter('authenticate', array(
            $this,
            'changeLoginErrorMessage'
        ), 50, 3);
        add_action('admin_notices', array(
            $this,
            'showMessageToSetPage'
        ));
    }

    function loginUrl($login_url, $redirect)
    {
        return (new Login())->loginUrl($login_url, $redirect);
    }

    function disableDefaultLoginPage()
    {
        return (new Login())->disableDefaultLoginPage();
    }

    function changeLoginErrorMessage($user, $username, $password)
    {
        return (new Login())->changeLoginErrorMessage($user, $username, $password);
    }

    /**
     *
     * @todo place this method inside views
     */
    function showMessageToSetPage()
    {
        global $bkpkFM;
        $settings = $bkpkFM->getData('settings');
        if (! empty($settings['login']['disable_wp_login_php']) && empty($settings['login']['resetpass_page']))
            echo '<div class="error"><p>' . sprintf(__('Please set %s!', $bkpkFM->name), "<a href='" . $bkpkFM->adminPageUrl('settings', false) . '#bkpk_settings_login' . "'>" . __('Reset Password Page', $bkpkFM->name) . "</a>") . '</p></div>';
        
        if (in_array(@$settings['registration']['user_activation'], array(
            'email_verification',
            'both_email_admin'
        )) && empty($settings['registration']['email_verification_page']))
            echo '<div class="error"><p>' . sprintf(__('Please set %s!', $bkpkFM->name), "<a href='" . $bkpkFM->adminPageUrl('settings', false) . '#bkpk_settings_registration' . "'>" . __('Email verification page', $bkpkFM->name) . "</a>") . '</p></div>';
    }
}