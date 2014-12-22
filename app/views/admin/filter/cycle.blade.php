@extends('layouts/default')

@section('content')

<ul class="nav nav-tabs">
  <li>
  	<a href="/admin/filter/{{ $survey_id }}">Manage Filter</a>
  </li>
  <li class="active">
  	<a href="/admin/cycle/{{ $survey_id }}">Manage Cycle</a>
  </li>
</ul>

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
			<h4>{{Lang::get('general.select_filter')}}</h4>
			<hr>
		</div>

		<div class="table-responsive">
			<table class="datatable table table-striped table-bordered">
				<thead>
					<tr>
						<th>{{Lang::get('backend.participant_filter')}}</th>
						<th>{{Lang::get('backend.display_name')}}</th>
						<th width="300px">{{Lang::get('general.action')}}</th>
					</tr>
				</thead>
				<tbody>
					@foreach($cycles as $cycle)
					<tr>
						<td class="cycle-name"><span>{{$cycle->name}}</span></td>
						<td class="cycle-display"><span>{{ $cycle->display_name }}</span></td>
						<td>
							<a href="#"><button class="btn cycle-btn" id="cycle-btn" data-id="{{ $cycle->id }}" style="background-color: {{ Setting::meta_data('general', 'theme_color')->value }}; color: #ffffff;">{{Lang::get('general.view')}}</button></a>
						</td>
					</tr>
					@endforeach
				</tbody>
			</table>
		</div>

	</div>			
</div>

<div class="modal fade" id="edit-cycle" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
			<div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="question-label-popup">{{ Lang::get('backend.manage_display_name') }}</h4>
      </div>
      {{ Form::open(array('url' => '/admin/cycle', 'class' => 'form-horizontal')) }}
      <div class="modal-body" id="popup_detail_question_body">
      	<div class="col-md-12">
      		<div class="row">
						<div class="form-group">
							{{ Form::label("Cycle Name:", "", array("class" => "control-label col-md-3")) }}
							<div class="col-md-9">
								<span id="cycle-name-label"></span>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="form-group">
							{{ Form::label("Display Name:", "", array("class" => "control-label col-md-3")) }}
							<div class="col-md-9">
								{{ Form::text("display_name","", array("class" => "form-control","id" => "form_display_name")) }}
								{{ Form::hidden("cycle_id","", array("class" => "form-control","id" => "form_cycle_id")) }}
								{{ Form::hidden("survey_id",$survey_id, array("class" => "form-control","id" => "form_survey_id")) }}
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

@stop