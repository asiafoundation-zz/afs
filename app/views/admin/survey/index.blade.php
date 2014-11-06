@extends('layouts/default')

@section('content')
<script type="text/javascript">
setInterval(function() {
  window.location.reload();
}, 3000);
</script>

<div class="row">
	<div class="col-md-12">
	<div class="notification">
		@if(Session::has('message'))
		<div class="alert {{ Session::get('alert-class', 'alert-info') }}">
			<button class="close" type="button" data-dismiss="alert">×</button>
			{{ Session::get('message') }}
		</div>
		@endif
	</div>
	<div class="modal-header">
		<h1>{{Lang::get('backend.manage_survey')}}</h1>
		<hr>
		<a href="/admin/survey/create/"><button class="btn" style="background-color: {{ Setting::meta_data('general', 'theme_color')->value }}; color: #ffffff;">{{Lang::get('backend.create_survey')}}</button></a>
	</div>

		<div class="table-responsive">
			<table class="datatable table table-striped table-bordered">
				<thead>
					<tr>
						<th>{{Lang::get('backend.no')}}</th>
						<th>{{Lang::get('backend.survey_name')}}</th>
						<th>{{Lang::get('backend.publish_status')}}</th>
						<th>{{Lang::get('general.manage')}}</th>
					</tr>
					</thead>
					<tbody>
					<?php $no = 1; ?>
					@foreach($surveys as $survey)
						<tr>
							<td>{{ $no }}</td>
							<td>{{ $survey['name'] }}</td>
							<td>{{ $survey['publish_text'] }}</td>
							<td> <a href="admin/surveys/manage/{{ $survey['id'] }}">{{Lang::get('general.manage')}}</a></td>
						</tr>
					<?php $no = $no+1; ?>
					@endforeach
					</tbody>
			</table>
		</div>

	</div>			
</div>

@stop
