<?php
namespace BPKPFieldManager;

class ShortcodesController
{

    function __construct()
    {
        global $bkpkFM;
        
        add_shortcode('llms-bkpk', array(
            $this,
            'init'
        ));
        add_shortcode('llms-bkpk-login', array(
            $this,
            'loginShortcode'
        ));
        add_shortcode('llms-bkpk-profile', array(
            $this,
            'profileShortcode'
        ));
        add_shortcode('llms-bkpk-registration', array(
            $this,
            'registrationShortcode'
        ));
        add_shortcode('llms-bkpk-field', array(
            $this,
            'fieldShortcode'
        ));
        add_shortcode('llms-bkpk-field-value', array(
            $this,
            'fieldValueShortcode'
        ));
        // add_action( 'media_buttons_context', array( $this, 'addUmButton' ) );
        // add_action( 'admin_footer', array( $this, 'shortcodeGeneratorPopup' ) );
    }

    function init($atts)
    {
        global $bkpkFM;
        extract(shortcode_atts(array(
            'type' => 'profile', // profile, registration, profile-registration, public, field-value
            'form' => null,
            'diff' => null,
            'id' => null, // Field ID or meta_key for field-value
            'key' => null
        ), $atts, 'llms-bkpk'));
        
        $actionType = strtolower($type);
        
        // Replace "both" to "profile-registration" and "none" to "public"
        $actionType = str_replace(array(
            'both',
            'none'
        ), array(
            'profile-registration',
            'public'
        ), $actionType);
        
        if ($actionType == 'login') :
            return $bkpkFM->userLoginProcess($form);
         

        elseif ($actionType == 'field') :
            return $this->fieldShortcode(array(
                'id' => $id
            ));
         

        elseif ($actionType == 'field-value') :
            return $this->fieldValueShortcode(array(
                'id' => $id,
                'key' => $key
            ));
         

        elseif ($actionType == 'reset-password') :
            return $this->resetPasswordShortcode();
         

        elseif ($actionType == 'email-verification') :
            return $this->emailVerificationShortcode();
         

        else :
            return $bkpkFM->userUpdateRegisterProcess($actionType, $form, $diff);
        

        endif;
    }

    function loginShortcode($atts)
    {
        global $bkpkFM;
        extract(shortcode_atts(array(
            'form' => null
        ), $atts));
        
        return $bkpkFM->userLoginProcess($form);
    }

    function profileShortcode($atts)
    {
        global $bkpkFM;
        extract(shortcode_atts(array(
            'form' => null,
            'diff' => null
        ), $atts));
        
        return $bkpkFM->userUpdateRegisterProcess('profile', $form, $diff);
    }

    function registrationShortcode($atts)
    {
        global $bkpkFM;
        extract(shortcode_atts(array(
            'form' => null
        ), $atts));
        
        return $bkpkFM->userUpdateRegisterProcess('registration', $form);
    }

    function fieldShortcode($atts)
    {
        global $bkpkFM;
        extract(shortcode_atts(array(
            'id' => null
        ), $atts));
        if (! $bkpkFM->isPro())
            return self::getProError();
        
        $bkpkFM->enqueueScripts(array(
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
        $bkpkFM->runLocalization();
        $umField = new Field($id);
        
        return $umField->generateField();
    }

    function fieldValueShortcode($atts)
    {
        global $bkpkFM;
        extract(shortcode_atts(array(
            'id' => null,
            'key' => null
        ), $atts));
        if (! $bkpkFM->isPro())
            return self::getProError();
        
        if (empty($id) && empty($key))
            return $bkpkFM->showError('Please provide field id or meta_key.', 'info', false);
        
        $umField = new Field($id);
        
        return $umField->displayValue($key);
    }

    function resetPasswordShortcode()
    {
        global $bkpkFM;
        if (! $bkpkFM->isPro())
            return self::getProError();
        
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
                return $password->resetPassword();
                break;
            
            default:
                $config = $bkpkFM->getExecutionPageConfig('lostpassword');
                $config['only_lost_pass_form'] = true;
                return $password->lostPasswordForm($config);
                break;
        }
    }

    function emailVerificationShortcode()
    {
        global $bkpkFM;
        if (! $bkpkFM->isPro())
            return self::getProError();
        
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
                return $bkpkFM->emailVerification();
                break;
        }
    }

    function addUmButton($context)
    {
        global $bkpkFM, $pagenow;
        if (! in_array($pagenow, array(
            'post.php',
            'post-new.php'
        )))
            return $context;
        
        if (! current_user_can('edit_posts') && ! current_user_can('edit_pages'))
            return $context;
        
        $img = $bkpkFM->assetsUrl . 'images/ump-icon.png';
        $container_id = 'bkpk_shortcode_popup';
        $title = __('Add lifterlms Back-Pack Shortcode', $bkpkFM->name);
        $context .= "<a class='thickbox' title='{$title}'
        href='#TB_inline?width=600&height=600&inlineId={$container_id}'>
        <img src='{$img}' /></a>";
        
        return $context;
    }

    function shortcodeGeneratorPopup()
    {
        global $bkpkFM, $pagenow;
        if (! in_array($pagenow, array(
            'post.php',
            'post-new.php'
        )))
            return;
        
        if (! current_user_can('edit_posts') && ! current_user_can('edit_pages'))
            return;
        
        $bkpkFM->enqueueScripts(array(
            'llms-bkpk',
            'llms-bkpk-admin'
        ));
        $bkpkFM->runLocalization();
        $actionTypes = $bkpkFM->validActionType();
        array_unshift($actionTypes, null);
        $formsList = $bkpkFM->getFormsName();
        array_unshift($formsList, null);
        $bkpkFM->render('shortcodePopup', array(
            'actionTypes' => $actionTypes,
            'formsList' => $formsList,
            'roles' => $bkpkFM->getRoleList()
        ));
    }

    function getProError()
    {
        global $bkpkFM;
        return '<div style="color:red">' . __('This shortcode is only supported on pro version. Get %s', $bkpkFM->name) . $bkpkFM->getProLink('lifterlms Back-Pack Pro') . '</div>';
    }
}