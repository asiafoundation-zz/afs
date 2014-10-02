<div class="modal fade" id="view_modal-{{ $user->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">View User</h4>
			</div>
			<div class="modal-body text-left">
				{{ Form::open(array('class' => 'form-horizontal')) }}


				<div class="form-group">
					{{ Form::label("Id", "", array("class" => "col-md-4 control-label")) }}
					<div class="col-md-8">
						<p class="form-control-static text-left">{{ $user->id}}</p>
					</div>
				</div>

				<div class="form-group">
					{{ Form::label("Group", "", array("class" => "col-md-4 control-label")) }}
					<div class="col-md-8">
						<?php $user_id = $user->id; $group_id = UserGroup::where('user_id','=',$user->id)->first()->group_id; ?>
						<p class="form-control-static text-left">{{ App\Modules\Avelca_User\Models\Group::find($group_id)->name }}</p>
					</div>
				</div>

				<div class="form-group">
					{{ Form::label("First Name", "", array("class" => "col-md-4 control-label")) }}
					<div class="col-md-8">
						<p class="form-control-static text-left">{{ $user->first_name}}</p>
					</div>
				</div>


				<div class="form-group">
					{{ Form::label("Last Name", "", array("class" => "col-md-4 control-label")) }}
					<div class="col-md-8">
						<p class="form-control-static text-left">{{ $user->last_name}}</p>
					</div>
				</div>



				<div class="form-group">
					{{ Form::label("Email", "", array("class" => "col-md-4 control-label")) }}
					<div class="col-md-8">
						<p class="form-control-static text-left">{{ $user->email}}</p>
					</div>
				</div>



				<div class="form-group">
					{{ Form::label("Activated", "", array("class" => "col-md-4 control-label")) }}
					<div class="col-md-8">
						<p class="form-control-static text-left">
							@if($user->activated == "1")
							{{ "Yes" }}
							@else
							{{ "No" }}
							@endif
						</p>
					</div>
				</div>


				<div class="form-group">
					{{ Form::label("Last Login", "", array("class" => "col-md-4 control-label")) }}
					<div class="col-md-8">
						<p class="form-control-static text-left">{{ $user->last_login}}</p>
					</div>
				</div>

				<div class="form-group">
					{{ Form::label("Created At", "", array("class" => "col-md-4 control-label")) }}
					<div class="col-md-8">
						<p class="form-control-static text-left">{{ $user->created_at}}</p>
					</div>
				</div>

				<div class="form-group">
					{{ Form::label("Updated At", "", array("class" => "col-md-4 control-label")) }}
					<div class="col-md-8">
						<p class="form-control-static text-left">{{ $user->updated_at}}</p>
					</div>
				</div>

			</div>
			<div class="modal-footer">
				<button type="button" class="btn" data-dismiss="modal" style="background-color: {{ Setting::meta_data('general', 'theme_color')->value }}; color: #ffffff;">{{ Lang::get("general.close") }}</button>
			</div>
		</form>
	</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div>





