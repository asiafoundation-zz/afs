<?php

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

App::before(function($request)
{		
	// Auto Sign Out
	$user = Sentry::getUser();

	if( ! is_null($user) )
	{
		$now = date_create(date("Y-m-d H:i:s"));
		$last_activity = date_create($user->last_activity);

		$user_id = $user->id;

		if( ! is_null($last_activity) )
		{
			$diff = date_diff($now, $last_activity)->format("%i");
			$timeout = Setting::meta_data('user_management','auto_sign_out')->value; // minutes
			$minimum_tracked = Setting::meta_data('user_management','minimum_auto_tracked')->value; // minutes

			if($diff >= $minimum_tracked)
			{
				$user->last_activity = date("Y-m-d H:i:s");
				$user->save();
			}

			if($diff >= $timeout)
			{
				$user->last_activity = null;
				$user->save();

				Sentry::logout();

				return Redirect::guest('signin')->with("status_warning", "You've been inactive for $diff minutes. Session timed out, please sign in again.");
			}
		}
	}
});


App::after(function($request, $response)
{
	//
});

/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

Route::filter('auth', function()
{
	if (Auth::guest())
	{
		if (Request::ajax())
		{
			return Response::make('Unauthorized', 401);
		}
		else
		{
			return Redirect::guest('login');
		}
	}
});


Route::filter('auth.basic', function()
{
	return Auth::basic();
});

/* Di bawah ini adalah tambahan */

Route::filter('is_signed_in', function()
{
	if (Sentry::check()) return Redirect::to('dashboard');
});

Route::filter('auth.sentry', function()
{
	if ( ! Sentry::check()) return Redirect::guest('signin');
});

Route::filter('check_permission', function()
{
	$permission = Request::segment(2);
	
	if ( ! is_null(Request::segment(3)))
	{
		$permission .= '.'.Request::segment(3);
	}

	$user = Sentry::getUser();

	if ( ! $user->hasAccess($permission))
	{
		return View::make('error.forbidden');
	}
});

Route::filter('check_permission_alternate', function()
{
	$permission = Request::segment(1);

	if ( ! is_null(Request::segment(2)))
	{
		$permission .= '.'.Request::segment(2);
	}

	$user = Sentry::getUser();

	if ( ! $user->hasAccess($permission))
	{
		return View::make('error.forbidden');
	}
});


Route::filter('password-expiry', function()
{
	$user = Sentry::getUser();

	if($user)
	{
		$update_password_at = $user->update_password_at;

		if( ! is_null($update_password_at))
		{
			$now = new DateTime('now');
			$update_password_at = new DateTime($update_password_at);

			$diff = date_diff($now, $update_password_at)->format("%a");
			$duration = Setting::meta_data('user_management', 'password_expiry_duration')->value - Setting::meta_data('user_management', 'password_expiry_reminder')->value;

			/* First day of tolerance period */
			if($diff == $duration)
			{
				$email = $user->email;
				$full_name = $user->first_name.' '.$user->last_name;
				$link = URL::to('account');

				$data = array(
					'full_name' => $full_name,
					'link' => $link,
					'email' => $email,
					'duration' => $duration
					);

				Mail::send(array('html' => 'avelca_user::emails.user.password-expiry-reminder'), $data, function($message) use ($email, $full_name)
				{
					$message->from(Setting::meta_data('general','administrator_email')->value, Setting::meta_data('general','organization')->value.' Administrator')->subject('Account Activation Link');
					$message->to($email, $full_name)->subject('Password Update Reminder');
				});
			}

			/* During tolerance period */
			if($diff >= $duration)
			{
				Session::flash('status_warning', 'Your password will be expire in '.$diff.' days.');
			}

			/* D-Day */
			if($diff == Setting::meta_data('user_management', 'password_expiry_duration')->value)
			{
				Session::forget('status_warning');
				Session::flash('status_error', 'Your password is expired. Please update your password.');
				return Redirect::to('account');
			}
		}
	}

});


/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function()
{
	if (Auth::check()) return Redirect::to('/');
});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function()
{
	if (Session::token() != Input::get('_token'))
	{
		throw new Illuminate\Session\TokenMismatchException;
	}
});


/*
|--------------------------------------------------------------------------
| Theme Filter
|--------------------------------------------------------------------------
|
*/

Route::filter('frontend_theme', function()
{
	Theme::init('avelca_frontend');
});

Route::filter('backend_theme', function()
{
	Theme::init('avelca_backend');
});

/*
|--------------------------------------------------------------------------
| Errors
|--------------------------------------------------------------------------
|
*/

App::missing(function($exception)
{
	Theme::init('avelca_frontend');

	return Response::view('error.404', array(), 404);
});