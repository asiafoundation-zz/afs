<script type="text/javascript">
$(document).ready(function(){

	<?php if( ! empty($formItem)): ?>

	<?php
	$ModelItem = str_singular(studly_case($formItem));
	$itemFields = $ModelItem::structure()['fields']; 
	?>

	$("#add_row").click(function(){
		var new_row = addNewRow($(this));
		$('#table_item_create>tbody').append(new_row);
		@if( ! empty($formulaResult))
		updateFormulaFields();
		@endif
	});

	$("#add_row_edit").click(function(){
		var new_row = addNewRow($(this), true);
		$('#table_item_edit>tbody').append(new_row);
		@if( ! empty($formulaResult))
		updateFormulaFields();
		@endif
	});

	function addNewRow(me, edit)
	{
		edit = edit || false;

		var new_row = '<tr>';
		@foreach($itemFields as $field => $structure)
		@if( $structure['type'] != 'fk')
		@if( $structure['type'] == 'radio' || $structure['type'] == 'checkbox')
		var {{ $field }}_value = me.closest('div.well').find('#{{ $field }}:checked').val();
		@else
		var {{ $field }}_value = me.closest('div.well').find('#{{ $field }}').val();
		@endif

		new_row += '<td class="text-left">';

		if(edit)
		{
			new_row += '<input type="hidden" name="id[]">';
		}

		@if( $structure['type'] == 'select')
		@if( ! empty($structure['table']) )
		new_row += me.closest('div.well').find('#{{ $field }} option:selected').text();
		new_row += '<input type="hidden" name="{{ $field }}[]" value="'+ {{ $field }}_value +'">';	
		@endif
		@else
		if(edit)
		{
			new_row += '<input type="text" class="form-control" name="{{ $field }}[]" value="'+ {{ $field }}_value +'">';
		}
		else
		{
			new_row += {{ $field }}_value;
			new_row += '<input type="hidden" name="{{ $field }}[]" value="'+ {{ $field }}_value +'">';	
		}
		@endif

		new_row += '</td>';

		$('#{{ $field }}').each(function(){
			@if( $structure['type'] == 'radio' || $structure['type'] == 'checkbox')
			$('this').removeAttr('checked');
			@else
			$(this).val('');
			@endif
		});

		@endif
		@endforeach
		new_row += '<td class="text-center"><a href="#" class="btn btn-default" onClick="$(this).closest(\'tr\').remove();"><i class="glyphicon glyphicon-trash"></i></a></td>';
		new_row += '</tr>';

		return new_row;
	}

	function updateFields()
	{
		$("select[name={{ $trigger }}]").each(function(){

			$(this).on('change', function(){

				var id = $(this).val();
				var url = "{{ URL::to('rest/v1/'.str_replace('_id','',$trigger)).'/' }}" + id;
				var me = $(this);

				$.ajax({url: url, type: "get", success: function(result){
					<?php foreach ($triggerFields as $triggerField): ?>
					$("input[name={{ $triggerField }}]").val(result.instance.{{ $triggerField }});
					<?php endforeach; ?>
					@if( ! empty($formulaResult))
					updateResultStatic(me);
					@endif
				}});
			});
		});

		$("select[name={{ $trigger }}\\[\\]]").each(function(){

			$(this).on('change', function(){

				var id = $(this).val();
				var url = "{{ URL::to('rest/v1/'.str_replace('_id','',$trigger)).'/' }}" + id;
				var me = $(this);

				$.ajax({url: url, type: "get", success: function(result){
					<?php foreach ($triggerFields as $triggerField): ?>
					me.closest("tr").find("input[name={{ $triggerField }}\\[\\]]").val(result.instance.{{ $triggerField }});
					<?php endforeach; ?>
					@if( ! empty($formulaResult))
					updateResultStatic(me);
					@endif
				}});
			});
		});
	}

	@if( ! empty($formulaResult))
	function updateFormulaFields()
	{
		<?php foreach ($operands as $operand): ?>
		$("input[name={{ $operand }}]").each(function(){
			updateResult($(this));
		});
		<?php endforeach; ?>

		<?php foreach ($operands as $operand): ?>
		$("input[name={{ $operand }}\\[\\]]").each(function(){
			updateResult($(this));
		});
		<?php endforeach; ?>
	}

	function updateResult(me)
	{
		me.on('load keyup change', function(){
			<?php foreach ($operands as $operand): ?>
			var {{ $operand }} = me.closest("div.well").find("input[name={{ $operand }}]").val();
			var {{ $operand }}_arr = me.closest("tr").find("input[name={{ $operand }}\\[\\]]").val();
			<?php endforeach; ?>

			var {{ $formulaResult }} = 1;
			var {{ $formulaResult }}_arr = 1;

			<?php foreach ($operands as $operand): ?>
			{{ $formulaResult }} = {{ $formulaResult }} {{ $operator }} {{ $operand }};
			{{ $formulaResult }}_arr = {{ $formulaResult }}_arr {{ $operator }} {{ $operand }}_arr;
			<?php endforeach; ?>

			me.closest("div.well").find("input[name={{ $formulaResult }}]").val({{ $formulaResult }});
			me.closest("tr").find("input[name={{ $formulaResult }}\\[\\]]").val({{ $formulaResult }}_arr);
		});
	}

	function updateResultStatic(me)
	{
		<?php foreach ($operands as $operand): ?>
		var {{ $operand }} = $(this).closest("div.well").find("input[name={{ $operand }}]").val();
		var {{ $operand }}_arr = $(this).closest("tr").find("input[name={{ $operand }}\\[\\]]").val();
		<?php endforeach; ?>

		var {{ $formulaResult }} = 1;
		var {{ $formulaResult }}_arr = 1;

		<?php foreach ($operands as $operand): ?>
		{{ $formulaResult }} = {{ $formulaResult }} {{ $operator }} {{ $operand }};
		{{ $formulaResult }}_arr = {{ $formulaResult }}_arr {{ $operator }} {{ $operand }}_arr;
		<?php endforeach; ?>

		me.closest("div.well").find("input[name={{ $formulaResult }}]").val({{ $formulaResult }});
		me.closest("tr").find("input[name={{ $formulaResult }}\\[\\]]").val({{ $formulaResult }}_arr);
	}
	@endif

	/* Initialize */
	updateFields();
	@if( ! empty($formulaResult))
	updateFormulaFields();
	@endif

	<?php endif; ?>

});
</script>