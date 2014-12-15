@extends($bladeLayout)

@section('content')

<?php $user = Sentry::getUser(); ?>

<div class="row">
	<div class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="{{ URL::to('dashboard') }}">Dashboard</a></li>
			<li class="active">{{ $name }}</li>
		</ol>

		<?php $customView = 'admin.'.$routeName.'.index'; ?>
		@if(View::exists($customView))
		@include($customView)
		@else


		<div class="page-header">
			<h1>
				{{ $name }}
				<small>List</small>

				<span class="pull-right">	
					@if($user->hasAccess($routeName.'.create'))
					@if( ! in_array('create', $disabledActions) )
					<a href="#" class="btn theme-color" id="new_button">
						<i class="fa fa-plus fa-fw"></i>
						Create New
					</a>
					@endif
					@endif
				</span>
			</h1>
		</div>

		<div class="table-responsive" id="list_page">

			<span class="text-left">

				<a href="javascript:;" id="filter" class="btn btn-default"><i class="fa fa-filter fa-fw"></i><span class="caret"></span></a>

				<div class="btn-group">
					<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
						<i class="fa fa-fw fa-th"></i> <span class="caret"></span>
					</button>
					<ul class="dropdown-menu multi-level">
						@if($user->hasAccess($routeName.'.export'))
						<li><a href="{{ URL::to('rest/'.$routeName.'/export') }}" class="DTTT_button_print" id="ToolTables_index_table_0" title="Export to Excel"><span>Export Excel</span></a></li>
						@endif

						@if($user->hasAccess($routeName.'.print-pdf'))
						<li><a href="{{ URL::to('admin/'.$routeName.'/print-pdf')}}" class="DTTT_button_print" title="Export to PDF">Eport To PDF</a></li>
						@endif

						@if( count($mainButtons) > 0)
						@foreach($mainButtons as $action_button => $urls)
						@if( is_array($urls) )
						<li class="divider"></li>
						<li class="dropdown-submenu"><a tabindex="-1" href="#">{{ $action_button }}</a>
							<ul class="dropdown-menu">
								@foreach($urls as $url => $key)
								@if($user->hasAccess($routeName.'.'.$key))
								<li><a href="{{ URL::to('admin/'.$routeName.'/'.$key) }}">{{ $url }}</a></li>
								@endif
								@endforeach
							</ul>
						</li>
						@else
						@if($user->hasAccess($routeName.'.'.$urls))
						<li><a href="{{ URL::to('admin/'.$routeName.'/'.$urls) }}">{{ $action_button }}</a></li>
						@endif
						@endif
						@endforeach
						@endif

						<?php $customView = 'admin.'.$routeName.'.menu.main'; ?>
						@if(View::exists($customView))
						@include($customView)
						@endif
					</ul>
				</div>

				<p>&nbsp;</p>
			</span>

			<?php $customView = 'admin.'.$routeName.'.filter'; ?>
			@if(View::exists($customView))
			@include($customView)
			@else
			@include('avelca::filter')
			@endif

			<table class="datatable table table-striped table-bordered" id="index_table">
				<thead>
					<tr>
						<th class="text-center">No</th>
						@foreach($indexFields as $field => $structure)
						<th class="text-center">{{ AvelcaController::tableHeader($field, $structure) }}</th>
						@endforeach
						<td id="actionColumn">&nbsp;</td>
					</tr>
				</thead>
				<tbody>
					<?php $customView = 'admin.'.$routeName.'.rowTable'; ?>
					@if(View::exists($customView))
					@include($customView)
					@else
					@include('avelca::rowTable')
					@endif
				</tbody>
			</table>

			@if( $records->count() > 0)
			<div class="pull-right">
				<ul id='pagination_index'></ul>
				<?php $customView = 'admin.'.$routeName.'.js.pagination'; ?>
				@if( ! View::exists($customView))
				<?php $customView = 'avelca::js.pagination'; ?>
				@endif
				{{ $records->links($customView)}}
			</div>
			@endif

			<?php $customView = 'admin.'.$routeName.'.additional.index'; ?>
			@if(View::exists($customView))
			@include($customView)
			@endif

		</div>

		
		<!-- Create -->
		<div id="create_page"></div>

		<!-- View -->
		<div class="modal fade" id="viewModal">
			<div class="modal-dialog {{ $modalDialog }}">
				<div class="modal-content"></div>
			</div>
		</div>

		<!-- Update -->
		<div class="modal fade" id="updateModal">
			<div class="modal-dialog {{ $modalDialog }}">
				<div class="modal-content"></div>
			</div>
		</div>

		<!-- Remove -->
		<div class="modal fade" id="removeModal">
			<div class="modal-dialog modal-sm">
				<div class="modal-content"></div>
			</div>
		</div>

	</div>
</div>

<?php $customView = 'admin.'.$routeName.'.js.datatable'; ?>
@if(View::exists($customView))
@include($customView)
@else
@include('avelca::js.datatable')
@endif

<?php $customView = 'admin.'.$routeName.'.js.script'; ?>
@if(View::exists($customView))
@include($customView)
@else
@include('avelca::js.script')
@endif


@endif
@stop
