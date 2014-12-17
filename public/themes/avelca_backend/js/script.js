
$().ready(function(){

	/* Toogle Sidebar */
	$('#toggle_sidebar').click(function()
	{
		if($(this).attr('hide') == 'N')
		{
			$('.navbar-static-side').hide();
			$('#page-wrapper').css('margin','0');
			$('.table').width('100%');
			$(this).attr('hide','Y');
		}
		else
		{
			$('.navbar-static-side').show();
			$('#page-wrapper').css('margin','0 0 0 250px');	
			$(this).attr('hide','N');
		}	
	}
	);

	/* Toogle Filter */
	$( "#filter" ).click(function() {
		$( ".well" ).slideToggle('fast');
	});

	/* Sidemenu */
	$('#side-menu').metisMenu();

	$(window).bind("load", function() {
		if ($(this).width() < 768) {
			$('div.sidebar-collapse').addClass('collapse')
		} else {
			$('div.sidebar-collapse').removeClass('collapse')
		}
	});

	$(window).bind("resize", function() {
		console.log($(this).width())
		if ($(this).width() < 768) {
			$('div.sidebar-collapse').addClass('collapse')
		} else {
			$('div.sidebar-collapse').removeClass('collapse')
		}
	});

	/* Get cookie for user error messages */
    if ($.cookie("action"))
    {
        var modalSelector = $.cookie("action");
        $(modalSelector).modal('show');
        $.cookie('action', '', { expires: -7, path: '/admin/user' });
    }
    /* END */

	function initializeComponents()
	{
		/* Select Picker */
		$('.selectpicker').each(function(){
			$(this).selectpicker({
				'liveSearch': true
			});
		});

		/* Datepicker */
		$('.datepicker').each(function(){
			$(this).datetimepicker({
				pickTime: false,
				useCurrent: true
			});
		});

		/* Timepicker */
		$('.timepicker').each(function(){
			$(this).datetimepicker({
				pickDate: false,
				useCurrent: true,
				useSeconds: false
			});
		});

		/* DateTimePicker */
		$('.datetimepicker').each(function(){
			$(this).datetimepicker({
				useCurrent: true,
				sideBySide: true
			});
		});
		/* General Tabs */
		$('#tabs a').click(function (e) {
			e.preventDefault();
			$(this).tab('show');
		});

		/* Colorpicker */
		$('#colorpicker').each(function(){
			$(this).colorpicker();
		});

		/* CKEditor */
		$('textarea#editor').each(function(){
			$(this).ckeditor();
		});

		$('textarea#basic_editor').each(function(){
			$(this).ckeditor({
				toolbar: [
				[ 'Source', '-', 'Bold', 'Italic', 'Underline', '-', 'Link', 'Unlink', '-', 'HorizontalRule']
				]
			});
		});
	}

	initializeComponents();

});
