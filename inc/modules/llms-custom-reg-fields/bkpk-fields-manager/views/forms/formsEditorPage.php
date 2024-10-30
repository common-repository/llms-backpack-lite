<?php
namespace BPKPFieldManager;

global $bkpkFM;
?>

<div class="wrap">
	<h1><?php _e( 'Forms', $bkpkFM->name );?>
		<a href="?page=llmsbkpk&action=new" class="add-new-h2"><?php _e( 'Add New', $bkpkFM->name ); ?></a>
	</h1>   
    <?php do_action( 'bkpk_admin_notice' ); ?>
    <div id="dashboard-widgets-wrap">
		<div class="metabox-holder">
			<div id="bkpk_admin_content">
                <?php
                if (! class_exists('WP_List_Table')) {
                    require_once (ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
                }
                $listTable = new FormsListTable();
                $listTable->prepare_items();
                $listTable->display();
                ?>                                          
            </div>

			<div id="bkpk_admin_sidebar">                            
                <?php
                $panelArgs = [
                    'panel_class' => 'panel-default'
                ];
                echo panel(__('Get started', $bkpkFM->name), $bkpkFM->boxHowToUse(), $panelArgs);
                /*if (empty($bkpkFM->isPro)) {
                    echo panel(__('Live Demo', $bkpkFM->name), $bkpkFM->boxLiveDemo(), $panelArgs);
                    echo panel(__('lifterlms Back-Pack Pro', $bkpkFM->name), $bkpkFM->boxGetPro(), $panelArgs);
                }*/
                echo panel(__('Shortcodes', $bkpkFM->name), $bkpkFM->boxShortcodesDocs(), $panelArgs);
                echo panel(__('Useful Links', $bkpkFM->name), $bkpkFM->boxLinks(), $panelArgs);
                ?>
            </div>
		</div>
	</div>
</div>

<script>
jQuery('#bkpk_admin_sidebar .panel-collapse').removeClass('in');
jQuery('#bkpk_admin_sidebar .panel-collapse').first().addClass('in');
</script>


