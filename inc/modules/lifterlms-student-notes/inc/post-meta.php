<?php
 add_action( 'add_meta_boxes', 'llms_notes_cd_meta_box_add' );
function llms_notes_cd_meta_box_add()
{
    add_meta_box( 'my-meta-box-id', 'Notes Meta', 'llms_notes_cd_meta_box_cb', 'llms_student_notes', 'normal', 'high' );
}

 
 function llms_notes_cd_meta_box_cb()
{
    // $post is already set, and contains an object: the WordPress post
    global $post;

    $text = get_post_meta( $post->ID, 'my_meta_box_text', true);
   $post_id  = $post->ID;
     
    // We'll use this nonce field later on when saving.
    wp_nonce_field( 'my_meta_box_nonce', 'meta_box_nonce' );
    ?>
    
      <p>
        <label for="my_meta_box_text">Type  : </label>
		<?php 
		$related_post_type =  get_post_meta( $post_id, 'related_post_type', true );
					echo $related_post_type;
					?>
	</p>
      <p>
        <label for="my_meta_box_text">Title :  </label>
		<?php 
		$related_post_id = absint( get_post_meta( $post_id, 'related_post_id', true ) );
			$edit_link = get_edit_post_link( $related_post_id );
			$edit_link = get_the_permalink( $related_post_id );

			if ( ! empty( $related_post_id ) ) {
				printf( __( '<a href="%1$s">%2$s</a>' ), $edit_link, get_the_title( $related_post_id ) );
			}
					?>
	</p>
      <p>
        <label for="my_meta_box_text">Notify Instructor :  </label>
		<?php 
		 $notify =  get_post_meta( $post_id, 'llms_notify_admin', true );
			if($notify == 1){
				echo "Yes";
			}else{
				echo "No";
			}

			
					?>
	</p> 
	<p>
        <label id="Respond" for="my_meta_box_text">Instructor Response :  </label><br/>
		<?php 
		 $admin_response =  get_post_meta( $post_id, 'admin_response', true );
		 $content = $admin_response;
			$editor_id = 'admin_response';
		$settings  = array( 'media_buttons' => false,'textarea_rows'=>10,'required'=>'required','quicktags' => false );
		 
		wp_editor( $content, $editor_id, $settings );
		
			
					?>
	</p>
     
    
    <?php    
}
add_action( 'save_post', 'llms_notes_meta_box_save' );
function llms_notes_meta_box_save( $post_id )
{
    // Bail if we're doing an auto save
    if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
     
    // if our nonce isn't there, or we can't verify it, bail
    if( !isset( $_POST['meta_box_nonce'] ) || !wp_verify_nonce( $_POST['meta_box_nonce'], 'my_meta_box_nonce' ) ) return;
     
 
     
    // Make sure your data is set before trying to save it
    if( isset( $_POST['my_meta_box_text'] ) )
        update_post_meta( $post_id, 'my_meta_box_text', $_POST['my_meta_box_text']);
	
   //update admin response   
	 update_post_meta( $post_id, 'admin_response', $_POST['admin_response']);
         
   
}