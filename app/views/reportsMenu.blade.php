<?php $user = Sentry::getUser(); ?>

@if($user->hasAccess('report'))


			<li>
			<a href="#">Report Category A <span class="fa arrow"></span></a>
			<ul class="nav nav-third-level collapse" style="height: auto;">

			
@if($user->hasAccess("report.report-a"))
<li><a href="{{ URL::to("admin/report/report-a") }}">Report A</a></li>
@endif


@if($user->hasAccess("report.report-b"))
<li><a href="{{ URL::to("admin/report/report-b") }}">Report B</a></li>
@endif



			</ul>
			</li>
			

			<li>
			<a href="#">Report Category B <span class="fa arrow"></span></a>
			<ul class="nav nav-third-level collapse" style="height: auto;">

			
@if($user->hasAccess("report.report-c"))
<li><a href="{{ URL::to("admin/report/report-c") }}">Report C</a></li>
@endif


@if($user->hasAccess("report.report-d"))
<li><a href="{{ URL::to("admin/report/report-d") }}">Report D</a></li>
@endif



			</ul>
			</li>
			


@endif


