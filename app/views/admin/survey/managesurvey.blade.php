@extends('layouts/default')

@section('content')
<div class="row">
	<div class="col-md-12">

		<div class="page-header">
			<h1>Manage Question Survey</h1>
		</div>

		<!--ol class="breadcrumb">
			<li><a href="{{ URL::to('dashboard') }}"><i class="fa fa-dashboard"></i> {{ Lang::get('general.dashboard') }}</a></li>
			<li><a href="{{ URL::to('admin/group') }}">Group</a></li>
			<li class="active">{{ Lang::get('general.list') }}</li>
		</ol-->
		
		<div class="table-responsive">
			<table class="datatable table table-striped table-bordered">
				<thead>
					<tr>
						<th>No</th>
						<th>Question Name</th>
						<th>Updated At</th>
						<th>Set Default</th>
					</tr>
					</thead>
					<tbody>
					@foreach($question as $value)
						<tr>
							<td>{{ $value->id }}</td>
							<td>{{ $value->question }}</td>
							<td>{{ $value->updated_at }}</td>
							<td></td>
						</tr>
					@endforeach
					</tbody>
					<!--tfoot>
						<tr>
							<th><input type="text" name="name" placeholder="Filter" class="search_init" /></th>
							<th><input type="text" name="created_at" placeholder="Filter" class="search_init" /></th>
							<th><input type="text" name="updated_at" placeholder="Filter" class="search_init" /></th>
							<th>&nbsp;</th>
						</tr>
					</tfoot-->
					</table>
				</div>
			</div>
		</div>

		@stop
