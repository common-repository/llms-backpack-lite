<?php
namespace BPKPFieldManager;

/**
 * Handle all reset password processes
 *
 * @since 1.2.1
 *       
 * @author Dennis Hall
 */
class ResetPassword
{

    /**
     * Handle resetPassword request, key validation, password reset
     */
    function lostPasswordForm($config = [])
    {
        global $bkpkFM;
        $methodName = "Lostpassword";
        
        $html = null;
        $html .= getHookHtml('login_form_lostpassword');
        if (empty($config))
            $config = $bkpkFM->getExecutionPageConfig('lostpassword');
        
        $login = $bkpkFM->getSettings('login');
        if (! empty($login['disable_lostpassword']))
            return $bkpkFM->showError(__('Password reset is currently not allowed.', $bkpkFM->name));
        
        $html .= $bkpkFM->renderPro('lostPasswordForm', array(
            'config' => $config,
            'disableAjax' => ! empty($login['disable_ajax']) ? true : false,
            'methodName' => $methodName
        ), 'login');
        
        return $html;
    }

    function resetPassword($config = [])
    {
        global $bkpkFM;
        if (empty($config))
            $config = $bkpkFM->getExecutionPageConfig('resetpass');
        
        $html = null;
        $html .= getHookHtml('login_form_resetpass');
        $html .= getHookHtml('login_form_rp');
        // $user = $bkpkFM->check_password_reset_key( @$_GET['key'], rawurldecode( @$_GET['login'] ) );
        $user = check_password_reset_key(@$_GET['key'], rawurldecode(@$_GET['login']));
        
        $errors = new \WP_Error();
        if (! is_wp_error($user)) {
            if (isset($_POST['pass1']) && $_POST['pass1'] != $_POST['pass2'])
                $errors->add('password_reset_mismatch', $bkpkFM->getMsg('password_reset_mismatch'));
            if ($bkpkFM->isHookEnable('validate_password_reset'))
                do_action('validate_password_reset', $errors, $user);
            if ((! $errors->get_error_code()) && isset($_POST['pass1']) && ! empty($_POST['pass1'])) {
                $bkpkFM->reset_password($user, $_POST['pass1']);
                do_action('llms_bkpk_after_reset_password', $user);
                $html .= $bkpkFM->showMessage($bkpkFM->getMsg('password_reseted'));
                
                $redirect = ! empty($config['redirect']) ? $config['redirect'] : null;
                $redirect = apply_filters('llms_bkpk_reset_password_redirect', $redirect, $user);
                if (! empty($redirect))
                    $html .= $bkpkFM->jsRedirect($redirect, 5);
                
                return $html;
            }
        } else {
            if ($user->get_error_code() == 'invalid_key')
                return $bkpkFM->showError($bkpkFM->getMsg('invalid_key'), false);
            elseif ($user->get_error_code() == 'expired_key')
                return $bkpkFM->showError($bkpkFM->getMsg('expired_key'), false);
            else
                return $bkpkFM->showError($user->get_error_message(), false);
        }
        
        return $bkpkFM->renderPro('resetPasswordForm', array(
            'config' => $config,
            'user' => $user,
            'errors' => $errors
        ), 'login');
    }

    /**
     * This method will call by POST method
     * Called by umAjaxProModel::postLostpassword()
     *
     * @todo Check if $bkpkFM->verifyNonce() required
     */
    function postLostPassword()
    {
        global $bkpkFM;
        
        $settings = $bkpkFM->getSettings('login');
        if (! empty($settings['resetpass_page'])) {
            $pageID = (int) $settings['resetpass_page'];
            $permalink = get_permalink($pageID);
        }
        
        $output = null;
        
        if ($bkpkFM->isHookEnable('login_form_retrievepassword')) {
            ob_start();
            do_action('login_form_retrievepassword');
            $output .= ob_get_contents();
            ob_end_clean();
        }
        
        $resetPassLink = ! empty($permalink) ? $permalink : null;
        $response = $bkpkFM->retrieve_password($resetPassLink);
        
        if ($response === true) {
            $output .= $bkpkFM->showMessage($bkpkFM->getMsg('check_email_for_link'), 'success', false);
            $redirect_to = ! empty($_POST['redirect_to']) ? $_POST['redirect_to'] : '';
            
            if ($bkpkFM->isHookEnable('lostpassword_redirect'))
                $redirect_to = apply_filters('lostpassword_redirect', $redirect_to);
            
            if (! empty($redirect_to))
                $output .= $bkpkFM->jsRedirect($redirect_to, 5);
        } elseif (is_wp_error($response))
            $output .= $bkpkFM->showError($response->get_error_message(), false);
        
        return $bkpkFM->printAjaxOutput($output);
    }
}