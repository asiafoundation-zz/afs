$(document).ready(function(){
	$('#header-select').multiSelect();
	$('.ms-visible').hide();
	$('.progress').hide();

	$('.excel-upload').change(function(){
		var file = this.files[0];
	    var name = file.name;
	    var size = file.size;
	    var type = file.type;
	    var formData = new FormData();
	    formData.append('file', this.files[0]);

	    // console.log(file);
	    // return false;

		$.ajax({
	        url: '/admin/survey/upload',  //Server script to process data
	        type: 'POST',
	        xhr: function() {  // Custom XMLHttpRequest

	            var xhr = $.ajaxSettings.xhr() ;
	            $('.progress').show();

                // set the onprogress event handler
                xhr.upload.onprogress = function(evt){
                    var progress = evt.loaded/evt.total*100;
                    $('.progress-bar').css('width', progress+'%' ).html(progress+'%').attr('aria-valuenow',progress);
                    /*console.log('progress', evt.loaded/evt.total*100);*/
                } ;
                // set the onload event handler
                xhr.upload.onload = function(){/*console.log('DONE!');*/$('.progress-bar').parent().fadeOut("slow");} ;
                // return the customized object
                return xhr;
	        },
	        // Form data
	        data: formData,
	        //Ajax events
	        // beforeSend: beforeSendHandler,
	        success: function(data){
	        	$('.ms-visible').show().fadeIn("slow");
	        	$('.upload-form').hide().fadeOut("slow");
	        	$.each(data, function(index, obj){
	        		$('#header-select').multiSelect('addOption', { value : obj.name, text : obj.label });
	        	});
	        },
	        // error: errorHandler,
	        //Options to tell jQuery not to process data or worry about content-type.
	        cache: false,
	        contentType: false,
	        processData: false
		})
	});

});