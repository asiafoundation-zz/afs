<script type="text/javascript">
$(document).ready(function(){

	$('.datatable').each(function(){
		$(this).dataTable({
			"bPaginate" : false,
			"bInfo" : false,
			"bFilter": false,
			"sPaginationType": "bs_full",
			"sDom": 'lfrtip<"clear spacer">T',
			"oTableTools": {
				"sSwfPath": "<?php echo URL::to(Theme::asset('swf/copy_csv_xls_pdf.swf')); ?>",
				"aButtons": [
				{
					"sExtends": "print",
					"fnClick": function( nButton, oConfig ) {
						$('td#actionColumn,th#actionColumn').hide();
						$('tfoot').hide();
						$('.navbar-static-side').hide();
						$('#page-wrapper').css('margin','0');
						$('.table').width('100%');
						this.fnPrint( true, oConfig );
						window.print();
					},
					"fnComplete": function ( nButton, oConfig, oFlash, sFlash ) {
						alert( 'Press escape to go back after printing.' );
					}
				}
				]
			},
			"aaSorting": []
		});

		var search_input = $(this).closest('.dataTables_wrapper').find('div[id$=_filter] input');
		search_input.attr('placeholder', 'Search');
		search_input.addClass('form-control input-sm');

		var length_sel = $(this).closest('.dataTables_wrapper').find('div[id$=_length] select');
		length_sel.addClass('form-control input-sm');

	});

$.datepicker.regional[""].dateFormat = 'yy-mm-dd';
$.datepicker.setDefaults($.datepicker.regional['']);

$('span.filter_column.filter_number_range.form-control').attr('class','filter_column filter_number_range');

});

function ajaxPagination(e,page,lastPage){
	e.preventDefault();
	if(page == 'next' || page == 'prev'){
		var temp_page = $("#"+page).data('page');
		temp_page = temp_page - 1;
		if(page == 'next'){
			temp_page = temp_page + 1;
		}
		$("#"+page).data('page',temp_page);
		page = temp_page;
	}
	var url = '{{ URL::to('admin/'.$routeName) }}';
	$.ajax({
		url : url,
		type : 'get',
		data : { page : page },
		success : function(response){
			if(response){
				$("#index_table").find('tbody').children().remove();
				$("#index_table").find('tbody').append(response);
				var before = $("#table-page").find('.active');
				$(before).removeClass('active');
				var bef_page = $(before).data('page');
				var current = '<span>'+page+'</span>';
				var before = '<a href="" onclick="ajaxPagination(event,'+bef_page+','+lastPage+')">'+bef_page+'</a>';
				$("#table-page").find("#_"+page).html(current);
				$("#table-page").find("#_"+page).addClass('active');
				$("#table-page").find("#_"+bef_page).html(before);
				$("#table-page").find('#prev').data('page',page-1);
				$("#table-page").find('#next').data('page',page+1);
				
				if(page==1){
					var a = '<span>«</span>';
					$("#prev").html(a);
					$("#prev").addClass('disabled');
				}else{
					var a = '<a href="" onclick="ajaxPagination(event,'+(page-1)+','+lastPage+')">«</a>';
					$("#prev").html(a);
					$("#prev").removeClass('disabled');
				}
				
				if(page == lastPage){
					var a = '<span>»</span>';
					$("#next").html(a);
					$("#next").addClass('disabled');
				}else{
					var a = '<a href="" onclick="ajaxPagination(event,'+(page+1)+','+lastPage+')">»</a>';
					$("#next").html(a);
					$("#next").removeClass('disabled');
				}
			}
		}
	});
}

</script>
