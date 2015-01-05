@extends('layouts/default')

@section('content')

<script src="{{ Theme::asset('js/leaflet.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ Theme::asset('css/leaflet.css') }}">
<!--
<script type="text/javascript" src="{{ URL::to('/uploads') }}/{{ $survey->geojson_file}}"></script>
-->

@include('admin/survey/js/mapjs')
<div class="row">
	<div class="col-md-12">
		<div class="notification">
		<div class="modal-header">
			<h1>{{ $survey->name }}</h1>
			@if(Session::has('message'))
				<div class="alert {{ Session::get('alert-class', 'alert-info') }}">
					<button class="close" type="button" data-dismiss="alert">×</button>
				{{ Session::get('message') }}
				</div>
			@endif
			<hr>
			@foreach($cycles as $cycle_id_loop =>$cycle)
			<a href="/admin/survey/cycle?cycle_id={{$cycle_id_loop}}&survey_id={{ $survey->id }}"><button class="btn" style="background-color: {{ Setting::meta_data('general', 'theme_color')->value }}; color: #ffffff;">Manage {{$cycle}}</button></a>
			@endforeach

      <!-- GeoJson functions 
		<div class="modal-body" style="position: relative; right: 0px; top: 0px; width: 100%; height: 690px">	
      <div id="map" class="map-canvas" style="position: absolute; right: 0px; top: 0px; width: 100%; height: 680px"></div>
		</div>

    <div class="modal-footer">-->
      <a style="align:right;" data-toggle="modal" href="#manage_default_question" ><button class="btn" style="background-color: {{ Setting::meta_data('general', 'theme_color')->value }}; color: #ffffff;">{{Lang::get('general.manage_default_question')}}</button></a>

      <a data-toggle="modal" href="#information_column" style="align:right;"><button class="btn" style="background-color: {{ Setting::meta_data('general', 'theme_color')->value }}; color: #ffffff;">{{Lang::get('general.edit_information')}}</button></a>

      
			<!-- <a href="/admin/survey/defaultquestion/{{ $survey->id }}" style="aligh:right;"><button class="btn" style="background-color: {{ Setting::meta_data('general', 'theme_color')->value }}; color: #ffffff;">{{Lang::get('general.publish')}}</button></a> -->
		</div>
	</div>
</div>

<div class="modal fade" id="manage_default_question" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
			<div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">{{ Lang::get('general.manage_default_question') }}</h4>
      </div>
      {{ Form::open(array('url' => '/admin/survey/defaultquestion', 'class' => 'form-horizontal')) }}
      <div class="modal-body" id="popup_modal">
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							{{ Form::label("Cycle Name", "", array("class" => "control-label col-md-3")) }}
							<div class="col-md-9">
								{{ Form::select('cycle_select', $cycles,$default_question->id_cycle, array("id" => "cycle_select_modal","onchange"=>"cycle_select_option($survey->id)")) }}
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					&nbsp;
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							{{ Form::label("Question Name", "", array("class" => "control-label col-md-3")) }}
							<div class="col-md-9">
								{{ Form::select('question_select', $question_lists,$default_question->id_question, array("class" => "question_select_modal","id" => "question_select_modal")) }}
							</div>
						</div>
					</div>
				</div>
      </div>
      <div class="modal-footer">
        <a type="button" class="btn btn-default" data-dismiss="modal">{{Lang::get('general.cancel')}}</a>
        <button class="btn" type="submit" style="background-color: {{ Setting::meta_data('general', 'theme_color')->value }}; color: #ffffff;">{{Lang::get('general.save')}}</button>
      </div>
      {{ Form::close() }}
    </div>
  </div>
</div><!-- 

<div class="modal fade" id="upload_file" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
			<div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">{{ Lang::get('general.upload_file') }}</h4>
      </div>
      {{ Form::open(array('url' => '/admin/survey', 'class' => 'form-horizontal')) }}
      <div class="modal-body">
      	{{ Form::hidden('survey_id', $survey->id) }}
      		<div class="row">
      			{{ Form::label("Add Header Survey CSV file", "", array("class" => "control-label col-md-3")) }}
      			<div class="col-md-3">
      				<input id="input-id" type="file" class="excel-upload" data-preview-file-type="text" name="header_file">
      			</div>
      		</div>
      		<div class="row">
      			{{ Form::label("Add Participants Survey CSV file", "", array("class" => "control-label col-md-3")) }}
      			<div class="col-md-3">
      				<input id="input-id" type="file" class="excel-upload" data-preview-file-type="text" name="baseline_file">
      			</div>
      		</div>
      	</div>
      <div class="modal-footer">
        <a type="button" class="btn btn-default" data-dismiss="modal">{{Lang::get('general.cancel')}}</a>
        <button class="btn" type="submit" class="btn btn-primary">{{Lang::get('general.save')}}</button>
      </div>
      {{ Form::close() }}
    </div>
  </div>
</div>

 <div class="modal fade" id="upload_map" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
			<div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">{{ Lang::get('general.upload_map') }}</h4>
      </div>
      {{ Form::open(array('url' => '/admin/survey', 'class' => 'form-horizontal')) }}
      <div class="modal-body">
      	<div class="form-group upload-field">
      		{{ Form::hidden('survey_id', $survey->id) }}
					{{ Form::label("Add Geojson file:", "", array("class" => "control-label col-md-3")) }}
					<div class="col-md-3">
						<input id="input-id" type="file" class="excel-upload" data-preview-file-type="text" name="geojson">
						<div class="progress" style="margin-top:10px;display: none;">
						  <div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
						    <span class="sr-only">0% Complete</span>
						  </div>
						</div>
					</div>
				</div>
      </div>
      <div class="modal-footer">
        <a type="button" class="btn btn-default" data-dismiss="modal">{{Lang::get('general.cancel')}}</a>
        <button class="btn" type="submit" class="btn btn-primary">{{Lang::get('general.save')}}</button>
      </div>
      {{ Form::close() }}
    </div>
  </div>
</div>
-->

<div class="modal fade" id="information_column" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
			<div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">{{ Lang::get('general.edit_information') }}</h4>
      </div>
      {{ Form::open(array('url' => '/admin/survey', 'class' => 'form-horizontal')) }}
      <div class="modal-body">
      	<div class="form-group upload-field">
      		{{ Form::hidden('survey_id', $survey->id) }}
					{{ Form::label("Add Information Column:", "", array("class" => "control-label col-md-3")) }}
					<div class="col-md-3">
						{{ Form::textarea('information', $survey->information, array("class" => "textarea")) }}
					</div>
				</div>
      </div>
      <div class="modal-footer">
        <a type="button" class="btn btn-default" data-dismiss="modal">{{Lang::get('general.cancel')}}</a>
        <button class="btn" type="submit" class="btn btn-primary">{{Lang::get('general.save')}}</button>
      </div>
      {{ Form::close() }}
    </div>
  </div>
</div>
@stop
