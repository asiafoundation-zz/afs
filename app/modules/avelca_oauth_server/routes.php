<?php

Route::group(array('prefix' => 'api'), function()
{
	Route::any('oauth/access-token', function()
	{
		return AuthorizationServer::performAccessTokenFlow();
	});
});

Route::group(array('before' => 'backend_theme|auth.sentry|check_permission|password-expiry', 'prefix' => 'admin'), function()
{
	Route::group(array('prefix' => 'api'), function()
	{
		Route::controller('client', 'App\Modules\Avelca_Oauth_Server\Controllers\ClientController');
		Route::controller('scope', 'App\Modules\Avelca_Oauth_Server\Controllers\ScopeController');
	});

});



View::creator('partial.sidemenu', function($view)
{
	$menus = Session::get('menus');

	/* Modify This */
	$data = array(
		'text' => 'oAuth Server',
		'icon' => 'fa fa-exchange fa-fw',
		'navigations' => array(
			'Client' => 'admin/api/client', // Text => 'URL'
			'Scope' => 'admin/api/scope'
			)
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