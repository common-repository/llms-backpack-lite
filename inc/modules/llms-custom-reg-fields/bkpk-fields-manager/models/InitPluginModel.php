<?php
namespace BPKPFieldManager;

class InitPluginModel
{

    function pluginInit()
    {
        global $bkpkFM;
        $bkpkFM->checkPro();
        if ($bkpkFM->isPro)
            $bkpkFM->loadModels($bkpkFM->modelsPath . 'pro/');
        $bkpkFM->loadDirectory($bkpkFM->pluginPath . '/helpers/', false);
        $this->loadControllers($bkpkFM->controllersPath);
        $bkpkFM->loadDirectory($bkpkFM->pluginPath . '/dev/', false);
        add_action('init', function () {
            do_action('llms_bkpk_loaded');
        });
        $bkpkFM->proLoaded();
        $this->wpInitHook();
    }

    /**
     * @depreciated since 1.2
     * Extensions should load by llms_bkpk_loaded action
     */
    function wpInitHook()
    {
        global $bkpkFM;
        $this->loadExtension();
        if (! empty($bkpkFM->extensions)) {
            $config = [
                'namespace' => ''
            ];
            foreach ($bkpkFM->extensions as $extension) {
                $bkpkFM->loadDirectory($extension . '/models/', false, $config);
                $bkpkFM->loadDirectory($extension . '/controllers/', true, $config);
                $bkpkFM->loadDirectory($extension . '/helpers/', false, $config);
            }
        }
    }

    /**
     * @depreciated since 1.2
     * Extensions should load by llms_bkpk_loaded action
     */
    function loadExtension()
    {
        global $bkpkFM;
        if ($bkpkFM->isPro())
            $extensions = apply_filters('llms_bkpk_load_extension', []);
        $bkpkFM->extensions = ! empty($extensions) ? $extensions : [];
    }

    function loadControllers($controllersPath)
    {
        global $bkpkFM;
        $controllersOrder = $bkpkFM->controllersOrder();
        
        $classes = $instance = [];
        foreach (scandir($controllersPath) as $file) {
            if (preg_match("/.php$/i", $file))
                $classes[str_replace(".php", "", $file)] = $controllersPath . $file;
        }
        
        $proClasses = $bkpkFM->loadProControllers($classes, $controllersPath);
        if (is_array($proClasses))
            $classes = $proClasses;
        
        foreach ($classes as $className => $classPath) {
            require_once ($classPath);
            if (! in_array($className, $controllersOrder))
                $controllersOrder[] = $className;
        }
        
        foreach ($controllersOrder as $className) {
            $classWithNamespace = '\\' . __NAMESPACE__ . '\\' . $className;
            if (class_exists($classWithNamespace))
                $instance[] = new $classWithNamespace();
        }
        
        return $instance;
    }

    function renderPro($viewName, $parameter = array(), $subdir = null, $ob = false)
    {
        global $bkpkFM;
        
        $viewPath = self::locateView($viewName, $subdir);
        if (! $viewPath)
            return;
        
        if ($parameter)
            extract($parameter);
        
        if ($ob)
            ob_start();
        
        $pageReturn = include $viewPath;
        
        if ($ob) {
            $html = ob_get_contents();
            ob_end_clean();
            return $html;
        }
        
        if ($pageReturn and $pageReturn != 1)
            return $pageReturn;
        
        if (isset($html))
            return $html;
    }

    function locateView($viewName, $subdir = null)
    {
        global $bkpkFM;
        
        $locations = array();
        if ($subdir)
            $subdir = $subdir . '/';
        
        $proLocations = $bkpkFM->locateProView($locations);
        if (is_array($proLocations))
            $locations = $proLocations;
        
        foreach ($bkpkFM->extensions as $extension)
            $locations[] = $extension . '/views/';
        $locations[] = $bkpkFM->viewsPath;
        
        foreach ($locations as $path) {
            $fullPath = $path . $subdir . $viewName . '.php';
            if (file_exists($fullPath))
                return $fullPath;
        }
        
        return false;
    }
}