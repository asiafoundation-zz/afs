@extends('layouts/default')

@section('content')

<div>
<!-- 	<ol class="breadcrumb">
	  <li class="active">Create a survey</li>
	  <li class="active">import baseline cycle</li>
	  <li><a href="#"></a>Import endline cycle</li>
	</ol> -->
	<h3>Upload survey excel</h3>
	<div class="modal-body">
		{{ Form::open(array('url' => '/admin/survey/upload', 'class' => 'form-horizontal', 'files' => true)) }}

		<div class="form-group">
			{{ Form::label("Baseline file", "", array("class" => "control-label col-md-3")) }}
			<div class="col-md-3">
				<input type="hidden" name="id_cycle" value="{{ Request::segment(4) }}">
				{{ Form::file('excel','',array('id'=>'','class'=>'')) }}
			</div>
		</div>
		<!--div class="form-group">
			{{ Form::label("Endline file", "", array("class" => "control-label col-md-3")) }}
			<div class="col-md-3">
				<input id="input-id" type="file" class="file" data-preview-file-type="text" name="excel">
			</div>
		</div-->

		<div class="modal-footer">
			<button class="btn" type="submit" style="background-color: {{ Setting::meta_data('general', 'theme_color')->value }}; color: #ffffff;">Upload / Finish</button>
		</div>
		{{ Form::close() }}
	</div>
</div>

@stop