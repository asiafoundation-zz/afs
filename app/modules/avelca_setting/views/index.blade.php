@extends('layouts/default')

@section('content')

<div class="row">
	<div class="col-lg-12">
		
		<div class="page-header">
			<h1>Setting</h1>
			<small class="pull-right">{{ Widget::laravel_version() }}</small><br>
		</div>


		<ul class="nav nav-tabs" id="tabs">

			<?php $i = 1; ?>
			@foreach($settings as $setting)
			<li 
			@if($i == 1)
			class="active"
			@endif
			><a href="#{{ $setting->category }}">{{ ucwords(str_replace('_',' ',$setting->category)) }}</a></li>
			<?php $i++; ?>
			@endforeach
		</ul>

		<div class="tab-content">

			<?php $i = 1; ?>

			@foreach($settings as $setting)
			<div class="tab-pane
			@if($i == 1)
			{{ 'active' }}
			@endif
			" id="{{ $setting->category }}">
			{{ Form::open(array('url' => 'admin/setting/update-'.str_replace('_','-',$setting->category), 'class' => 'form-horizontal')) }}

			{{ Form::hidden('category', $setting->category) }}

			<?php $meta_data = json_decode($setting->meta_data); ?>

			<fieldset><br>

				@foreach($meta_data as $data => $key)
				<div class="form-group">

					<label class="col-md-2 control-label">{{ ucwords(str_replace('_',' ',$data)) }}</label>
					<div class="col-md-4">
						<?php
						switch($key->type)
						{
							case 'text':
							echo '<input type="text" class="form-control" name="'.$data.'" value="'.$key->value.'" required>';
							break;

							case 'radio':
							echo Form::hidden($data.'_type', 'radio');
							echo Form::hidden($data.'_options', json_encode($key->options));
							foreach($key->options as $option => $x):
								echo '<label class="radio-inline">';
							if($key->selected == $x):
								echo Form::radio($data, $x, true).' '.$option;
							else:
								echo Form::radio($data, $x).' '.$option;
							endif;
							echo '</label>';
							endforeach;
							break;

							case 'number':
							echo '<div class="input-group">';
							echo '<input type="number" min="0" class="form-control" name="'.$data.'" value="'.$key->value.'" required>';
							echo '<span class="input-group-addon">'.$key->unit.'</span>';
							echo '</div>';
							echo Form::hidden($data.'_unit', $key->unit);
							break;

							case 'color':
							echo '
							<select name="theme_color" id="colorpicker">
								<option value="#8f2041">#8f2041</option>
								<option value="#00948a">#00948a</option>
								<option value="#cf3510">#cf3510</option>
								<option value="#fac51c">#fac51c</option>
								<option value="#553982">#553982</option>
								<option value="#ac725e">#ac725e</option>
								<option value="#d06b64">#d06b64</option>
								<option value="#f83a22">#f83a22</option>
								<option value="#fa573c">#fa573c</option>
								<option value="#ff7537">#ff7537</option>
								<option value="#ff7537">#ff7537</option>
								<option value="#ffad46">#ffad46</option>
								<option value="#42d692">#42d692</option>
								<option value="#16a765">#16a765</option>
								<option value="#7bd148">#7bd148</option>
								<option value="#b3dc6c">#b3dc6c</option>
								<option value="#fbe983">#fbe983</option>
								<option value="#fad165">#fad165</option>
								<option value="#92e1c0">#92e1c0</option>
								<option value="#9fe1e7">#9fe1e7</option>
								<option value="#9fc6e7">#9fc6e7</option>
								<option value="#4986e7">#4986e7</option>
								<option value="#9a9cff">#9a9cff</option>
								<option value="#b99aff">#b99aff</option>
								<option value="#c2c2c2">#c2c2c2</option>
								<option value="#cabdbf">#cabdbf</option>
								<option value="#cca6ac">#cca6ac</option>
								<option value="#f691b2">#f691b2</option>
								<option value="#cd74e6">#cd74e6</option>
								<option value="#a47ae2">#a47ae2</option>
							</select>
							';
							break;

							case 'select':
							echo Form::hidden($data.'_type', 'select');
							echo Form::hidden($data.'_options', json_encode($key->options));
							$pilihan = array();
							foreach($key->options as $option => $x):
								$pilihan[$x] = $option;
							endforeach;

							echo Form::select($data, $pilihan, $key->selected, array('class' => 'form-control'));
							break;

						}

						?>


					</div>
				</div>

				@endforeach

				<div class="form-group">
					<div class="col-md-4 col-md-offset-2">
						<input type="submit" value="Save" class="btn" style="background-color: {{ Setting::meta_data('general', 'theme_color')->value }}; color: #ffffff;">
					</div>
				</div>
			</fieldset>



			{{ Form::close() }}
		</div>
		<?php $i++; ?>

		@endforeach

	</div>

	<script type="text/javascript">

		$().ready(function(){

			/* Color Picker */
			$('select[id="colorpicker"]').simplecolorpicker({
				'theme' : 'fontawesome',
				'selectColor' : "{{ Setting::meta_data('general', 'theme_color')->value }}"
			});

		});
	</script>

</div>
</div>

@stop