@extends('layouts/default')

@section('content')

<div class="row">
	<div class="col-lg-12">
		{{ Widget::greeting() }}

		<div class="jumbotron">
			<h2>Getting Started</h2>
			<p>Avelca helps you create business application faster. You just need to focus on specific value-added features of your application and let Avelca handles the core functionality and generares required resources. You can still modify source code or configure settings. </p>

			<p><a href="{{ URL::to('admin/setting') }}" class="btn theme-color">Go to Setting <span class="glyphicon glyphicon glyphicon-chevron-right"></span></a></p>
		</div>
	</div>
</div>

@stop