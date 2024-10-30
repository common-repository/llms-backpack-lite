<?php
global $bkpkFM;
// Expected $csvCache, $maxSize
?>

<div class="wrap">
	<div id="icon-users" class="icon32 icon32-posts-page">
		<br />
	</div>
	<h2><?php _e( 'Export & Import', $bkpkFM->name ); ?></h2>   
    <?php do_action( 'bkpk_admin_notice' ); ?>
    <div id="dashboard-widgets-wrap">
		<div class="metabox-holder">
			<div id="bkpk_admin_content">
                <?php echo $bkpkFM->proDemoImage( 'export-import.png' ); ?>                                          
            </div>

			<div id="bkpk_admin_sidebar">                            
                <?php
                $panelArgs = [
                    'panel_class' => 'panel-default'
                ];
                
                if (empty($bkpkFM->isPro)) {
                    echo BPKPFieldManager\panel(__('Live Demo', $bkpkFM->name), $bkpkFM->boxLiveDemo(), $panelArgs);
                    echo BPKPFieldManager\panel(__('lifterlms Back-Pack Pro', $bkpkFM->name), $bkpkFM->boxGetPro(), $panelArgs);
                }
                echo BPKPFieldManager\panel(__('Shortcodes', $bkpkFM->name), $bkpkFM->boxShortcodesDocs(), $panelArgs);
                ?>
            </div>
		</div>
	</div>
</div>
