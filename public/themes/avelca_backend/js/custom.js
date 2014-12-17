$(document).ready(function(){
	// $('#header-select').multiSelect();
	// $('.ms-visible').hide();
	$('.progress').show();
	$('.loading-flag').hide();
	$('#loading-flag').hide();

	// $('.excel-upload').change(function(){
	// 	var file = this.files[0];
	//     var name = file.name;
	//     var size = file.size;
	//     var type = file.type;
	//     var formData = new FormData();
	//     formData.append('file', this.files[0]);

 //   		$.ajax({
	//         url: '/admin/survey/upload',
	//         type: 'POST',
	//         xhr: function(){

	//             var xhr = $.ajaxSettings.xhr() ;
	//             $('.progress').show();

 //                xhr.upload.onprogress = function(evt){
 //                    var progress = evt.loaded/evt.total*100;
 //                    $('.progress-bar').css('width', progress+'%' ).html(progress+'%').attr('aria-valuenow',progress);
 //                } ;

 //                xhr.upload.onload = function(){/*console.log('DONE!');*/$('.progress-bar').parent().fadeOut("slow");} ;
 //                return xhr;
	//         },
	//         data: formData,
	//         success: function(data){
	//         	$('.excel-upload').after('<input type="hidden" name="uploaded_file" value="'+ data +'">');	        	
	//         },
	//         cache: false,
	//         contentType: false,
	//         processData: false
	// 	})
	// });
	
	$.fn.modal.Constructor.prototype.enforceFocus = function() {};

	$('.btn-defaultquestion').click(function(){
		$('#default-question').modal('show');

		$.ajax({
			type : 'GET',
			url : '/admin/questioncategory',
			dataType : 'json',
			data : {},
			success : function(data){
				console.log(data);
				$('#default-question #select-question-category option').remove()
				$('#default-question #select-question-category').append('<option></option>');
				$.each(data, function(index, obj){
					$('#default-question #select-question-category').append('<option value="'+ obj.id +'">'+ obj.name +'</option>');
				});
			}
		});
	});

	$('#default-question #select-question-category').click(function(){
		var id_category = $(this).val();

		$.ajax({
			type : 'GET',
			url : '/admin/question',
			dataType : 'json',
			data : { id_category : id_category },
			success : function(data){
				$('#default-question #select-question option').remove()
				$('#default-question #select-question').append('<option></option>');
				$.each(data, function(index, obj){
					$('#default-question #select-question').append('<option value="'+ obj.id +'">'+ obj.question +'</option>');
				});
			}
		});
	});

	$(function(){
	    $('#select-question-category').select2({});
	});

	$(function(){
	    $('#select-question').select2({});
	});

	$('#question_select_modal').select2({});
	$('#cycle_select_modal').select2({});

});

$('.survey_is_default').click(function(){
	$.post( "/admin/survey", { survey_id: $(this).val(), is_default:1 })
	.done(function( data ) {
		window.location.href = "/admin/survey/index";
	});

});

