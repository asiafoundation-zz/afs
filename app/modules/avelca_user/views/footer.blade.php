		<script type="text/javascript">

		$(document).ready(function(){
			/* Filter DataTable */
			$('.datatable').each(function(){
				
				oTable = $(this).dataTable({
					"sPaginationType": "bs_full"
				});

				var search_input = $(this).closest('.dataTables_wrapper').find('div[id$=_filter] input');
				search_input.attr('placeholder', 'Search');
				search_input.addClass('form-control input-sm');

				var length_sel = $(this).closest('.dataTables_wrapper').find('div[id$=_length] select');
				length_sel.addClass('form-control input-sm');

				/* Start Footer */
				var asInitVals = new Array();

				$("tfoot input").keyup( function () {
					/* Filter on the column (the index) of this element */
					oTable.fnFilter( this.value, $("tfoot input").index(this) );
				} );

				$("tfoot input").each( function (i) {
					asInitVals[i] = this.value;
					$(this).addClass('form-control input-sm');
					$(this).attr('placeholder', 'Filter');
				} );

				$("tfoot input").focus( function () {
					if ( this.className == "search_init" )
					{
						this.className = "";
						this.value = "";
						$(this).addClass('form-control input-sm');
						$(this).attr('placeholder', 'Filter');
					}
				} );

				$("tfoot input").blur( function (i) {
					if ( this.value == "" )
					{
						this.className = "search_init";
						this.value = asInitVals[$("tfoot input").index(this)];
						$(this).addClass('form-control input-sm');
						$(this).attr('placeholder', 'Filter');
					}
				} );
				/* End Footer */
			});
});

</script>