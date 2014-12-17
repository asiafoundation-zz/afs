<div class="modal fade" id="create_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Create Group</h4>
			</div>
			<div class="modal-body">
				<form action="{{ URL::to('admin/group/create') }}" method="post">

					<div class="form-group">
						{{ Form::label("Name") }}<br>
						{{ Form::text("name", null, array("class" => "form-control")) }}
					</div>

					<div class="form-group">
						<?php $last = ''; ?>
						@foreach($permissions as $data)
						@if( ! str_contains($data->name, '.'))
						<hr>{{ Form::label(ucwords($data->name)) }}<br>
						@else
						<?php
						$name = str_replace('-',' ', $data->name);
						$name = explode('.', $name);
						?>
						@if( empty($name[2]) && $last != $name[0] )
						<hr>{{ Form::label(ucwords($name[0].' '.$name[1])) }}<br>
						@endif
						@endif
						<div class="checkbox">
							<label>
								<input name="permissions[]" type="checkbox" value="{{ $data->id }}">
								@if(str_contains($data->name, '.'))
								<?php
								$name = str_replace('-',' ', $data->name);
								$name = explode('.', $name);
								$last = $name[0];
								?>
								@if( ! empty($name[2]) )
								{{ ucwords($name[2]) }}
								<?php $last = $name[1].' '.$name[2]; ?>
								@else
								{{ ucwords($name[1]) }}
								@endif
								@else
								{{ 'List' }}
								<?php $last = $data->name; ?>
								@endif
							</label>
						</div>
						@endforeach
					</div>
				</div>

				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">{{ Lang::get("general.close") }}</button>
					<button class="btn" type="submit" style="background-color: {{ Setting::meta_data('general', 'theme_color')->value }}; color: #ffffff;">{{ Lang::get("general.confirm") }}</button>
				</div>
			</form>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog modal-sm -->
</div>