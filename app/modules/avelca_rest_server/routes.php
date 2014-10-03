<?php

Route::group(array('before' => 'auth.sentry', 'prefix' => 'rest'), function() {

	$files = \File::allFiles(app_path().'/controllers/API');

	foreach ($files as $file) {
		$name = explode("/", $file);

		$version = $name[count($name) - 2];
		$name = end($name);
		$name = str_replace(".php", "", $name);
		$routeName = str_replace("Controller", '', $name);

		$parts = preg_split('/(?=[A-Z])/', $routeName, -1, PREG_SPLIT_NO_EMPTY);
		$routeName = '';

		for($i = 0; $i < count($parts); $i++)
		{
			$routeName .= $parts[$i];

			if($i != (count($parts) - 1) )
			{
				$routeName .= '-';
			}
		}

		$routeName = strtolower(\Str::singular($routeName));

		if($version != 'custom')
		{
			Route::get($version.'/'.$routeName.'/{id}', array('uses' => 'API\\'.$version.'\\'.$name.'@show'));
		}
	}
});

Route::group(array('before' => 'oauth'), function() {

	$files = \File::allFiles(app_path().'/controllers/API');

	foreach ($files as $file) {
		$name = explode("/", $file);

		$version = $name[count($name) - 2];
		$name = end($name);
		$name = str_replace(".php", "", $name);
		$routeName = str_replace("Controller", '', $name);

		$parts = preg_split('/(?=[A-Z])/', $routeName, -1, PREG_SPLIT_NO_EMPTY);
		$routeName = '';

		for($i = 0; $i < count($parts); $i++)
		{
			$routeName .= $parts[$i];

			if($i != (count($parts) - 1) )
			{
				$routeName .= '-';
			}
		}

		$routeName = strtolower(\Str::plural($routeName));

		if($version != 'custom')
		{
			\Route::resource($version.'/'.$routeName, 'API\\'.$version.'\\'.$name);

			if(File::exists(app_path().'/controllers/API/'.$version.'/custom/'.$name.'.php'))
			{
				\Route::controller($version.'/'.$routeName.'/custom', 'API\\'.$version.'\custom\\'.$name);
			}
		}
	}
});



View::creator('partial.sidemenu', function($view)
{
	$menus = Session::get('menus');

	/* Modify This */
	$data = array(
		'text' => 'REST Server',
		'url' => '#',
		'icon' => 'fa fa-magic fa-fw',
		'show' => false
		);
	/* End Modify This */

	if (empty($menus))
	{
		Session::put('menus', array($data));
	}
	else
	{
		Session::push('menus', $data);
	}

	$menus = Session::get('menus');

	$view->with('menus', $menus);
});