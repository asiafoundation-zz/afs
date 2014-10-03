<?php $no = $start_no; $user = Sentry::getUser(); ?>

@foreach($records as $record)
<tr>
	<td class="text-center">{{ $no++ }}</td>
	@foreach($indexFields as $field => $structure)
	@if( in_array($structure['type'], $numeric_types) )
	<td class="text-right">
		@elseif( in_array($structure['type'], $option_types) )
		<td class="text-center">
			@else
			<td class="text-left">
				@endif
				{{ AvelcaController::viewIndexContent($record, $structure, $field) }}       
			</td>
			@endforeach
			<td class="text-center" id="actionColumn">

				<div class="btn-group">
					<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
						<i class="fa fa-fw fa-list"></i> <span class="caret"></span>
					</button>
					<ul class="dropdown-menu text-left" role="menu">
						@if($user->hasAccess($routeName.'.view'))
						@if( ! in_array('view', $disabledActions) )		
						<li><a href="{{ URL::to('admin/'.$routeName.'/view/'.$record->id) }}" data-toggle="modal" data-target="#viewModal">View</a></li>
						@endif
						@endif

						@if($user->hasAccess($routeName.'.edit'))
						@if( ! in_array('edit', $disabledActions) )				
						<li><a href="{{ URL::to('admin/'.$routeName.'/edit/'.$record->id) }}" data-toggle="modal" data-target="#updateModal">Update</a></li>
						@endif
						@endif

						@if($user->hasAccess($routeName.'.delete'))
						@if( ! in_array('delete', $disabledActions) )
						<li><a href="{{ URL::to('admin/'.$routeName.'/delete/'.$record->id) }}" data-toggle="modal" data-target="#removeModal">Remove</a></li>
						@endif
						@endif

						@if( count($actionButtons) > 0)

						@if( ! in_array('view', $disabledActions) && ! in_array('edit', $disabledActions) && ! in_array('delete', $disabledActions) )
						<li class="divider"></li>
						@endif

						@foreach($actionButtons as $action_button => $url)
						<?php
						$url = explode('|', $url);
						$param = '';

						if(count($url) > 1)
						{
							$parameters = explode('/', $url[1]);
							for($i = 0; $i < count($parameters); $i++)
							{
								if($i == 0)
								{
									$param .= '/';
								}
								$param .= $record->$parameters[$i];

								if($i != count($parameters))
								{
									$param .= '/';
								}
							}
						}
						?>
						@if($user->hasAccess($routeName.'.'.$url[0]))
						<li><a href="{{ URL::to('admin/'.$routeName.'/'.$url[0].$param) }}">{{ $action_button }}</a></li>
						@endif

						@endforeach
						@endif

						<?php $customView = 'admin.'.$routeName.'.menu.action'; ?>
						@if(View::exists($customView))
						@include($customView)
						@endif
					</ul>
				</div>

			</td>
		</tr>
		@endforeach