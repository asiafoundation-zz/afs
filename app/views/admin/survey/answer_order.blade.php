<div class="col-md-12">
	@foreach($answers as $answer)
	<div class="row">
		<div class="form-group">
			{{ Form::label("Answer Name:", "Answer Name: ".$answer->answer , array("class" => "control-label col-md-3")) }} 
			<div class="col-md-2">
				 {{Lang::get('backend.order_no')}} {{ Form::text("order[$answer->id]","$answer->order", array("class" => "form-control","id" => "form_filter_display_name")) }}
			</div>
		</div>
	</div>
	@endforeach()
</div>