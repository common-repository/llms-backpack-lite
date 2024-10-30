
<?php global $bkpkFM; ?>

<div class="wrap">
	<div id="icon-edit-pages" class="icon32 icon32-posts-page">
		<br />
	</div>
	<h2><?php _e( 'Forms Editor', $bkpkFM->name );?> <span
			class="add-new-h2 bkpk_add_button" onclick="umNewForm(this);"><?php _e( 'New Form', $bkpkFM->name );?></span>
	</h2>   
    <?php do_action( 'bkpk_admin_notice' ); ?>
    <div id="dashboard-widgets-wrap">
		<div class="metabox-holder">
			<div id="bkpk_admin_content">
				<form id="bkpk_forms_form" action="" method="post"
					onsubmit="umUpdateForms(this); return false;">
                    <?php
                    echo $bkpkFM->createInput('save_field', 'submit', array(
                        'value' => __('Save Changes', $bkpkFM->name),
                        'class' => 'pf_save_button  button-primary'
                    ));
                    ?>
                    <br />
					<br />
					<div id="bkpk_fields_container">                 
                        <?php
                        
                        // $bkpkFM->render( 'form', array( 'fields'=>$fields, 'id'=>1 ) );
                        $i = 0;
                        if ($forms) {
                            foreach ($forms as $form) {
                                $i ++;
                                $form['id'] = $i;
                                $bkpkFM->render("form", array(
                                    "id" => $i,
                                    "form" => $form,
                                    "fields" => $fields
                                ));
                            }
                        }
                        ?>                                     
                    </div>
                    <?php
                    echo $bkpkFM->nonceField();
                    
                    echo $bkpkFM->createInput('save_field', 'submit', array(
                        'value' => __('Save Changes', $bkpkFM->name),
                        'class' => 'pf_save_button  button-primary'
                    ));
                    
                    echo "&nbsp;&nbsp;&nbsp;";
                    
                    echo $bkpkFM->createInput('new_form', 'button', array(
                        'value' => __('New Form', $bkpkFM->name),
                        'class' => '  button-primary',
                        'onclick' => 'umNewForm(this)'
                    ));
                    ?>
                </form>
				<input type="hidden" id="form_count" value="<?php echo $i; ?>" />
			</div>


			<div id="bkpk_admin_sidebar">                            
                <?php
                echo $bkpkFM->metaBox(__('3 steps to get started', $bkpkFM->name), $bkpkFM->boxHowToUse());
                if (! @$bkpkFM->isPro)
                    echo $bkpkFM->metaBox(__('lifterlms Back-Pack Pro', $bkpkFM->name), $bkpkFM->boxGetPro());
                echo $bkpkFM->metaBox('Shortcodes', $bkpkFM->boxShortcodesDocs());
                // echo $bkpkFM->metaBox( __( 'Tips', $bkpkFM->name ), $bkpkFM->boxTips(), false, false);
                ?>
            </div>
		</div>
	</div>
</div>

<script>
jQuery(function() {
    //jQuery( ".draggable" ).draggable({  revert: "valid" });
    /*jQuery( ".droppable" ).droppable({
			drop: function( event, ui ) {
			     alert(2);
				jQuery( this )
					.addClass( "ui-state-highlight" )
					.find( "p" )
						.html( "Dropped!" );
			}        
    });
    jQuery( ".sortable" ).sortable();
    jQuery( "#bkpk_admin_sidebar" ).sortable();
    jQuery( "#fields_form").validationEngine();*/
    
    
//jQuery(".form_tabs").tabs();  
    
 

jQuery( "#bkpk_fields_container" ).sortable({
    handle: '.hndle'
}); 

jQuery( "#bkpk_admin_sidebar" ).sortable({
    handle: '.hndle'
});   

jQuery('.bkpk_dropme').sortable({
    connectWith: '.bkpk_dropme',
    cursor: 'pointer'
}).droppable({
    accept: '.button',
    activeClass: 'bkpk_highlight',
    drop: function(event, ui) {
        //console.log( jQuery(this).html() );
        //alert( jQuery( this.parents() ) );
       /* var $li = jQuery('<div>').html('List ' + ui.draggable.html());
        $li.appendTo(this);*/
    }
});    

//alert( jQuery(".bkpk_selected_fields > div").size() );
//jQuery(".bkpk_selected_fields").each(function(d){alert( jQuery(this).html );});
   
    
    
});
</script>
