<?php
namespace BPKPFieldManager;

class MethodsModel
{

    function userUpdateRegisterProcess($actionType, $formName, $rolesForms = null)
    {
        global $bkpkFM;
        
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
        
        $actionType = strtolower($actionType);
        
        if (empty($actionType))
            return $bkpkFM->showError(__('Please provide a name of action type.', $bkpkFM->name));
        
        if (! $bkpkFM->validActionType($actionType))
            return $bkpkFM->showError(sprintf(__('Sorry. type="%s" is not valid.', $bkpkFM->name), $actionType));
            
            /*
         * if ( ! $bkpkFM->isPro() ) {
         * if ( ! in_array( $actionType, array('profile','public') ) )
         * return $bkpkFM->showError( "type='$actionType' is only supported, in pro version. Get " . $bkpkFM->getProLink( 'lifterlms Back-Pack Pro' ), "info", false );
         * }
         */
        
        $user = wp_get_current_user();
        $userID = (isset($user->ID) ? (int) $user->ID : 0);
        $isLoggedIn = ! empty($userID);
        
        if ($actionType == 'profile-registration')
            $actionType = $isLoggedIn ? 'profile' : 'registration';
            
            // Checking Permission
        if ($actionType == 'profile') {
            if (! $isLoggedIn) {
                $msg = $bkpkFM->getMsg('profile_required_loggedin');
                return empty($msg) ? null : $bkpkFM->showMessage($msg, 'info');
            }
            
            if (! empty($_REQUEST['user_id'])) {
                if ($userID != esc_attr($_REQUEST['user_id'])) {
                    if ($user->has_cap('add_users')) {
                        $userID = esc_attr($_REQUEST['user_id']);
                        $user = get_user_by('id', $userID);
                        if (empty($user))
                            return $bkpkFM->showError(__('No user found!.', $bkpkFM->name));
                    } else
                        return $bkpkFM->showError(__("You do not have permission to access user profile.", $bkpkFM->name));
                }
                
                /*
                 * if( $user->has_cap( 'add_users' ) ){
                 * $userID = esc_attr( $_REQUEST['user_id'] );
                 * $user = get_user_by('id', $userID);
                 * if( empty($user) )
                 * return $bkpkFM->showError( __( 'No user found!.', $bkpkFM->name ) );
                 * }else
                 * return $bkpkFM->showError( __( "You do not have permission to access user profile.", $bkpkFM->name ) );
                 */
            }
        } elseif ($actionType == 'registration') {
            if ($isLoggedIn && ! $user->has_cap('add_users'))
                return $bkpkFM->showMessage(sprintf(__('You have already registered. See your <a href="%s">profile</a>', $bkpkFM->name), $bkpkFM->getProfileLink()), 'info');
            elseif (! apply_filters('llms_bkpk_allow_registration', true))
                return $bkpkFM->showError(__('User registration is currently not allowed.', $bkpkFM->name));
            // elseif ( ! get_option( 'users_can_register' ) )
        } elseif ($actionType == 'public') {
            if (! empty($_REQUEST['user_id'])) {
                $userID = esc_attr($_REQUEST['user_id']);
                $user = get_user_by('id', $userID);
                if (empty($user))
                    return $bkpkFM->showError(__('No user found!.', $bkpkFM->name));
            } else {
                if (! $isLoggedIn) {
                    $msg = $bkpkFM->getMsg('public_non_lggedin_msg');
                    return empty($msg) ? null : $bkpkFM->showMessage($msg, 'info');
                }
            }
        }
        
        if (! empty($rolesForms)) {
            if (is_string($rolesForms))
                $rolesForms = $bkpkFM->toArray($rolesForms);
            if ($userID && in_array($actionType, array(
                'profile',
                'public'
            ))) {
                $role = $bkpkFM->getUserRole($userID);
                if (isset($rolesForms[$role])) {
                    $formName = $rolesForms[$role];
                }
            }
        }
        
        if (empty($formName))
            return $bkpkFM->showError(__('Please provide a form name.', $bkpkFM->name));
        
        $formBuilder = new FormGenerate($formName, $actionType, $userID);
        
        if (! $formBuilder->isFound())
            return $bkpkFM->ShowError(sprintf(__('Form "%s" is not found.', $bkpkFM->name), $formName));
            
            // $savedValues = in_array( $actionType, array('profile','public') ) ? get_userdata( $userID ) : null;
        
        $form = $formBuilder->getForm();
        
        // $bkpkFM->dump($form);
        
        /*
         * $form = $bkpkFM->getFormData( $formName );
         * if ( is_wp_error( $form ) )
         * return $bkpkFM->ShowError( $form );
         */
        
        $form['form_class'] = ! empty($form['form_class']) ? $form['form_class'] : '';
        $form['form_class'] = 'bkpk_user_form ' . $form['form_class'];
        if (empty($form['disable_ajax']))
            $form['onsubmit'] = "umInsertUser(this);";
        
        $output = $bkpkFM->render('generateForm', array(
            'form' => $form,
            // 'fieldValues' => in_array( $actionType, array('profile','public') ) ? get_userdata( $userID ) : null,
            'actionType' => $actionType,
            'userID' => $userID,
            'methodName' => 'InsertUser'
        ));
        
        return $output;
    }

    function userLoginProcess($formName = null)
    {
        global $bkpkFM;
        
        if (! empty($formName)) {
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
        } else {
            $bkpkFM->enqueueScripts(array(
                'llms-bkpk',
                'placeholder'
            ));
        }
        $bkpkFM->runLocalization();
        
        return (new Login())->loginForm($formName);
        // Commented since 1.2.1
        // return $bkpkFM->generateLoginForm($formName);
    }
}