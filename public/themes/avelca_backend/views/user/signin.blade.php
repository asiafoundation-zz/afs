<!DOCTYPE html>
<html id="newhome" lang="en">


  <head>

    <meta charset="utf-8">
    <title>SURVEY Q Admin - Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{ HTML::style(Theme::asset('css/login.css')) }}
    {{ HTML::script(Theme::asset('js/jquery-1.10.2.js')) }}    
    <!--script src="js/modernizr-2.7.1.min.js"></script--> 
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

  </head>

  <body>
  
  <div class="header">
    <h1>
      Survey Q
      <span class="logo"></span>
    </h1>
  </div>
  <div class="float-container">
  {{ Form::open(array('url' => 'signin', 'method' => 'post')) }}
    <h6>welcome administrator</h6>
    <div class="form-group">
      <input class="form-control" placeholder="E-mail" name="email" type="email" autofocus value="{{ Input::old('email') }}" />
    </div>
    <div class="form-group">
      <input class="form-control" placeholder="Password" name="password" type="password" />
      <button type="submit" class="btn btn-green">sign in</button>
    </div>
    <div class="form-group">
      <div class="half-control">
        <div class="checkbox">
          <input id="check2" name="remember_me" type="checkbox" value="1">  
          <label for="check2" >Remember me</label>  
        </div>
      </div>
      <div class="half-control">
        {{ HTML::link('reset-password', 'Reset Password') }}
      </div>
    </div>
  </div>
  {{ Form::close() }}

  {{ HTML::script(Theme::asset('js/bootstrap.min.js')) }}
  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js"></script>
  <script type="text/javascript">
  </script>

</body>
</html>