<?php
global $bkpkFM;
// Expected: $data, $roles
?>

<div class="wrap">
	<h2><?php _e( 'E-mail Notification', $bkpkFM->name ); ?></h2>   
    <?php do_action( 'bkpk_admin_notice' ); ?>
    <div id="dashboard-widgets-wrap">
		<div class="metabox-holder">
			<div id="bkpk_admin_content">
                <?php echo $bkpkFM->proDemoImage( 'email-notification.png' ); ?>
            </div>

			<div id="bkpk_admin_sidebar">                            
                <?php
                $variable = null;
                $variable .= "<strong>" . __('Site Placeholder', $bkpkFM->name) . "</strong><p>";
                $variable .= "%site_title%, ";
                $variable .= "%site_url%, ";
                $variable .= "%login_url%, ";
                $variable .= "%logout_url%, ";
                $variable .= "%activation_url%, ";
                $variable .= "%email_verification_url%";
                $variable .= "</p>";
                
                $variable .= "<strong>" . __('User Placeholder', $bkpkFM->name) . "</strong><p>";
                $variable .= "%ID%, ";
                $variable .= "%user_login%, ";
                $variable .= "%user_email%, ";
                $variable .= "%password%, ";
                $variable .= "%display_name%, ";
                $variable .= "%first_name%, ";
                $variable .= "%last_name%";
                $variable .= "</p>";
                
                $variable .= "<strong>" . __('Custom Field', $bkpkFM->name) . "</strong><p>";
                $variable .= "%your_custom_llms_bkpk_key%</p>";
                
                $variable .= "<p><em>(" . __("Placeholder will be replaced with the relevant value when used in email subject or body.", $bkpkFM->name) . ")</em></p>";
                
                $panelArgs = [
                    'panel_class' => 'panel-default'
                ];
                
                if (empty($bkpkFM->isPro)) {
                    echo BPKPFieldManager\panel(__('Live Demo', $bkpkFM->name), $bkpkFM->boxLiveDemo(), $panelArgs);
                    echo BPKPFieldManager\panel(__('lifterlms Back-Pack Pro', $bkpkFM->name), $bkpkFM->boxGetPro(), $panelArgs);
                }
                echo BPKPFieldManager\panel(__('Placeholder', $bkpkFM->name), $variable, $panelArgs);
                echo BPKPFieldManager\panel(__('Shortcodes', $bkpkFM->name), $bkpkFM->boxShortcodesDocs(), $panelArgs);
                ?>
            </div>
		</div>
	</div>
</div>
