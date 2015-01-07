@extends('layouts/default')

@section('content')
<script type="text/javascript">
$( document ).ready(function() {
	@if($category_show)
	$('#category_filter_notification').modal('show');
	@endif
});

@if($is_refresh)
setInterval(function() {
  window.location.reload();
}, 20000);
@endif

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
			@if($is_refresh == 0)
			<a href="/admin/survey/create/"><button class="btn" style="background-color: {{ Setting::meta_data('general', 'theme_color')->value }}; color: #ffffff;">{{Lang::get('backend.create_survey')}}</button></a>
			@endif
			<a href="/survey/reupload/"><button class="btn" style="background-color: {{ Setting::meta_data('general', 'theme_color')->value }}; color: #ffffff;">{{Lang::get('general.remove_all_surveys')}}</button></a>
		</div>

		<div class="table-responsive">
			<table class="datatable table table-striped table-bordered">
				<thead>
					<tr>
						<th width="50px">{{Lang::get('backend.no')}}</th>
						<th>{{Lang::get('backend.survey_name')}}</th>
						<th>{{Lang::get('backend.publish_status')}}</th>
						<th width="100px">{{Lang::get('general.is_default')}}</th>
						<th width="300px">{{Lang::get('general.action')}}</th>
					</tr>
				</thead>
				<tbody>
				<?php $no = 1; ?>
				@foreach($surveys as $survey)
					<tr>
						<td>{{ $no }}</td>
						<td>
							@if($survey['publish'] == 0 || $survey['publish'] == 2 || $survey['publish'] == 3 || $survey['publish'] == 6)
							{{ $survey['name'] }}
							@else
							<a href="{{ URL::to('admin/survey/managesurvey') }}/{{ $survey['id'] }}">
								{{ $survey['name'] }}
							</a>
							@endif
						</td>
						<td>
							@if($survey['publish_style'] == "importing")
							{{ $survey['percentage'] }}
							<!-- <div class="progress">
								<div class="progress-bar" style="width: 100%;">
									%
									<span class="sr-only"></span>
								</div>
							</div> -->
							@else
							{{ $survey['publish_text'] }}
							@endif
						</td>
						<td align="center">
							{{ Form::radio('is_default', $survey['id'],$survey['is_default'],array('class' => 'survey_is_default')) }}
						</td>
						<td>
							@if($survey['publish_style'] == "category")
							<a href="/admin/survey/category/{{$survey['id']}}" style="aligh:right;"><button class="btn" style="background-color: {{ Setting::meta_data('general', 'theme_color')->value }}; color: #ffffff;">{{$survey['publish_text']}}</button></a>
							@elseif($survey['publish_style'] == "completed" || $survey['publish_style'] == "published" || $survey['publish_style'] == "unpublish")
							<a href="/admin/filter/{{ $survey['id'] }}" style="aligh:right;"><button class="btn" style="background-color: {{ Setting::meta_data('general', 'theme_color')->value }}; color: #ffffff;">{{Lang::get('general.manage_filter')}}</button></a>
							@endif
							<a href="/survey/singledelete/{{ $survey['id'] }}" style="aligh:right;"><button class="btn btn-danger">{{Lang::get('backend.delete_survey')}}</button></a>
						</td>
					</tr>
				<?php $no = $no+1; ?>
				@endforeach
				</tbody>
			</table>
		</div>

	</div>			
</div>

<div class="modal fade" id="category_filter_notification" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
			<div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title">{{ Lang::get('backend.parsing_finish') }}</h4>
      </div>
      <div class="modal-body" id="popup_detail_question_body">
      	{{ Lang::get('backend.mapping_category') }}
      	<a href="/admin/survey/category/{{$survey_category_id}}" style="aligh:right;"><button class="btn" style="background-color: {{ Setting::meta_data('general', 'theme_color')->value }}; color: #ffffff;">{{Lang::get('backend.select_category')}}</button></a>
      </div>
      <div class="modal-footer">
        <a type="button" class="btn btn-default" data-dismiss="modal">{{Lang::get('general.back')}}</a>
      </div>
    </div>
  </div>
</div>

@stop
