@extends('layouts/default')

@section('content')

<div>
<!-- 	<ol class="breadcrumb">
	  <li class="active">Create a survey</li>
	  <li><a href="#"></a>import baseline cycle</li>
	  <li class="active">Import endline cycle</li>
	</ol> -->

	{{ Session::get('message') ? Form::showMessage(Session::get('message'), 1) : ''}}

	<h3>Create survey</h3>
	<div class="modal-body">
		{{ Form::open(array('url' => '/admin/survey/baseline', 'class' => 'form-horizontal', 'files' => true)) }}

		<!--div class="form-group">
			{{ Form::label("Survey Name", "", array("class" => "control-label col-md-3")) }}
			<div class="col-md-4">
				{{ Form::text("survey_name","", array("class" => "form-control")) }}
			</div>
		</div-->
		<div class='upload-form'>
			<div class="form-group">
				{{ Form::label("cycle Name", "", array("class" => "control-label col-md-3")) }}
				<div class="col-md-3">
					{{ Form::text("cycle_name","", array("class" => "form-control")) }}
				</div>
			</div>

			<div class="form-group">
				{{ Form::label("Add Excel file", "", array("class" => "control-label col-md-3")) }}
				<div class="col-md-3">
					<input id="input-id" type="file" class="excel-upload" data-preview-file-type="text" name="excel">
					<div class="progress" style="margin-top:10px">
					  <div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
					    <span class="sr-only">0% Complete</span>
					  </div>
					</div>
				</div>
			</div>
		</div>

		<div class="form-group">
			<!--{{ Form::label("", "", array("class" => "control-label col-md-3")) }}-->
			<div class="ms-visible">
				<div class="col-md-1">
					<select multiple="multiple" id="header-select" name="my-select[]">
						<!--option value='elem_1'>elem 1</option>
						<option value='elem_2'>elem 2</option>
						<option value='elem_3'>elem 3</option>
						<option value='elem_4'>elem 4</option>
						<option value='elem_100'>elem 100</option-->
					</select>
				</div>
			</div>
		</div>

		<div class="modal-footer">
			<button class="btn" type="submit" style="background-color: {{ Setting::meta_data('general', 'theme_color')->value }}; color: #ffffff;">Next</button>
		</div>
		{{ Form::close() }}
	</div>
</div>

@stop