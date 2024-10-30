<?php
namespace BPKPFieldManager;

class SettingsController
{

    function __construct()
    {
        add_action('wp_ajax_bkpk_update_settings', array(
            $this,
            'ajaxUpdateSettings'
        ));
    }

    function ajaxUpdateSettings()
    {
        global $bkpkFM;
        $bkpkFM->verifyNonce();
        
        if (@$_REQUEST['action_type'] == 'authorize_pro')
            $bkpkFM->updateProAccountSettings($_REQUEST);
        
        $settings = $bkpkFM->arrayRemoveEmptyValue(@$_REQUEST);
        
        $extraFieldCount = @$settings['backend_profile']['field_count'];
        $extraFields = @$settings['backend_profile']['fields'];
        
        if (is_array($extraFields)) {
            foreach ($extraFields as $key => $val) {
                if ($key >= $extraFieldCount)
                    unset($settings['backend_profile']['fields'][$key]);
            }
        }
        
        unset($settings['action']);
        unset($settings['pf_nonce']);
        unset($settings['is_ajax']);
        unset($settings['backend_profile']['field_count']);
        
        $settings = apply_filters('llms_bkpk_pre_configuration_update', $settings, 'settings');
        
        $bkpkFM->updateData('settings', $settings);
        
        echo $bkpkFM->showMessage(__('Settings successfully saved.', $bkpkFM->name));
        die();
    }
}