$(document).ready(function(){
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
	            // var myXhr = $.ajaxSettings.xhr();
	            // if(myXhr.upload){ // Check if upload property exists
	            //     myXhr.upload.addEventListener('progress',progressHandlingFunction, false); // For handling the progress of the upload
	            // }
	            // return myXhr;

	            var xhr = $.ajaxSettings.xhr() ;

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
	        	console.log(data);
	        },
	        // error: errorHandler,
	        //Options to tell jQuery not to process data or worry about content-type.
	        cache: false,
	        contentType: false,
	        processData: false
		})
	});

	function progressHandlingFunction(e){
	    if(e.lengthComputable){
	        $('progress').attr({value:e.loaded,max:e.total});
	    }
	}

	$('#my-select').multiSelect();
});