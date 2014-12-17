@if( ! empty($menus) )
@foreach($menus as $menu => $property)

@if( array_key_exists('show', $property) )

	@if($property['show'] === true)

	@if( ! array_key_exists('icon', $property))
	<?php $property['icon'] = 'fa fa-circle-o fa-fw'; ?>
	@endif

	<li>
		<a href="
		@if( ! empty($property['navigations']) )
		{{ '#' }}
		@else
		{{ URL::to($property['url']) }}
		@endif
		"><i class="{{ $property['icon'] }}"></i> {{ $property['text'] }} 
			@if( ! empty($property['navigations']) )
			<span class="fa arrow"></span>
			@endif
		</a>

		@if( ! empty($property['navigations']) )
		<ul class="nav nav-third-level">
			@foreach($property['navigations'] as $navigation => $url)
			<li><a href="{{ URL::to($url) }}">{{ $navigation }}</a></li>
			@endforeach
		</ul>
		@endif
	</li>

	@endif
@else

@if(empty($property['icon']))
<?php $property['icon'] = 'fa fa-circle-o fa-fw'; ?>
@endif

<li>
	<a href="
	@if( ! empty($property['navigations']) )
	{{ '#' }}
	@else
	{{ URL::to($property['url']) }}
	@endif
	"><i class="{{ $property['icon'] }}"></i> {{ $property['text'] }} 
		@if( ! empty($property['navigations']) )
		<span class="fa arrow"></span>
		@endif
	</a>

	@if( ! empty($property['navigations']) )
	<ul class="nav nav-third-level">
		@foreach($property['navigations'] as $navigation => $url)
		<li><a href="{{ URL::to($url) }}">{{ $navigation }}</a></li>
		@endforeach
	</ul>
	@endif
</li>
@endif

@endforeach
@endif

<?php Session::forget('menus'); ?>