<?php namespace App\Modules\Avelca_User\Controllers;

use Setting;
use View;
use Redirect;
use Mail;
use Validator;
use Sentry;
use Input;
use Session;
use URL;

use App\Modules\Avelca_User\Models\User;

class UserController extends \BaseController {

	/* Sign Up */

	public function getSignUp()
	{
		if(Setting::meta_data('user_management','allow_sign_up')->selected == 1)
		{
			return View::make('user.signup');
		}
		else
		{
			return Redirect::to('signin');
		}
	}

	public function postSignUp()
	{
		$rules = array(
			'first_name' => 'required|min:3',
			'last_name' => 'required:min:3',
			'email' => 'required|email',
			'password' => 'required|min:6',
			'is_read' => 'accepted'
			);

		$validator = Validator::make(Input::all(), $rules);

		if ( $validator->fails() )
		{
			return Redirect::to('signup')->withErrors($validator)->withInput();
		}
		else
		{
			$now = date('Y-m-d H:i:s');

			try
			{
				$is_auto_activation = Setting::meta_data('user_management','auto_activation')->selected;

				if($is_auto_activation == 1)
				{
					$user = Sentry::getUserProvider()->create(array(
						'first_name' => ucwords(Input::get('first_name')),
						'last_name' => ucwords(Input::get('last_name')),
						'email'    => Input::get('email'),
						'password' => Input::get('password'),
						'activated_at' => $now,
						'activated' => 1
						));

					$status = 'You can now sign in with your account.';
				}
				else
				{
					$user = Sentry::getUserProvider()->create(array(
						'first_name' => ucwords(Input::get('first_name')),
						'last_name' => ucwords(Input::get('last_name')),
						'email'    => Input::get('email'),
						'password' => Input::get('password'),
						'activated_at' => $now,
						'activated' => 0
						));

					$activationCode = $user->getActivationCode();

					$email = $user->email;
					$full_name = $user->first_name.' '.$user->last_name;
					$link = URL::to('activate').'/'.$activationCode.'/'.$user->id;

					$data = array(
						'full_name' => $full_name,
						'link' => $link,
						'email' => $email
						);

					Mail::send(array('html' => 'avelca_user::emails.activation'), $data, function($message) use ($email, $full_name)
					{
						$message->from(Setting::meta_data('general','administrator_email')->value, Setting::meta_data('general','organization')->value.' Administrator')->subject('Account Activation Link');
						$message->to($email, $full_name)->subject('Account Activation Link');
					});

					$status = 'Please check your email to activate your account.';
				}

				$default_group = Setting::meta_data('user_management','default_group')->value;
				
				$group = Sentry::getGroupProvider()->findByName($default_group);
				$user->addGroup($group);

				return Redirect::to('signin')->with('status', $status);
			}
			catch (\Cartalyst\Sentry\Users\LoginRequiredException $e)
			{
				return Redirect::to('signup')->with('status_error', 'Login field is required.')->withInput();
			}
			catch (\Cartalyst\Sentry\Users\PasswordRequiredException $e)
			{
				return Redirect::to('signup')->with('status_error', 'Password field is required.')->withInput();
			}
			catch (\Cartalyst\Sentry\Users\UserExistsException $e)
			{
				return Redirect::to('signup')->with('status_error', 'User with this login already exists.');
			}

			return Redirect::to('signup')->with('status_error', $status)->withInput();
		}
	}

	/* Sign In */

	public function getSignIn()
	{
		if( User::count() == 0 )
		{
			return Redirect::to('install');
		}
		else
		{
			$redirect = Input::get('redirect');

			if( ! empty($redirect) )
			{
				Session::put('intendedUrl', $redirect);
			}
			
			return View::make('user.signin');

		}
	}

	public function postSignIn()
	{
		$rules = array(
			'email' => 'required|email',
			'password' => 'required|min:6'
			);
		$validator = Validator::make(Input::all(), $rules);

		if ( $validator->fails() )
		{
			return Redirect::to('signin')->withErrors($validator)->withInput();
		}
		else
		{
			$credentials = array(
				'email'    => Input::get('email'),
				'password' => Input::get('password')
				);
			try
			{
				if(Input::get('remember_me') == '1')
				{
					$user = Sentry::authenticateAndRemember($credentials);
				}
				else
				{
					$user = Sentry::authenticate($credentials);
				}

				if ($user)
				{
					try
					{
						$throttle = Sentry::findThrottlerByUserId($user->id);
						$throttle->clearLoginAttempts();

						$user->submit_reset_password_at = null;
						$user->save();

						$redirect = Session::get('intendedUrl');

						if( ! empty($redirect))
						{
							Session::forget('intendedUrl');
							Session::forget('url.intended');
							return Redirect::to($redirect);
						}

						return Redirect::intended('dashboard');
					}
					catch (\Cartalyst\Sentry\Users\UserNotFoundException $e)
					{
						return Redirect::to('signin')->withErrors(array('login' => $e->getMessage()))->withInput();
					}
				}
			}
			catch (\Cartalyst\Sentry\Users\LoginRequiredException $e)
			{
				return Redirect::to('signin')->withErrors(array('login' => $e->getMessage()))->withInput();
			}
			catch (\Cartalyst\Sentry\Users\PasswordRequiredException $e)
			{
				$user = User::where('email', '=', Input::get('email'))->first();

				$email = $user->email;
				$full_name = $user->first_name.' '.$user->last_name;
				$link = URL::to('reset-password');

				$data = array(
					'full_name' => $full_name,
					'link' => $link,
					'email' => $email
					);

				Mail::send(array('html' => 'avelca_user::emails.failed-signin'), $data, function($message) use ($email, $full_name)
				{
					$message->from(Setting::meta_data('general','administrator_email')->value, Setting::meta_data('general','organization')->value.' Administrator')->subject('Account Activation Link');
					$message->to($email, $full_name)->subject('Failed Sign In Notification');
				});

				return Redirect::to('signin')->withErrors(array('login' => $e->getMessage()))->withInput();
			}
			catch (\Cartalyst\Sentry\Users\WrongPasswordException $e)
			{
				return Redirect::to('signin')->withErrors(array('login' => $e->getMessage()))->withInput();
			}
			catch (\Cartalyst\Sentry\Users\UserNotFoundException $e)
			{
				return Redirect::to('signin')->withErrors(array('login' => $e->getMessage()));
			}
			catch (\Cartalyst\Sentry\Users\UserNotActivatedException $e)
			{
				return Redirect::to('signin')->withErrors(array('login' => $e->getMessage()));
			}
			catch (\Cartalyst\Sentry\Throttling\UserSuspendedException $e)
			{
				$throttle = Sentry::findThrottlerByUserLogin(Input::get('email'));
				$time = $throttle->getSuspensionTime();
				return Redirect::to('signin')->withErrors(array('status_error' => "User is suspended for $time minutes."));
			}
			catch (\Cartalyst\Sentry\Throttling\UserBannedException $e)
			{
				return Redirect::to('signin')->withErrors(array('login' => $e->getMessage()));
			}
			catch(\Exception $e)
			{
				return Redirect::to('signin')->withErrors(array('login' => $e->getMessage()));
			}
		}
	}

	/* Self Reset Password */

	public function getSelfResetPassword()
	{
		return View::make('user.self-reset-password');
	}

	public function postSelfResetPassword()
	{
		$input = Input::all();

		$rules = array(
			'email' => 'required|email'
			);

		$validator = Validator::make($input, $rules);

		if ($validator->fails())
		{
			return Redirect::to('reset-password')->withErrors($validator);
		}
		else
		{
			try
			{
				$user = Sentry::findUserByLogin(Input::get('email'));

				$resetCode = $user->getResetPasswordCode();

				$email = $user->email;
				$full_name = $user->first_name.' '.$user->last_name;
				$link = URL::to('confirm-reset-password').'/'.$resetCode.'/'.$user->id;

				$data = array(
					'full_name' => $full_name,
					'link' => $link,
					'email' => $email
					);

				Mail::send(array('html' => 'avelca_user::emails.reset-password'), $data, function($message) use ($email, $full_name)
				{
					$message->subject('Reset Password Link');
					$message->from(Setting::meta_data('general','administrator_email')->value, Setting::meta_data('general','organization')->value.' Administrator');
					$message->to($email, $full_name);
				});

				$user_id = $user->id;

				$user = User::find($user_id);
				$user->submit_reset_password_at = date('Y-m-d H:i:s');
				$user->save();

				return Redirect::to('reset-password')->with('status', "Your password has been reset. Please check your email to confirm your new password.");
			}
			catch (\Cartalyst\Sentry\Users\UserNotFoundException $e)
			{
				return Redirect::to('reset-password')->with('status_error', 'User was not found.');
			}
		}
	}

	/* Confirm Reset Password */
	public function getConfirmResetPassword($reset_code, $user_id)
	{
		$submit_reset_password_at = User::find($user_id)->submit_reset_password_at;

		$now = new \DateTime('now');
		$submit_reset_password_at = new \DateTime($submit_reset_password_at);

		$diff = date_diff($now, $submit_reset_password_at)->format("%a");

		if($diff <= 1)
		{
			try
			{
				$user = Sentry::findUserById($user_id);
				$reset_password = $reset_code;

				/* Check if the reset password code is valid */
				if ($user->checkResetPasswordCode($reset_password))
				{
					$new_password = str_random(6);

					/* Attempt to reset the user password */
					if ($user->attemptResetPassword($reset_password, $new_password))
					{
						$user = User::find($user_id);
						$user->submit_reset_password_at = '';
						$user->password = \Hash::make($new_password);
						$user->save();

						return Redirect::to('signin')->with('status', 'New Password : '.$new_password);
					}
					else
					{
						return Redirect::to('signin')->with('status_error', 'Failed to reset password.');
					}
				}
				else
				{
					return Redirect::to('signin')->with('status_error', 'Failed to reset password.');
				}
			}
			catch (\Cartalyst\Sentry\Users\UserNotFoundException $e)
			{
				return Redirect::to('signin')->with('status_error', 'User was not found.');
			}
		}
		else
		{
			return Redirect::to('reset-password')->with('status_error', 'Your Password Confirmation is expired. Please request new password reset.');
		}
	}

	/* Activate */

	public function getActivate($activation_code, $user_id)
	{
		try
		{
			$user = Sentry::findUserById($user_id);

			if ($user->attemptActivation($activation_code))
			{
				return Redirect::to('signin')->with('status', 'User activation success.');
			}
			else
			{
				return Redirect::to('signin')->with('status_error', 'User activation failed.');
			}
		}
		catch (\Cartalyst\Sentry\Users\UserNotFoundException $e)
		{
			return Redirect::to('signin')->with('status_error', 'User was not found.');
		}
		catch (\Cartalyst\Sentry\Users\UserAlreadyActivatedException $e)
		{
			return Redirect::to('signin')->with('status_error', 'User is already activated.');
		}
	}

	/* Sign Out */

	public function getSignOut()
	{
		Sentry::logout();
		Session::flush();
		Session::flash('status', "You've successfully signed out.");
		return Redirect::to('signin');
	}

	/* Account */

	public function getAccount()
	{
		return View::make('user.account')->with('user', Sentry::getUser());
	}

	/* Update Profile */

	public function postUpdateProfile()
	{
		$rules = array(
			'first_name' => 'required|min:3',
			'last_name' => 'required|min:3',
			'email' => 'required|email'
			);

		$validator = Validator::make(Input::all(), $rules);

		if ( $validator->fails() )
		{
			return Redirect::back()->withErrors($validator);
		}
		else
		{
			try
			{
				$user = Sentry::getUser();

				$user->email = Input::get('email');
				$user->first_name = ucwords(Input::get('first_name'));
				$user->last_name = ucwords(Input::get('last_name'));

				if ($user->save())
				{
					$message = 'Profile updated.';
				}
				else
				{
					$message = 'User information was not updated';
				}
			}

			catch (\Cartalyst\Sentry\Users\UserExistsException $e)
			{
				$message = 'User with this login already exists.';
			}
			catch (\Cartalyst\Sentry\Users\UserNotFoundException $e)
			{
				$message = 'User was not found.';
			}

			return Redirect::back()->with('status', $message);
		}
	}

	/* Update Password */

	public function postUpdatePassword()
	{
		$rules = array(
			'current_password' => 'required|min:6',
			'password' => 'required|min:6|confirmed|different:current_password'
			);

		$validator = Validator::make(Input::all(), $rules);

		if ( $validator->fails() )
		{
			return Redirect::back()->withErrors($validator);
		}
		else
		{			
			$user = Sentry::getUser();

			if($user->checkPassword( Input::get('current_password')))
			{
				$user->update_password_at = date('Y-m-d H:i:s');
				$user->password = Input::get('password');
				$user->save();

				$email = $user->email;
				$full_name = $user->first_name.' '.$user->last_name;
				$link = URL::to('reset-password');

				$data = array(
					'full_name' => $full_name,
					'link' => $link,
					'email' => $email
					);

				Mail::send(array('html' => 'avelca_user::emails.update-password'), $data, function($message) use ($email, $full_name)
				{
					$message->subject('Password Updated');
					$message->from(Setting::meta_data('general','administrator_email')->value, Setting::meta_data('general','organization')->value.' Administrator');
					$message->to($email, $full_name);
				});

				$message = 'Password updated';
			}
			else
			{
				$message = 'Current password did not match.';
			}

			return Redirect::back()->with('status', $message);
		}
	}



}
