<?php
/* Comments functions */
if(!function_exists('lcr_init')):
function lcr_init() {

	// read filters
	add_filter( 'comments_array','lcr_comments_array' , 10, 2 );
}
endif;
add_action( 'init', 'lcr_init' );
if(!function_exists('lcr_comments_array')):
function lcr_comments_array( $comments, $post_id ) {
	 $result = array();
	 foreach($comments as $comment){
		$commenter_id	=	$comment->user_id;
		if ( lcr_user_can_read_comments( get_current_user_id(), $post_id ,$commenter_id) ) {
			$result[] 	= 	$comment;
		}
	}
	return $result;
}
endif;
if(!function_exists('lcr_user_can_read_comments')):
function lcr_user_can_read_comments( $user_id = null, $post_id = null,$commenter_id = null ) {
		$result = true;

		if ( $user_id === null ) {
			$user_id = get_current_user_id();
		}
		if ( $commenter_id === null ) {
			$result = true;
		}
		$post_type = get_post_type( $post_id );
		//check post types for LifterLMS
		if($post_type == 'course' || 
		$post_type == 'lesson' || 
		$post_type == 'quiz'
		){
			//check for user groups 
			//  group logic: show comments to same membership group users
			if ( ! function_exists( 'wp_get_current_user' ) ) {
				include_once ABSPATH . 'wp-includes/pluggable.php';
			}
			$current_user = wp_get_current_user();
			$userid = $current_user->ID;
			if ( ! function_exists( 'get_user_memberships_data' ) ) {
				include_once (WP_PLUGIN_DIR . '/lifterlms/includes/class.llms.person.php');
			}
			$lms_person = new LLMS_Person();
			$my_groups = $lms_person->get_user_memberships_data($userid);
			$my_group = array_keys( $my_groups );
			$ass_users_groups = $lms_person->get_user_memberships_data($commenter_id);
			$ass_users_group = array_keys( $ass_users_groups );

			//skip if membership group is not the same group the user is in
			if(empty(array_diff($ass_users_group ,$my_group))) {
				$result = true;
			} else {
				$result = false;
			}
		}
		return $result;
	}
endif;

/**
 * Display the comment form via shortcode on singular pages
 * Remove the default comment form.
 * Hide the unwanted "Comments are closed" message with CSS.
 *
 * @see http://wordpress.stackexchange.com/a/177289/26350
 */

add_shortcode( 'llms_comments', function( $atts = array(), $content = '' ) {
	global $post;
	//print_r($post);
	if( is_singular() && post_type_supports( get_post_type(), 'comments' ) ) {
		ob_start();
		//comment_form();?>
		<div id="llms-comment-wrap">
			<div class="llms-comments-layer">
				<img src="https://cdnjs.cloudflare.com/ajax/libs/galleriffic/2.0.1/css/loader.gif" />
			</div>
			<div class="llms-commentlist clearfix">
				<h1 id="llms-comments" class="page_title"><?php comments_number( esc_html__( '0 Comments', 'llms' ), esc_html__( '1 Comment', 'llms' ), '% ' . esc_html__( 'Comments', 'llms' ) ); ?></h1>
				<?php
				//Gather comments for a specific page/post 
				$comments = get_comments(array(
					'post_id' => $post->ID,
					'status' => 'approve' //Change this to the type of comments to be displayed
				));

				//Display the list of comments
				wp_list_comments(array(
					'per_page' => -1, //Allow comment pagination
					'reverse_top_level' => true , //Show the oldest comments at the top of the list
					'callback'=>'et_custom_comments_display'
				), $comments);
				?>
			</div>
			<?php 

			//show comment form 
			comments_template();
			?>
		</div>
		<style>
			#comments {display:none;}
			#llms-comment-wrap #comments{display:block}
			.llms-commentlist li{list-style:none;}
			.llms-comments-layer{
				width:100%;
				text-align: center;
				position: absolute;
				z-index: 99999;
				background: lightgrey;
				opacity: 0.8;
				height: 100%;
				display:none;
			}
			.llms-comments-layer img{bottom:15%;position:absolute;}
			.llms-comment-error{color:red;}
			.llms-comment-success{color:green;}
		</style>
		<?php 
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-accordion' );
		wp_enqueue_script( 'jquery-effects-shake' );
		wp_enqueue_script( 'jquery-effects-bounce' );
		?>
		<script>
			jQuery(document).ready(function($){
				var llms_comments_layer = 0;
				llms_comments_layer = $('.llms-comments-layer');
				$('#submit').click(function(e){
					console.log('just clicked submit');
					e.preventDefault();
					var form = jQuery('#commentform');
			    	var comment = jQuery('#llms-comment').val(); 
					//shake button if no data added 
					if(comment=='Comment'){
						$('#llms-comment').after('<span class="llms-comment-error">Please enter a comment.</span>');
						$('.llms-comment-error').delay(5000).slideUp( "slow");
						//$( this ).effect( "bounce", "slow" );
						return false;
					}
					if(comment==''){
						$('#llms-comment').after('<span class="llms-comment-error">Please enter a comment.</span>');
						$('.llms-comment-error').delay(5000).slideUp( "slow");
						//$( this ).effect( "bounce", "slow" );
						return false;
					}
					//show layer 
					if(comment!='Comment')
					llms_comments_layer.slideDown();
			 		function llms_comments_extract(html) {
						var regex = new RegExp('<body[^>]*>((.|\n|\r)*)</body>', 'i');
						return jQuery(regex.exec(html)[1]);
					}
					jQuery.ajax({
						url: form.attr('action'),
						type: "POST",
						data: form.serialize(),
						success: function (data) {
							//hide layer 
							llms_comments_layer.slideUp();
							var newComments = llms_comments_extract(data).find('.llms-commentlist').html();
							var oldComments = jQuery('.llms-commentlist');
							if (oldComments.length > 0 && newComments.length > 0) {
								// Update comments
								$('#commentform')[0].reset();
								console.log( "success new messages");
								oldComments.html('');
								oldComments.html(newComments);
								//success message 
								$('#comment').after('<span class="llms-comment-success">Comment added successfully.</span>');
								$('.llms-comment-success').delay(5000).slideUp( "slow");
								//oldComments.replaceWith(newComments);
							} else {
								// Fallback (page reload) if something went wrong
								location.reload(); 
							}
						},
						error: function (jqXhr, textStatus, errorThrown) {
							//hide layer 
							llms_comments_layer.slideUp();
							var error = llms_comments_extract(jqXhr.responseText).html();
							alert( "error"+error);
							console.log( "error"+error);
						}
					});
				}); //endof  click 
			}); //end of ready 
		</script>
		<?php 
		return ob_get_clean();
	}
	return '';
}, 10, 2 );