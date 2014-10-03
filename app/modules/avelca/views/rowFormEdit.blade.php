	@foreach($editFields as $field => $structure)

	<div class="form-group">
		{{ AvelcaController::label($field, $structure, $rules) }}
		{{ AvelcaController::field($field, $structure, $rules) }}
	</div>
	@endforeach


	@if( ! empty($formItem))

	<!-- New Row -->
	<div class="well" id="new_row">
		<?php
		$Model = str_singular(studly_case($formItem));
		$indexFields = $Model::structure()['fields']; 
		$item_name = ucwords(str_singular(str_replace('_',' ',$formItem)));
		?>

		@foreach($indexFields as $field => $structure)
		@if( $structure['type'] != 'fk')
		<div class="form-group">
			{{ AvelcaController::label($field, $structure) }}
			{{ AvelcaController::field($field, $structure) }}
		</div>
		@endif
		@endforeach
		<div class="form-group">
			<div class="col-md-4 col-md-offset-3">
				<a href="javascript:;" class="btn btn" style="background-color: {{ Setting::meta_data('general', 'theme_color')->value }}; color: #ffffff;" id="add_row_edit"><i class="glyphicon glyphicon-plus"></i> Add</a>
			</div>
		</div>
	</div>
	<!-- End New Row -->

	<div class="table-responsive" id="list_page">

		<?php
		$Model = str_singular(studly_case($formItem));
		$editFields = $Model::structure()['fields']; 
		?>

		<table class="table table-striped table-bordered" id="table_item_edit">
			<thead>
				<tr>
					@foreach($editFields as $field => $structure)
					@if( $structure['type'] != 'fk')
					<th class="text-center">{{ AvelcaController::tableHeader($field, $structure) }}</th>
					@endif
					@endforeach
					<th>&nbsp;</th>
				</tr>
			</thead>
			<tbody>
				@foreach($record->$formItem()->get() as $record_item)
				<tr>
					{{ Form::hidden('id[]', $record_item->id) }}
					@foreach($editFields as $field => $structure)
					@if( $structure['type'] != 'fk')

					@if( in_array($structure['type'], $numeric_types) )
					<td class="text-right">
					@elseif( in_array($structure['type'], $option_types) )
					<td class="text-center">
					@else
					<td class="text-left">
					@endif
					{{ AvelcaController::fieldFormItem($field, $structure, true, $record_item) }}
					</td>

					@endif
					@endforeach
					<td class="text-center">
						<a href="#" class="btn btn-default" onClick="$(this).closest('tr').remove();"><i class="glyphicon glyphicon-trash"></i></a>
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>

	</div>

	@endif