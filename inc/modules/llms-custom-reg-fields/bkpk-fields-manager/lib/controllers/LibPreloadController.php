<?php
namespace BPKPFieldManager;

class LibPreloadController
{

    function __construct()
    {
        global $bkpkFramework;
        
        /**
         * Commented since 1.1.8rc1
         */
        // add_action( 'wp_enqueue_scripts', array( $this, 'enqueFrontScripts' ) );
        
        add_action('wp_enqueue_scripts', array(
            $this,
            'addjQuery'
        ));
        add_action('admin_print_scripts', array(
            $this,
            'setVariable'
        ));
        add_action('wp_print_scripts', array(
            $this,
            'setVariable'
        ));
    }

    function setVariable()
    {
        global $bkpkFramework;
        $ajaxurl = admin_url('admin-ajax.php');
        $nonceText = $bkpkFramework->settingsArray('nonce');
        $nonce = wp_create_nonce($nonceText);
        
        if (is_admin())
            echo "<script type='text/javascript'>pf_nonce='$nonce';</script>";
        else
            echo "<script type='text/javascript'>ajaxurl='$ajaxurl';pf_nonce='$nonce';</script>";
    }

    /**
     * Not in use since 1.1.8rc1
     *
     * Enquing front side script/style.
     * Loading all or condional by post id
     * called once by add_action( 'wp_enqueue_scripts', array( $this, 'enqueFrontScripts' ) );
     */
    function enqueFrontScripts()
    {
        if (is_admin())
            return;
        
        global $bkpkFM, $post;
        if (! isset($bkpkFM->scripts['front']))
            return;
        
        foreach ($bkpkFM->scripts['front'] as $data) {
            $loadScript = true;
            if ($data['depends']) {
                if ($data['depends'] != $post->ID)
                    $loadScript = false;
            }
            
            if ($loadScript) {
                if ($data['type'] == 'js')
                    wp_enqueue_script($data['handle'], $data['url'], array(
                        'jquery'
                    ));
                elseif ($data['type'] == 'css')
                    wp_enqueue_style($data['handle'], $data['url']);
            }
        }
    }

    function addjQuery()
    {
        if (is_admin())
            return;
        wp_enqueue_script('jquery');
    }
}