@extends('layouts/default')

@section('content')

<div>
	<ol class="breadcrumb">
	  <li class="active">Create a survey</li>
	  <li class="active">import baseline cycle</li>
	  <li><a href="#"></a>Import endline cycle</li>
	</ol>
	<h3>Upload survey excel</h3>
	<div class="modal-body">
		{{ Form::open(array('url' => '/admin/survey/import', 'class' => 'form-horizontal', 'files' => true)) }}
		<div class="form-group">
			<div class="ms-visible">
				<div class="col-md-1">
					<select multiple="multiple" id="header-select" name="header[]">
						<?php
							foreach($header as $value){
								echo "<option value='". $value['header1'] .";". $value['header3'] ."'>". $value['header3'] ."</option>";
							}
						?>
					</select>
					<input type="hidden" name="id_cycle" value="{{ Request::segment(4) }}">
					<?php
						foreach($header as $value){
							echo "<input type='hidden' name='question[]' value='". $value['header1'] .";". $value['header3'] ."'>";
						}
					?>
					
				</div>
			</div>
		</div>

		<div class="modal-footer">
			<button class="btn" type="submit" style="background-color: {{ Setting::meta_data('general', 'theme_color')->value }}; color: #ffffff;">Import / Finish</button>
		</div>
		{{ Form::close() }}
	</div>
</div>

@stop