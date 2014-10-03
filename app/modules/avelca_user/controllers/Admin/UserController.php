<?php namespace App\Modules\Avelca_User\Controllers\Admin;

use App\Modules\Avelca_User\Models\Group;
use App\Modules\Avelca_User\Models\UserGroup;
use App\Modules\Avelca_User\Models\Throttle;
use App\Modules\Avelca_User\Models\User;
use App\Modules\Avelca_User\Models\Permission;

use Sentry;
use View;
use Validator;
use Input;
use Redirect;
use URL;
use Mail;
use Setting;

class UserController extends \BaseController {

	/* Index */

	public function getIndex()
	{
		$groups = Group::all();
		$users = User::orderBy('id', 'desc')->get();

		$data = array(
			'groups' => $groups,
			'users' => $users,
			'default_group' => Setting::meta_data('user_management','default_group')->value,
			'auto_activation' => Setting::meta_data('user_management','auto_activation')->selected
			);
		
		return View::make('avelca_user::user.index', $data);
	}

	/* View */

	public function getView($id)
	{
		$user = User::find($id);
		return View::make('avelca_user::user.view')->with('user', $user);
	}


	/* Edit */


	public function postEdit()
	{
		$rules = array(
			'group_id' => 'required|integer',
			'first_name' => 'required|between:3,50',
			'last_name' => 'required|between:3,50',
			'email' => 'required|email',
			'activated' => 'required|integer'
			);

		$validator = Validator::make(Input::all(), $rules);

		if ($validator->fails())
		{
			return Redirect::to('admin/user/edit')->with('messages', $validator->messages());
		}
		else
		{
			$user_id = Input::get('id');

			$user = Sentry::getUserProvider()->findById($user_id);

			$user->email = Input::get('email');
			$user->first_name = Input::get('first_name');
			$user->last_name = Input::get('last_name');
			$user->activated = Input::get('activated');

			$user_group = UserGroup::where('user_id','=',$user->id)->first();
			$user_group->group_id = Input::get('group_id');
			$user_group->save();

			if ($user->save())
			{
				return Redirect::to('admin/user')->with('status', 'Success.');
			}
			else
			{
				return Redirect::to('admin/user')->with('status', 'Failed.');
			}
		}
	}

	/* Create */


	public function postCreate()
	{
		$rules = array(
			'group_id' => 'required|integer',
			'first_name' => 'required|between:3,50',
			'last_name' => 'required|between:3,50',
			'email' => 'required|email',
			'password' => 'required|confirmed|min:6',
			'activated' => 'required|integer'
			);

		$validator = Validator::make(Input::all(), $rules);

		if ($validator->fails())
		{
			return Redirect::to('admin/user/create')->with('messages', $validator->messages());
		}
		else
		{
			$now = date('Y-m-d H:i:s');

			try
			{
				$user = Sentry::getUserProvider()->create(array(
					'email'    => Input::get('email'),
					'password' => Input::get('password'),
					'first_name' => Input::get('first_name'),
					'last_name' => Input::get('last_name'),
					'activated' => Input::get('activated')
					));

				$group = Sentry::getGroupProvider()->findById(Input::get('group_id'));

				$user->addGroup($group);

				$status = 'Success.';
				return Redirect::to('admin/user')->with('status', $status);
			}
			catch (\Cartalyst\Sentry\Users\LoginRequiredException $e)
			{
				return Redirect::to('admin/user')->with('status_error', 'Login field is required.');
			}
			catch (\Cartalyst\Sentry\Users\PasswordRequiredException $e)
			{
				return Redirect::to('admin/user')->with('status_error', 'Password field is required.');
			}
			catch (\Cartalyst\Sentry\Users\UserExistsException $e)
			{
				return Redirect::to('admin/user')->with('status_error', 'User with this login already exists.');
			}

			return Redirect::to('admin/user')->with('status_error', $status);
		}
		
	}

	/* Delete */

	public function postDelete()
	{	
		$user = Sentry::getUserProvider()->findById(Input::get('id'));

		$id = $user->id;

		$user->delete();

		$throttle = Throttle::where('user_id','=',$id);

		if($throttle->count() > 0)
		{
			$throttle->delete();
		}

		return Redirect::to('admin/user')->with('status', 'Successfully deleted.');
	}

	/* Reset Password */

	public function postResetPassword()
	{
		$rules = array(
			'id' => 'required'
			);

		$validator = Validator::make(Input::all(), $rules);
		if ($validator->fails())
		{
			return Redirect::to('admin/user')->withErrors($validator);
		}
		else
		{
			try
			{
				$user = Sentry::findUserById(Input::get('id'));

				$resetCode = $user->getResetPasswordCode();

				$email = $user->email;
				$full_name = $user->first_name.' '.$user->last_name;
				$link = URL::to('confirm-reset-password').'/'.$resetCode.'/'.$user->id;

				$data = array(
					'full_name' => $full_name,
					'link' => $link,
					'email' => $email
					);

				Mail::queue(array('html' => 'avelca_user::emails.reset-password'), $data, function($message) use ($email, $full_name)
				{
					$message->subject('Reset Password Link');
					$message->from(Setting::meta_data('general','administrator_email')->value, Setting::meta_data('general','organization')->value.' Administrator');
					$message->to($email, $full_name);
				});

				$user = User::find(Input::get('id'));
				$user->submit_reset_password_at = date('Y-m-d H:i:s');
				$user->save();

				return Redirect::to('admin/user')->with('status', "The password has been reset. Please check the email to confirm your new password.");
			}
			catch (\Cartalyst\Sentry\Users\UserNotFoundException $e)
			{
				return Redirect::to('admin/user')->with('status_error', 'User was not found.');
			}
		}
	}

	public function postSetPassword()
	{
		$rules = array(
			'id' => 'required',
			'password' => 'required'
			);

		$validator = Validator::make(Input::all(), $rules);
		if ($validator->fails())
		{
			return Redirect::to('admin/user')->withErrors($validator);
		}
		else
		{
			$user = Sentry::findUserById(Input::get('id'));

			$user->update_password_at = date('Y-m-d H:i:s');
			$user->password = Input::get('password');
			$user->save();

			return Redirect::to('admin/user')->with('status', "Password updated.");
		}
	}

	
	/* Ban */

	public function postBan()
	{
		
		try
		{
			$throttle = Sentry::findThrottlerByUserId(Input::get('id'));

			if ($throttle->ban())
			{
				return Redirect::to('admin/user')->with('status_error', 'Failed to ban user.');
			}
			else
			{
				return Redirect::to('admin/user')->with('status', 'The user has been banned.');
			}
		}
		catch (\Cartalyst\Sentry\Users\UserNotFoundException $e)
		{
			return Redirect::to('admin/user')->with('status_error', 'User was not found.');
		}
		
	}

	/* Unban */

	public function postUnban()
	{

		try
		{
			$throttle = Sentry::findThrottlerByUserId(Input::get('id'));

			if ($throttle->unban())
			{
				return Redirect::to('admin/user')->with('status_error', 'Failed to unban user.');
			}
			else
			{
				return Redirect::to('admin/user')->with('status', 'The user has been unbanned.');
			}
		}
		catch (\Cartalyst\Sentry\Users\UserNotFoundException $e)
		{
			return Redirect::to('admin/user')->with('status_error', 'User was not found.');
		}

	}

	/* Suspend */

	public function postSuspend()
	{
		
		try
		{
			$throttle = Sentry::findThrottlerByUserId(Input::get('id'));

			if ($throttle->suspend())
			{
				return Redirect::to('admin/user')->with('status_error', 'Failed to suspend user.');
			}
			else
			{
				return Redirect::to('admin/user')->with('status', 'The user has been suspended.');
			}
		}
		catch (\Cartalyst\Sentry\Users\UserNotFoundException $e)
		{
			return Redirect::to('admin/user')->with('status_error', 'User was not found.');
		}
		
	}

	/* Unsuspend */

	public function postUnsuspend()
	{
		try
		{
			$throttle = Sentry::findThrottlerByUserId(Input::get('id'));

			if ($throttle->unsuspend())
			{
				return Redirect::to('admin/user')->with('status_error', 'Failed to unsuspend user.');
			}
			else
			{
				return Redirect::to('admin/user')->with('status', 'The user has been unsuspended.');
			}
		}
		catch (\Cartalyst\Sentry\Users\UserNotFoundException $e)
		{
			return Redirect::to('admin/user')->with('status_error', 'User was not found.');
		}
		
	}
}