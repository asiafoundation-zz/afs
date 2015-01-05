@extends('layouts/default')

@section('content')
<script type="text/javascript">
 function popup_question(question_id,survey_id,cycle_id){

 	var question_text = $("#question_test_"+question_id).text();
 	$('#question_popup_table').hide();
 	$('.loading-flag').show();
 	$('#popup_detail_question').modal('show');
 	
	$.post( "{{ URL::to('admin') }}/survey/cycle", { question_id: question_id,survey_id: survey_id,cycle_id: cycle_id })
	.done(function( data ) {
		$('#question_popup_table').show();
		$('.loading-flag').hide();
		$("#question_popup_table").html(data);

		$("#question-label-popup").html(question_text);
	});
	return false;
}

function manage_answer_order(survey_id,question_id,cycle_id){

 	var questions = $("#question_test_"+question_id).text();
 	$("#questions-text").html(questions);
 	$("#form_filter_category_id").val(question_id);
 	$("#form_filter_cycle_id").val(cycle_id);
 	$('#manage_answer_order').modal('show');

 	$.get( "{{ URL::to('answeredit') }}/"+question_id, { question_id:question_id,cycle_id:cycle_id })
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
			<h1>{{ $survey->name }} ( {{$cycle->name}} )</h1>
			<hr>
		</div>
		<div class="table-responsive">
			<table class="datatable table table-striped table-bordered">
				<thead>
					<tr>
						<th>{{Lang::get('backend.survey_name')}}</th>
						<th>{{Lang::get('backend.category_question')}}</th>
						<th>{{Lang::get('backend.question')}}</th>
						<th>{{Lang::get('general.action')}}</th>
					</tr>
				</thead>
				<tbody>
				@foreach($questions as $question)
					<tr>
						<td>{{$question->master_code}}@if(!empty($question->code))_{{ $question->code }} @endif </td>
						<td><span id="category_question_test_{{ $question->question_id }}">{{ $question->question_category }}</span></td>
						<td><span id="question_test_{{ $question->question_id }}">{{ $question->question }}</span></td>
						<td>
							<a href="#" onclick="popup_question({{ $question->question_id }},{{ $survey->id }},{{$cycle->id}})"><button class="btn" style="background-color: {{ Setting::meta_data('general', 'theme_color')->value }}; color: #ffffff;">{{Lang::get('general.view')}}</button></a>
							<a href="#" onclick="manage_answer_order({{ $survey->id }},{{ $question->question_id }},{{ $cycle->id }} )"><button class="btn" style="background-color: {{ Setting::meta_data('general', 'theme_color')->value }}; color: #ffffff;">{{Lang::get('backend.answer_order')}}</button></a>
						</td>
					</tr>
				@endforeach
				</tbody>
			</table>
		</div>
		<?php echo $questions->links(); ?>

	</div>			
</div>

<div class="modal fade" id="manage_answer_order" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
			<div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="question-label-popup"><span id="questions-text"></span></h4>
      </div>
      {{ Form::open(array('url' => 'answerpost', 'class' => 'form-horizontal')) }}
      <div id="popup_order_detail_question_body"></div>
      <div class="modal-footer">
        <a type="button" class="btn btn-default" data-dismiss="modal">{{Lang::get('general.back')}}</a>
        <button class="btn" type="submit" class="btn btn-primary">{{Lang::get('general.save')}}</button>
      </div>
      {{ Form::hidden("question_id","", array("class" => "form-control","id" => "form_filter_category_id")) }}
      {{ Form::hidden("cycle_id","", array("class" => "form-control","id" => "form_filter_cycle_id")) }}
      {{ Form::hidden("survey_id",$survey->id, array("class" => "form-control","id" => "form_filter_survey_id")) }}
			{{ Form::close() }}
    </div>
  </div>
</div>

<div class="modal fade" id="popup_detail_question" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
			<div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="question-label-popup">{{ Lang::get('backend.question') }}</h4>
      </div>
      <div class="modal-body" id="popup_detail_question_body">
      	<div class="loading-flag">
      		<img src="{{ Theme::asset('img/ajax-loader.gif') }}">
      	</div>
      	<table class="datatable table table-striped table-bordered" id="question_popup_table"></table>
      </div>
      <div class="modal-footer">
        <a type="button" class="btn btn-default" data-dismiss="modal">{{Lang::get('general.back')}}</a>
      </div>
    </div>
  </div>
</div>
@stop
