<div class="col-md-12">
	@foreach($categories as $category)
	<div class="row">
		<div class="form-group">
			{{ Form::label("Filter Category Name:", "Filter Category ".$category->name , array("class" => "control-label col-md-3")) }} 
			<div class="col-md-2">
				 {{Lang::get('backend.order_no')}} {{ Form::text("order[$category->id]","$category->order", array("class" => "form-control","id" => "form_filter_display_name")) }}
			</div>
		</div>
	</div>
	@endforeach()
</div>