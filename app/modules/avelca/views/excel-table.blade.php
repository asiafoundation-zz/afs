<?php $no = 1; ?>
<table>
	<thead>
		<tr >
			<th>No</th>
			@foreach($indexFields as $field => $structure)
			<th class="text-center">{{ AvelcaController::tableHeader($field, $structure) }}</th>
			@endforeach
		</tr>
	</thead>
	<tbody>
		@foreach($records as $record)
		<tr>
			<td>{{ $no++ }}</td>
			@foreach($indexFields as $field => $structure)
			<td class="text-center">
				{{ AvelcaController::viewIndexContent($record, $structure, $field) }}
			</td>
			@endforeach
		</tr>
		@endforeach
	</tbody>
</table>