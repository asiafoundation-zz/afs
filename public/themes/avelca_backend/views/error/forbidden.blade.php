@extends('layouts/default')

@section('content')
<div class="row">
	<div class="col-md-12">

		<div class="page-header">
			<h1>403 Forbidden</h1>
		</div>

		<p>You don't have permission to access <b>{{ Request::segment(1) }}</b> on this system.</p>
		<p>You'll be redirected to Dashboard in 5 seconds. Click <a href="{{ URL::to('dashboard') }}">here</a> if you are not redirected.</p>

	</div>
</div>

<script type="text/javascript">
	$( document ).ready(function() {
		var delay = 5000;
		setTimeout(function(){
			window.location = "{{ URL::to('dashboard') }}";
		},
		delay); 
	});
</script>
@stop
