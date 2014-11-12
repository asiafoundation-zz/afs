@extends('layouts/default')

@section('content')
@if($is_refresh)
<script type="text/javascript">
setInterval(function() {
  window.location.reload();
}, 3000);
</script>
@endif

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
			<a href="/survey/reupload/"><button class="btn" style="background-color: {{ Setting::meta_data('general', 'theme_color')->value }}; color: #ffffff;">{{Lang::get('general.remove_all_surveys')}}</button></a>
		</div>

		<div class="table-responsive">
			<table class="datatable table table-striped table-bordered">
				<thead>
					<tr>
						<th width="50px">{{Lang::get('backend.no')}}</th>
						<th>{{Lang::get('backend.survey_name')}}</th>
						<th>{{Lang::get('backend.publish_status')}}</th>
<!-- 						<th width="50px">{{Lang::get('backend.is_default')}}</th> -->
						<th width="300px">{{Lang::get('general.action')}}</th>
					</tr>
				</thead>
				<tbody>
				<?php $no = 1; ?>
				@foreach($surveys as $survey)
					<tr>
						<td>{{ $no }}</td>
						<td>
							@if($survey['publish'] == 1 || $survey['publish'] == 3)
							<a href="{{ URL::to('admin/survey/managesurvey') }}/{{ $survey['id'] }}">
								{{ $survey['name'] }}
							</a>
							@else
							{{ $survey['name'] }}
							@endif
						</td>
						<td>
							@if($survey['publish_style'] == "importing")
							{{ $survey['publish_text'] }}
							<div class="progress">
								<div class="progress-bar" style="width: {{ $survey['percentage'] }}%;">
									{{ $survey['percentage'] }}%
									<span class="sr-only"></span>
								</div>
							</div>
							@else
							{{ $survey['publish_text'] }}
							@endif
						</td>
<!-- 						<td align="center">
							{{ Form::checkbox('name', 'value'); }}
						</td> -->
						<td>
							<a href="/admin/filter/{{ $survey['id'] }}" style="aligh:right;"><button class="btn" style="background-color: {{ Setting::meta_data('general', 'theme_color')->value }}; color: #ffffff;">{{Lang::get('general.manage_filter')}}</button></a>
						</td>
					</tr>
				<?php $no = $no+1; ?>
				@endforeach
				</tbody>
			</table>
		</div>

	</div>			
</div>

@stop
