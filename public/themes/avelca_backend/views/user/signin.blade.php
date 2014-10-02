@extends('layouts/authentication')

@section('content')

<div class="panel-heading">
	<h3 class="panel-title">Sign in</h3>
</div>
<div class="panel-body">
	{{ Form::open(array('url' => 'signin', 'method' => 'post')) }}
	<fieldset>
		<div class="form-group">
			<input class="form-control" placeholder="E-mail" name="email" type="email" autofocus value="{{ Input::old('email') }}">
		</div>
		<div class="form-group">
			<input class="form-control" placeholder="Password" name="password" type="password">
		</div>
		<div class="checkbox">
			<label>
				<input name="remember_me" type="checkbox" value="1"> Stay signed in
			</label>
		</div>
		<input type="submit" value="Sign In" class="btn btn-lg btn-block" style="background-color: {{ Setting::meta_data('general', 'theme_color')->value }}; color: #ffffff;">
	</fieldset>
	{{ Form::close() }}
	
	<hr>
	{{ HTML::link('reset-password', 'Reset Password') }} 

	@if(Setting::meta_data('user_management', 'allow_sign_up')->selected == 1)
	<span class="pull-right">{{ HTML::link('signup', 'Sign Up') }}</span>
	@endif

</div>

@stop