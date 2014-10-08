@extends('layouts/default')

@section('content')

<div>
	<ol class="breadcrumb">
	  <li class="active"><a href="/admin/survey/cycle">Add survey Name</a></li>
	  <li class="active">Upload survey result</li>
	  <!--li><a href="#">Create survey</a></li-->
	</ol>
	<h3>Upload survey excel</h3>
	<div class="modal-body">
		{{ Form::open(array('url' => '', 'class' => 'form-horizontal')) }}

		<div class="form-group">
			{{ Form::label("Baseline file", "", array("class" => "control-label col-md-3")) }}
			<div class="col-md-3">
				<input id="input-id" type="file" class="file" data-preview-file-type="text" name="map">
			</div>
		</div>

		<div class="form-group">
			{{ Form::label("Endline file", "", array("class" => "control-label col-md-3")) }}
			<div class="col-md-3">
				<input id="input-id" type="file" class="file" data-preview-file-type="text" name="map">
			</div>
		</div>

		<div class="modal-footer">
			<button class="btn" type="submit" style="background-color: {{ Setting::meta_data('general', 'theme_color')->value }}; color: #ffffff;">Upload / Finish</button>
		</div>
		{{ Form::close() }}
	</div>
</div>

@stop