jQuery( function($) {
	var student_notes_layer = 0;
	  var xhr;
	 student_notes_layer = $('.student-notes-layer');
	//clickon submitted button
    $( "#notes-submit-btn" ).click(function(e){
		e.preventDefault();
		//check for ajax already 
		 try {
			xhr.onreadystatechange = null;
			xhr.abort();
		} catch (e) {}

		//get values of data 
		var note = $('#llms_note_text').val(); 
		var note = tinymce.editors['llms_note_text'].getContent(); 
		var llms_notify_admin = $('input[name=llms_notify_admin]').is(':checked'); 
		var related_post_id = $('input[name=related_post_id]').val(); 
		var related_post_type = $('input[name=related_post_type]').val(); 
		var related_user_id = $('input[name=related_user_id]').val(); 
		//prepare data for sending ajax 
		var data = {
				'action': 'student_notes_ajax',
				'note': note      ,
				'llms_notify_admin': llms_notify_admin      ,
				'related_post_id': related_post_id      ,
				'related_post_type': related_post_type      ,
				'related_user_id': related_user_id      ,
			};
			//shake butto if not data added 
			if(!note)
			$( this ).effect( "shake" );
			
			//show layer 
			if(note)
			student_notes_layer.slideDown();
			//send ajax 
			
			// We can also pass the url value separately from ajaxurl for front end AJAX implementations
			xhr = jQuery.post(sn_object.ajax_url, data, function(response) {
				//receive data 
				//parse json 
				var obj = $.parseJSON(''+response+'');
				
				//reset form 
				$('#student-notes-form')[0].reset();
				
				//hide layer 
				student_notes_layer.slideUp();
				
				//add content to accordian 
				$('#accordion-Historical .accordian-content').html(obj.list);
				if(obj.note_id)
				$('#student-notes-form').after(obj.success_msg);
					$( ".alert-success" ).delay( 5000 ).slideUp( "slow", function() {
					});
				
			});
		 
    }); //on click event end 
	
	//delete note 
	 
	$( ".all-notes-conainter" ).on( "click", "a.del-note", function(e) {
		
		//check ofr user option
		  
		// confirm 
		var r = confirm("Are you sure. This can not be undone?");
		if (r == true) {
			txt = "You pressed OK!";
			  
		} else {
			txt = "You pressed Cancel!";
			
			 return false;
		}
	 
		e.preventDefault();
	     
		//check for ajax already 
		 try {
			xhr.onreadystatechange = null;
			xhr.abort();
		} catch (e) {}
		
		var del_note = 0; 
		del_note =$(this);
		var note_id = $(this).data('note-id');
		//if note id is there 
		if(note_id){
			//show layer 
		student_notes_layer.slideDown();
			var data = {
				'action': 'student_notes_ajax_del',
				'note_id': note_id      
				 
			};
			//send ajax 
			
			// We can also pass the url value separately from ajaxurl for front end AJAX implementations
			xhr = jQuery.post(sn_object.ajax_url, data, function(response) {
				//receive data 
				 //hide layer 
				 student_notes_layer.slideUp();
				 
				//parse json 
				var obj = $.parseJSON(''+response+'');
				del_note.html('deleted...');
				del_note.parent().parent().html(obj.success_msg);
				
				$( ".alert-danger" ).delay( 5000 ).slideUp( "slow", function() {
					$(this).parent().slideUp();
					});
				
			});
			
		}
		
	 });//end delete 
	
}); //jquery ready end