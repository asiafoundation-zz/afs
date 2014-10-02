<?php
$b = time(); 

$hour = date("g",$b);
$m = date ("A", $b);

if ($m == "AM")
{
	if ($hour == 12)
	{
		$greeting = Lang::get('general.good_evening');
	}
	elseif ($hour < 4)
	{
		$greeting = Lang::get('general.good_evening');
	}
	elseif ($hour > 3)
	{
		$greeting = Lang::get('general.good_morning');
	}
}

elseif ($m == "PM")
{
	if ($hour == 12)
	{
		$greeting = Lang::get('general.good_afternoon');
	}
	elseif ($hour < 7)
	{
		$greeting = Lang::get('general.good_afternoon');
	}
	elseif ($hour > 6)
	{
		$greeting = Lang::get('general.good_evening');
	}
}
?>

<?php $user = Sentry::getUser(); ?>

<div class="page-header">
	<h1>{{ $greeting.', <b>'.$user->first_name.'</b>!' }}
		<small>{{ \Carbon\Carbon::now()->format('D, d M Y H:i') }}</small>
	</h1>
</div>