<div class="modal fade" id="edit_modal-{{ $user->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Edit User</h4>
			</div>
			<div class="modal-body text-left">
				{{ Form::open(array('url' => 'admin/user/edit', 'class' => 'form-horizontal', 'method' => 'post')) }}

				<input type="hidden" name="id" value="{{ $user->id }}">

				<div class="form-group">
					{{ Form::label("First Name", "", array("class" => "col-md-4 control-label")) }}
					<div class="col-md-8">
						{{ Form::text("first_name", $user->first_name, array("class" => "form-control")) }}
					</div>
				</div>

				<div class="form-group">
					{{ Form::label("Last Name", "", array("class" => "col-md-4 control-label")) }}
					<div class="col-md-8">
						{{ Form::text("last_name", $user->last_name, array("class" => "form-control")) }}
					</div>
				</div>

				<div class="form-group">
					{{ Form::label("Email", "", array("class" => "col-md-4 control-label")) }}
					<div class="col-md-8">
						{{ Form::text("email", $user->email, array("class" => "form-control")) }}
					</div>
				</div>

				<div class="form-group">
					{{ Form::label("Group", "", array("class" => "col-md-4 control-label")) }}
					<div class="col-md-8">

						<select class="form-control" name="group_id">
							<?php foreach($groups as $data): ?>
								<option value="{{ $data->id }}" 
									<?php
									$group_id = UserGroup::where('user_id','=',$user->id)->first()->group_id;

									if($group_id == $data->id)
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
						{{ Form::label("Activated", "", array("class" => "col-md-4 control-label")) }}
						<div class="col-md-8 text-left">
							<label class="checkbox-inline">
								@if($user->activated == 1)
								{{ Form::radio("activated", "1", "true")." Yes" }}
								@else
								{{ Form::radio("activated", "1")." Yes" }}
								@endif
							</label>
							<label class="checkbox-inline">
								@if($user->activated == 0)
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