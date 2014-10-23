@extends('layouts/default')

@section('content')

<div>
	<ol class="breadcrumb">
	  <li><a href="#">Create a survey</a></li>
	  <li class="active">Import baseline cycle</li>
	  <li class="active">Import endline cycle</li>
	  <!--li><a href="#">Create survey</a></li-->
	</ol>
	
	<!--h4 style="text-align:center">You don't have any survey yet <a class="label label-primary" href="#">click here to create a new survey</a></h4-->
	<!--div class="alert alert-info" role="alert">
		You don't have any survey yet <a href="/admin/survey/cycle" class="alert-link">create a new survey</a>
	</div-->

	<h3>Create survey</h3>
	<div class="modal-body">
		{{ Form::open(array('url' => '/admin/survey', 'class' => 'form-horizontal')) }}

		<div class="form-group">
			{{ Form::label("Survey Name", "", array("class" => "control-label col-md-3")) }}
			<div class="col-md-3">
				{{ Form::text("survey_name","", array("class" => "form-control")) }}
			</div>
		</div>

		<!--div class="form-group">
			{{ Form::label("Baseline file", "", array("class" => "control-label col-md-3")) }}
			<div class="col-md-3">
				<input id="input-id" type="file" class="file" data-preview-file-type="text" name="baseline_file">
			</div>
		</div>

		<div class="form-group">
			{{ Form::label("Endline file", "", array("class" => "control-label col-md-3")) }}
			<div class="col-md-3">
				<input id="input-id" type="file" class="file" data-preview-file-type="text" name="endline_file">
			</div>
		</div-->

		<div class="modal-footer">
			<button class="btn" type="submit" style="background-color: {{ Setting::meta_data('general', 'theme_color')->value }}; color: #ffffff;">Next</button>
		</div>
		{{ Form::close() }}
	</div>
</div>

@stop