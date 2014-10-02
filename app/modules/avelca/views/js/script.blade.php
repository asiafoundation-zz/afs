<script type="text/javascript">
$(document).ready(function(){

	/* Remote Modal Bootstrap */
    $('body').on('hidden.bs.modal', '.modal', function () {
        $(this).removeData('bs.modal');
    });

	/* Index & Create Page*/
	$( "#new_button" ).unbind().click(function() {

		var btn_text = $(this).text().trim();

		if(btn_text == 'Create New')
		{
			$(this).html('<i class="fa fa-list fa-fw"></i> Back to List');
			$.ajax({
				'url' : '{{ URL::to("admin/".$routeName."/create") }}',
				'type' : 'GET',
				'success' : function(response){
					$("#create_page").html('');
					$("#create_page").append(response);

					<?php $customView = 'admin.'.$routeName.'.js.initializeComponents'; ?>
					@if( ! View::exists($customView))
					<?php $customView = 'avelca::js.initializeComponents'; ?>
					@endif
					@include($customView)
				}
			})
		}
		else
		{
			$(this).html('<i class="fa fa-plus fa-fw"></i> Create New');
			$("#create_page").html('');
		}

		$( "#list_page" ).toggle();

		var text = $('.page-header small').text();

		if(text == 'List')
		{
			$('.page-header small').text('Create');
		}
		else
		{
			$('.page-header small').text('List');
		}
	});

});
</script>