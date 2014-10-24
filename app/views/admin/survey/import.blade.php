@extends('layouts/default')

@section('content')

<div>
	<ol class="breadcrumb">
	  <li class="active">Create a survey</li>
	  <li class="active">import baseline cycle</li>
	  <li><a href="#"></a>Import endline cycle</li>
	</ol>
	<h3>{{ $content[0] }}</h3>
	{{ $content[1] }}
	<div class="modal-body">
		{{ Form::open(array('url' => $action, 'class' => 'form-horizontal', 'files' => true)) }}
		<div class="form-group">
			<div class="ms-visible">
				<div class="col-md-1">
					<select multiple="multiple" id="header-select" name="header[]">
						<?php
							if($base_header == true)
							{
								foreach($header as $value)
								{
									echo "<option value='". $value['header1'] .";". $value['header3'] ."'>". $value['header3'] ."</option>";
								}	
							}
							else
							{
								foreach($header as $value)
								{
									$arr_value = explode(';', $value);
									echo "<option value='". $value ."'>". $arr_value[1] ."</option>";
								}
							}
						?>
					</select>
					
					<?php
						if($base_header == true)
						{
							echo "<input type='hidden' name='id_cycle' value='". Request::segment(4) ."''>";

							foreach($header as $value){
								echo "<input type='hidden' name='unselected[]' value='". $value['header0'] .";". $value['header1'] .";". $value['header3'] ."'>";
							}
						}
						else
						{
							echo "<input type='hidden' name='id_cycle' value='". $id_cycle ."'>";

							foreach($header as $value){
								echo "<input type='hidden' name='unselected[]' value='". $value ."'>";
							}	
						}
					?>
					
				</div>
			</div>
		</div>

		<div class="modal-footer">
			<button class="btn" type="submit" style="background-color: {{ Setting::meta_data('general', 'theme_color')->value }}; color: #ffffff;">{{ $button }}</button>
		</div>
		{{ Form::close() }}
	</div>
</div>

@stop