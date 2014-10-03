@extends('layouts/authentication')

@section('content')



<div class="panel-heading">
	<h3 class="panel-title">Reset Password</h3>
</div>
<div class="panel-body">
	{{ Form::open(array('url' => 'reset-password', 'method' => 'post')) }}
	<fieldset>
		<div class="form-group">
			<input type="email" class="form-control" placeholder="Email Address" name="email" required autofocus>
		</div>
		<input type="submit" value="Reset Password" class="btn btn-lg btn-block" style="background-color: {{ Setting::meta_data('general', 'theme_color')->value }}; color: #ffffff;">
	</fieldset>
	{{ Form::close() }}
	
	<hr>
	{{ HTML::link('signin', 'Sign In') }} 

	@if(Setting::meta_data('user_management', 'allow_sign_up')->selected == 1)
	<span class="pull-right">{{ HTML::link('signup', 'Sign Up') }}</span>
	@endif

</div>

@stop