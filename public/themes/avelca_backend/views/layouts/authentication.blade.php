<!DOCTYPE html>
<html>

<head>

	<meta charset="utf-8">

	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title>{{ Setting::meta_data('general', 'name')->value }} - {{ Setting::meta_data('general', 'tag_line')->value }}</title>

	<link rel="shortcut icon" type="image/x-icon" href="{{ Theme::asset('images/favicon.ico') }}">

	<!-- Core CSS - Include with every page -->
	{{ HTML::style(Theme::asset('css/bootstrap.min.css')) }}
	{{ HTML::style(Theme::asset('font-awesome/css/font-awesome.css')) }}

	<!-- SB Admin CSS - Include with every page -->
	{{ HTML::style(Theme::asset('css/style.css')) }}

	<!-- Core Scripts - Include with every page -->
	{{ HTML::script(Theme::asset('js/jquery-1.10.2.js')) }}
	{{ HTML::script(Theme::asset('js/bootstrap.min.js')) }}
	{{ HTML::script(Theme::asset('js/plugins/metisMenu/jquery.metisMenu.js')) }}

</head>

<body>

	<div class="container">

		<div class="row">
			<div class="col-md-4 col-md-offset-4 text-center">
				<br>
				<a href="{{ URL::to('/') }}">{{ HTML::image(Theme::asset('images/logo.png')) }}</a>
			</div>
		</div>

		<div class="row">
			<div class="col-md-4 col-md-offset-4">
				{{ Widget::get('form-validation') }}
			</div>
		</div>

		<div class="row">
			<div class="col-md-4 col-md-offset-4">
				<div class="login-panel panel panel-default">
					@yield('content')
				</div>
			</div>
		</div>
	</div>

	<!-- SB Admin Scripts - Include with every page -->
	{{ HTML::script(Theme::asset('js/script.js')) }}
</body>

</html>
