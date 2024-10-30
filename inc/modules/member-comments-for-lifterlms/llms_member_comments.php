<?php

/**
 * Lifter lms Back pack comments and sync module
 *
 **/

//add shorocode
function bkpk_llms_comments_shortcode_catcher( $atts ) {
	$atts = shortcode_atts( array(
		'tb' => 0,
		'inst' => 1,
		'height' => 500,
	), $atts, 'llms_comments' );
	extract($atts);
	global $post;
	$post_id = $post->ID;
	if( is_singular() && post_type_supports( get_post_type(), 'comments' ) ) {
		ob_start();
	?>
		
		<div id="llms-comment-wrap-<?php echo $inst; ?>" class=" bkpk-comments-container llms-comment-wrap-<?php echo $inst; ?> bkpk-comments-instance-<?php echo $inst; ?>" data-instance='<?php echo $inst; ?>' data-tb='<?php echo $tb; ?>' data-post-id='<?php echo $post_id; ?>'>
			<div class="llms-comments-layer-<?php echo $inst; ?>">
				<img src="https://cdnjs.cloudflare.com/ajax/libs/galleriffic/2.0.1/css/loader.gif" />
			</div>
			<h1 id="llms-comments-number-<?php echo $inst; ?>" class="page_title llms-comments-number-<?php echo $inst; ?>"><span id='bkpk-inst-<?php echo $inst; ?>-commnts-counts'><?php echo bkpk_get_comments_count($post_id, $inst); ?></span> Comment(s)</h1>
				
			<?php
			echo bkpk_get_comments_full_html($post_id ,$inst ,$height );
			?>
			<div class='comment-form'>
			<form name='comment-form'>
			<p class="comment-form-comment">
				<label for="comment">Comment</label>
				<!---->
				<?php 
				//tool box editor 
				if($tb){
				wp_editor(
							'',
							'bkpk-comment-'.$inst,
							array(
							  'media_buttons' => false,
							  'textarea_rows' => 8,
							  'tabindex' => 4,
							  'tinymce' => array(
								'theme_advanced_buttons1' => 'bold,italic,ul,pH,temp',
							  ),
							));
				}else{	
				//normal textarea 
				?>
				<textarea id="<?php echo  'bkpk-comment-'.$inst; ?>" name="comment" cols="45" rows="8" maxlength="65525" required="required"></textarea>
				<?php  } ?>
					
				
			</p>
			<p class="bkpk-comment-submit">	
				<input name="submit" type="submit" id="submit" class="submit" value="Post Comment"> 
				<input type="hidden" name="comment_post_ID" value="<?php echo $post_id ;?>" id="comment_post_ID">
				<input type="hidden" name="comment_parent" id="comment_parent" value="0">
				<input type="hidden" name="inst" id="inst" value="<?php echo $inst; ?>">
			</p>
			</form>
			</div>

		</div>
		
		<style>
			/* #comments {display:none;} */
			#llms-comment-wrap #comments{display:block}
			#llms-comment-wrap-<?php echo $inst; ?> #comments{display:block}
			
			.llms-commentlist li{list-style:none;}
			.llms-comments-layer-<?php echo $inst; ?>{
				width:100%;
				text-align: center;
				position: absolute;
				z-index: 99999;
				background: lightgrey;
				opacity: 0.8;
				height: 100%;
				display:none;
			}
			.llms-comments-layer-<?php echo $inst; ?> img{bottom:15%;position:absolute;}
			.llms-comment-error{color:red;}
			.llms-comment-success{color:green;}
			.reply a {
				color: #2d2929;
				border: 1px solid gray;
				padding: 5px;
				cursor: pointer;
				font-weight: bold;
				box-shadow: 0 0 2px grey;
				background: #16b5af;  
			}
			 
			span.cancel-reply {
				color: red;
				border: 1px solid gray;
				padding: 5px;
				cursor: pointer;
				font-weight: bold;
				box-shadow: 0 0 2px grey;
				background: azure;    margin-left: 10px;
			}
			span.cancel-reply:hover {
				background: lightpink;
			}
			li.comment {
				margin-top: 15px;
				border-top: 0.5px dashed;
				padding-top: 20px;
			}
			.llms-comment-wrap-<?php echo $inst; ?> img.avatar.avatar-32.photo {
				min-width: 100px;
			}
			.llms-comment-wrap-<?php echo $inst; ?> .children img.avatar.avatar-32.photo {
				min-width: 50px;
			}
			.comment .comment-form label {
					margin-top: 25px;
				}
			/* over flow css */
			ul.llms-commentlist.comment-list.clearfix {overflow-y:auto;height:auto;max-height:500px;}
		</style>
		<?php 
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-accordion' );
		wp_enqueue_script( 'jquery-effects-shake' );
		wp_enqueue_script( 'jquery-effects-bounce' );
		$comments_per_page = get_option('comments_per_page');
		?>
		<script>
		 var comments_per_page  = <?php  echo $comments_per_page; ?>;
		<?php include_once('comments_js.js'); ?>
		</script>
		<?php 
		return ob_get_clean();
	}
	return '';
	 
}
add_shortcode( 'llms_comments', 'bkpk_llms_comments_shortcode_catcher' );

//receive ajax 
add_action( 'wp_ajax_bkpk_new_comment', 'bkpk_new_comment_catcher' );

function bkpk_new_comment_catcher(){
	global $wpdb;
	$return = array();
	$comment = $_POST['comment'];
	$comment_post_ID = intval($_POST['comment_post_ID']);
	$comment_parent = intval($_POST['comment_parent']);
	$inst = intval($_POST['inst']);
	$post_id_del = isset($_REQUEST['note_id']) ? $_REQUEST['note_id'] : '';
	$current_user 	= get_current_user_id(); 
 	
	$comment_id = 0;
	$return = array(
			'comment_id'	=> $comment_id,
			'msg' => __('Some error occure please try again ','llms-bkpk'),
	);
	global  $current_user; //for this example only :)
	if($comment and $comment_post_ID){
	$commentdata = array(
		'comment_post_ID' => $comment_post_ID, // to which post the comment will show up
		'comment_author' => $current_user->display_name , //fixed value - can be dynamic 
		'comment_author_email' => $current_user->user_email, //fixed value - can be dynamic 
		'comment_author_url' => '', //fixed value - can be dynamic 
		'comment_content' => $comment, //fixed value - can be dynamic 
		'comment_type' => '', //empty for regular comments, 'pingback' for pingbacks, 'trackback' for trackbacks
		'comment_parent' => $comment_parent, //0 if it's not a reply to another comment; if it's a reply, mention the parent comment ID here
		'user_id' => $current_user->ID, //passing current user ID or any predefined as per the demand
	);

	//Insert new comment and get the comment ID
	$comment_id = wp_new_comment( $commentdata );
	//add comment meta 
	add_comment_meta( $comment_id, 'inst', $inst );
	add_comment_meta( $comment_id, 'related_post', $comment_post_ID );
	
	$current_html = '';
		$return = array(
			'comment_id'	=> $comment_id,
			'comment_parent'	=> $comment_parent,
			'msg' => 'Comment added Successfully. ',
			'inst' => $inst,
			'comment_post_ID' => $comment_post_ID,
			'count' =>bkpk_get_comments_count($comment_post_ID,$inst ),
			'html' => bkpk_get_comment_html($comment_id),
		);
	}
	  
	echo json_encode($return);
	wp_die();
}

/** receive sync ajax **/
//receive ajax 
add_action( 'wp_ajax_bkpk_comments_sync', 'bkpk_comments_sync_catcher' );

function bkpk_comments_sync_catcher(){
	$data = $_POST['data'];
	$return = array();
	$i = 0;
	foreach($data as $k=>$v){
		$postid = $v[0];
		$inst = $v[1];
		$count = $v[2];
		$height = $v[3];
		$db_count = bkpk_get_comments_count($postid ,$inst );
		$return[$i]['update'] = false;
		if($count != $db_count){
			$full_html = bkpk_get_comments_full_html($postid ,$inst ,$height ,1);
			//$return['update'] = true;
			$return[$i] =  array('postid'=>$postid,'inst'=>$inst,'height'=>$height,'count'=>$db_count,'html'=>$full_html,'update'=>true);
		}
		$i++;
	}
	//preturn data 
	echo json_encode($return);
	wp_die();
}

/**
 * get single comment id
 */
 
 function bkpk_get_comment_html($comment_id){
	 ob_start();
	 $comments = get_comments(array(
					'comment__in' => array($comment_id),
					'status' => 'all' //Change this to the type of comments to be displayed
				)); 
				
				wp_list_comments(array(
				   'style' => 'ol',
					'per_page' => -1, //Allow comment pagination
					'reverse_top_level' => false , //Show the oldest comments at the top of the list
					//'callback'=>'et_custom_comments_display'
				), $comments);
				
	 return ob_get_clean();
 }

 
/**
 * get full html of comment
 *
 * @param $postid : int : current page id of which comments belong
 * @param $inst : int : instance id 
 * @param $height : int: height of currnt ul container class, inline css
 * @param $sync : boolean : set request to be a synchronous , mainly not to send outer conatiner ul tag again
 **/

 function bkpk_get_comments_full_html($postid ,$inst,$height = 500,$sync=false){
	ob_start();
	  
	$type = get_post_type($postid);
	$args = array(
			'post_id' => $postid,
			 'post_type' => $type, 
								'meta_query' => array(
									array(
										'key'   => 'inst',
										'value' => $inst
									)
								)
							);

					// The Query
					$comments_query = new WP_Comment_Query;
					$comments = $comments_query->query( $args );
				
				//don't send ul ourt is it's sync request
				if(!$sync){
				?>								
					<ul class="llms-commentlist comment-list clearfix" data-inst='<?php echo $inst; ?>' id='bkpk-comment-list-<?php echo $inst; ?>' style='max-height:<?php echo $height; ?>px;'>
				<?php 
				}
					//Display the list of comments
					 wp_list_comments(array(
					   'style' => 'ol',
						'per_page' => -1, //Allow comment pagination
						'reverse_top_level' => true  //Show the oldest comments at the top of the list
						
					), $comments); 
			//don't send ul ourt is it's sync request
			if(!$sync){
				?>
				</ul>
				<?php 
			}
	  return ob_get_clean();
  }
/** 
 * get comments count per section/instance
 * @ post_id 
 * @ inst id 
 *
 **/ 

function bkpk_get_comments_count($post_id,$instance_id ){
	$type = get_post_type($post_id);
	$args = array(
			'post_id' => $post_id,
			 'post_type' => $type, 
								'meta_query' => array(
									array(
										'key'   => 'inst',
										'value' => $instance_id
									)
								)
			);

			// The Query
			$comments_query = new WP_Comment_Query;
			$comments = $comments_query->query( $args );
					
					
			$counts = count($comments);
return $counts;				
}