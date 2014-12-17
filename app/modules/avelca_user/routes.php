<?php


/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

Route::group(array('before' => 'backend_theme|is_signed_in'), function()
{
	/* Back End - Authentication & Authorization */
	Route::get('signup', '\App\Modules\Avelca_User\Controllers\UserController@getSignUp');
	Route::post('signup', '\App\Modules\Avelca_User\Controllers\UserController@postSignUp');

	Route::get('signin', '\App\Modules\Avelca_User\Controllers\UserController@getSignIn');

	Route::group(array('after' => 'password-expiry'), function()
	{
		Route::post('signin', '\App\Modules\Avelca_User\Controllers\UserController@postSignIn');
	});

	Route::get('reset-password', '\App\Modules\Avelca_User\Controllers\UserController@getSelfResetPassword');
	Route::post('reset-password', '\App\Modules\Avelca_User\Controllers\UserController@postSelfResetPassword');

	Route::get('confirm-reset-password/{reset_code}/{user_id}', '\App\Modules\Avelca_User\Controllers\UserController@getConfirmResetPassword');

	Route::get('activate/{activation_code}/{user_id}', '\App\Modules\Avelca_User\Controllers\UserController@getActivate');
});


Route::group(array('before' => 'backend_theme|auth.sentry'), function()
{
	/* Back End - Authentication & Authorization (Continue) */
	Route::get('signout', '\App\Modules\Avelca_User\Controllers\UserController@getSignOut');

	/* Back End - Account */
	Route::get('account', '\App\Modules\Avelca_User\Controllers\UserController@getAccount');
	Route::post('update-profile', '\App\Modules\Avelca_User\Controllers\UserController@postUpdateProfile');
	Route::post('update-password', '\App\Modules\Avelca_User\Controllers\UserController@postUpdatePassword');
});

/*
|--------------------------------------------------------------------------
| Backend
|--------------------------------------------------------------------------
*/

Route::group(array('before' => 'backend_theme|auth.sentry|check_permission|password-expiry', 'prefix' => 'admin'), function()
{
	Route::controller('user', '\App\Modules\Avelca_User\Controllers\Admin\UserController');
	Route::controller('group', '\App\Modules\Avelca_User\Controllers\Admin\GroupController');
});



View::creator('partial.sidemenu', function($view)
{
	$menus = Session::get('menus');

	/* Modify This */
	$data = array(
		'text' => 'Access Control',
		'icon' => 'fa fa-lock fa-fw',
		'show' => false,
		'navigations' => array(
			'User' => 'admin/user', // Text => 'URL'
			'Group' => 'admin/group'
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