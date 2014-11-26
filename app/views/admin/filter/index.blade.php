@extends('layouts/default')

@section('content')

<script type="text/javascript">
 function popup_filter(survey_id,category_id){

 	var category_text = $("#filter_category_name_"+category_id).text();
 	var category_display_text = $("#filter_category_display_name_"+category_id).text();
 	$("#popup_filter_participant_name").html(category_text);
 	$("#form_filter_category_id").val(category_id);

 	$('#edit_filter_option').modal('show');
	return false;
}
function is_active_filter(survey_id,category_id){
	$.post( "{{ URL::to('/admin/filter') }}", { survey_id: survey_id,category_id:category_id,is_active: $("#question_select_modal").val() })
	.done(function( data ) {
		window.location.href = "/admin/filter/"+survey_id;
	});
	return false;
}

function manage_filter_order(survey_id,category_id){

 	var category_text = $("#filter_category_name_"+category_id).text();
 	console.log(category_text);
 	var category_display_text = $("#filter_category_display_name_"+category_id).text();
 	$("#popup_order_filter_participant_name").html(category_text);
 	$("#form_filter_category_id").val(category_id);
 	$('#manage_filter_order').modal('show');

 	$.get( "{{ URL::to('admin/filterorder') }}", { category_id:category_id })
	.done(function( data ) {
		$("#popup_order_detail_question_body").html(data);
	});
	return false;
}
</script>

<div class="row">
	<div class="col-md-12">
		<div class="notification">
			@if(Session::has('message'))
			<div class="alert {{ Session::get('alert-class', 'alert-info') }}">
				<button class="close" type="button" data-dismiss="alert">×</button>
				{{ Session::get('message') }}
			</div>
			@endif
		</div>
		<div class="modal-header">
			<h1>{{Lang::get('general.select_filter')}}</h1>
			<hr>
		</div>

		<div class="table-responsive">
			<table class="datatable table table-striped table-bordered">
				<thead>
					<tr>
						<th>{{Lang::get('backend.participant_filter')}}</th>
						<th>{{Lang::get('backend.display_name')}}</th>
						<th width="150px">{{Lang::get('backend.availability')}}</th>
						<th width="300px">{{Lang::get('general.action')}}</th>
					</tr>
				</thead>
				<tbody>
				@foreach($categories as $category)
					<tr>
						<td><span id="filter_category_name_{{ $category->id }}">{{ strtoupper($category->name) }}</span></td>
						<td><span id="filter_category_display_name_{{ $category->id }}">{{ $category->display_name }}</span></td>
						<td>
							{{ Form::select('is_active', array(0 => 'Disable',1 => 'Enable'),$category->is_active, array("id" => "question_select_modal","class" => "control-label","onchange" => "is_active_filter( $survey_id,$category->id )")) }}
						</td>
						<td>
							<a href="#" onclick="popup_filter({{ $survey_id }},{{ $category->id }})"><button class="btn" style="background-color: {{ Setting::meta_data('general', 'theme_color')->value }}; color: #ffffff;">{{Lang::get('general.view')}}</button></a>
							<a href="#" onclick="manage_filter_order({{ $survey_id }},{{ $category->id }})"><button class="btn" style="background-color: {{ Setting::meta_data('general', 'theme_color')->value }}; color: #ffffff;">{{Lang::get('backend.manage_filter_order')}}</button></a>
						</td>
					</tr>
				@endforeach
				</tbody>
			</table>
		</div>

	</div>			
</div>

<div class="modal fade" id="edit_filter_option" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
			<div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="question-label-popup">{{ Lang::get('backend.manage_display_name') }}</h4>
      </div>
      {{ Form::open(array('url' => '/admin/filter', 'class' => 'form-horizontal')) }}
      <div class="modal-body" id="popup_detail_question_body">
      	<div class="col-md-12">
      		<div class="row">
						<div class="form-group">
							{{ Form::label("Participant Name:", "", array("class" => "control-label col-md-3")) }}
							<div class="col-md-9">
								<span id="popup_filter_participant_name"></span>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="form-group">
							{{ Form::label("Display Name:", "", array("class" => "control-label col-md-3")) }}
							<div class="col-md-9">
								{{ Form::text("display_name","", array("class" => "form-control","id" => "form_filter_display_name")) }}
								{{ Form::hidden("category_id","", array("class" => "form-control","id" => "form_filter_category_id")) }}
								{{ Form::hidden("survey_id",$survey_id, array("class" => "form-control","id" => "form_filter_survey_id")) }}
							</div>
						</div>
					</div>
				</div>
      </div>
      <div class="modal-footer">
        <a type="button" class="btn btn-default" data-dismiss="modal">{{Lang::get('general.back')}}</a>
        <button class="btn" type="submit" class="btn btn-primary">{{Lang::get('general.save')}}</button>
      </div>
				{{ Form::close() }}
    </div>
  </div>
</div>
<div class="modal fade" id="manage_filter_order" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
			<div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="question-label-popup">{{ Lang::get('backend.manage_filter_order') }} <span id="popup_order_filter_participant_name"></span></h4>
      </div>
      {{ Form::open(array('url' => '/admin/filterorder', 'class' => 'form-horizontal')) }}
      <div id="popup_order_detail_question_body"></div>
      <div class="modal-footer">
        <a type="button" class="btn btn-default" data-dismiss="modal">{{Lang::get('general.back')}}</a>
        <button class="btn" type="submit" class="btn btn-primary">{{Lang::get('general.save')}}</button>
      </div>
      {{ Form::hidden("category_id","", array("class" => "form-control","id" => "form_filter_category_id")) }}
      {{ Form::hidden("survey_id",$survey_id, array("class" => "form-control","id" => "form_filter_survey_id")) }}
			{{ Form::close() }}
    </div>
  </div>
</div>
@stop
