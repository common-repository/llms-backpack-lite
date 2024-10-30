<?php

/**
 * Template tag for user login
 * 
 * @since 1.1.3
 * @author Dennis Hall
 * 
 * @param unknown $formName
 */
function bkpkFMLogin($formName = null)
{
    global $bkpkFM;
    
    return $bkpkFM->userLoginProcess($formName);
}

/**
 * Template tag for user registration and profile update
 *
 * @since 1.1.3
 * @author Dennis Hall
 *        
 * @param unknown $actionType            
 * @param unknown $formName            
 * @param unknown $rolesForms            
 */
function bkpkFMProfileRegister($actionType, $formName, $rolesForms = null)
{
    global $bkpkFM;
    
    return $bkpkFM->userUpdateRegisterProcess($actionType, $formName, $rolesForms);
}

/**
 * Template tag for form builder
 *
 * @since 1.1.3
 * @author Dennis Hall
 *        
 * @param unknown $actionType            
 * @param unknown $formName            
 * @param unknown $rolesForms            
 */
function bkpkFMFormBuilder($actionType, $formName, $rolesForms = null)
{
    global $bkpkFM;
    
    return $bkpkFM->userUpdateRegisterProcess($actionType, $formName, $rolesForms);
}