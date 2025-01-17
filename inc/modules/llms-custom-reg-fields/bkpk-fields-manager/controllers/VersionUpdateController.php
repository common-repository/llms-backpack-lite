<?php
namespace BPKPFieldManager;

class VersionUpdateController
{

    function __construct()
    {
        add_action('admin_menu', array(
            $this,
            'init'
        ), 15);
        add_action('network_admin_menu', array(
            $this,
            'init'
        ), 15);
        add_action('admin_init', array(
            $this,
            'deleteFreeVersion'
        ));
        
        add_action('admin_notices', array(
            $this,
            'showAdminNotices'
        ));
        
        // add_filter( 'site_transient_update_plugins',array( $this, 'pluginUpdateNotification' ) );
    
    /**
     * Set on plugin activation
     */
        // register_activation_hook( $bkpkFM->file, array( $this, 'runEvent' ) );
        // add_action( 'bkpk_plugin_activation_event', array( $this, 'init' ) );
    }

    /**
     * Check if data update is needed after version update
     */
    function init()
    {
        global $bkpkFM;
        
        $history = $bkpkFM->getData('history', true);
        
        if (empty($history)) {
            if (is_multisite())
                $history = get_option('llms_bkpk_history');
        }
        
        $lastVersion = null;
        if (! empty($history)) {
            if (isset($history['version']['last_version']))
                $lastVersion = $history['version']['last_version'];
        } else
            $history = array();
        
        if (version_compare($bkpkFM->version, $lastVersion, '<='))
            return;
            
            // Determine last version and run data update
        if ($lastVersion)
            self::runUpgrade($lastVersion);
        else {
            if (get_option('llms_bkpk_fields'))
                self::runUpgrade('1.1.0');
            elseif (get_option('llms_bkpk_field'))
                self::runUpgrade('1.0.3');
        }
        
        // Saveing last version data
        $history['version']['last_version'] = $bkpkFM->version;
        $history['version'][$bkpkFM->version] = array(
            'timestamp' => time()
        );
        
        $bkpkFM->updateData('history', $history, true);
        
        // nocache_headers();
    }

    /**
     * Migrate data from previous version by wp_schedule_single_event on plugin activation.
     */
    /*
     * function runEvent(){
     * wp_schedule_single_event( current_time( 'timestamp' ), 'bkpk_plugin_activation_event' );
     * }
     */
    
    /**
     * Replace download url for pro version
     * Running force update while upgrading from free to pro
     */
    /*
     * function pluginUpdateNotification( $data ){
     * global $bkpkFM;
     *
     * // When new version is available
     * if( isset( $data->response[ $bkpkFM->pluginSlug ] ) ){
     * $plugin = $data->response[ $bkpkFM->pluginSlug ];
     * if( $bkpkFM->isPro() ){
     * $data->response[ $bkpkFM->pluginSlug ]->url = $bkpkFM->website;
     * $data->response[ $bkpkFM->pluginSlug ]->package = $bkpkFM->generateProUrl( 'download', $plugin->new_version );
     * }
     * }
     *
     * // Running Force Upgrade (free to pro)
     * if( isset( $data->checked[ $bkpkFM->pluginSlug ] ) ){
     * if( !$bkpkFM->isPro && $bkpkFM->isLicenceValidated() ){
     * $upgrade = new \stdClass;
     * $upgrade->id = '0';
     * $upgrade->slug = $bkpkFM->pluginSlug;
     * $upgrade->new_version = substr( $bkpkFM->version, 0, 5 );
     * $upgrade->upgrade_notice= __( 'Upgrading from free version to pro version', $bkpkFM->name );
     * $upgrade->url = $bkpkFM->website;
     * $upgrade->package = $bkpkFM->generateProUrl( 'download', 'latest_stable' );
     *
     * $data->response[ $bkpkFM->pluginSlug ] = $upgrade;
     * }
     * }
     *
     * return $data;
     * }
     */
    
    /**
     * Run upgrade one by one
     */
    function runUpgrade($versionFrom)
    {
        global $bkpkFM;
        
        if (in_array($versionFrom, array(
            '1.0.0',
            '1.0.1',
            '1.0.2',
            '1.0.3'
        ))) {
            self::upgradeFrom_1_0_3_To_1_1_0();
            self::upgradeAvatarFrom_1_0_3_To_1_1_0();
            $versionFrom = '1.1.0';
        }
        
        if (in_array($versionFrom, array(
            '1.0.5',
            '1.1.0',
            '1.1.1',
            '1.1.2rc1',
            '1.1.2rc2'
        )))
            self::upgradeFrom_1_1_0_To_1_1_2();
        
        if (in_array($versionFrom, array(
            '1.1.2rc3',
            '1.1.2rc4',
            '1.1.2',
            '1.1.3rc1',
            '1.1.3rc2'
        )))
            $bkpkFM->upgrade_to_1_1_3();
        
        if (version_compare($versionFrom, '1.1.5rc3', '<')) {
            $bkpkFM->upgrade_to_1_1_5();
            self::upgrade_to_1_1_5($versionFrom);
        }
        
        if (version_compare($versionFrom, '1.1.6rc2', '<'))
            self::upgrade_to_1_1_6();
        
        if (version_compare($versionFrom, '1.1.6', '<='))
            self::upgrade_to_1_1_7();
        
        $bkpkFM->notifyVersionUpdate();
    }

    function upgrade_to_1_1_7()
    {
        global $bkpkFM;
        
        $fields = $bkpkFM->getData('fields');
        if (is_array($fields)) {
            foreach ($fields as $key => $field) {
                if (! isset($field['field_type']))
                    continue;
                
                if ($field['field_type'] == 'password') {
                    $fields[$key]['field_type'] = 'custom';
                    $fields[$key]['input_type'] = 'password';
                } elseif ($field['field_type'] == 'email') {
                    $fields[$key]['field_type'] = 'custom';
                    $fields[$key]['input_type'] = 'email';
                } elseif ($field['field_type'] == 'user_avatar' || $field['field_type'] == 'file') {
                    if (! empty($field['crop_image']))
                        $fields[$key]['resize_image'] = '1';
                }
            }
            
            $bkpkFM->updateData('fields', $fields);
        }
    }

    function upgrade_to_1_1_6()
    {
        global $bkpkFM;
        
        $data = $bkpkFM->getData('settings');
        $data['login']['disable_registration_link'] = true;
        $bkpkFM->updateData('settings', $data);
    }

    function upgrade_to_1_1_5($versionFrom)
    {
        global $bkpkFM;
        
        $pageName = apply_filters('llms_bkpk_front_execution_page', 'resetpass');
        $pageID = $bkpkFM->postIDbyPostName($pageName);
        if (! empty($pageID)) {
            
            // Set resetpass page to ['login']['resetpass_page'] and ['registration']['email_verification_page']
            $settings = $bkpkFM->getData('settings');
            if (empty($settings['login']['resetpass_page']))
                $settings['login']['resetpass_page'] = $pageID;
            if (empty($settings['registration']['email_verification_page']))
                $settings['registration']['email_verification_page'] = $pageID;
            $bkpkFM->updateData('settings', $settings);
            
            // set resetpass page content to null
            if ($versionFrom != '1.1.5rc2') {
                $resetpassPage = array(
                    'ID' => $pageID,
                    'post_content' => ''
                );
                wp_update_post($resetpassPage);
            }
        }
        
        // Check default language is other than english or wpml is active
        if (get_bloginfo('language') != 'en-US' || function_exists('icl_object_id'))
            update_option('llms_bkpk_show_translation_update_notice', 1);
            
            // Reset cache
        $bkpkFM->updateData('cache', null);
        
        // Create index.html file
        $uploads = $bkpkFM->uploadDir();
        if (file_exists($uploads['path']) && is_dir($uploads['path']) && ! file_exists($uploads['path'] . 'index.html'))
            touch($uploads['path'] . 'index.html');
        
        if (! wp_next_scheduled('llms_bkpk_schedule_event'))
            wp_schedule_event(current_time('timestamp'), 'daily', 'llms_bkpk_schedule_event');
    }

    /**
     * Distribute one page settings data to multipart array
     */
    function upgradeFrom_1_1_0_To_1_1_2()
    {
        global $bkpkFM;
        
        $roles = $bkpkFM->getRoleList();
        if (! $roles) {
            $roles = array(
                'administrator' => 'Administrator',
                'editor' => 'Editor',
                'author' => 'Author',
                'contributor' => 'Contributor',
                'subscriber' => 'Subscriber'
            );
        }
        
        /**
         * Converting Settings
         */
        $data = $bkpkFM->getData('settings'); // Retrieve old settings data.
        $defaultLoginSettings = $bkpkFM->defaultSettingsArray('login');
        
        $settings['general']['profile_page'] = @$data['profile_page'];
        $settings['general']['profile_in_admin'] = @$data['profile_in_admin'];
        $settings['general']['recaptcha_public_key'] = @$data['recaptcha_public_key'];
        $settings['general']['recaptcha_private_key'] = @$data['recaptcha_private_key'];
        
        $settings['login']['login_by'] = @$data['login_by'];
        $settings['login']['login_page'] = @$data['login_page'];
        $settings['login']['disable_ajax'] = @$data['disable_ajax_login'];
        
        $settings['login']['login_form'] = @$defaultLoginSettings['login_form'];
        foreach ($roles as $roleKey => $roleVal)
            $settings['login']['loggedin_profile'][$roleKey] = $defaultLoginSettings['loggedin_profile']['subscriber'];
        
        $bkpkFM->updateData('settings', $settings);
        
        /**
         * Converting Emails
         */
        $data = get_option('llms-bkpk-email');
        
        foreach ($roles as $key => $val) {
            $emails['registration']['user_email'][$key]['subject'] = str_replace(array(
                '%BLOG_TITLE%',
                '%BLOG_URL%'
            ), array(
                '%site_title%',
                '%site_url%'
            ), @$data['user_email']['subject']);
            $emails['registration']['user_email'][$key]['body'] = str_replace(array(
                '%BLOG_TITLE%',
                '%BLOG_URL%'
            ), array(
                '%site_title%',
                '%site_url%'
            ), @$data['user_email']['body']);
            $emails['registration']['admin_email'][$key]['subject'] = str_replace(array(
                '%BLOG_TITLE%',
                '%BLOG_URL%'
            ), array(
                '%site_title%',
                '%site_url%'
            ), @$data['admin_email']['subject']);
            $emails['registration']['admin_email'][$key]['body'] = str_replace(array(
                '%BLOG_TITLE%',
                '%BLOG_URL%'
            ), array(
                '%site_title%',
                '%site_url%'
            ), @$data['admin_email']['body']);
        }
        $emails['registration']['user_email']['bkpk_disable'] = @$data['user_email']['enable'] ? '' : true;
        $emails['registration']['admin_email']['bkpk_disable'] = @$data['admin_email']['enable'] ? '' : true;
        
        $bkpkFM->updateData('emails', $emails);
    }

    function upgradeFrom_1_0_3_To_1_1_0()
    {
        global $bkpkFM;
        
        $cache = get_option('llms_bkpk_cache');
        if (isset($cache['upgrade']['1.0.3']['fields_upgraded']))
            return;
            
            // Check if upgrade is needed
        $fields = $bkpkFM->getData('fields');
        $exists = false;
        if ($fields) {
            if (is_array($fields)) {
                foreach ($fields as $value) {
                    if (isset($value['field_type']))
                        $exists = true;
                }
            }
        }
        if ($exists)
            return;
        
        $i = 0;
        // get Default fields
        $prevDefaultFields = get_option('llms_bkpk_field_checked');
        if ($prevDefaultFields) {
            foreach ($prevDefaultFields as $fieldName => $noData) {
                if ($fieldName == 'avatar')
                    $fieldName = 'user_avatar';
                $fieldData = $bkpkFM->getFields('key', $fieldName);
                if (! $fieldData)
                    continue;
                $i ++;
                $newField[$i]['field_title'] = isset($fieldData['title']) ? $fieldData['title'] : null;
                $newField[$i]['field_type'] = $fieldName;
                $newField[$i]['title_position'] = 'top';
            }
        }
        
        // get meta key
        $prevFields = get_option('llms_bkpk_field');
        if ($prevDefaultFields) {
            foreach ($prevFields as $fieldData) {
                if (! $fieldData)
                    continue;
                $i ++;
                $fieldType = $fieldData['meta_type'] == 'dropdown' ? 'select' : 'text';
                $newField[$i]['field_title'] = isset($fieldData['meta_label']) ? $fieldData['meta_label'] : null;
                $newField[$i]['field_type'] = $fieldType;
                $newField[$i]['title_position'] = 'top';
                $newField[$i]['description'] = isset($fieldData['meta_description']) ? $fieldData['meta_description'] : null;
                $newField[$i]['meta_key'] = isset($fieldData['meta_key']) ? $fieldData['meta_key'] : null;
                $newField[$i]['required'] = $fieldData['meta_required'] == 'yes' ? 'on' : null;
                if (isset($fieldData['meta_option'])) {
                    if ($fieldData['meta_option'] and is_string($fieldData['meta_option'])) {
                        $options = $bkpkFM->arrayRemoveEmptyValue(unserialize($fieldData['meta_option']));
                        if ($options)
                            $newField[$i]['options'] = implode(',', $options);
                    }
                }
                $newField[$i] = $bkpkFM->arrayRemoveEmptyValue($newField[$i]);
            }
        }
        
        // Defining Form data
        $newForm['profile']['form_key'] = 'profile';
        $n = 0;
        while ($n < $i) {
            $n ++;
            $newForm['profile']['fields'][] = $n;
        }
        
        if (isset($newField)) {
            $bkpkFM->updateData('fields', $newField);
            $bkpkFM->updateData('forms', $newForm);
            $cache['upgrade']['1.0.3']['fields_upgraded'] = true;
            update_option('llms_bkpk_cache', $cache);
        }
        
        return true;
    }

    function upgradeAvatarFrom_1_0_3_To_1_1_0()
    {
        global $bkpkFM;
        
        $cache = get_option('llms_bkpk_cache');
        if (isset($cache['upgrade']['1.0.3']['avatar_upgraded']))
            return;
        
        $users = get_users(array(
            'meta_key' => 'llms_bkpk_avatar'
        ));
        if (! $users)
            return;
        
        $uploads = wp_upload_dir();
        foreach ($users as $user) {
            $oldUrl = get_user_meta($user->ID, 'llms_bkpk_avatar', true);
            if ($oldUrl) {
                $newPath = str_replace($uploads['baseurl'], '', $oldUrl);
                update_user_meta($user->ID, 'user_avatar', $newPath);
            }
        }
        
        $cache['upgrade']['1.0.3']['avatar_upgraded'] = true;
        update_option('llms_bkpk_cache', $cache);
        
        return true;
    }

    function showAdminNotices()
    {
        global $bkpkFM;
        
        if (get_option('llms_bkpk_show_translation_update_notice')) {
            $url = $bkpkFM->adminPageUrl('settings', false);
            $url = add_query_arg(array(
                'action_type' => 'notice',
                'action_name' => 'dismiss_translation_notice'
            ), $url);
            echo '<div class="updated fade"><p>' . __('Some texts of BPKPFieldManagerPro have been updated. If you are using your site in any other languages than english, please update your translation.');
            echo ' <a href="' . $url . '" class="button">' . __('Dismiss', $bkpkFM->name) . '</a></p></div>';
        }
    }

    /**
     * Deactivate and delete free version of the plugin.
     */
    function deleteFreeVersion()
    {
        global $bkpkFM;
        
        if ($bkpkFM->isPro && file_exists(WP_PLUGIN_DIR . '/llms-bkpk/llms-bkpk.php')) {
            deactivate_plugins('llms-bkpk/llms-bkpk.php', true);
            delete_plugins([
                'llms-bkpk/llms-bkpk.php'
            ]);
        }
    }
}