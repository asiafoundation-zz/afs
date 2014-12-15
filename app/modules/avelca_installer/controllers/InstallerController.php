<?php namespace App\Modules\Avelca_Installer\Controllers;

use View;
use Validator;
use Redirect;
use Sentry;
use Input;
use DB;
use App\Modules\Avelca_User\Models\Permission;

use App\Modules\Avelca_User\Controllers\ModuleController as AvelcaUser;
use App\Modules\Avelca_Module\Controllers\ModuleController as AvelcaModule;
use App\Modules\Avelca_Setting\Controllers\ModuleController as AvelcaSetting;

class InstallerController extends \BaseController {

	public function getIndex()
	{
		return View::make('avelca_installer::index');
	}

	public function postIndex()
	{
		$rules = array(
			'first_name' => 'required|min:3',
			'last_name' => 'required:min:3',
			'email' => 'required|email',
			'password' => 'required|min:6|confirmed'
			);

		$validator = Validator::make(Input::all(), $rules);

		if ( $validator->fails() )
		{
			return Redirect::to('install')->withErrors($validator);
		}
		else
		{
			DB::table('users')->truncate();
			DB::table('throttle')->truncate();
			DB::table('users_groups')->truncate();

			Sentry::getUserProvider()->create(array(
				'email'       => Input::get('email'),
				'password'    => Input::get('password'),
				'first_name'  => Input::get('first_name'),
				'last_name'   => Input::get('last_name'),
				'created_at' => date('Y-m-d H:i:s'),
				'activated_at' => date('Y-m-d H:i:s'),
				'activated'   => 1
				));

			if(DB::table('groups')->whereName('Administrator')->count() == 0)
			{
				$permissions = Permission::all();
				$all_permission = array();

				foreach ($permissions as $permission) {
					$all_permission[$permission->name] = 1;
				}

				Sentry::getGroupProvider()->create(array(
					'name'        => 'Administrator',
					'permissions' => $all_permission
					));
			}

			$user  = Sentry::getUserProvider()->findByLogin(Input::get('email'));
			$group = Sentry::getGroupProvider()->findByName('Administrator');
			$user->addGroup($group);

			return Redirect::to('signin')->with('status', 'Successfully installed.');
		}
	}

}