<form class="form-horizontal well" role="form" style="display: none;" action="{{ URL::to('admin/'.$routeName) }}" method="get">
	<legend>Filter</legend>
	@foreach($indexFields as $field => $structure)
	<div class="form-group">
		{{ AvelcaController::label($field, $structure, $rules) }}
		{{ AvelcaController::fieldFilter($field, $structure, $rules) }}
	</div>
	@endforeach

	<div class="form-group">
		<div class="col-sm-4 col-sm-offset-3" id="featured_filter">
			<input type="submit" value="Filter" class="btn theme-color">
			<a href="{{ URL::to('admin/'.$routeName) }}" class="btn theme-color">Clear Filter</a>
		</div>
	</div>
</form>