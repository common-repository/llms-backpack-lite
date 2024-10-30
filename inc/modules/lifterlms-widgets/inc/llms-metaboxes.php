<?php
/*
 * 
 * ref https://www.sitepoint.com/adding-custom-meta-boxes-to-wordpress/
 *
 */
function custom_meta_box_markup($object){
    wp_nonce_field(basename(__FILE__), "meta-box-nonce");
    ?>
        <div>
            <label class="post-attributes-label" for="meta-box-text"><span style="color:red; font-size:20px;">*</span>&nbsp;<strong>Title&nbsp;:&nbsp;</label>
            <input name="meta-box-text" type="text" value="<?php echo get_post_meta($object->ID, "meta-box-text", true); ?>"></strong>
            <br/><br/>
			<!-- label class="post-attributes-label" >Content :</label -->
			<?php 
			$content   = '';
			$content   = get_post_meta($object->ID, "llms_dynamic_sidebar_0", true);
		$editor_id = 'llms_dynamic_sidebar_0';
		$settings  = array( 'media_buttons' => false,'textarea_rows'=>10,'required'=>'required','quicktags' => false );
		wp_editor( $content, $editor_id, $settings );
		?>
		<br/>
		<center><input class="button button-primary button-large" type="Submit" value="Add This Widget " /></center>
		<?php 
		/*************** delete code below this ******************/
		//delete / update post widgets on delete 
		$del_sidebar_id = isset($_REQUEST['del_sidebar_id']) ? $_REQUEST['del_sidebar_id'] : 0; 
		$del_widget_id = isset($_REQUEST['del_widget_id']) ? $_REQUEST['del_widget_id'] : 0 ;
	if($del_sidebar_id or $del_widget_id  ){
		$dynamic_widgets_full_list   = get_post_meta($object->ID, "_dynamic_widgets",true);
		//check for if exists 
		if(is_array($dynamic_widgets_full_list)){
			foreach($dynamic_widgets_full_list as $sd_key => $sd_value){
				if($sd_key==$del_sidebar_id){
					 unset($dynamic_widgets_full_list[$sd_key]);
					  echo '<br/><center><span class="notice" >Widget deleted<span></center>';
				}
			}
			// update widgets for post 
			update_post_meta($object->ID, "_dynamic_widgets", $dynamic_widgets_full_list);
		}
		//del wp widgets on delt button press  
		$widget_text_all = get_option( 'widget_text', array() );
		$all_widgets_d = get_option( 'sidebars_widgets' ); 
		$slug1 ='llms_quiz';
		$slug2 ='course';
		//NOTE; ADD some logic for quiz and question , hey quiz is already added
		$sidebar = 'llms_lesson_widgets_side';
		//for quiz 
		if($slug1 == $object->post_type){
			$sidebar = 'sidebar-1'; 
		}
		//for course 
		if($slug2 == $object->post_type){
			$sidebar = 'llms_course_widgets_side'; 
		}
		$aw = $all_widgets_d[$sidebar];
		foreach($aw as $i => $inst)
			{
				//check if the id for the text widgets exists.
				$pos = strpos($inst, 'text');
				if($pos !== false)
				{
					$text_widget_id_array = explode("-",$all_widgets_d[$sidebar][$i]);
					$text_widget_id  = $text_widget_id_array[1];
					 $stored_posted_del_id  = $widget_text_all[$text_widget_id]['wid_del_id'];
					 //check if widget have stored posted id 
					 if($stored_posted_del_id){
						 //check if it is same to current post 
						 if($stored_posted_del_id == $del_widget_id){
							 //don't show that widget   remove the text widget by unsetting it's id
							  unset($widget_text_all[$text_widget_id]);
							  unset($all_widgets_d[$sidebar][$i]);
							  //echo '<span class="notice" >Widget deleted<span>';
						 }
					 }
				}
			}
		//update wp widgets after delete from sidebar 
		 update_option( 'widget_text', $widget_text_all );
		 update_option('sidebars_widgets', $all_widgets_d);
}//end delete 
		$active_sidebars = get_option( 'sidebars_widgets' ); //get all sidebars and widgets
		/*********** delete code above this **********/
		$dynamic_widgets   = get_post_meta($object->ID, "_dynamic_widgets",true);
		?>
		<br/><hr/>
		<div class='all-sidebar'>
		<h1> All widgets</h1>
		<?php 
		//loop show all sidebars 
		foreach($dynamic_widgets as $single_wid){
			echo "<div class='single-sidebar'>";
			$content   = $single_wid[0]['content'];
			$editor_id = $single_wid[0]['id'];
		
			$settings  = array( 'media_buttons' => false,'textarea_rows'=>10,'required'=>'required','quicktags' => false );
			//title
			echo '<br/><labelclass="post-attributes-label" ><strong>Title&nbsp;:&nbsp;</label>';
			echo $single_wid[0]['title']; ?></strong>
			<a style="float:right; margin-right:10px; color:red;" href='post.php?post=<?php echo $object->ID; ?>&action=edit&del_sidebar_id=<?php echo $single_wid[0]['index']; ?>&del_widget_id=<?php echo $single_wid[0]['id']; ?>' >Delete</a><br/><br/>
			<?php 
			//content
			// echo '<br/><label>Content:</label>';		 
			wp_editor( $content, $editor_id, $settings );
			echo "<br/><hr/>";
			echo "</div>";
		}
		if(!$dynamic_widgets){
			echo "<center><span class='notice'>You have no dynamic widget for this post.</span></center>";
		}
		?>
        </div>
        </div>
    <?php  
}
function add_custom_meta_box()
{
	//add meta box to leeson quiz and question only 
    add_meta_box("demo-meta-box", "Dynamic Sidebar - Meta Box", "custom_meta_box_markup", "lesson", "side", "default", null);
    add_meta_box("demo-meta-box", "Dynamic Sidebar - Meta Box", "custom_meta_box_markup", "llms_quiz", "side", "default", null);
    add_meta_box("demo-meta-box", "Dynamic Sidebar - Meta Box", "custom_meta_box_markup", "course", "side", "default", null);
}
//add meta box 
add_action("add_meta_boxes", "add_custom_meta_box");
//save meta box 
function save_custom_meta_box($post_id, $post, $update){
    if (!isset($_POST["meta-box-nonce"]) || !wp_verify_nonce($_POST["meta-box-nonce"], basename(__FILE__)))
        return $post_id;
    if(!current_user_can("edit_post", $post_id))
        return $post_id;
    if(defined("DOING_AUTOSAVE") && DOING_AUTOSAVE)
        return $post_id;
	// NOTE;  need to update code to save on quiz and question too
    $slug = "lesson";
    $slug1 = "llms_quiz";
    $slug2 = "course";
    if($slug == $post->post_type or $slug1 == $post->post_type or $slug2 == $post->post_type  ){}
	else{
        return $post_id;
	}
    $llms_dynamic_sidebar_0 = "";
    $meta_box_text_value = "";
    $meta_box_dropdown_value = "";
    $meta_box_checkbox_value = "";
    if(isset($_POST["meta-box-text"]))
    {
        $meta_box_text_value = $_POST["meta-box-text"];
    }   
    update_post_meta($post_id, "meta-box-text", $meta_box_text_value); 
    update_post_meta($post_id, "meta-box-text", ''); 
	if(isset($_POST["llms_dynamic_sidebar_0"]))
    {
        $llms_dynamic_sidebar_0 = $_POST["llms_dynamic_sidebar_0"];
    }   
    update_post_meta($post_id, "llms_dynamic_sidebar_0", $llms_dynamic_sidebar_0);
    update_post_meta($post_id, "llms_dynamic_sidebar_0", '');
	// to add custom meta for more editor 
	$dynamic_widgets   = get_post_meta($post_id, "_dynamic_widgets",true);
	if(!is_array($dynamic_widgets)){
		$dynamic_widgets = array(); //set to empty array on blank 
	}
	$pos = count($dynamic_widgets);
	$index = 0;
	$last_index = intval($pos)-1;
	 if(isset($dynamic_widgets[$last_index][0]['index'])){
		 $index = intval($dynamic_widgets[$last_index][0]['index']) + 1;
	}
	$new =  array(
				array ( 
					'index' => $index, 
					'id' => 'wid-text-id-'.$post_id.'-'.$index,
					'title' => $meta_box_text_value,
					'content'=>$llms_dynamic_sidebar_0,
				)
		);
	$pos = count($dynamic_widgets);
	$dynamic_widgets[] = $new;
	 // add only if title is posted 
	 if(isset($meta_box_text_value) && !empty($meta_box_text_value)){
	// update widgets for post 
	 update_post_meta($post_id, "_dynamic_widgets", $dynamic_widgets);
	 //add widget to wp widgets 
	 //add widget logic 
		//$widget_name = 'widget-32_text-__i__';
		$sidebar_id = 'llms_lesson_widgets_side'; //NOTE: for lesson add another too. 
		//for quiz 
		if($slug1 == $post->post_type){
			$sidebar_id = 'sidebar-1'; 
		}
		//for course 
		if($slug2 == $post->post_type){
			$sidebar_id = 'llms_course_widgets_side'; 
		}
		$active_sidebars = get_option( 'sidebars_widgets' ); //get all sidebars and widgets
		//$widget_options = get_option(''.$widget_name.'' ); //FOr text widgets 
	   $widget_text = get_option( 'widget_text', array() );
	   ksort($widget_text); //sort to get number at last in key
	   foreach($widget_text as $wkey => $val){
		   //echo $wkey.'<br/>';
	   }
	   //new key for text widget 
	   $nwkey = $wkey + 1;
	   $widget_text[$nwkey] = array(
	    'title' => $meta_box_text_value ,
	    'text' => $llms_dynamic_sidebar_0 ,
	    'filter' => 'content',
	    'visual' => 1
	   );
	   //for specific page id 
	   $widget_text[$nwkey]['post_id'] = $post_id;
	   $widget_text[$nwkey]['wid_del_id'] = 'wid-text-id-'.$post_id.'-'.$index;
	   //update all text widgets 
	   update_option( 'widget_text', $widget_text );
	   //get sidebar  
		if(isset($active_sidebars[''.$sidebar_id.''])) { //check if sidebar exists and it is empty
				// add to side bar  
			   $active_sidebars[''.$sidebar_id.''] =  array_merge($active_sidebars[''.$sidebar_id.''],array('text-'.$nwkey ));
			   update_option('sidebars_widgets', $active_sidebars);
		}
	}//end outer check for new widget save 
	
	//run update
	bkpk_update_widgets($post_id);
}
add_action("save_post", "save_custom_meta_box", 10, 3);


/** 
 * update widgets
 *
 **/
function bkpk_update_widgets($post_id){
	
	//update  widgets	
	$dynamic_widgets_full_list   = get_post_meta($post_id, "_dynamic_widgets",true);
		//check for if exists 
		if(is_array($dynamic_widgets_full_list)){
			foreach($dynamic_widgets_full_list as $sd_key => $sd_value){
				if(isset($_REQUEST[$sd_value[0]['id']])){
					
					//update content in post meta 
					$dynamic_widgets_full_list[$sd_key][0]['content'] = $_POST[$sd_value[0]['id']];
					
					$widget_text_all = get_option( 'widget_text', array() );
					//checking loop  -- get index -- update text widget 
					foreach($widget_text_all as $w_key => $w_value){
						if($w_value['wid_del_id'] == $sd_value[0]['id']){
							$widget_text_all[$w_key]['text'] = $_POST[$sd_value[0]['id']];
						}
					}
					update_option( 'widget_text', $widget_text_all );
				}
			}
			// update widgets for post 
			update_post_meta($post_id, "_dynamic_widgets", $dynamic_widgets_full_list);
		}
}


/* to show only specific widget on specific page */
#ref http://blog.rutwick.com/show-or-hide-a-wp-widget-only-on-specific-pages
add_filter('sidebars_widgets', 'llms_hide_extra_widgets');
function llms_hide_extra_widgets($all_widgets)
{ 
wp_reset_postdata(); 
global $post;
//only filter to on front end 
if(!is_admin()){
$current_post_id = get_the_ID();
$widget_text = get_option( 'widget_text', array() );
     $slug1 ='llms_quiz';
     $slug2 ='course';
	//NOTE; ADD some logic for quiz and question 
	$sidebar = 'llms_lesson_widgets_side';
	//for quiz 
	if(is_object($post)){
	if($slug1 == $post->post_type){
		$sidebar = 'sidebar-1'; 
	}}
	if(is_object($post)){
	//for course 
	if($slug2 == $post->post_type){
		$sidebar = 'llms_course_widgets_side'; 
	}}
	$aw = $all_widgets[$sidebar];
	foreach($aw as $i => $inst)
        {
            //check if the id for the text widgets exists.
            $pos = strpos($inst, 'text');
            if($pos !== false)
            {
				$text_widget_id_array = explode("-",$all_widgets[$sidebar][$i]);
				$text_widget_id  = $text_widget_id_array[1];
				$stored_posted_id =0;
				if(!empty($widget_text[$text_widget_id]['post_id'])){
				 $stored_posted_id  = $widget_text[$text_widget_id]['post_id'];
				}
				 //check if widget have stored posted id 
				 if($stored_posted_id){
					 //check if it is same to current post 
					 if($stored_posted_id != $current_post_id){
						 //don't show that widget   remove the text widget by unsetting it's id
						  unset($all_widgets[$sidebar][$i]);
					 }
				 }
            }
        }
}
	//sent new sidebar back
    return $all_widgets;
}
//color andf font slash issue 
function llms_plug_text_replace($text) {
 $text =   wp_kses_stripslashes( $text );
 return $text;
}
add_filter('widget_text', 'llms_plug_text_replace');