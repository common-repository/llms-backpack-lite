/**
 * lifter lms Back Pack
 * js for ajax comments and auto sync
 **/

jQuery(document).ready(function($){
	//helper function
	function isJSON (something) {
		if (typeof something != 'string')
			something = JSON.stringify(something);

		try {
			JSON.parse(something);
			return true;
		} catch (e) {
			return false;
		}
	}			
		
	//remove old wordrpess comment and form
	$('#comments').remove();
	var xhr;
	var llms_comments_layer = 0;
				
	llms_comments_layer = $('.llms-comments-layer-1');
				
	//on submit click 
	$( ".bkpk-comments-container" ).delegate( ".bkpk-comment-submit .submit", "click", function(e) {
					
					console.log('just clicked submit comment');
					e.preventDefault();
					//save to textare 
					if(typeof(tinyMCE) !== 'undefined')
					tinyMCE.triggerSave(false, true);
				
					console.log('tinyMCE full :-');
					if(typeof(tinyMCE) !== 'undefined')
					console.log(tinyMCE);
					
					bkpkintervalManager(false);
					var current  = $(this);
					var inst = current.parent().find('input[name=inst]').val(); 
					//check for ajax already 
					 try {
						xhr.onreadystatechange = null;
						xhr.abort();
					} catch (e) {}
					
					var form_div = current.parent().parent(); 
					var comment_parent = current.parent().find('input[name=comment_parent]').val(); 
					var comment_post_ID = current.parent().find('input[name=comment_post_ID]').val(); 
					
			    	var comment = current.parent().parent().children().find('textarea').val(); 
			    	var current_textarea = current.parent().parent().children().find('textarea'); 
			    	var current_textarea_div = current.parent().parent().children().find('textarea').parent(); 
					console.log('comment is----');
					console.log(comment)
					console.log('comment parent id is----');
					console.log(comment_parent)	
					console.log('comment comment_post_ID id is----');
					console.log(comment_post_ID)
					comment = $.trim(comment);
										
					if(comment_post_ID =='')
						return false
					
					//shake button if no data added 
					if(comment=='Comment'){
						current_textarea_div.children().find('bkpk-msg').remove();
						current_textarea.after('<span class="llms-comment-error bkpk-msg">Please enter a comment not text Comment.</span>');
						$('.llms-comment-error').delay(5000).slideUp( "slow");
						 
						return false;
					}
					if(comment==''){
						current_textarea_div.children().find('bkpk-msg').remove();
						current_textarea.after('<span class="llms-comment-error 2 bkpk-msg">Please enter a comment.</span>');
						$('.llms-comment-error').delay(5000).slideUp( "slow");
						
						return false;
					}
					//show layer 
					if(comment!='Comment')
					llms_comments_layer.slideDown();
				
						var data = {
							'action': 'bkpk_new_comment',
							'comment': comment,
							'comment_parent': comment_parent,
							 'comment_post_ID': comment_post_ID ,
							 'inst': inst 
						};
			 		
					jQuery.ajax({
						url: ajaxurl,
						type: "POST",
						data: data,
						success: function (response) {
							//hide layer 
							var msg = response;
							llms_comments_layer.slideUp();
							
							//start sync again 
							bkpkintervalManager(true, bkpksync, 10000);
							
							if(isJSON(response)){
								var obj = $.parseJSON(''+response+'');
							
								if(obj.comment_id){
									msg = obj.msg;
								}
								
								//add to current instance response 
								console.log(obj.html);
								console.log(inst);
								//jQuery('.llms-comment-wrap-1 .llms-commentlist' ).append('<li> new comment added !!!!</li>');
								if(obj.comment_parent){
									//add to commetn pareint 
									  var ul_check = $.contains('.llms-comment-wrap-'+inst+' #comment-'+obj.comment_parent,'ol');
									 //var ul_check = jQuery(').has('ol');
									  
									  //check if need to add ul or not
									  console.log('have ol'+ul_check);
									  if(ul_check){
										jQuery('.llms-comment-wrap-'+inst+' #div-comment-'+obj.comment_parent ).after(obj.html);
									  }else{
										  jQuery('.llms-comment-wrap-'+inst+' #div-comment-'+obj.comment_parent ).after('<ol class="children">'+obj.html+'</ol>');
									  }
									//remove extra form
									form_div.remove();
								}
								else{
									//add to main comment
									jQuery('.llms-comment-wrap-'+inst+' .llms-commentlist:first' ).append(obj.html);
								}
								jQuery('span#bkpk-inst-'+inst+'-commnts-counts' ).html(obj.count);
								//jQuery('.llms-comment-wrap-'+inst+' .llms-commentlist' ).append(obj.count);
							
							console.log( "success new messages should be addeded!!");
							}
								// Update comments
								
								console.log( "success new messages");
								 
								//success message 
								current_textarea.after('<span class="llms-comment-success">'+msg+'</span>');
								//set content to empty 
								current_textarea.val(' ');
								if(typeof(tinyMCE) !== 'undefined')
								tinyMCE.activeEditor.setContent('');
								
								$('.llms-comment-success').delay(5000).slideUp( "slow");
								 
						},
						error: function (jqXhr, textStatus, errorThrown) {
							//hide layer 
							llms_comments_layer.slideUp();
							var error = jqXhr.responseText;
							alert( "error"+error);
							console.log( "error"+error);
						}
					});
	}); //endof  click submit buttn
		
//cancel  reply button
$( ".comment-list" ).delegate( ".cancel-reply", "click", function(e) {
	console.log('cancel reply clicked');
	var cancel_reply = $(this);
	var cancel_reply_div = cancel_reply.parent().parent().parent();
	cancel_reply_div.find('.comment-form').remove();
	cancel_reply.remove();
	//start sync again 
	bkpkintervalManager(true, bkpksync, 10000);
	
});		

//click on reply button
$( ".comment-list" ).delegate( ".reply a", "click", function(e) {
   
					console.log('just clicked reply comment');
					e.preventDefault();
					bkpkintervalManager(false);
					var reply_btn = $(this);
					var reply_btn_div = reply_btn.parent().parent();
					var parent_comment_id  = reply_btn_div.attr('id');
					 parent_comment_id  = parent_comment_id.replace("div-comment-","");
					 
					console.log(parent_comment_id);
					
					//add canel button
					reply_btn.parent().children('.canel-reply').remove();
					reply_btn.after('<span class="cancel-reply"> Cancel Reply</span>');
					
					
  
					 
					var inst = reply_btn_div.parent().parent();
					inst = inst.data('inst');
					console.log(inst);
					
					var newinst = reply_btn.closest('.bkpk-comments-container');
					//newinst = newinst.data('inst');
					
					var newinst_id =  newinst.attr('id');
					var tb=  newinst.data('tb');
					console.log('tb is nowset for'+tb);
					 newinst_id  = newinst_id.replace("llms-comment-wrap-",""); //replace so only get id 
					 if(newinst_id){
						 inst = newinst_id; 
					 }
					console.log('current inst new closes:  time 6.59pm ');
					
					console.log(newinst_id);
					console.log(inst);
					
					//remove other form 
					$('#repl-form-'+parent_comment_id).remove();
					$('.comment div.comment-form').remove();
					
					var form_clone =  jQuery('.llms-comment-wrap-'+inst+' div.comment-form:first' ).clone();
					console.log('this is currnet clone');
					console.log(form_clone);
					form_clone.find('input[name=comment_parent]').val(parent_comment_id);
					//find textrea
					var oldtextarea = form_clone.find('#bkpk-comment-'+inst);
					form_clone.attr('id','repl-form-'+parent_comment_id);
					console.log('old textare');
					console.log(oldtextarea);
					//change id to new  do t only if tb is 1 
					oldtextarea.attr('id','bkpk-comment-'+parent_comment_id);
					console.log('dsply block added');
					oldtextarea.css('display','block');
					//now change html to textare only  
					form_clone.find('#wp-bkpk-comment-'+inst+'-wrap').html(oldtextarea);
					//change container wrap id 
					form_clone.find('#wp-bkpk-comment-'+inst+'-wrap').attr('id','wp-bkpk-comment-'+parent_comment_id+'-wrap');
					
					reply_btn_div.after(form_clone);
					
					if(tb){
					//add tinyMCE
					// tinymce.init({ selector: '#'+'bkpk-comment-'+parent_comment_id  });
					 var idd = '#'+'bkpk-comment-'+parent_comment_id ;
					 if(typeof(tinyMCE) !== 'undefined')
					 tinymce.remove(idd);
					 //add some settngs , 	NOTE: find some other method  translation etc might not work this way
					 if(typeof(tinyMCE) !== 'undefined')
					 tinymce.init({theme:"modern",skin:"lightgray",language:"en",formats:{alignleft: [{selector: "p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li", styles: {textAlign:"left"}},{selector: "img,table,dl.wp-caption", classes: "alignleft"}],aligncenter: [{selector: "p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li", styles: {textAlign:"center"}},{selector: "img,table,dl.wp-caption", classes: "aligncenter"}],alignright: [{selector: "p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li", styles: {textAlign:"right"}},{selector: "img,table,dl.wp-caption", classes: "alignright"}],strikethrough: {inline: "del"}},relative_urls:false,remove_script_host:false,convert_urls:false,browser_spellcheck:true,fix_list_elements:true,entities:"38,amp,60,lt,62,gt",entity_encoding:"raw",keep_styles:false,cache_suffix:"wp-mce-4711-20180425",resize:"vertical",menubar:false,branding:false,preview_styles:"font-family font-size font-weight font-style text-decoration text-transform",end_container_on_empty_block:true,wpeditimage_html5_captions:true,wp_lang_attr:"en-US",wp_keep_scroll_position:false,wp_shortcut_labels:{"Heading 1":"access1","Heading 2":"access2","Heading 3":"access3","Heading 4":"access4","Heading 5":"access5","Heading 6":"access6","Paragraph":"access7","Blockquote":"accessQ","Underline":"metaU","Strikethrough":"accessD","Bold":"metaB","Italic":"metaI","Code":"accessX","Align center":"accessC","Align right":"accessR","Align left":"accessL","Justify":"accessJ","Cut":"metaX","Copy":"metaC","Paste":"metaV","Select all":"metaA","Undo":"metaZ","Redo":"metaY","Bullet list":"accessU","Numbered list":"accessO","Insert\/edit image":"accessM","Remove link":"accessS","Toolbar Toggle":"accessZ","Insert Read More tag":"accessT","Insert Page Break tag":"accessP","Distraction-free writing mode":"accessW","Keyboard Shortcuts":"accessH"},plugins:"charmap,colorpicker,hr,lists,media,paste,tabfocus,textcolor,fullscreen,wordpress,wpautoresize,wpeditimage,wpemoji,wpgallery,wplink,wpdialogs,wptextpattern,wpview,image",selector:""+idd+"",wpautop:true,indent:false,toolbar1:"formatselect,bold,italic,bullist,numlist,blockquote,alignleft,aligncenter,alignright,link,wp_more,spellchecker,fullscreen,wp_adv,tincan,tincan_iframe",toolbar2:"strikethrough,hr,forecolor,pastetext,removeformat,charmap,outdent,indent,undo,redo,wp_help",toolbar3:"",toolbar4:"",tabfocus_elements:":prev,:next",body_class:idd+" post-type-lesson post-status-publish page-template-default locale-en-us",theme_advanced_buttons1:"bold,italic,ul,pH,temp"});
					 
					}
					 
					
	}); //endof  click reply buttn
								
	//auto sync on pages 
	function bkpksync(){
					console.log(' bkpk sync functon ran');
					 //console.clear();
					//get post id  send current count get id 
					 hash = {};
					 i =0; 
					jQuery('.bkpk-comments-container').each(function(){
						 						
						var a = $(this);
						var postid = a.data('post-id');
						var instance = a.data('instance');
						var count = jQuery('span#bkpk-inst-'+instance+'-commnts-counts').text();
						 
						console.log('post id'+postid);
						console.log('instance id'+instance);
						console.log('count id'+count);
						hash[i] = [ postid , instance, count];
												
						i++;
						});
						console.log(hash);
						
						//send ajax and get back full li's
						
				var data = {
							'action': 'bkpk_comments_sync',
							'data': hash
						};
					jQuery.ajax({
						url: ajaxurl,
						type: "POST",
						data: data,
						success: function (response) {
							
							var obj = $.parseJSON(''+response+'');
							console.log(obj);
							console.log('bkpk:these are updated values');
							for(var i=0;i<obj.length;i++){
								var postid = obj[i]['postid'];
								var inst = obj[i]['inst'];
								var count = obj[i]['count'];
								var html = obj[i]['html'];
								var update = obj[i]['update'];
								console.log('after synce ');
								console.log(postid);
								console.log(inst);
								console.log(html);
								 
								console.log(update);
								if(update){
									jQuery('.llms-comment-wrap-'+inst+' .llms-commentlist').html(html);
								
									jQuery('span#bkpk-inst-'+inst+'-commnts-counts' ).html(count);
								}
							}
						}
						
					});
	}
				
	//interval manager 
	var intervalID = null;

	function bkpkintervalManager(flag, animate, time) {
	   if(flag){
		 intervalID =  setInterval(animate, time);
		 console.log(' bkpk: sync  interval start');
		// console.clear();
	   }else{
		 clearInterval(intervalID);
		  console.log(' bkpk: sync interval ends');
		   //console.clear();
	   }
	}
	//first ran sycn manager
	bkpkintervalManager(true, bkpksync, 10000);
							
}); //end of ready 