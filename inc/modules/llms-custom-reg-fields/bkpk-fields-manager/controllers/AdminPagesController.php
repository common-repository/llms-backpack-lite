<?php
namespace BPKPFieldManager;

class AdminPagesController
{

    function __construct()
    {
        add_action('admin_menu', array(
            $this,
            'menuItem'
        ),12);
        add_action('admin_notices', array(
            $this,
            'umAdminNotices'
        ));
    }

    function menuItem()
    {
        global $bkpkFM, $umAdminPages;
        
        $parentSlug = 'llmsbkpk';
        $parentSlug = 'llms_bkpk_forms';
        $parentSlug = 'null';
        $pages = $bkpkFM->adminPages();
        $isPro = $bkpkFM->isPro();
        foreach ($pages as $key => $page) {
            if (! $isPro && empty($page['is_free']))
                continue;
            if ($isPro && ! empty($page['not_in_pro']))
                continue;
            $menuTitle = (! $isPro && ! $page['is_free']) ? '<span style="opacity:.5;filter:alpha(opacity=50);">' . $page['menu_title'] . '</span>' : $page['menu_title'];
            $callBack = ! empty($page['callback']) ? $page['callback'] : array(
                $this,
                $key . '_init'
            );
			$parent = 'lifterlms';
			$parent = null;
			//$parent = 'bkpk_settings';
			$parentSlug = $parent;
			
            $hookName = add_submenu_page($parentSlug, $page['page_title'], $menuTitle, 'manage_options', $page['menu_slug'], $callBack);
			//remove_submenu_page('edit.php?post_type=custom-type','my-page-slug');
            add_action('load-' . $hookName, array(
                $this,
                'onLoadUmAdminPages'
            ));
            $pages[$key]['hookname'] = $hookName;
        }
        
        $umAdminPages = $pages;
        
        add_filter('plugin_action_links_' . $bkpkFM->pluginSlug, array(
            &$this,
            'pluginSettingsMenu'
        ));
    }

    function onLoadUmAdminPages()
    {
        do_action('llms_bkpk_load_admin_pages');
    }

    function pluginSettingsMenu($links)
    {
        global $bkpkFM;
        
        $settings_link = '<a href="' . get_admin_url(null, 'admin.php?page=llms-bkpk-settings') . '">' . __('Settings', $bkpkFM->name) . '</a>';
        array_unshift($links, $settings_link);
        return $links;
    }

    function umAdminNotices()
    {
        global $current_screen;
        
        if ($current_screen->parent_base == 'llmsbkpk')
            do_action('llms_bkpk_admin_notices');
    }

    function forms_init()
    {
        global $bkpkFM;
        
        $bkpkFM->enqueueScripts(array(
            'jquery-ui-sortable',
            'font-awesome',
            'llms-bkpk',
            'llms-bkpk-admin',
            'bootstrap',
            'bootstrap-multiselect'
        ));
        $bkpkFM->runLocalization();
        
        if (! empty($_REQUEST['action'])) {
            switch ($_REQUEST['action']) {
                case 'new':
                    $bkpkFM->render('editForm', array(
                        'formName' => ''
                    ), 'forms');
                    break;
                
                case 'edit':
                    $formName = ! empty($_REQUEST['form']) ? $_REQUEST['form'] : null;
                    $bkpkFM->render('editForm', array(
                        'formName' => $formName
                    ), 'forms');
                    break;
                
                case 'delete':
                    $bkpkFM->render('formsEditorPage', array(), 'forms');
                    break;
            }
        } else {
            $bkpkFM->render('formsEditorPage', array(), 'forms');
        }
    }

    function fields_init()
    {
        global $bkpkFM;
        
        $bkpkFM->enqueueScripts(array(
            'jquery-ui-sortable',
            'font-awesome',
            'bootstrap',
            'bootstrap-multiselect',
            'llms-bkpk',
            'llms-bkpk-admin'
        ));
        $bkpkFM->runLocalization();
        
        $bkpkFM->render('fieldsEditorPage', array(), 'fields');
    }

    function email_notification_init()
    {
        global $bkpkFM;
        
        $bkpkFM->enqueueScripts(array(
            'jquery-ui-core',
            'jquery-ui-tabs',
            'jquery-ui-all',
            'font-awesome',
            'bootstrap',
            
            'llms-bkpk',
            'llms-bkpk-admin'
        ));
        $bkpkFM->runLocalization();
        
        $data = array(
            'registration' => $bkpkFM->getEmailsData('registration'),
            'email_verification' => $bkpkFM->getEmailsData('email_verification'),
            'admin_approval' => $bkpkFM->getEmailsData('admin_approval'),
            'activation' => $bkpkFM->getEmailsData('activation'),
            'deactivation' => $bkpkFM->getEmailsData('deactivation'),
            'lostpassword' => $bkpkFM->getEmailsData('lostpassword'),
            'reset_password' => $bkpkFM->getEmailsData('reset_password'),
            'profile_update' => $bkpkFM->getEmailsData('profile_update')
        );
        
        $bkpkFM->renderPro('emailNotificationPage', array(
            'data' => $data,
            'roles' => $bkpkFM->getRoleList()
        ), 'email');
    }

    function export_import_init()
    {
        global $bkpkFM;
        
        $bkpkFM->enqueueScripts(array(
            'jquery-ui-core',
            'jquery-ui-sortable',
            'jquery-ui-draggable',
            'jquery-ui-droppable',
            'jquery-ui-datepicker',
            'jquery-ui-dialog',
            'jquery-ui-progressbar',
            
            'font-awesome',
            'bootstrap',
            'bootstrap-multiselect',
            
            'llms-bkpk',
            'llms-bkpk-admin',
            'jquery-ui-all',
            'fileuploader',
            'opentip'
        ));
        $bkpkFM->runLocalization();
        
        $cache = $bkpkFM->getData('cache');
        $csvCache = @$cache['csv_files'];
        
        // importPage maxSize: 20M
        $bkpkFM->renderPro('importExportPage', array(
            'csvCache' => $csvCache,
            'maxSize' => (20 * 1024 * 1024)
        ), 'exportImport');
    }

    function settings_init()
    {
        global $bkpkFM;
        
        self::moreExecution();
        
        $bkpkFM->enqueueScripts(array(
            'jquery-ui-core',
            'jquery-ui-widget',
            'jquery-ui-mouse',
            'jquery-ui-sortable',
            'jquery-ui-draggable',
            'jquery-ui-droppable',
            'jquery-ui-accordion',
            'jquery-ui-tabs',
            'jquery-ui-all',
            
            'font-awesome',
            // 'bootstrap',
            
            'llms-bkpk',
            'llms-bkpk-admin',
            'validationEngine'
        ));
        $bkpkFM->runLocalization();
        
        $settings = $bkpkFM->getData('settings');
        $forms = $bkpkFM->getData('forms');
        $fields = $bkpkFM->getData('fields');
        $default = $bkpkFM->defaultSettingsArray();
        
        $bkpkFM->render('settingsPage', array(
            'settings' => $settings,
            'forms' => $forms,
            'fields' => $fields,
            'default' => $default
        ));
    }

    function pro_ads_init()
    {
        global $bkpkFM;
        $bkpkFM->enqueueScripts([
            'bootstrap'
        ]);
        $bkpkFM->renderPro('proAdsPage');
    }

    function advanced_init()
    {
        global $bkpkFM;
        
        $bkpkFM->enqueueScripts(array(
            'jquery-ui-core',
            'jquery-ui-tabs',
            'jquery-ui-all',
            
            'llms-bkpk',
            'llms-bkpk-admin',
            'bootstrap',
            'bootstrap-multiselect',
            'multiple-select'
        ));
        $bkpkFM->runLocalization();
        
        $bkpkFM->renderPro('advancedPage', array(
            'advanced' => $bkpkFM->getData('advanced')
        ), 'advanced');
    }

    function moreExecution()
    {
        $actionType = ! empty($_GET['action_type']) ? $_REQUEST['action_type'] : false;
        if ($actionType == 'notice') {
            if (! empty($_GET['action_name']))
                $_GET['action_name'] == 'dismiss_translation_notice' ? delete_option('llms_bkpk_show_translation_update_notice') : false;
        }
    }
}