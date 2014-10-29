<?php $user = Sentry::getUser(); ?>

<ul class="nav" id="side-menu">

	<li>
		<a href="{{ URL::to('dashboard') }}"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a>
	</li>
	
		<?php
			$survey = Survey::all();

			if($survey->count() > 0){
				?>
				<li>
					<a href="/admin/survey/managesurvey">Manage Survey<span class="fa"></span></a>
				</li>
				<li>
					<a href="/admin/filter">Manage Filter<span class="fa"></span></a>
				</li>
				<?php
			}else{
				?>
				<li>
					<a href="/admin/survey/">Manage Survey<span class="fa"></span></a>
				</li>
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


