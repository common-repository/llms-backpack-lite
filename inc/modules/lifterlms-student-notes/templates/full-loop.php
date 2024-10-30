 <!-- collapse template-->
 <!-- div class="all-notes-conainter" -->
 <div id="accordion-Historical" >
 	<h3><?php _e('All My Entries','lifterlms_student_notes'); ?> </h3>
 	<div class="accordian-content">
 		<?php 
 		include_once(LLMS_S_N_TEMPLATE_DIR . 'ajax/full-loop.php');
		?>
		<?php //echo $author_id.'-'.$current_user_id; ?>
	</div><!-- end of accordian content  -->
</div><!-- end of accordian -->
<!-- /div -->

