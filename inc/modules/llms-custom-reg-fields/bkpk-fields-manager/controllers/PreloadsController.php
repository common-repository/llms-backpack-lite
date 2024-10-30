<?php
namespace BPKPFieldManager;

class PreloadsController
{

    function __construct()
    {
        global $bkpkFM;
        
        add_action('plugins_loaded', array(
            $this,
            'loadTextDomain'
        ));
        // Commented since 1.1.8rc1
        // $bkpkFM->addScript( 'jquery', 'front' );
        add_filter('get_avatar', array(
            $this,
            'getAvatar'
        ), 10, 5);
        add_filter('user_row_actions', array(
            $this,
            'userProfileLink'
        ), 10, 2);
        
        add_filter('wp_mail_from', array(
            $this,
            'mailFromEmail'
        ));
        add_filter('wp_mail_from_name', array(
            $this,
            'mailFromName'
        ));
        add_filter('wp_mail_content_type', array(
            $this,
            'mailContentType'
        ));
        
        add_action('wp_ajax_bkpk_common_request', array(
            $bkpkFM,
            'ajaxUmCommonRequest'
        ));
        
        add_action('llms_bkpk_admin_notices', array(
            $this,
            'adminNotices'
        ));
        add_action('admin_notices', array(
            $bkpkFM,
            'activateLicenseNotice'
        ));
        
        add_filter('pf_file_upload_allowed_extensions', array(
            $this,
            'fileUploadExtensions'
        ));
        add_filter('pf_file_upload_size_limit', array(
            $this,
            'fileUploadMaxSize'
        ));
        add_filter('pf_file_upload_is_overwrite', array(
            $this,
            'fileUploadOverwrite'
        ));
        add_action('pf_file_upload_after_uploaded', array(
            $this,
            'updateFileCache'
        ), 10, 2);
        
        register_activation_hook($bkpkFM->file, array(
            $this,
            'bkpkFMActivation'
        ));
        register_deactivation_hook($bkpkFM->file, array(
            $this,
            'bkpkFMDeactivation'
        ));
        
        add_action('llms_bkpk_schedule_event', array(
            $bkpkFM,
            'cleanupFileCache'
        ));
        add_filter('xmlrpc_methods', array(
            $this,
            'newXmlRpcMethods'
        ));
        add_action('init', array(
            $this,
            'processPostRequest'
        ), 30);
        add_action('delete_user', array(
            $this,
            'deleteFiles'
        ), 10, 2);
        add_filter('llms_bkpk_user_modified_old_data_tracker', array(
            $this,
            'deleteOldFiles'
        ));
        
        add_action('wp_ajax_um-debug', array(
            $this,
            'debug'
        ));
        add_action('wp_ajax_bkpk_file_uploader', array(
            $bkpkFM,
            'ajaxFileUploader'
        ));
        add_action('wp_ajax_nopriv_bkpk_file_uploader', array(
            $bkpkFM,
            'ajaxFileUploader'
        ));
        add_action('wp_ajax_bkpk_show_uploaded_file', array(
            $bkpkFM,
            'ajaxShowUploadedFile'
        ));
        add_action('wp_ajax_nopriv_bkpk_show_uploaded_file', array(
            $bkpkFM,
            'ajaxShowUploadedFile'
        ));
        add_action('wp_ajax_bkpk_validate_unique_field', array(
            $bkpkFM,
            'ajaxValidateUniqueField'
        ));
        add_action('wp_ajax_nopriv_bkpk_validate_unique_field', array(
            $bkpkFM,
            'ajaxValidateUniqueField'
        ));
        add_action('shutdown', array(
            $this,
            'checkWpFooterEnable'
        ));
        
        if ($bkpkFM->isPro) {
            add_action('wp', array(
                $bkpkFM,
                'validateUMPKey'
            ));
            add_action('wp_ajax_ump_license_validation', array(
                $bkpkFM,
                'validateUMPKey'
            ));
            add_filter('pre_set_site_transient_update_plugins', array(
                $bkpkFM,
                'checkForUpdate'
            ));
        }
    }

    function loadTextDomain()
    {
        global $bkpkFM;
        load_plugin_textdomain($bkpkFM->name, false, basename($bkpkFM->pluginPath) . '/helpers/languages');
    }

    /**
     * Filter for get_avatar.
     * Allow to change degault avatar to custom one.
     *
     * @param type $avatar            
     * @param type $id_or_email            
     * @param type $size            
     * @param type $default            
     * @param type $alt            
     * @return html img tag
     */
    function getAvatar($avatar = '', $id_or_email, $size = '96', $default = '', $alt = false)
    {
        global $bkpkFM;
        
        $safe_alt = (false === $alt) ? '' : esc_attr($alt);
        
        if (is_numeric($id_or_email))
            $user_id = (int) $id_or_email;
        elseif (is_string($id_or_email))
            $user_id = email_exists($id_or_email);
        elseif (is_object($id_or_email)) {
            if (! empty($id_or_email->user_id))
                $user_id = (int) $id_or_email->user_id;
            elseif (! empty($id_or_email->comment_author_email))
                $user_id = email_exists($id_or_email->comment_author_email);
        }
        
        if (! isset($user_id))
            return $avatar;
        
        $umAvatar = get_user_meta($user_id, 'user_avatar', true);
        
        $file = $bkpkFM->determinFileDir($umAvatar);
        if (! empty($file)) {
            $avatar = "<img alt='{$safe_alt}' src='{$file['url']}' class='avatar avatar-{$size} photo' height='{$size}' width='{$size}' />";
        }
        
        return $avatar;
    }

    function userProfileLink($actions, $user_object)
    {
        global $bkpkFM;
        $general = $bkpkFM->getSettings('general');
        
        if (isset($general['profile_in_admin']) && ! empty($general['profile_page'])) {
            $url = add_query_arg('user_id', $user_object->ID, get_permalink($general['profile_page']));
            $actions['front_profile'] = "<a href=\"$url\" target=\"_blank\">" . __('Profile', $bkpkFM->name) . "</a>";
        }
        
        return $actions;
    }

    function mailFromEmail($data)
    {
        global $bkpkFM;
        $general = $bkpkFM->getSettings('general');
        
        if (! empty($general['mail_from_email'])) {
            if (is_email($general['mail_from_email']))
                return $general['mail_from_email'];
        }
        
        return $data;
    }

    function mailFromName($data)
    {
        global $bkpkFM;
        $general = $bkpkFM->getSettings('general');
        
        if (! empty($general['mail_from_name']))
            return $general['mail_from_name'];
        
        return $data;
    }

    function mailContentType($data)
    {
        global $bkpkFM;
        $general = $bkpkFM->getSettings('general');
        
        if (! empty($general['mail_content_type']))
            return $general['mail_content_type'];
        
        return $data;
    }

    /**
     * Showing new version availablity notic at user meta admin pages
     */
    function adminNotices()
    {
        global $bkpkFM;
        
        $currentPlugin = get_site_transient('update_plugins');
        if (isset($currentPlugin->response[$bkpkFM->pluginSlug])) {
            $plugin = $currentPlugin->response[$bkpkFM->pluginSlug];
            $path = 'plugins.php#' . str_replace(' ', '-', strtolower($bkpkFM->title));
            $pluginsPage = is_multisite() ? network_admin_url($path) : admin_url($path);
            echo '<div class="error"><p>' . sprintf(__('There is a new version of %1$s available. Visit <a href="%2$s">Plugins</a> page to update the plugin.', $bkpkFM->name), "$bkpkFM->title $plugin->new_version", $pluginsPage) . '</p></div>';
        }
    }

    function fileUploadExtensions($allowedExtensions)
    {
        global $bkpkFM;
        
        if (isset($_REQUEST['field_id'])) {
            if ($_REQUEST['field_id'] == 'csv_upload_user_import') {
                $allowedExtensions = array(
                    "csv"
                );
            } elseif ($_REQUEST['field_id'] == 'txt_upload_ump_import') {
                $allowedExtensions = array(
                    "txt"
                );
            } elseif (strpos($_REQUEST['field_id'], 'bkpk_field_') !== false) {
                
                if (empty($_REQUEST['form_key']))
                    return $allowedExtensions;
                
                $formName = esc_attr($_REQUEST['form_key']);
                
                if (! empty($formName)) {
                    $form = new FormGenerate($formName, null, null);
                    $validFields = $form->validInputFields();
                    
                    if (! empty($validFields[$_REQUEST['field_name']])) {
                        $field = $validFields[$_REQUEST['field_name']];
                        if (! empty($field['allowed_extension'])) {
                            $allowedExtensions = str_replace(' ', '', $field['allowed_extension']);
                            $allowedExtensions = explode(",", $allowedExtensions);
                        }
                    }
                }
                /*
                 * $fieldID = str_replace( "bkpk_field_", "", $_REQUEST['field_id'] );
                 * $fields = $bkpkFM->getData( 'fields' );
                 * if ( isset( $fields[$fieldID]['allowed_extension'] ) ) {
                 * $allowedExtensions = str_replace( ' ', '', $fields[$fieldID]['allowed_extension'] );
                 * $allowedExtensions = explode( ",", $allowedExtensions );
                 * }
                 */
            }
        }
        
        return $allowedExtensions;
    }

    function fileUploadMaxSize($sizeLimit)
    {
        global $bkpkFM;
        
        if (isset($_REQUEST['field_id'])) {
            if ($_REQUEST['field_id'] == 'csv_upload_user_import') {
                $sizeLimit = 10 * 1024 * 1024;
            } elseif (strpos($_REQUEST['field_id'], 'bkpk_field_') !== false) {
                $fieldID = str_replace("bkpk_field_", "", $_REQUEST['field_id']);
                $fields = $bkpkFM->getData('fields');
                if (isset($fields[$fieldID]['max_file_size']))
                    $sizeLimit = $fields[$fieldID]['max_file_size'] * 1024;
            }
        }
        return $sizeLimit;
    }

    function fileUploadOverwrite($replaceOldFile)
    {
        if (isset($_REQUEST['field_id'])) {
            if ($_REQUEST['field_id'] == 'csv_upload_user_import')
                $replaceOldFile = true;
        }
        return $replaceOldFile;
    }

    function updateFileCache($fieldName, $filePath)
    {
        global $bkpkFM;
        $cache = $bkpkFM->getData('cache');
        
        $fileCache = isset($cache['file_cache']) ? $cache['file_cache'] : array();
        if (! in_array($filePath, $fileCache)) {
            $fileCache[time()] = $filePath;
            $cache['file_cache'] = $fileCache;
            $bkpkFM->updateData('cache', $cache);
        }
    }

    function bkpkFMActivation()
    {
        if (! wp_next_scheduled('llms_bkpk_schedule_event'))
            wp_schedule_event(current_time('timestamp'), 'daily', 'llms_bkpk_schedule_event');
        
        // wp_schedule_event( current_time( 'timestamp' ), 'daily', 'llms_bkpk_schedule_event');
    }

    /**
     * Since 1.1.5
     */
    function bkpkFMDeactivation()
    {
        wp_clear_scheduled_hook('llms_bkpk_schedule_event');
    }

    function newXmlRpcMethods($methods)
    {
        global $bkpkFM;
        $methods['ump.validate'] = array(
            $bkpkFM,
            'remoteValidationPro'
        );
        
        return $methods;
    }

    /**
     * Process UM post request which need to execute before header sent to browser.
     */
    function processPostRequest()
    {
        global $bkpkFM;
        
        // Check if it is a valid request.
        if (empty($_POST['bkpk_post_method_nonce']) || empty($_POST['method_name']))
            return;
            
            // Verify the request with nonce validation. method_name is used for nonce generation
        if (! wp_verify_nonce($_POST['bkpk_post_method_nonce'], $_POST['method_name']))
            return $bkpkFM->process_status = __('Security check', $bkpkFM->name);
            
            // Call method when need to trigger. Store process status to $bkpkFM->process_status for further showing message.
        $methodName = $_POST['method_name'];
        $postMethodName = 'post' . ucwords($methodName);
        // $bkpkFM->bkpk_post_method_status->$methodName = $bkpkFM->$postMethodName();
        
        $response = $bkpkFM->$postMethodName();
        
        if (! isset($bkpkFM->bkpk_post_method_status)) {
            $bkpk_post_method_status = new \stdClass();
            $bkpk_post_method_status->$methodName = $response;
            $bkpkFM->bkpk_post_method_status = $bkpk_post_method_status;
        } else
            $bkpkFM->bkpk_post_method_status->$methodName = $response;
    }

    /**
     * Delete user's avatar and files.
     * Called by delete_user action.
     *
     * @param int $userID            
     * @param int $reassign:
     *            Don't needs to focus on $reassign. Everytime llmsbkpk get deleted.
     */
    function deleteFiles($userID, $reassign)
    {
        File::deleteFiles($userID);
    }

    /**
     * Delete old files while user update their profile.
     * Called by llms_bkpk_user_modified_old_data_tracker filter.
     *
     * @param array $oldData            
     * @param WP_User $user            
     */
    function deleteOldFiles($oldData)
    {
        File::deleteOldFiles($oldData);
        return $oldData;
    }

    /**
     * Check if wp_footer enabled.
     * We need to store it for serving next request as shotdown action trigger at end
     * Related function: isWpFooterEnabled
     */
    function checkWpFooterEnable()
    {
        set_site_transient('llms_bkpk_is_wp_footer_enabled', true);
    }

    /**
     * Debuging UMP.
     *
     * Write debug code to views/debug.php
     * Access debug output by http://example.com/wp-admin/admin-ajax.php?action=um-debug
     */
    function debug()
    {
        global $bkpkFM;
        
        if ($bkpkFM->isAdmin()) {
            $bkpkFM->render('debug');
        }
        
        die();
    }
}
