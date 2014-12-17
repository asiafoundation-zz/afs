<!-- Auto Detect Model -->
{{ AvelcaController::mainNavigation() }}
<!-- End Auto Detect Model -->

<?php $customView = 'reportsMenu'; ?>
@if(View::exists($customView))
<li>
	<a href="#"><i class="glyphicon glyphicon-stats"></i> Report<span class="fa arrow"></span></a>
	<ul class="nav nav-second-level">
		@include($customView)		
	</ul>
</li>
@endif