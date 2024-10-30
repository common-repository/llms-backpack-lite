<?php
global $bkpkFM;
// Expected: $settings, $forms, $fields, $default
?>

<div class="wrap">
	<h1><?php _e( 'lifterlms Back-Pack Settings', $bkpkFM->name ); ?></h1>
    <?php do_action( 'bkpk_admin_notice' ); ?>
    <div id="dashboard-widgets-wrap">
		<div class="metabox-holder">
			<div id="bkpk_admin_content">
                <?php
                if ($bkpkFM->isPro)
                    $bkpkFM->renderPro("activationForm", null, "settings");
                
                $isPro = $bkpkFM->isPro();
                $title = array(
                    'general' => __('General', $bkpkFM->name),
                    'login' => __('Login', $bkpkFM->name),
                    'registration' => __('Registration', $bkpkFM->name),
                    'redirection' => $isPro ? __('Redirection', $bkpkFM->name) : '<span class="pf_blure">' . __('Redirection', $bkpkFM->name) . '</span>',
                    'profile' => $isPro ? __('Backend Profile', $bkpkFM->name) : '<span class="pf_blure">' . __('Backend Profile', $bkpkFM->name) . '</span>'
                );
                ?>

                <form id="bkpk_settings_form" action="" method="post"
					onsubmit="umUpdateSettings(this); return false;">
					<div id="bkpk_settings_tab">
						<ul>
							<li><a href="#bkpk_settings_general"><?php echo $title['general']; ?></a></li>
							<li><a href="#bkpk_settings_login"><?php echo $title['login']; ?></a></li>
							<li><a href="#bkpk_settings_registration"><?php echo $title['registration']; ?></a></li>
							<?php if($isPro) {?>
							<li><a href="#bkpk_settings_redirection"><?php echo $title['redirection']; ?></a></li>
							<li><a href="#bkpk_settings_backend_profile"><?php echo $title['profile']; ?></a></li>
							<?php }?>
							<li><a href="#bkpk_settings_text"><?php _e( 'Text', $bkpkFM->name ); ?></a></li>
                        <?php do_action( 'llms_bkpk_settings_tab' ); ?>
                	</ul>

						
                        <?php
                        echo '<div id="bkpk_settings_general">';
                        echo $bkpkFM->renderPro("generalSettings", array(
                            'general' => isset($settings['general']) ? $settings['general'] : $default['general']
                        ), "settings");
                        
                        echo $bkpkFM->renderPro("generalProSettings", array(
                            'general' => isset($settings['general']) ? $settings['general'] : $default['general']
                        ), "settings");
                        echo '</div>';
                        
                        echo '<div id="bkpk_settings_login">';
                        echo $bkpkFM->renderPro("loginSettings", array(
                            'login' => isset($settings['login']) ? $settings['login'] : $default['login']
                        ), "settings");
                        echo '</div>';
                        
                        echo '<div id="bkpk_settings_registration">';
                        echo $bkpkFM->renderPro("registrationSettings", array(
                            'registration' => isset($settings['registration']) ? $settings['registration'] : $default['registration']
                        ), "settings");
                        echo '</div>';
                        
                        if ($isPro) {
                            echo '<div id="bkpk_settings_redirection">';
                            echo $bkpkFM->renderPro("redirectionSettings", array(
                                'redirection' => isset($settings['redirection']) ? $settings['redirection'] : $default['redirection']
                            ), "settings");
                            echo '</div>';
                        }
                        
                        if ($isPro) {
                            echo '<div id="bkpk_settings_backend_profile">';
                            echo $bkpkFM->renderPro("backendProfile", array(
                                'backend_profile' => isset($settings['backend_profile']) ? $settings['backend_profile'] : $default['backend_profile'],
                                'forms' => $forms,
                                'fields' => $fields
                            ), "settings");
                            echo '</div>';
                        }
                        
                        echo '<div id="bkpk_settings_text">';
                        echo $bkpkFM->renderPro("textSettings", array(
                            'text' => isset($settings['text']) ? $settings['text'] : array()
                        ), "settings");
                        echo '</div>';
                        
                        do_action('llms_bkpk_settings_tab_details');
                        ?>

					</div>

                <?php
                echo $bkpkFM->nonceField();
                echo $bkpkFM->createInput("save_field", "submit", array(
                    "value" => __("Save Changes", $bkpkFM->name),
                    "id" => "update_settings",
                    "class" => "button-primary",
                    "enclose" => "p"
                ));
                ?>

                </form>

			</div>

			<div id="bkpk_admin_sidebar">
                <?php
                $panelArgs = [
                    'panel_class' => 'panel-default'
                ];
                echo $bkpkFM->metaBox(__('Get started', $bkpkFM->name), $bkpkFM->boxHowToUse());
                /*if (! @$bkpkFM->isPro) {
                    echo $bkpkFM->metaBox(__('Live Demo', $bkpkFM->name), $bkpkFM->boxLiveDemo());
                    echo $bkpkFM->metaBox(__('lifterlms Back-Pack Pro', $bkpkFM->name), $bkpkFM->boxGetPro());
                }*/
                echo $bkpkFM->metaBox('Shortcodes', $bkpkFM->boxShortcodesDocs());
                // echo $bkpkFM->metaBox( __( 'Tips', $bkpkFM->name ), $bkpkFM->boxTips(), false, false);
                ?>
            </div>
		</div>
	</div>
</div>


<script>
jQuery(function() {
    jQuery('.bkpk_dropme').sortable({
        connectWith: '.bkpk_dropme',
        cursor: 'pointer'
    }).droppable({
        accept: '.button',
        activeClass: 'bkpk_highlight'
    });

    jQuery("#bkpk_settings_tab").tabs();
    jQuery("#loggedin_profile_tabs").tabs();
    jQuery("#redirection_tabs").tabs();

    umSettingsToggleCreatePage();
    umSettingsToggleError();
    jQuery('#bkpk_login_login_page, #bkpk_login_resetpass_page, #bkpk_registration_email_verification_page').change(function() {
        umSettingsToggleCreatePage();
        umSettingsToggleError();
    });

    umSettingsRegistratioUserActivationChange();

});
</script>
