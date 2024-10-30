<?php
global $bkpkFM;
// Expected: $actionTypes, $formsList, $roles
?>
<div id="bkpk_shortcode_popup" style="display: none;">
    <?php
    
    echo $bkpkFM->createInput('action_type', 'select', array(
        'value' => '',
        'label' => __('Action Type', $bkpkFM->name),
        'id' => 'bkpk_action_type',
        'class' => 'bkpk_input',
        'label_class' => 'pf_label',
        'after' => ' <span>(' . __('Required', $bkpkFM->name) . ')</span>',
        'enclose' => 'p'
    ), $actionTypes);
    
    echo $bkpkFM->createInput('form_name', 'select', array(
        'value' => '',
        'label' => __('Form Name', $bkpkFM->name),
        'id' => 'bkpk_form_name',
        'class' => 'bkpk_input',
        'label_class' => 'pf_label',
        'after' => ' <span id="bkpk_is_form_required"></span>',
        'enclose' => 'p'
    ), $formsList);
    
    echo '<div id="bkpk_rolebased_container" style="display:none">';
    
    echo $bkpkFM->createInput('bkpk_rolebased_link', 'checkbox', array(
        'label' => '<strong>' . __('Use role based user profile (advanced)', $bkpkFM->name) . '</strong>',
        'id' => 'bkpk_rolebased_link',
        'enclose' => 'p'
    ));
    
    echo '<div id="bkpk_rolebased_content" style="display:none">';
    echo '<p><em>(' . __('Assign form to user role. Leave blank for using default form', $bkpkFM->name) . ')</em></p>';
    foreach ($roles as $roleName => $roleTitle) {
        echo $bkpkFM->createInput("rolebased[$roleName]", 'select', array(
            'value' => '',
            'label' => $roleTitle,
            'id' => 'bkpk_rolebased_' . $roleName,
            'class' => 'bkpk_rolebased',
            'label_class' => 'bkpk_label_left',
            'enclose' => 'div'
        ), $formsList);
    }
    echo '</div>';
    echo '</div>';
    
    echo $bkpkFM->createInput('', 'button', array(
        'value' => __('Insert Shortcode', $bkpkFM->name),
        'id' => 'bkpk_generator_button',
        'class' => 'button-primary',
        'enclose' => 'p'
    ));
    
    ?>
</div>

<?php if ( ! $bkpkFM->isPro() ){ ?>
<script type="text/javascript">
        jQuery(document).ready(function(){
            jQuery("#bkpk_action_type option").each(function(){
                if( jQuery(this).text() == "login" )
                    jQuery(this).attr("disabled","disabled");
            });
        });
    </script>
<?php } ?>


<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery("#bkpk_action_type").change(function(){
            if( jQuery(this).val() == '' )
                jQuery("#bkpk_is_form_required").text("");
            else if( jQuery(this).val() == 'login' )
                jQuery("#bkpk_is_form_required").text("(<?php _e( 'Optional', $bkpkFM->name ); ?>)");
            else
                jQuery("#bkpk_is_form_required").text("(<?php _e( 'Required', $bkpkFM->name ); ?>)");
            
            if( jQuery(this).val() == 'profile' || jQuery(this).val() == 'profile-registration' || jQuery(this).val() == 'public' )
                jQuery("#bkpk_rolebased_container").fadeIn();
            else
                jQuery("#bkpk_rolebased_container").fadeOut();
        });
        
        jQuery("#bkpk_rolebased_link").click(function(){
            if( jQuery(this).is(":checked") )
                jQuery("#bkpk_rolebased_content").fadeIn();
            else
                jQuery("#bkpk_rolebased_content").fadeOut();
        })
             
        jQuery("#bkpk_generator_button").click(function(){
            if( !jQuery("#bkpk_action_type").val() ){
                alert( 'Action Type is required!' );return;
            }

            if( !(jQuery("#bkpk_action_type").val() == 'login') ){
                if( !jQuery("#bkpk_form_name").val() ){
                    alert( 'Form Name is required for ' + jQuery("#bkpk_action_type").val() + '!' );return;
                }
            }
                
            
            shortcode = '[llms-bkpk type="' + jQuery("#bkpk_action_type").val() + '"';
            if( jQuery("#bkpk_form_name").val() )
                shortcode += ' form="' + jQuery("#bkpk_form_name").val() + '"';
            
            var diff = '';
            if( jQuery("#bkpk_action_type").val() == 'profile' || jQuery("#bkpk_action_type").val() == 'profile-registration' || jQuery("#bkpk_action_type").val() == 'public' ){
                if( jQuery("#bkpk_rolebased_link").is(":checked") ){
                    jQuery(".bkpk_rolebased").each(function(){
                        if( jQuery(this).val() ){
                            diff += jQuery(this).attr("id").replace("bkpk_rolebased_", '') + '=' + jQuery(this).val() + ', ';
                        }
                    });
                }
            }
            
            if( diff )
                shortcode += ' diff="' + diff.trim().replace(/,$/, '') + '"';

            shortcode += ']';
            tinyMCE.activeEditor.execCommand('mceInsertContent', false, shortcode);
            //tinyMCE.execInstanceCommand("elm1","mceInsertContent",false,shortcode);
            tb_remove();
        });
    });
</script>