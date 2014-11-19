@extends('layouts/default')

@section('content')

<div>
<!-- 	<ol class="breadcrumb">
	  <li><a href="#">Create a survey</a></li>
	  <li class="active">Import baseline cycle</li>
	  <li class="active">Import endline cycle</li>
	  <!--li><a href="#">Create survey</a></li
	</ol> -->
	
	<!--h4 style="text-align:center">You don't have any survey yet <a class="label label-primary" href="#">click here to create a new survey</a></h4-->
	<!--div class="alert alert-info" role="alert">
		You don't have any survey yet <a href="/admin/survey/cycle" class="alert-link">create a new survey</a>
	</div-->

	<h3>Create survey</h3>
	<div class="modal-body">
		{{ Form::open(array('url' => '/admin/survey', 'class' => 'form-horizontal', 'files'=>true)) }}

		<div class="form-group">
			{{ Form::label("Survey Name", "", array("class" => "control-label col-md-3")) }}
			<div class="col-md-3">
				{{ Form::text("survey_name","", array("class" => "form-control")) }}
			</div>
		</div>

		<div class="form-group upload-field">
			{{ Form::label("Add Excel file", "", array("class" => "control-label col-md-3")) }}
			<div class="col-md-3">
				<input id="input-id" type="file" class="excel-upload" data-preview-file-type="text" name="excel">
<!-- 				<div class="progress" style="margin-top:10px">
				  <div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
				    <span class="sr-only">0% Complete</span>
				  </div>
				</div> -->
			</div>
		</div>
		
		<div class="form-group upload-field">
			{{ Form::label("Add GeoJson file", "", array("class" => "control-label col-md-3")) }}
			<div class="col-md-3">
				<input id="input-id" type="file" class="excel-upload" data-preview-file-type="text" name="geojson">
			</div>
		</div>

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