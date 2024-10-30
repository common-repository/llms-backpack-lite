<?php
//new Note form
$bkpk_notifinst = \llms_bkpk\Config::get_settings_value( 'llms-bkpk-student-notes-notify-instructor-text','StudentNotes');
$bkpk_notify_enable = \llms_bkpk\Config::get_settings_value( 'llms-bkpk-student-notes-notify-enable','StudentNotes');

$bkpk_hidesntb = \llms_bkpk\Config::get_settings_value( 'llms-bkpk-student-notes-hide-toolbar','StudentNotes');
	if ($bkpk_hidesntb ) { ?>
	<style type="text/css">#wp-llms_note_text-editor-container .mce-top-part{display:none;}</style>
	<?php }

?>
  <div class="student-notes-layer">
  <img src="https://cdnjs.cloudflare.com/ajax/libs/galleriffic/2.0.1/css/loader.gif" />
  </div>
 <form action="#"  id="student-notes-form" method="post" enctype="multipart/form-data" data-form-id='stnotes'>
	<div class="form-group">
		<!--<label>  <?php _e('Note','lifterlms_student_notes'); ?> </label> -->
		
		<?php 
		$content   = '';
		$editor_id = 'llms_note_text';
		$settings  = array( 'media_buttons' => false,'textarea_rows'=>10,'required'=>'required','quicktags' => false );
		 
		wp_editor( $content, $editor_id, $settings );
		?>
		<div id="post-status-info"></div>
	</div>
    <?php if($bkpk_notify_enable){ ?>
	<div class=" checkbox">
			<label class="notify-instructor"> 
				<input class="notify-instructor"  type="checkbox" name="llms_notify_admin"  >
				<?php echo $bkpk_notifinst; ?></label>
			</label>
	</div>
    <?php } ?>
	<input type="hidden" name="related_post_id"  value="<?php the_ID(); ?>" />
	<input type="hidden" name="related_post_type"  value="<?php echo get_post_type(get_the_ID()); ?>" />
	<input type="hidden" name="related_user_id"  value="<?php echo get_current_user_id(); ?>" />
	<?php wp_nonce_field( 'my_image_upload', 'my_image_upload_nonce' ); ?>
	
	<button type="submit" id="notes-submit-btn" class="btn btn-primary"><?php _e('Save Note','lifterlms_student_notes'); ?></button>
	
 </form><script type="text/javascript">
 jQuery(document).ready(function ($) {
		// inject the html
		jQuery("#post-status-info").after("<div class='sum-of-chars' style='border: 1px solid #e5e5e5; border-top:0; display: block; background-color: #F7F7F7; padding: 0.3em 0.7em;'><?php _e ('Sum of characters:', 'lifterlms_student_notes'); ?> <b id=\"ilc_excerpt_counter\"></b> <div id=\"ilc_excerpt_counter_1\"><?php _e ('Number of selected characters', 'lifterlms_student_notes'); ?>: <b id=\"ilc_live_counter\">()</b></div></div>");
		// count on load
		window.onload = function () {
			//setTimeout(function() {
			setInterval(function() {
				//var tiny_text_data	=	tinymce.get('content');
				var tiny_text_data	=	tinymce.get('llms_note_text').getContent();
				if(tiny_text_data){
					//cont = tiny_text_data.getContent().replace(/(<[a-zA-Z\/][^<>]*>|\[([^\]]+)\])|(\s+)/ig,'');
					cont = tiny_text_data.replace(/(<[a-zA-Z\/][^<>]*>|\[([^\]]+)\])|(\s+)/ig,'');
					jQuery("#ilc_excerpt_counter").text(cont.length);
				}
				
			}, 300);
		}
	});
	</script>
	<style>
	#ilc_excerpt_counter_1{display:none;}
	</style>