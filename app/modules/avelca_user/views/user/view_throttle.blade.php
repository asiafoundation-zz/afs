<div class="modal fade" id="view_throttle_modal-{{ $user->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">View User Throttle</h4>
			</div>
			<div class="modal-body text-lefts">
				{{ Form::open(array('class' => 'form-horizontal')) }}

				<div class="form-group">
					{{ Form::label("IP Address", "", array("class" => "col-md-4 control-label")) }}
					<div class="col-md-8">
						<p class="form-control-static text-left">{{ $user->throttle->ip_address }}</p>
					</div>
				</div>

				<div class="form-group">
					{{ Form::label("Failed Attempts", "", array("class" => "col-md-4 control-label")) }}
					<div class="col-md-8">
						<p class="form-control-static text-left">{{ $user->throttle->attempts }}</p>
					</div>
				</div>

				<div class="form-group">
					{{ Form::label("Suspended", "", array("class" => "col-md-4 control-label")) }}
					<div class="col-md-8">
						<p class="form-control-static text-left">{{ $user->throttle->suspended }}</p>
					</div>
				</div>

				<div class="form-group">
					{{ Form::label("Banned", "", array("class" => "col-md-4 control-label")) }}
					<div class="col-md-8">
						<p class="form-control-static text-left">{{ $user->throttle->banned }}</p>
					</div>
				</div>

				<div class="form-group">
					{{ Form::label("Last Attempt At", "", array("class" => "col-md-4 control-label")) }}
					<div class="col-md-8">
						<p class="form-control-static text-left">{{ $user->throttle->last_attempt_at }}</p>
					</div>
				</div>

				<div class="form-group">
					{{ Form::label("Suspended At", "", array("class" => "col-md-4 control-label")) }}
					<div class="col-md-8">
						<p class="form-control-static text-left">{{ $user->throttle->suspended_at }}</p>
					</div>
				</div>

				<div class="form-group">
					{{ Form::label("Banned At", "", array("class" => "col-md-4 control-label")) }}
					<div class="col-md-8">
						<p class="form-control-static text-left">{{ $user->throttle->banned_at }}</p>
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