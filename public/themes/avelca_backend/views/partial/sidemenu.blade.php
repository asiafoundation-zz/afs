<?php $user = Sentry::getUser(); ?>

<ul class="nav" id="side-menu">

	<li>
		<a href="{{ URL::to('dashboard') }}"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a>
	</li>
	<li>
		<?php
			$survey = Survey::all();

			if($survey->count() > 0){
				?>
				<a href="/admin/survey/managesurvey">Manage Survey<span class="fa"></span></a>		
				<?php
			}else{
				?>
				<a href="/admin/survey/">Manage Survey<span class="fa"></span></a>
				<?php
			}
		?>
		
		<!--ul class="nav nav-third-level collapse" style="height: auto;">
			<li>
				<a href="#">Upload Survey</a>
			</li>
			<li>
				<a href="#">Upload Oversampling Survey</a>
			</li>
		</ul-->
	</li>


	<?php // @include('partial.main_menu') ?>

	@if( $user->hasAccess('module') )
	<!--li>
		<a href="#"><i class="glyphicon glyphicon-flash"></i> Modules<span class="fa arrow"></span></a>

		<ul class="nav nav-second-level">

			<li>
				<a href="{{ URL::to('admin/module') }}">Manage</a>
			</li>


			@include('partial.module_list')

		</ul>
	</li-->
	@endif


	@if( $user->hasAccess('setting') || $user->hasAccess('user') || $user->hasAccess('group') )
	<li>
		<a href="#"><i class="fa fa-cog fa-fw"></i> Administration<span class="fa arrow"></span></a>
		<ul class="nav nav-second-level">

			@if( $user->hasAccess('user') || $user->hasAccess('group') )
			<li>
				@if( $user->hasAccess('user') )
				
					<a href="{{ URL::to('admin/user') }}">User</a>
				
				@endif
				
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


