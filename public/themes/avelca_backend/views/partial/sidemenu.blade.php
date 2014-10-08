<?php $user = Sentry::getUser(); ?>

<ul class="nav" id="side-menu">

	<li>
		<a href="{{ URL::to('dashboard') }}"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a>
	</li>
	<li>
		<a href="#">Upload Excel <span class="fa arrow"></span></a>
		<ul class="nav nav-third-level collapse" style="height: auto;">
			<li>
				<a href="#">Upload Survey</a>
			</li>
			<li>
				<a href="#">Upload Oversampling Survey</a>
			</li>
		</ul>
	</li>

	@include('partial.main_menu')

	@if( $user->hasAccess('module') )
	<li>
		<a href="#"><i class="glyphicon glyphicon-flash"></i> Modules<span class="fa arrow"></span></a>

		<ul class="nav nav-second-level">

			<li>
				<a href="{{ URL::to('admin/module') }}">Manage</a>
			</li>


			@include('partial.module_list')

		</ul>
	</li>
	@endif


	@if( $user->hasAccess('setting') || $user->hasAccess('user') || $user->hasAccess('group') )
	<li>
		<a href="#"><i class="fa fa-cog fa-fw"></i> Administration<span class="fa arrow"></span></a>
		<ul class="nav nav-second-level">

			@if( $user->hasAccess('user') || $user->hasAccess('group') )
			<li>
				<a href="#">Access Control <span class="fa arrow"></span></a>
				<ul class="nav nav-third-level">
					@if( $user->hasAccess('user') )
					<li>
						<a href="{{ URL::to('admin/user') }}">User</a>
					</li>
					@endif
					@if( $user->hasAccess('group') )
					<li>
						<a href="{{ URL::to('admin/group') }}">Group</a>
					</li>
					@endif
				</ul>
				<!-- /.nav-third-level -->
			</li>
			@endif

			@if( $user->hasAccess('setting') )
			<li>
				<a href="{{ URL::to('admin/setting') }}">Setting</a>
			</li>
			@endif

		</ul>
		<!-- /.nav-second-level -->
	</li>
	@endif
</ul>
<!-- /.nav-second-level -->
</li>
</ul>
<!-- /#side-menu -->


