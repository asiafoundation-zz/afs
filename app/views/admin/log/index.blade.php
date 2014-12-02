@extends('layouts/default')

@section('content')

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
			<h1>{{Lang::get('general.logs')}}</h1>
			<h5>{{Lang::get('backend.all_participants')}}</h5>
		</div>
		<?php echo $participant_loads->links(); ?>
		<div class="table-responsive">
		@foreach($logs as $log)
			<table class="datatable table table-striped table-bordered">
				<tr>
					<td rowspan="4">{{Lang::get('backend.participant_no')}}{{$log['id']}}</td>
				</tr>
				<tr>
					<td>
						<table class="datatable table-striped table-bordered" >
								<thead>
									<td align="center" style="padding:20px;40px;">{{Lang::get('backend.cycle')}}</td>
									<td align="center" style="padding:20px;40px;">{{Lang::get('backend.main_sample')}}</td>
									<td align="center" style="padding:20px;40px;">{{Lang::get('backend.region')}}</td>
									@foreach($log['filters'] as $filter)
										<td  align="center" style="padding:20px;40px;">
											{{ ucfirst($filter->category) }}
										</td>
										@endforeach
								</thead>
								<tbody>
									<tr>
										<td align="center">{{$log['cycle']}}</td>
										<td align="center">@if($log['sample_type'] == 0) {{Lang::get('backend.main_sample')}} @else {{Lang::get('backend.oversample')}} @endif</td>
										<td align="center">{{$log['region']}}</td>
										@foreach($log['filters'] as $filter)
										<td align="center">
											{{ $filter->category_items }}
										</td>
										@endforeach
									</tr>
								</tbody>
							</table>
						</td>
					</tr>

					<tr>
						<td>
							<div class="questions" style="overflow-x:scroll;width:900px;">
							<table class="datatable table table-striped table-bordered">
								<thead>
									@foreach($log['questions'] as $filter)
										<td>
											{{ $filter->master_code }}@if($filter->code != "")_{{ $filter->code }} @endif <button type="button" class="btn btn-default" data-toggle="collapse" data-parent="#accordion" data-target="#questions-{{ $log['id'] }}-{{ $filter->question_id }}" aria-expanded="true" aria-controls="demo">{{Lang::get('backend.questions')}}</button>
											<div id="questions-{{ $log['id'] }}-{{ $filter->question_id }}" class="panel-collapse collapse out" role="tabpanel" aria-labelledby="headingOne">
												{{ $filter->questions }}
											</div>
										</td>
										@endforeach
								</thead>
								<tbody>
									<tr>
										@foreach($log['questions'] as $filter)
										<td>
											{{ $filter->answers }}
										</td>
										@endforeach
									</tr>
								</tbody>
							</table>
							</div>
						</td>
					</tr>
			</table>
			@endforeach
		</div>
		<?php echo $participant_loads->links(); ?>
	</div>			
</div>
@stop
