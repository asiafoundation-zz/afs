$(document).ready(function(){
	// $('#header-select').multiSelect();
	// $('.ms-visible').hide();
	$('.progress').show();

	$('.excel-upload').change(function(){
		var file = this.files[0];
	    var name = file.name;
	    var size = file.size;
	    var type = file.type;
	    var formData = new FormData();
	    formData.append('file', this.files[0]);

   		$.ajax({
	        url: '/admin/survey/upload',
	        type: 'POST',
	        xhr: function(){

	            var xhr = $.ajaxSettings.xhr() ;
	            $('.progress').show();

                xhr.upload.onprogress = function(evt){
                    var progress = evt.loaded/evt.total*100;
                    $('.progress-bar').css('width', progress+'%' ).html(progress+'%').attr('aria-valuenow',progress);
                } ;

                xhr.upload.onload = function(){/*console.log('DONE!');*/$('.progress-bar').parent().fadeOut("slow");} ;
                return xhr;
	        },
	        data: formData,
	        success: function(data){
	        	$('.excel-upload').after('<input type="hidden" name="uploaded_file" value="'+ data +'">');	        	
	        },
	        cache: false,
	        contentType: false,
	        processData: false
		})
	});
	
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

});

/*
 * Importing JS
 *
 */
// Declared Category filter page flag
/*
 * 0 = region
 * 1 = wave
 * 2 = oversample
 * 3 = filter
 * 4 = question
 */
var page = 0;
function page_flag(move){
	if (move == 'next' && page != 4) {
		page = parseInt(page) + 1;
	};
	if (move == 'back' && page != 0) {
		page = parseInt(page) - 1;
	};
	return page;
}
// Declared Category filter page flag

// Declared Options Selected
var options_selected = [];
function category_clicked(category,category_question,code,number,label,base) {
	// Assign category to options selected
	options_selected.push({
		category:page,
		category_question:category_question,
		code:code,
		number:number,
		label:label,
		base:base
	});

	// Remove option selected from header select
	$("#header-select #header-option-"+code).remove();
	$("#header-selected").append("<option class='header-options-clicked' id='header-option-"+code+"' value='"+code+"' ondblclick=\"category_unclicked('"+category+"','"+category_question+"','"+code+"','"+number+"','"+label+"','"+base+"')\">"+label+"</option>");
}

function category_unclicked(category,category_question,code,number,label,base) {
	// Remove option selected from header select
	for (i = 0; i < options_selected.length; i++) {
		if (options_selected[i].code == code) {
			options_selected.splice(i, options_selected.length);
		};
	};
	
	$("#header-selected option[value='"+code+"']").remove();
	$("#header-select").prepend("<option class='header-options' id='header-option-"+code+"' value='"+code+"' ondblclick=\"category_clicked('"+category+"','"+category_question+"','"+code+"','"+number+"','"+label+"','"+base+"')\">"+label+"</option>");
}

// If category back
function category_back() {
	// Remove option selected from header select
	var total_array = options_selected.length;
	for (i = 0; i < options_selected.length; i++) {
		if (options_selected[i].category == page) {
			// Reassign to table form
			var first_array = i;
			$("#header-selected option[value='"+options_selected[i].code+"']").remove();
			$("#header-select").prepend("<option class='header-options' id='header-option-"+options_selected[i].code+"' value='"+options_selected[i].code+"' ondblclick=\"category_clicked('"+options_selected[i].category+"','"+options_selected[i].category_question+"','"+options_selected[i].code+"','"+options_selected[i].number+"','"+options_selected[i].label+"','"+options_selected[i].base+"')\">"+options_selected[i].label+"</option>");
		};
	};
	// Remove from option selected
	options_selected.splice(first_array, total_array);
}

// Load all data and dispaly it in pop up
function view_table_category() {
	var region_table = "";
	var wave_table = "";
	var oversample_table = "";
	var category_filter_table = "<tr>";
	var question_table = "";

	for (i = 0; i < options_selected.length; i++) {
		if (options_selected[i].category == 0) {
			region_table = region_table+"<td>"+options_selected[i].code+"</td><td>"+options_selected[i].label+"</td>";
		};
		if (options_selected[i].category == 1) {
			wave_table = wave_table+"<td>"+options_selected[i].code+"</td><td>"+options_selected[i].label+"</td>";
		};
		if (options_selected[i].category == 2) {
			oversample_table = oversample_table+"<td>"+options_selected[i].code+"</td><td>"+options_selected[i].label+"</td>";
		};
		if (options_selected[i].category == 3) {
			category_filter_table = category_filter_table+"<td>"+options_selected[i].code+"</td><td>"+options_selected[i].label+"</td>";
		};
		if (options_selected[i].category == 4) {
			question_table = question_table+"<tr><td>"+options_selected[i].code+"</td><td>"+options_selected[i].label+"</td></tr>";
		};
		category_filter_table = category_filter_table+"</tr>";
	};
	// Create Region Table
	$("#category_region").html(region_table);
	$("#category_wave").html(wave_table);
	$("#category_oversample").html(oversample_table);
	$("#category_filter_table").html(category_filter_table);
	$("#category_question").html(question_table);
}

function change_page(move) {
	var title = $("#category_title").text();
	var text = $("#category_text").text();
	var current_page = page;

	// Change text
	if (current_page == 0) {
		if (move == 'next') {
			page_flag(move);
			title = title.replace(/Region/g, "Wave");
			text = text.replace(/Region/g, "Wave");
		}
		// Remove Header Selected Value
		$('#header-selected').find('option').remove().end();
	};
	if (current_page == 1) {
		if (move == 'next') {
			page_flag(move);
			title = title.replace(/Wave/g, "Oversample");
			text = text.replace(/Wave/g, "Oversample");
		}
		if (move == 'back') {
			title = title.replace(/Wave/g, "Region");
			text = text.replace(/Wave/g, "Region");

			page_flag(move);
			category_back();
		};
		// Remove Header Selected Value
		$('#header-selected').find('option').remove().end();
	};
	if (current_page == 2) {
		if (move == 'next') {
			page_flag(move);
			title = title.replace(/Oversample/g, "Category");
			text = text.replace(/Oversample/g, "Category");
		}
		if (move == 'back') {
			title = title.replace(/Oversample/g, "Wave");
			text = text.replace(/Oversample/g, "Wave");

			page_flag(move);
			category_back();
		};
		// Remove Header Selected Value
		$('#header-selected').find('option').remove().end();
	};
	// Preparing saving process
	if (current_page == 3) {
		if (move == 'next') {
			page_flag(move);
			title = title.replace(/Category/g, "Question");
			text = text.replace(/Category/g, "Question");
		}
		if (move == 'back') {
			title = title.replace(/Category/g, "Oversample");
			text = text.replace(/Category/g, "Oversample");

			page_flag(move);
			category_back();
		};
		// Remove Header Selected Value
		$('#header-selected').find('option').remove().end();
	};

	if (current_page == 4) {
		if (move == 'next') {
			page_flag(move);
			title = title.replace(/Category/g, "Question");
			text = text.replace(/Category/g, "Question");

			view_table_category();
			$('#view_table_category').modal('show');
		};
		if (move == 'back') {
			title = title.replace(/Question/g, "Category");
			text = text.replace(/Question/g, "Category");

			// dblclick Trigger the last options
			$(".header-options-clicked").each(function(){
				var elem = document.getElementById(this.id);
				elem.ondblclick.apply(elem);
			});

			page_flag(move);
			category_back();
			$('#header-selected').find('option').remove().end();
		};
		// Remove Header Selected Value
	}
	// Change label in page
	$("#category_title").text(title);
	$("#category_text").text(text);
}

function post_category() {
	var survey_id_val = $("#survey_id").val();
	$.post( "survey/category", { survey_id: survey_id_val, options_selected: options_selected })
	.done(function( data ) {
		// redirect to index page
		window.location.href = "/admin/survey/index";
	});
}
function select_all(move){
	// dblclick Trigger the last options
	$(".header-options").each(function(){
		var elem = document.getElementById(this.id);
		elem.ondblclick.apply(elem);
	});
	return false;
}

/*
 * End Importing JS
 *
 */
 /*
 * Manage Survey JS
 *
 */
 function cycle_select_option(survey_id){
	$.get( "survey/cycles", { survey_id: survey_id, options_selected: $("#cycle_select_modal").val() })
	.done(function( data ) {
		// redirect to index page
alert('aa');
	});
	return false;
}
 /*
 * End Manage Survey JS
 *
 */