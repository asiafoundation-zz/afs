<div class="modal fade" id="create_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Create User</h4>
			</div>
			<div class="modal-body">
				{{ Form::open(array('url' => 'admin/user/create', 'class' => 'form-horizontal')) }}

				<div class="form-group">
					{{ Form::label("First Name", "", array("class" => "col-md-4 control-label")) }}
					<div class="col-md-8">
						{{ Form::text("first_name","", array("class" => "form-control", "value" => Input::old('first_name') )) }}
					</div>
				</div>


				<div class="form-group">
					{{ Form::label("Last Name", "", array("class" => "col-md-4 control-label")) }}
					<div class="col-md-8">
						{{ Form::text("last_name","", array("class" => "form-control", "value" => Input::old('last_name') )) }}
					</div>
				</div>

				<div class="form-group">
					{{ Form::label("Email", "", array("class" => "col-md-4 control-label")) }}
					<div class="col-md-8">
						{{ Form::text("email","", array("class" => "form-control", "value" => Input::old('email') )) }}
					</div>
				</div>

				<div class="form-group">
					{{ Form::label("Group", "", array("class" => "col-md-4 control-label")) }}
					<div class="col-md-8">

						<select class="form-control" name="group_id">
							<?php foreach($groups as $data): ?>
								<option value="{{ $data->id }}" 
									<?php
									if($default_group == $data->name)
									{
										echo 'selected="selected"';
									}
									?>
									>{{ $data->name }}</option>
								<?php endforeach; ?>
							</select>

						</div>
					</div>


					<div class="form-group">
						{{ Form::label("Password", "", array("class" => "col-md-4 control-label")) }}
						<div class="col-md-8">
							{{ Form::password("password", array("class" => "form-control")) }}
						</div>
					</div>

					<div class="form-group">
						{{ Form::label("Confirm Password", "", array("class" => "col-md-4 control-label")) }}
						<div class="col-md-8">
							{{ Form::password("password_confirmation", array("class" => "form-control")) }}
						</div>
					</div>

					<div class="form-group">
						{{ Form::label("Activated", "", array("class" => "col-md-4 control-label")) }}
						<div class="col-lg-4">
							<label class="checkbox-inline">
								@if($auto_activation == 1)
								{{ Form::radio("activated", "1", "true")." Yes" }}
								@else
								{{ Form::radio("activated", "1")." Yes" }}
								@endif
							</label>
							<label class="checkbox-inline">
								@if($auto_activation == 0)
								{{ Form::radio("activated", "0", "true")." No" }}
								@else
								{{ Form::radio("activated", "0")." No" }}
								@endif
							</label>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">{{ Lang::get("general.close") }}</button>
					<button class="btn" type="submit" style="background-color: {{ Setting::meta_data('general', 'theme_color')->value }}; color: #ffffff;">{{ Lang::get("general.confirm") }}</button>
				</div>
			</form>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div>