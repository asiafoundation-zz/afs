<?php

Route::group(array('before' => 'backend_theme|auth.sentry|check_permission|password-expiry', 'prefix' => 'admin'), function()
{
	Route::controller('module', '\App\Modules\Avelca_Module\Controllers\ModuleController');
});


View::creator('partial.sidemenu', function($view)
{
	$menus = Session::get('menus');

	/* Modify This */
	$data = array(
		'text' => 'Module',
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