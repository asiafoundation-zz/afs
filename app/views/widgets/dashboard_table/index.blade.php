<table class="table table-bordered table-condensed table-striped">
	<thead>
		<tr>
		@foreach($fields as $field)
		<th class="text-center">{{ ucwords(str_replace('_',' ',$field)) }}</th>
		@endforeach
	</tr>
	</thead>
	<tbody>
		@foreach($records as $record)
		<tr>
			@foreach($fields as $field)
			<td>{{ $record->$field }}</td>
			@endforeach
		</tr>
		@endforeach
	</tbody>
</table>