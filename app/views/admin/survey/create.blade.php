@extends('layouts/default')

@section('content')

<div>
	<h3>Create survey</h3>
	<div class="modal-body">
		{{ Form::open(array('url' => '/admin/survey', 'class' => 'form-horizontal', 'files'=>true)) }}

		<div class="form-group">
			{{ Form::label("Survey Name", "", array("class" => "control-label col-md-3")) }}
			<div class="col-md-3">
				{{ Form::text("survey_name","", array("class" => "form-control")) }}
			</div>
		</div>

		<div class="form-group">
			{{ Form::label("Language Name", "", array("class" => "control-label col-md-3")) }}
			<div class="col-md-3">
				{{ Form::text("url_name","", array("class" => "form-control")) }}
			</div>
		</div>

		<div class="form-group upload-field">
			{{ Form::label("Add Header Survey CSV file", "", array("class" => "control-label col-md-3")) }}
			<div class="col-md-3">
				<input id="input-id" type="file" class="excel-upload" data-preview-file-type="text" name="header_file"></div>
		</div>

		<div class="form-group upload-field">
			{{ Form::label("Add Participants Survey CSV file", "", array("class" => "control-label col-md-3")) }}
			<div class="col-md-3">
				<input id="input-id" type="file" class="excel-upload" data-preview-file-type="text" name="baseline_file"></div>
		</div>
		
		<!-- <div class="form-group upload-field">
			{{ Form::label("Add GeoJson file", "", array("class" => "control-label col-md-3")) }}
			<div class="col-md-3">
				<input id="input-id" type="file" class="excel-upload" data-preview-file-type="text" name="geojson">
			</div>
		</div> -->

		<div class="modal-footer">
			<button class="btn" type="submit" style="background-color: {{ Setting::meta_data('general', 'theme_color')->value }}; color: #ffffff;">Next</button>
		</div>
		{{ Form::close() }}
	</div>
</div>

@stop
@if(Session::has('survey_deleted'))
<h1 class="alert">{{ Session::get('survey_deleted') }}</h1>
@endif
@stop