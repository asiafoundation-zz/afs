@extends('layouts/default')

@section('content')
<script type="text/javascript">
setInterval(function() {
  window.location.reload();
}, 20000);
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
			<h1>{{ $survey->name }} ({{$cycle->name}})</h1>
			<hr>
		</div>

		<div class="table-responsive">
			<table class="datatable table table-striped table-bordered">
				<thead>
					<tr>
						<th>{{Lang::get('backend.survey_name')}}</th>
						<th>{{Lang::get('backend.publish_status')}}</th>
						<th width="100px">{{Lang::get('general.action')}}</th>
					</tr>
				</thead>
				<tbody>
				@foreach($questions as $question)
					<tr>
						<td>{{$question->master_code}}@if(!empty($question->master_code)){{}} @endif</td>
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
						<td>{{ Form::checkbox('name', 'value'); }}{{Lang::get('general.is_default')}}</td>
					</tr>
				@endforeach
				</tbody>
			</table>
		</div>

	</div>			
</div>

@stop
