<?php
namespace BPKPFieldManager;

/**
 * Initialize class for BPKPFieldManager and hold global $bkpkFM object
 *
 * @since 1.2.1
 *       
 * @author Dennis Hall
 */
class BPKPFieldManager extends Frameworkbkpk
{

    public $title;

    public $version;

    public $name = 'bkpk';

    public $prefix = 'bkpk_';

    public $prefixLong = 'bkpk_llms_';

    public $website = '#';

    public function __construct($file)
    {
        $this->pluginSlug = plugin_basename($file);
        $this->pluginPath = dirname($file);
        $this->file = $file;
        $this->modelsPath = $this->pluginPath . '/models/';
        $this->controllersPath = $this->pluginPath . '/controllers/';
        $this->viewsPath = $this->pluginPath . '/views/';
        
        $this->pluginUrl = plugins_url('', $file);
        $this->assetsUrl = $this->pluginUrl . '/assets/';
        
        $pluginHeaders = [
            'Name' => 'Plugin Name',
            'Version' => 'Version'
        ];
        
        $pluginData = get_file_data($this->file, $pluginHeaders);
        
        $this->title = $pluginData['Name'];
        $this->version = $pluginData['Version'];
        
        // Load Plugins & Framework modal classes
        global $bkpkFramework, $bkpkFMCache;
        $bkpkFMCache = new \stdClass();
        
        $this->loadModels($this->modelsPath);
        $this->loadModels($bkpkFramework->modelsPath);
    }
}