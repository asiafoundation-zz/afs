@extends('layouts/default')

@section('content')

<div>
	<ol class="breadcrumb">
	  <li class="active">Add survey Name</li>
	  <li><a href="/admin/survey/upload">Upload survey result</a></li>
	  <!--li><a href="#">Create survey</a></li-->
	</ol>

	<h3>Create survey</h3>
	
	<div class="modal-body">
		{{ Form::open(array('url' => '', 'class' => 'form-horizontal')) }}

		<div class="form-group">
			{{ Form::label("Survey Name", "", array("class" => "control-label col-md-3")) }}
			<div class="col-md-4">
				{{ Form::text("survey_name","", array("class" => "form-control")) }}
			</div>
		</div>

		<div class="form-group">
			{{ Form::label("Cycle Name", "", array("class" => "control-label col-md-3")) }}
			<div class="col-md-3">
				{{ Form::text("cycle_name","", array("class" => "form-control")) }}
			</div>
		</div>

		<div class="form-group">
			{{ Form::label("Add Map", "", array("class" => "control-label col-md-3")) }}
			<div class="col-md-3">
				<input id="input-id" type="file" class="file" data-preview-file-type="text" name="map">
			</div>
		</div>

		<div class="modal-footer">
			<button class="btn" type="submit" style="background-color: {{ Setting::meta_data('general', 'theme_color')->value }}; color: #ffffff;">Next</button>
		</div>
		{{ Form::close() }}
	</div>
</div>

@stop