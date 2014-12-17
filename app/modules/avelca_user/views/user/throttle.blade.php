<!-- Reset Password Modal -->
<div class="modal fade" id="reset_password_modal-{{ $user->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Confirm Reset Password</h4>
			</div>
			<div class="modal-body">
				<form action="{{ URL::to('admin/user/reset-password') }}" method="post">
					<input type="hidden" name="id" value="{{ $user->id }}">
					<p>Do you wish to reset password <b>{{ $user->first_name." ".$user->last_name }}</b>?</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">{{ Lang::get("general.close") }}</button>
					<button class="btn" type="submit" style="background-color: {{ Setting::meta_data('general', 'theme_color')->value }}; color: #ffffff;">{{ Lang::get("general.confirm") }}</button>
				</div>
			</form>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- Ban Modal -->
<div class="modal fade" id="ban_user_modal-{{ $user->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Confirm Ban User</h4>
			</div>
			<div class="modal-body">
				<form action="{{ URL::to('admin/user/ban') }}" method="post">
					<input type="hidden" name="id" value="{{ $user->id }}">
					<p>Do you wish to ban <b>{{ $user->first_name." ".$user->last_name }}</b>?</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">{{ Lang::get("general.close") }}</button>
					<button class="btn" type="submit" style="background-color: {{ Setting::meta_data('general', 'theme_color')->value }}; color: #ffffff;">{{ Lang::get("general.confirm") }}</button>
				</div>
			</form>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<!-- Unban Modal -->
<div class="modal fade" id="unban_user_modal-{{ $user->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Confirm Unban User</h4>
			</div>
			<div class="modal-body">
				<form action="{{ URL::to('admin/user/unban') }}" method="post">
					<input type="hidden" name="id" value="{{ $user->id }}">
					<p>Do you wish to unban <b>{{ $user->first_name." ".$user->last_name }}</b>?</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">{{ Lang::get("general.close") }}</button>
					<button class="btn" type="submit" style="background-color: {{ Setting::meta_data('general', 'theme_color')->value }}; color: #ffffff;">{{ Lang::get("general.confirm") }}</button>
				</div>
			</form>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<!-- Suspend Modal -->
<div class="modal fade" id="suspend_user_modal-{{ $user->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Confirm Suspend User</h4>
			</div>
			<div class="modal-body">
				<form action="{{ URL::to('admin/user/suspend') }}" method="post">
					<input type="hidden" name="id" value="{{ $user->id }}">
					<p>Do you wish to suspend <b>{{ $user->first_name." ".$user->last_name }}</b>?</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">{{ Lang::get("general.close") }}</button>
					<button class="btn" type="submit" style="background-color: {{ Setting::meta_data('general', 'theme_color')->value }}; color: #ffffff;">{{ Lang::get("general.confirm") }}</button>
				</div>
			</form>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<!-- Unsuspend Modal -->
<div class="modal fade" id="unsuspend_user_modal-{{ $user->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Confirm Unsuspend User</h4>
			</div>
			<div class="modal-body">
				<form action="{{ URL::to('admin/user/unsuspend') }}" method="post">
					<input type="hidden" name="id" value="{{ $user->id }}">
					<p>Do you wish to unsuspend <b>{{ $user->first_name." ".$user->last_name }}</b>?</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">{{ Lang::get("general.close") }}</button>
					<button class="btn" type="submit" style="background-color: {{ Setting::meta_data('general', 'theme_color')->value }}; color: #ffffff;">{{ Lang::get("general.confirm") }}</button>
				</div>
			</form>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
										</div><!-- /.modal -->