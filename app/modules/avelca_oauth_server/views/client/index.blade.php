@extends('layouts/default')

@section('content')
<div class="row">
	<div class="col-md-12">

		<div class="page-header">
			<h1>Client</h1>
		</div>

		<ol class="breadcrumb">
			<li><a href="{{ URL::to('dashboard') }}"><i class="fa fa-dashboard"></i> {{ Lang::get('general.dashboard') }}</a></li>
			<li><a href="{{ URL::to('admin/api/client') }}">API</a></li>
			<li><a href="{{ URL::to('admin/api/client') }}">oAuth Client</a></li>
			<li class="active">{{ Lang::get('general.list') }}</li>
		</ol>


		<a href="#create_modal" class="btn" data-toggle="modal" style="background-color: {{ Setting::meta_data('general', 'theme_color')->value }}; color: #ffffff;"><i class="fa fa-plus"></i> {{ Lang::get('general.create') }}</a>

		<br><br>

		<div class="table-responsive">
			<table class="datatable table table-striped table-bordered">
				<thead>
					<tr>
						<th>Name</th>
						<th>ID</th>
						<th>Secret</th>
						<th>Created At</th>
						<th>&nbsp;</th></tr>

					</thead>
					<tbody>
						@foreach($clients as $client)
						<tr>
							<td>{{ $client->name }}</td>
							<td>{{ $client->id }}</td>
							<td>{{ $client->secret }}</td>
							<td>{{ $client->created_at }}</td>
							<td>
								<div class="text-center">



									<div class="btn-group">
										<button type="button" class="btn dropdown-toggle" data-toggle="dropdown" style="background-color: {{ Setting::meta_data('general', 'theme_color')->value }}; color: #ffffff;">
											<i class="fa fa-gear"></i>
										</button>
										<ul class="dropdown-menu" role="menu" style="text-align: left;">

											<li><a href="#edit_modal-{{ $client->id }}" data-toggle="modal"><i class="fa fa-edit"></i> {{ Lang::get("general.edit") }}</a></li>
											<li><a href="#delete_modal-{{ $client->id }}" data-toggle="modal"><i class="fa fa-trash-o"></i> {{ Lang::get("general.delete") }}</a></li>

										</ul>

									</div>

									<!-- Edit Modal -->
									@include('avelca_oauth_server::client.edit')
									<!-- /.modal -->

									<!-- Delete Modal -->
									@include('avelca_oauth_server::client.delete')
									<!-- /.modal -->

								</div>
							</td>
						</tr>

						@endforeach
					</tbody>
					<tfoot>
						<tr>
							<th><input type="text" name="name" placeholder="Filter" class="search_init" /></th>
							<th><input type="text" name="id" placeholder="Filter" class="search_init" /></th>
							<th><input type="text" name="secret" placeholder="Filter" class="search_init" /></th>
							<th><input type="text" name="created_at" placeholder="Filter" class="search_init" /></th>
							<th>&nbsp;</th></tr>

						</tfoot>
					</table>
				</div>

				<!-- Create Modal -->
				@include('avelca_oauth_server::client.create')
				<!-- /.modal -->

			</div>
		</div>
		@stop
