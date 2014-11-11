<!DOCTYPE html>
<html>

<head>

	<meta charset="utf-8">

	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title>
		@if( ! empty($name) )
		{{ $name }} {{ $title }} - 
		@endif
		Asia Foundation Survey
	</title>

	<!-- <link rel="shortcut icon" type="image/x-icon" href="{{ Theme::asset('images/favicon.ico') }}"> -->

	<!-- Core CSS - Include with every page -->
	{{ HTML::style(Theme::asset('css/bootstrap.min.css')) }}
	{{ HTML::style(Theme::asset('font-awesome/css/font-awesome.css')) }}

	<!-- Page-Level Plugin CSS - Tables -->
	{{ HTML::style(Theme::asset('css/plugins/dataTables/dataTables.bootstrap.css')) }}

	<!-- Datatable -->
	{{ HTML::style(Theme::asset('css/datatables_bootstrap3/datatables.css')) }}

	<!-- Colorpicker -->
	{{ HTML::style(Theme::asset('css/colorpicker/bootstrap-colorpicker.css')) }}

	<!-- Color Picker -->
	{{ HTML::style(Theme::asset('css/jquery.simplecolorpicker.css')) }}
	{{ HTML::style(Theme::asset('css/jquery.simplecolorpicker-fontawesome.css')) }}

	<!-- DateTime -->
	{{ HTML::style(Theme::asset('css/bootstrap-datetimepicker.min.css')) }}

	<!-- Bootstrap Select -->
	{{ HTML::style(Theme::asset('css/bootstrap-select/bootstrap-select.min.css')) }}

	<!-- SB Admin CSS - Include with every page -->
	{{ HTML::style(Theme::asset('css/style.css')) }}
	{{ HTML::style(Theme::asset('css/theme.css')) }}

	<!-- Custom css -->
	{{ HTML::style(Theme::asset('css/multi-select.css')) }}	
	{{ HTML::style(Theme::asset('css/custom.css')) }}
	{{ HTML::style(Theme::asset('css/select2.css')) }}	

	<!-- Color Theme -->
	<?php $theme_color = Setting::meta_data('general', 'theme_color')->value; ?>
	<style type="text/css">
	.theme-color {
		background-color: {{ $theme_color }};
		color: #ffffff;
	}
	a, a:hover {
		color: {{ $theme_color }};
	}
	button.theme-color:hover, a.theme-color:hover, input.theme-color:hover {
		background-color: {{ $theme_color }};
		color: #f1f1f1;
	}
	.navbar, .navbar-top-links li a:hover, .navbar-top-links li a:focus, .nav .open>a:focus {
		background-color: {{ $theme_color }};
	}

	table.table thead {
		background-color: {{ $theme_color }};
		color: #ffffff;
	}
	ul.pagination>li.active a, ul.pagination>li.active a:hover {
		background-color: {{ $theme_color }};
	}
	</style>

	<!-- Core Scripts - Include with every page -->
	{{ HTML::script(Theme::asset('js/jquery-1.10.2.js')) }}
	{{ HTML::script(Theme::asset('js/bootstrap.min.js')) }}
	{{ HTML::script(Theme::asset('js/plugins/metisMenu/jquery.metisMenu.js')) }}

	<!-- Datepicker -->
	{{ HTML::script(Theme::asset('js/jquery-ui/jquery-ui.min.js')) }}
	{{ HTML::style(Theme::asset('css/jquery-ui/overcast/jquery-ui.css')) }}

	<!-- DataTable -->
	{{ HTML::script(Theme::asset('js/datatables/jquery.dataTables.min.js')) }}  
	{{ HTML::script(Theme::asset('js/datatables_bootstrap3/datatables.js')) }}
	{{ HTML::script(Theme::asset('js/jquery.dataTables.columnFilter.js')) }}

	{{ HTML::style(Theme::asset('css/dataTables.tableTools.min.css')) }}
	{{ HTML::script(Theme::asset('js/dataTables.tableTools.min.js')) }}

	<!-- Bootstrap Paginator -->
	{{ HTML::script(Theme::asset("js/bootstrap-paginator.js")) }}
</head>

<body>

	<div id="wrapper">

		<nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".sidebar-collapse">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<ul class="nav navbar-top-links navbar-left">
					<li><a href="javascript:;" id="toggle_sidebar" hide="N" ><i class="glyphicon glyphicon-align-justify"></i></a></li>
				</ul>
				<a class="navbar-brand" href="{{ URL::to('dashboard') }}">{{ Setting::meta_data('general', 'name')->value }}</a>
			</div>
			<!-- /.navbar-header -->

			<ul class="nav navbar-top-links navbar-right">

				@include('partial.right_navbar')

				<li class="dropdown">
					<a class="dropdown-toggle" data-toggle="dropdown" href="#">
						<?php $user = Sentry::getUser(); ?>
						<i class="fa fa-user fa-fw"></i> {{ $user->first_name.' ('.$user->email.')' }} <i class="fa fa-caret-down"></i>
					</a>
					<ul class="dropdown-menu dropdown-user">
						<li><a href="{{ URL::to('account') }}"><i class="fa fa-user fa-fw"></i> My Account</a>
						</li>
						<li class="divider"></li>
						<li><a href="{{ URL::to('signout') }}"><i class="fa fa-sign-out fa-fw"></i> Sign Out</a>
						</li>
					</ul>
					<!-- /.dropdown-user -->
				</li>
				<!-- /.dropdown -->
			</ul>
			<!-- /.navbar-top-links -->

		</nav>
		<!-- /.navbar-static-top -->

		<nav class="navbar-default navbar-static-side" role="navigation">
			<div class="sidebar-collapse">
				@include('partial.sidemenu')
			</div>
			<!-- /.sidebar-collapse -->
		</nav>
		<!-- /.navbar-static-side -->

		<div id="page-wrapper">

			{{ Widget::get('form-validation') }}

			@yield('content')

			<hr>

			<footer>
				<p class="text-center">{{ date('Y') }} &copy; {{ Setting::meta_data('general', 'organization')->value }} - Powered by {{ HTML::link('http://www.avelca.com', 'Avelca', array('target', '_blank')) }}</p>
			</footer>

		</div>
		<!-- /#page-wrapper -->

	</div>
	<!-- /#wrapper -->

	<div class="modal fade" id="default-question" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h4 class="modal-title">Modal title</h4>
				</div>
				{{ Form::open(array('url' => '/admin/defaultquestion', 'class' => 'form-horizontal')) }}
				<div class="modal-body">
					<p>Select Category</p>
					<select data-required="1" id="select-question-category" style="width:100%" placeholder="Select question category" name="id_category">
		            	<option></option>
		          	</select>
		          	<p>Select Question</p>
					<select data-required="1" id="select-question" style="width:100%" placeholder="Select question" name="id_question">
		            	<option></option>
		          	</select>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button type="submut" class="btn btn-primary">Save changes</button>
				</div>
				{{ Form::close() }}
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->

	<!-- Page-Level Plugin Scripts - Tables -->
	{{ HTML::script(Theme::asset('js/plugins/dataTables/jquery.dataTables.js')) }}
	{{ HTML::script(Theme::asset('js/plugins/dataTables/dataTables.bootstrap.js')) }}

	<!-- CKEDITOR -->
	{{ HTML::script(Theme::asset('js/ckeditor/ckeditor.js')) }}
	{{ HTML::script(Theme::asset('js/ckeditor/adapters/jquery.js')) }}

	<!-- Bootstrap Select -->
	{{ HTML::script(Theme::asset('js/bootstrap-select/bootstrap-select.min.js')) }}

	<!-- Color Picker -->
	{{ HTML::script(Theme::asset('js/jquery.simplecolorpicker.js')) }}

	<!-- Colorpicker -->
	{{ HTML::script(Theme::asset('js/colorpicker/bootstrap-colorpicker.min.js')) }}

	<!-- DateTime -->
	{{ HTML::script(Theme::asset('js/moment.min.js')) }}
	{{ HTML::script(Theme::asset('js/bootstrap-datetimepicker.min.js')) }}

	<!-- Jquery Cookie -->
	{{ HTML::script(Theme::asset('js/jquery.cookie.js')) }}

	<!-- SB Admin Scripts - Include with every page -->
	{{ HTML::script(Theme::asset('js/script.js')) }}

	<!-- my custom javascript -->
	{{ HTML::script(Theme::asset('js/custom.js')) }}
	{{ HTML::script(Theme::asset('js/jquery.multi-select.js')) }}
	{{ HTML::script(Theme::asset('js/select2.js')) }}


</body>

</html>
