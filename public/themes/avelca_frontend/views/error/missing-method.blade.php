@extends('layouts/default')

@section('content')

<div class="content">
	<div class="content-tittle">
		<div class="container">
			<h1>Sorry, the page you requested was not found.</h1>
		</div>
	</div>
	<div class="content-inner">
		<div class="container">
		<p>Please check the URL for proper spelling and capitalization. If you're having trouble locating a destination on this system, try visiting the {{ HTML::link('/', 'Home') }}. Also, you may report to your Administrator regarding this problem.</p>
		</div>
	</div>
</div>

<script type="text/javascript">
	$( document ).ready(function() {
		var delay = 5000;
		setTimeout(function(){
			window.location = "{{ URL::to('/') }}";
		},
		delay); 
	});
</script>
@stop
