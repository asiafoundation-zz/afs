@extends('layouts/default')

@section('content')

<div class="row">
	<div class="col-lg-12">
		
		<div class="page-header">
			<h1>Modules</h1>
		</div>

		@if(count($modules) > 0)

		<div class="table-responsive">
			<table class="datatable table table-striped table-bordered">
				<thead>
					<tr>
						<th class="text-center">Name</th>
						<th class="text-center">Author</th>
						<th class="text-center">Version</th>
						<th class="text-center">Status</th>
						<th class="text-center">Action</th>
					</tr>
				</thead>

				<tbody>
					@foreach($modules as $module)


					<?php
					if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
					{
						$sub = '\\';

					} else {
						$sub = '/';
					}
					?>

					<?php
					$property = File::get($module.'/module.json');
					$property = json_decode($property);
					?>

					<tr>
						<?php
						$parts = explode($sub, $module);
						$module_name = end($parts);
						?>
						<td>
							<b>{{ ucwords(str_replace('_',' ',str_replace('-',' ',$module_name))) }}</b>
							@if( ! empty($property->core) )
							{{ '<div class="label label-warning">Core</div>' }}
							@endif

							<br>
							{{ $property->description }}
						</td>
						<td class="text-center">{{ $property->author }}</td>
						<td class="text-center">{{ $property->version }}</td>
						<td class="text-center">
							@if($property->enabled === false)
							{{ '<div class="label label-danger">Disabled</div>' }}
							@else
							{{ '<div class="label label-success">Enabled</div>' }}
							@endif
						</td>
						<td class="text-center">

							@if( empty($property->core) )

							@if($property->enabled === false)
							<a href="#enable_{{ $module_name }}" data-toggle="modal" title="Enable" class="btn btn-default"><i class="fa fa-power-off fa-fw"></i></a>
							@else
							<a href="#disable_{{ $module_name }}" data-toggle="modal" title="Disable" class="btn btn-default"><i class="fa fa-power-off fa-fw"></i></a>
							@endif

							@include('avelca_module::enable')

							@include('avelca_module::disable')

							@endif

						</td>
					</tr>
					@endforeach
				</tbody>
				<tfoot>
					<tr>
						<th><input type="text" name="name" placeholder="Filter" class="search_init" /></th>
						<th><input type="text" name="author" placeholder="Filter" class="search_init" /></th>
						<th><input type="text" name="version" placeholder="Filter" class="search_init" /></th>
						<th><input type="text" name="status" placeholder="Filter" class="search_init" /></th>
						<th>&nbsp;</th></tr>

					</tfoot>


				</table>
			</div>




			@else

			<p>No module installed.</p>

			@endif
		</div>
	</div>

	@include('avelca_user::footer')


	@stop