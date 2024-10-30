<?php
namespace BPKPFieldManager;

class AjaxModel
{

    function postInsertUser()
    {
        global $bkpkFM;
        $bkpkFM->verifyNonce();
        
        $umUserInsert = new UserInsert();
        
        return $umUserInsert->postInsertUserProcess();
    }

    /**
     * This method will call with bkpk_login action
     */
    function postLogin()
    {
        return (new Login())->postLogin();
    }

    function postLostpassword()
    {
        return (new ResetPassword())->postLostPassword();
    }

    function ajaxValidateUniqueField()
    {
        global $bkpkFM;
        $bkpkFM->verifyNonce(false);
        
        $status = false;
        if (! isset($_REQUEST['fieldId']) or ! $_REQUEST['fieldValue'])
            return;
        
        $id = ltrim($_REQUEST['fieldId'], 'bkpk_field_');
        $fields = $bkpkFM->getData('fields');
        
        if (isset($fields[$id])) {
            $fieldData = $bkpkFM->getFieldData($id, $fields[$id]);
            $status = $bkpkFM->isUserFieldAvailable($fieldData['field_name'], $_REQUEST['fieldValue']);
            
            if (! $status) {
                $msg = sprintf(__('%s already taken', $bkpkFM->name), $_REQUEST['fieldValue']);
                if (isset($_REQUEST['customCheck'])) {
                    echo "error";
                    die();
                }
            }
            
            $response[] = $_REQUEST['fieldId'];
            $response[] = isset($status) ? $status : true;
            $response[] = isset($msg) ? $msg : null;
            
            echo json_encode($response);
        }
        
        die();
    }

    function ajaxFileUploader()
    {
        global $bkpkFM;
        $bkpkFM->verifyNonce();
        
        // list of valid extensions, ex. array("jpeg", "xml", "bmp")
        $allowedExtensions = array(
            'jpg',
            'jpeg',
            'png',
            'gif'
        );
        // max file size in bytes
        $sizeLimit = 1 * 1024 * 1024;
        $replaceOldFile = FALSE;
        
        $allowedExtensions = apply_filters('pf_file_upload_allowed_extensions', $allowedExtensions);
        $sizeLimit = apply_filters('pf_file_upload_size_limit', $sizeLimit);
        $replaceOldFile = apply_filters('pf_file_upload_is_overwrite', $replaceOldFile);
        
        $uploader = new FileUploader($allowedExtensions, $sizeLimit);
        $result = $uploader->handleUpload($replaceOldFile);
        // to pass data through iframe you will need to encode all html tags
        echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
        die();
    }

    function ajaxShowUploadedFile()
    {
        global $bkpkFM;
        $bkpkFM->verifyNonce();
        
        if (isset($_REQUEST['showimage'])) {
            if (isset($_REQUEST['imageurl']))
                echo "<img src='{$_REQUEST['imageurl']}' />";
            die();
        }
        
        $file = new File();
        $file->ajaxUpload();
        
        die();
    }

    function ajaxWithdrawLicense()
    {
        global $bkpkFM;
        $bkpkFM->verifyNonce();
        
        $status = $bkpkFM->withdrawLicense();
        if (is_wp_error($status))
            echo $bkpkFM->showError($status);
        elseif ($status === true) {
            echo $bkpkFM->showMessage(__('License has been withdrawn', $bkpkFM->name));
            echo $bkpkFM->jsRedirect($bkpkFM->adminPageUrl('settings', false));
        } else
            echo $bkpkFM->showError(__('Something went wrong!', $bkpkFM->name));
        
        die();
    }

    function ajaxGeneratePage()
    {
        global $bkpkFM;
        check_admin_referer('generate_page');
        
        $pages = array(
            'login' => 'Login',
            'resetpass' => 'Reset password',
            'verify-email' => 'Email verification'
        );
        
        if (! empty($_REQUEST['page'])) {
            $page = $_REQUEST['page'];
            if (isset($pages[$page])) {
                $content = ('login' == $page) ? '[llms-bkpk-login]' : '';
                $pageID = wp_insert_post(array(
                    'post_title' => $pages[$page],
                    'post_content' => $content,
                    'post_status' => 'publish',
                    'post_name' => $page,
                    'post_type' => 'page'
                ));
            }
        }
        
        if (! empty($pageID)) {
            $settings = $bkpkFM->getData('settings');
            switch ($page) {
                case 'login':
                    $settings['login']['login_page'] = $pageID;
                    $bkpkFM->updateData('settings', $settings);
                    wp_redirect($bkpkFM->adminPageUrl('settings', false) . '#bkpk_settings_login');
                    exit();
                    break;
                
                case 'resetpass':
                    $settings['login']['resetpass_page'] = $pageID;
                    $bkpkFM->updateData('settings', $settings);
                    wp_redirect($bkpkFM->adminPageUrl('settings', false) . '#bkpk_settings_login');
                    exit();
                    break;
                
                case 'verify-email':
                    $settings['registration']['email_verification_page'] = $pageID;
                    $bkpkFM->updateData('settings', $settings);
                    wp_redirect($bkpkFM->adminPageUrl('settings', false) . '#bkpk_settings_registration');
                    exit();
                    break;
            }
        }
        wp_redirect($bkpkFM->adminPageUrl('settings', false));
        exit();
    }

    function ajaxSaveAdvancedSettings()
    {
        global $bkpkFM;
        $bkpkFM->checkAdminReferer(__FUNCTION__);
        
        if (! isset($_REQUEST))
            $bkpkFM->showError(__('Error occurred while updating', $bkpkFM->name));
        
        $data = $bkpkFM->arrayRemoveEmptyValue($_REQUEST);
        $data = $bkpkFM->removeNonArray($data);
        
        $bkpkFM->updateData('advanced', stripslashes_deep($data));
        echo $bkpkFM->showMessage(__('Successfully saved.', $bkpkFM->name));
        
        die();
    }

    function ajaxTestMethod()
    {
        global $bkpkFM;
        echo 'Working...';
        $bkpkFM->dump($_REQUEST);
        die();
    }
}