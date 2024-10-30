 <!-- ajax loop  template-->
  
  
 <?php 
 
/* echo count($posts_array); */
	if($posts_array){
		//$i=10;
	foreach ( $posts_array as $post1 )  { ?>
	
	<?php //print_r($post1); 
	//add_post_meta($post1->ID, 'x_wp_sort_order' , $i);
	//$i++;
	?>
	 <div class="single-note">
		
		<div class="year">
		<?php _e('Added:','lifterlms_student_notes'); ?>
		<?php echo $post1->post_date; ?>
	<?php //echo human_time_diff( get_the_time('U'), current_time('timestamp') ) . ' ago'; ?>

		</div>
		<div class="content">
		<?php $content = wpautop($post1->post_content); echo $content; ?>
		 
		</div>
		<?php $was_notified = get_post_meta($post1->ID,'llms_notify_admin', true); ?>
		
		<?php if($was_notified==1){ ?>
			<div class="">Instructor was notified.</div>
		<?php } ?>
		<?php $admin_response = get_post_meta($post1->ID,'admin_response', true); ?>
		
		<?php if($admin_response){ ?>
			<div class="admin_response"><?php echo $admin_response; ?></div>
		<?php } ?>
						
		<?php  if($user_id == $post1->post_author ){ ?>
		 
		 <div class="del">
			<a class="del-note" data-note-id="<?php echo $post1->ID; ?>" onclicki=" return (confirm('Are you sure.This can not be undone?'))" href="?post_id_del=<?php echo $post1->ID; ?>">Delete</a>
			
			<a class="dnld-note" href="?note_id=<?php echo $post1->ID; ?>&download_note=1">Download</a>
		 
		 </div>
				
		<?php } ?>
	
 	 </div>
	<?php } }else{?>
	<div class="alert alert-info">There are no notes added for this <?php echo get_post_type(get_the_ID()); ?>. </div>
	<?php } ?>
<?php //echo $author_id.'-'.$current_user_id; ?>
 
 
 

