@extends('layouts/default')

@section('content')

<script type="text/javascript" src="{{ Theme::asset('js/category.js') }}"></script>

<div>
	<input type=hidden name="survey_id" id="survey_id" value="{{ $survey->id }}">
	<h3 id="category_title">{{ $content[0] }}</h3>
	<span id="category_text">{{ $content[1] }}</span>

	<div class="modal-body">
		<a class="btn"  style="background-color: {{ Setting::meta_data('general', 'theme_color')->value }}; color: #ffffff;" onclick="select_all()">{{Lang::get('general.select_all')}}</a>
		<hr>
		<div class="form-group">
			<div class="ms-visible">
				<div class="col-md-6">
					<select multiple="multiple" id="header-select" name="header[]">
						@if($base_header == true)
							@foreach ($header as $header_single)
									<option  class="header-options" id="header-option-{{ $header_single['header1']}}" value="{{ $header_single['header1']}};{{ $header_single['header2']}}" ondblclick="category_clicked(
									'region',
									'{{ $header_single['header0'] }}',
									'{{ $header_single['header1'] }}',
									'{{ $header_single['header2'] }}',
									'{{ $header_single['header3'] }}',
									'{{ $header_single['header4'] }}')">{{ $header_single['header3']}}</option>
							@endforeach
						@endif
					</select>
				</div>
				<div class="col-md-6">
					<select multiple="multiple" id="header-selected" name="header_selected[questions][]">
					</select>
				</div>
			</div>
		</div>
	</div>

	<div class="modal-footer">
		<a class="btn"  style="background-color: {{ Setting::meta_data('general', 'theme_color')->value }}; color: #ffffff;" onclick="change_page('back')">{{Lang::get('general.back')}}</a>
		<a class="btn"  style="background-color: {{ Setting::meta_data('general', 'theme_color')->value }}; color: #ffffff;" onclick="change_page('next')">{{Lang::get('general.next')}}</a>
	</div>
</div>

<div class="modal fade" id="view_table_category" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
			<div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">{{ Lang::get('backend.category_selected') }}</h4>
      </div>
      <div class="modal-body" id="loading-flag">
      	<div class="col-md-4">&nbsp;</div>
      	<div class="col-md-4">
      		<img src="{{ Theme::asset('img/ajax-loader.gif') }}">
      	</div>
      	<div class="col-md-4">&nbsp;</div>
      </div>
      <div class="modal-body" id="popup_modal">
      	<div class="row">
	      	<div class="col-md-3">
						<table class="datatable table table-striped table-bordered" id="category">
							<thead>
								<tr>
									<th>{{Lang::get('backend.region_code')}}</th>
									<th>{{Lang::get('backend.region_name')}}</th>
								</tr>
								</thead>
								<tbody>
									<tr id="category_region"></tr>
								</tbody>
						</table>
					</div>
					<div class="col-md-3">
						<table class="datatable table table-striped table-bordered" id="category">
							<thead>
								<tr>
									<th>{{Lang::get('backend.wave_code')}}</th>
									<th>{{Lang::get('backend.wave_name')}}</th>
								</tr>
								</thead>
								<tbody>
									<tr id="category_wave"></tr>
								</tbody>
						</table>
					</div>
					<div class="col-md-3">
						<table class="datatable table table-striped table-bordered" id="category">
							<thead>
								<tr>
									<th>{{Lang::get('backend.oversample_code')}}</th>
									<th>{{Lang::get('backend.oversample_name')}}</th>
								</tr>
								</thead>
								<tbody>
									<tr id="category_oversample"></tr>
								</tbody>
						</table>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<table class="datatable table table-striped table-bordered" id="category">
							<thead>
								<tr>
									<th>{{Lang::get('backend.category_filter_code')}}</th>
									<th>{{Lang::get('backend.category_filter_name')}}</th>
								</tr>
								</thead>
								<tbody id="category_filter_table"></tbody>
						</table>
					</div>
					<div class="col-md-6">
						<table class="datatable table table-striped table-bordered" id="category">
							<thead>
								<tr>
									<th>{{Lang::get('backend.question_code')}}</th>
									<th>{{Lang::get('backend.question_name')}}</th>
								</tr>
								</thead>
								<tbody id="category_question"></tbody>
						</table>
					</div>
				</div>
				
      </div>
      <div class="modal-footer" id="button-footer">
        <a type="button" class="btn btn-default" data-dismiss="modal">{{Lang::get('general.cancel')}}</a>
        <a type="button" class="btn btn-primary" onclick="post_category()">{{Lang::get('general.save_changes')}}</a>
      </div>
    </div>
  </div>
</div>

@stop