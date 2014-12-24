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
function page_category(page){
	var current_category;
	if (page == 0) {
		current_category = 'region';
	}
	if (page == 1) {
		current_category = 'wave';
	}
	if (page == 2) {
		current_category = 'oversample';
	}
	if (page == 3) {
		current_category = 'filter';
	}
	if (page == 4) {
		current_category = 'question';
	}
	return current_category;
}
// Declared Options Selected
var options_selected = [];
function category_clicked(category,category_question,code,number,label,base) {
	// Assign category to options selected
	options_selected.push({
		category:page,
		category_question:category_question,
		code:code,
		label:label
	});

	category = page;
	// Remove option selected from header select
	$("#header-select #header-option-"+code).remove();
	$("#header-selected").append("<option class='header-options-clicked' id='header-option-"+code+"' value='"+code+"' ondblclick=\"category_unclicked('"+category+"','"+category_question+"','"+code+"','"+number+"','"+label+"','"+base+"')\">"+label+"</option>");
}

function category_unclicked(category,category_question,code,number,label,base) {
	// Remove option selected from header select
	for (i = 0; i < options_selected.length; i++) {
		if (options_selected[i].code == code) {
			options_selected.splice(i, 1);
		};
	};
	
	category = page_category(page);
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
		//Redirect back 
		if (move == 'back') {
			window.location.href = "/admin/survey/index";
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

			// dblclick Trigger the last options
			$(".header-options-clicked").each(function(){
				var elem = document.getElementById(this.id);
				elem.ondblclick.apply(elem);
			});

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

			// dblclick Trigger the last options
			$(".header-options-clicked").each(function(){
				var elem = document.getElementById(this.id);
				elem.ondblclick.apply(elem);
			});

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

			// dblclick Trigger the last options
			$(".header-options-clicked").each(function(){
				var elem = document.getElementById(this.id);
				elem.ondblclick.apply(elem);
			});

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
			$('#view_table_category').modal({
				show:true,
				backdrop:"static"
				});
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
	$('#popup_modal').hide();
	$('#loading-flag').show();
	$('#button-footer').hide();
	$('.close').hide();

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