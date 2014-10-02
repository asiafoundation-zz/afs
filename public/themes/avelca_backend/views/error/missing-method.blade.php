@extends('layouts/default')

@section('content')
<div class="row">
	<div class="col-md-12">

		<div class="page-header">
			<h1>Sorry, the page you requested was not found.</h1>
		</div>

		<p>Please check the URL for proper spelling and capitalization. If you're having trouble locating a destination on this system, try visiting the {{ HTML::link('dashboard', 'Dashboard') }}. Also, you may report to your Administrator regarding this problem.</p>

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
