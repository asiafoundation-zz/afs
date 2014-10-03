@extends('layouts/default')

@section('content')

<div class="content">
	<div class="content-tittle">
		<div class="container">
			<h1>Page not Found</h1>
		</div>
	</div>
	<div class="content-inner">
		<div class="container">
			<p>This page you were trying to reach at this address doesn't seem to exist. This is usually the result of a bad or outdated link. We apologize for any inconvenience.</p>
			<p>Please go back to <a href="{{ URL::to('/') }}">Home</a></p>
		</div>
	</div>
</div>

@stop