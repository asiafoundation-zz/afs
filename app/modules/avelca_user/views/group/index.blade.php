@extends('layouts/default')

@section('content')
<div class="row">
	<div class="col-md-12">

		<div class="page-header">
			<h1>Group</h1>
		</div>

		<ol class="breadcrumb">
			<li><a href="{{ URL::to('dashboard') }}"><i class="fa fa-dashboard"></i> {{ Lang::get('general.dashboard') }}</a></li>
			<li><a href="{{ URL::to('admin/group') }}">Group</a></li>
			<li class="active">{{ Lang::get('general.list') }}</li>
		</ol>

		<?php $customView = 'admin.group.index'; ?>
		@if(View::exists($customView))
		@include($customView)
		@else

		<a href="#create_modal" class="btn" data-toggle="modal" style="background-color: {{ Setting::meta_data('general', 'theme_color')->value }}; color: #ffffff;"><i class="fa fa-plus"></i> {{ Lang::get('general.create') }}</a>

		<br><br>

		<div class="table-responsive">
			<table class="datatable table table-striped table-bordered">
				<thead>
					<tr>
						<th>Name</th>
						<th>Created At</th>
						<th>Updated At</th>
						<th>&nbsp;</th></tr>

					</thead>
					<tbody>
						@foreach($groups as $group)
						<tr>
							<td>{{ $group->name }}</td>
							<td>{{ $group->created_at }}</td>
							<td>{{ $group->updated_at }}</td>
							<td>
								<div class="text-center">

									<div class="btn-group">
										<button type="button" class="btn dropdown-toggle" data-toggle="dropdown" style="background-color: {{ Setting::meta_data('general', 'theme_color')->value }}; color: #ffffff;">
											<i class="fa fa-gear"></i>
										</button>
										<ul class="dropdown-menu" role="menu" style="text-align: left;">
											<li><a href="#edit_modal-{{ $group->id }}" data-toggle="modal"><i class="fa fa-edit"></i> {{ Lang::get("general.edit") }}</a></li>
											<li><a href="#delete_modal-{{ $group->id }}" data-toggle="modal"><i class="fa fa-trash-o"></i> {{ Lang::get("general.delete") }}</a></li>
										</ul>
									</div>

								</div>
							</td>
						</tr>
						@endforeach
					</tbody>
					<tfoot>
						<tr>
							<th><input type="text" name="name" placeholder="Filter" class="search_init" /></th>
							<th><input type="text" name="created_at" placeholder="Filter" class="search_init" /></th>
							<th><input type="text" name="updated_at" placeholder="Filter" class="search_init" /></th>
							<th>&nbsp;</th></tr>
						</tfoot>
					</table>
				</div>

				<!-- Create Modal -->
				@include('avelca_user::group.create')
				<!-- /.modal -->

				<div class="text-center">
				@foreach($groups as $group)
					<!-- Edit Modal -->
					@include('avelca_user::group.edit')
					<!-- /.modal -->

					<!-- Delete Modal -->
					@include('avelca_user::group.delete')
					<!-- /.modal -->
				@endforeach
				</div>

			</div>
		</div>

		@include('avelca_user::footer')

		@endif

		@stop
