<?php
namespace BPKPFieldManager;

/**
 * global varaiable $bkpkFramework are available as instance of bkpkFramework class
 * all classes from models directory are preloaded with $bkpkFramework
 * any method of models class can be access as $bkpkFramework->methodNmae();.
 */

if (! class_exists('Frameworkbkpk')) {

    class Frameworkbkpk
    {

        public $version = '1.0.0';

        public $prefix = 'pf_';

        public $name = 'plugin-framework';

        public $frameworkPath;

        public $modelsPath;

        public $controllersPath;

        public $viewsPath;

        public $pluginPath;

        public $frameworkUrl;

        public $pluginUrl;

        public $assetsUrl;

        public $scripts = array();

        public function __construct()
        {
            $this->frameworkPath = dirname(__FILE__);
            $this->modelsPath = $this->frameworkPath . '/models/';
            $this->controllersPath = $this->frameworkPath . '/controllers/';
            $this->viewsPath = $this->frameworkPath . '/views/';
            
            $this->loadModels($this->modelsPath);
            $this->pluginPath = $this->directoryUp($this->frameworkPath);
            $this->frameworkUrl = plugins_url('', __FILE__);
            $this->pluginUrl = $this->directoryUp($this->frameworkUrl);
            $this->assetsUrl = $this->pluginUrl . '/assets/';
        }

        public function init()
        {
            add_action('wp_ajax_pf_ajax_request', array(
                $this,
                'pfAjaxRequest'
            ));
            add_action('wp_ajax_nopriv_pf_ajax_request', array(
                $this,
                'pfAjaxRequest'
            ));
            
            $this->pluginInit();
        }

        public function pfAjaxRequest()
        {
            $methodName = @$_REQUEST['method_name'];
            if ($methodName) {
                $methodName = 'ajax' . ucwords($methodName);
                $this->$methodName();
            } else {
                echo 'not found!';
            }
            
            die();
        }

        /**
         * get data from option table with or without cache.
         *
         * @param $key :
         *            field key without prefix.
         */
        public function getData($key, $networkwide = false)
        {
            global $bkpkFMCache;
            
            if (! isset($bkpkFMCache->$key)) {
                if ($networkwide && is_multisite()) {
                    $bkpkFMCache->$key = get_site_option($this->prefixLong . $key);
                } else {
                    $bkpkFMCache->$key = get_option($this->prefixLong . $key);
                }
            }
            
            return apply_filters("llms_bkpk_get_option_$key", $bkpkFMCache->$key);
        }

        /**
         * Update Data to option table and set cache if triggered.
         *
         * @param $key :
         *            field key without prefix.
         * @param $data :
         *            Data to be set
         */
        public function updateData($key, $data, $networkwide = false)
        {
            global $bkpkFMCache;
            
            $bkpkFMCache->$key = $data;
            
            if ($networkwide && is_multisite()) {
                return update_site_option($this->prefixLong . $key, $data);
            } else {
                return update_option($this->prefixLong . $key, $data);
            }
        }

        /**
         * Load all class from models directory.
         */
        public function loadModels($dir, $enc = false)
        {
            $classes = ! $enc ? $this->loadDirectory($dir) : $this->loadEncDirectory($dir);
            if (! is_array($classes)) {
                return;
            }
            foreach ($classes as $class) {
                $this->objects[] = $class;
            }
        }

        /**
         * Not in use since 1.1.8rc1
         * Add js or css for enque.
         *
         * @param string $scriptName
         *            : script/style name with extension
         * @param string $type:
         *            where that script should loaded, arg: admin, front, shortcode, common
         *            Default: common
         * @param string|int $depends:
         *            for conditional loading, arg: name of shortcode, admin page hook, post/page id
         */
        public function addScript($scriptName, $type = null, $depends = null, $subdir = null)
        {
            $scriptData = $this->fileinfo($scriptName);
            $handle = $scriptData->name;
            $scriptType = $scriptData->ext;
            $subdir = $subdir ? "$subdir/" : null;
            $url = $this->assetsUrl . $scriptType . '/' . $subdir . $scriptName;
            
            $scripts = $this->scripts;
            $type = $type ? $type : 'common';
            
            // for enque wp script
            if (! $scriptType) {
                $handle = $scriptName;
                $url = null;
                $scriptType = 'js';
            }
            
            if ($type == 'shortcode') :
                $scripts[$type][$depends][] = array(
                    'handle' => $handle,
                    'url' => $url,
                    'type' => $scriptType
                );
             else :
                $scripts[$type][] = array(
                    'handle' => $handle,
                    'url' => $url,
                    'type' => $scriptType,
                    'depends' => $depends
                );
            endif;
            
            $this->scripts = $scripts;
        }

        /**
         * Include all file from directory
         *
         *
         * @param string $dir            
         * @param boolean $createInstance:
         *            if true, create instence of each class and return all instance as an array.
         * @param array $config:
         *            allows: exclude, namespace
         * @return boolean|object[]
         */
        public function loadDirectory($dir, $createInstance = true, $config = [])
        {
            extract($config);
            if (! file_exists($dir)) {
                return false;
            }
            
            foreach (scandir($dir) as $item) {
                if (preg_match('/.php$/i', $item)) {
                    if (isset($exclude) && in_array($item, $exclude)) {
                        continue;
                    }
                    $namespace = isset($namespace) ? $namespace : __NAMESPACE__;
                    $className = $namespace . '\\' . str_replace('.php', '', $item);
                    
                    if (! class_exists($className)) {
                        require_once $dir . $item;
                    }
                    
                    if ($createInstance) {
                        if (class_exists($className)) {
                            $classes[] = new $className();
                        }
                    }
                }
            }
            
            return isset($classes) ? $classes : false;
        }

        /**
         * Render view file.
         *
         * @param string $viewName:
         *            name of view file without extension
         */
        public function render($viewName, $parameter = array(), $subdir = null, $ob = false)
        {
            $path = $this->viewsPath;
            if ($subdir) {
                $path .= $subdir . '/';
            }
            $path .= $viewName . '.php';
            
            if ($parameter) {
                extract($parameter);
            }
            
            if ($ob) {
                ob_start();
            }
            $pageReturn = include $path;
            
            if ($ob) {
                $html = ob_get_contents();
                ob_end_clean();
                
                return $html;
            }
            
            if ($pageReturn and $pageReturn != 1) {
                return $pageReturn;
            }
            
            if (isset($html)) {
                return $html;
            }
        }

        /**
         * Dynamicaly call any method from models class
         * by bkpkFramework instance.
         */
        public function __call($name, $args)
        {
            if (! is_array(@$this->objects)) {
                return;
            }
            
            global $pfInstance;
            $pfInstance = $this;
            
            foreach ($this->objects as $object) {
                if (method_exists($object, $name)) {
                    $count = count($args);
                    if ($count == 0) {
                        return $object->$name();
                    } elseif ($count == 1) {
                        return $object->$name($args[0]);
                    } elseif ($count == 2) {
                        return $object->$name($args[0], $args[1]);
                    } elseif ($count == 3) {
                        return $object->$name($args[0], $args[1], $args[2]);
                    } elseif ($count == 4) {
                        return $object->$name($args[0], $args[1], $args[2], $args[3]);
                    } elseif ($count == 5) {
                        return $object->$name($args[0], $args[1], $args[2], $args[3], $args[4]);
                    } elseif ($count == 6) {
                        return $object->$name($args[0], $args[1], $args[2], $args[3], $args[4], $args[5]);
                    }
                }
            }
            
            return false;
        }
    }
}