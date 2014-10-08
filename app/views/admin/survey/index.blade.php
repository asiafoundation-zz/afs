@extends('layouts/default')

@section('content')

<div>
	<!--ol class="breadcrumb">
	  <li class="active">Survey</li>
	  <li><a href="#">Home</a></li>
	  <li><a href="#">Library</a></li>
	</ol-->
	
	<!--h4 style="text-align:center">You don't have any survey yet <a class="label label-primary" href="#">click here to create a new survey</a></h4-->
	<div class="alert alert-info" role="alert">
		You don't have any survey yet <a href="/admin/survey/cycle" class="alert-link">create a new survey</a>
	</div>
</div>

@stop