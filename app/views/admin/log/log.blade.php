<table class="datatable table table-striped table-bordered">
@foreach($logs as $log)
<tr>
	<td rowspan="2">Participant No:{{$log['id']}}</td>
	<td>
		<tr>
		<table>
			<thead></thead>
			<tbody>
				<tr></tr>
				<tr>
					<td>{{$log['cycle']}}</td>
					<td>{{$log['sample_type']}}</td>
				@foreach($log['filters'] as $filter)
				<td>
					{{ $filter['category_items'] }}
				</td>
				@endforeach
				</tr>
			</tbody>
		</table>
		</tr>
	</td>
	<td>
		<tr>
		<table>
			<thead></thead>
			<tbody>
				<tr></tr>
				<tr>
				@foreach($log['questions'] as $filter)
				<td>
					{{ $filter['answers'] }}
				</td>
				@endforeach
				</tr>
			</tbody>
		</table>
		</tr>
	</td>
</tr>
@endforeach
</table>