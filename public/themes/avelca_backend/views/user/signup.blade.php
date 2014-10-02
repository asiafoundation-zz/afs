@extends('layouts/authentication')

@section('content')

<div class="panel-heading">
  <h3 class="panel-title">Sign Up</h3>
</div>
<div class="panel-body">
  {{ Form::open(array('url' => 'signup', 'method' => 'post')) }}
  <fieldset>

    <div class="form-group">
      <input class="form-control" placeholder="First name" name="first_name" type="text" autofocus value="{{ Input::old('first_name') }}">
    </div>
    <div class="form-group">
      <input type="text" class="form-control" placeholder="Last Name" name="last_name" required value="{{ Input::old('last_name') }}">
    </div>
    <div class="form-group">
      <input type="email" class="form-control" placeholder="Email Address" name="email" required value="{{ Input::old('email') }}">
    </div>
    <div class="form-group">
      <input type="password" class="form-control" placeholder="Password" name="password" required>
    </div>
    <div class="form-group">
      <input type="password" class="form-control" placeholder="Confirm Password" name="confirm_password" required>

    </div>
    <div class="checkbox">
      <label>
        <input type="checkbox" name="is_read" value="1"> I've read {{ HTML::link('privacy-policy','Privacy Policy') }} and {{ HTML::link('terms-of-service','Terms of Service') }}
      </label>
    </div>
    <input type="submit" value="Sign Up" class="btn btn-lg btn-block" style="background-color: {{ Setting::meta_data('general', 'theme_color')->value }}; color: #ffffff;">
  </fieldset>
  {{ Form::close() }}
  
  <hr>
  {{ HTML::link('reset-password', 'Reset Password') }} <span class="pull-right">{{ HTML::link('signin', 'Sign In') }}</span>

</div>

@stop