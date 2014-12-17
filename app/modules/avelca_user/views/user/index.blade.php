@extends('layouts/default')

@section('content')
<div class="row">
	<div class="col-md-12">

		<div class="page-header">
			<h1>User</h1>
		</div>

		<ol class="breadcrumb">
			<li><a href="{{ URL::to('dashboard') }}"><i class="fa fa-dashboard"></i> {{ Lang::get('general.dashboard') }}</a></li>
			<li><a href="{{ URL::to('admin/user') }}">User</a></li>
			<li class="active">List</li>
		</ol>

		<?php $customView = 'admin.user.index'; ?>
		@if(View::exists($customView))
		@include($customView)
		@else

		<div class="text-left">
			<a href="#create_modal" data-toggle="modal" class="btn" style="background-color: {{ Setting::meta_data('general', 'theme_color')->value }}; color: #ffffff;"><i class="fa fa-plus"></i> Create</a>
		</div>

		<br>

		<div class="table-responsive">
			
			<table class="datatable table table-striped table-bordered">
				<thead>
					<tr>
						<th>Full Name</th>
						<th>Group</th>
						<th>Activated</th>
						<th>Last Login</th>
						<th>Created At</th>
						<th>&nbsp;</th></tr>
					</thead>
					<tbody>
						@foreach($users as $user)
						<tr>
							<td>{{ $user->first_name.' '.$user->last_name }}</td>
							<td>
								<?php $group_id = UserGroup::where('user_id','=',$user->id)->first()->group_id; ?>
								{{ App\Modules\Avelca_User\Models\Group::find($group_id)->name }}
							</td>
							<td>
								@if($user->activated == '1')
								{{ 'Yes' }}
								@else
								{{ 'No' }}
								@endif
							</td>
							<td>{{ $user->last_login }}</td>
							<td>{{ $user->created_at }}</td>
							<td>
								<div class="btn-group">
									<button type="button" class="btn dropdown-toggle" data-toggle="dropdown" style="background-color: {{ Setting::meta_data('general', 'theme_color')->value }}; color: #ffffff;">
										<i class="fa fa-gear fa-fw"></i>
									</button>
									<ul class="dropdown-menu" role="menu" style="text-align: left;">

										<li><a href="#view_modal-{{ $user->id }}" data-toggle="modal"><i class="fa fa-search fa-fw"></i> {{ Lang::get("general.view") }}</a></li>

										<li><a href="#edit_modal-{{ $user->id }}" data-toggle="modal"><i class="fa fa-edit fa-fw"></i> {{ Lang::get("general.edit") }}</a></li>

										<li class="divider"></li>

										<li><a href="#delete_modal-{{ $user->id }}" data-toggle="modal"><i class="fa fa-trash-o fa-fw"></i> {{ Lang::get("general.delete") }}</a></li>

										<li class="divider"></li>

										<li><a href="#reset_password_modal-{{ $user->id }}" data-toggle="modal"><i class="fa fa-refresh fa-fw"></i> {{ Lang::get("general.reset_password") }}</a></li>

										<li><a href="#set_password_modal-{{ $user->id }}" data-toggle="modal"><i class="fa fa-exclamation fa-fw"></i> Set Password</a></li>

										<li><a href="#view_throttle_modal-{{ $user->id }}" data-toggle="modal"><i class="fa fa-lock fa-fw"></i> {{ Lang::get("general.view") }} Throttle</a></li>

										<li class="divider"></li>

										<?php $throttle = Sentry::findThrottlerByUserId($user->id); ?>
										@if($throttle->isBanned())

										<li><a href="#unban_user_modal-{{ $user->id }}" data-toggle="modal"><i class="fa fa-undo fa-fw"></i> {{ Lang::get("general.unban") }}</a></li>
										@else

										<li><a href="#ban_user_modal-{{ $user->id }}" data-toggle="modal"><i class="fa fa-ban fa-fw"></i> {{ Lang::get("general.ban") }}</a></li>
										@endif

										@if($throttle->isSuspended())

										<li><a href="#unsuspend_user_modal-{{ $user->id }}" data-toggle="modal"><i class="fa fa-undo fa-fw"></i> {{ Lang::get("general.unsuspend") }}</a></li>
										@else

										<li><a href="#suspend_user_modal-{{ $user->id }}" data-toggle="modal"><i class="fa fa-bolt fa-fw"></i> {{ Lang::get("general.suspend") }}</a></li>

										@endif

									</ul>

								</div>
							</td>
						</tr>

						@endforeach
					</tbody>
					<tfoot>
						<tr>
							<th><input type="text" name="full_name" placeholder="Filter" class="search_init" /></th>
							<th><input type="text" name="activated" placeholder="Filter" class="search_init" /></th>
							<th><input type="text" name="last_login" placeholder="Filter" class="search_init" /></th>
							<th><input type="text" name="created_at" placeholder="Filter" class="search_init" /></th>
							<th><input type="text" name="updated_at" placeholder="Filter" class="search_init" /></th>
							<th>&nbsp;</th></tr>

						</tfoot>
					</table>
				</div>

				<!-- Create Modal -->
				@include('avelca_user::user.create')
				<!-- /.modal -->

				<div class="text-center">
					@foreach($users as $user)

					<!-- Edit Modal -->
					@include('avelca_user::user.edit')
					<!-- /.modal -->

					<!-- View Throttle Modal -->
					@include('avelca_user::user.view_throttle')
					<!-- /.modal -->

					<!-- Delete Modal -->
					@include('avelca_user::user.delete')
					<!-- /.modal -->

					<!-- Throttle Modal -->
					@include('avelca_user::user.throttle')
					<!-- /.modal -->

					<!-- View Modal -->
					@include('avelca_user::user.view')
					<!-- /.modal -->



					<!-- Set Password Modal -->
					@include('avelca_user::user.set_password')
					<!-- /.modal -->

					@endforeach
				</div>

			</div>
		</div>

		@include('avelca_user::footer')

		@endif
		@stop
