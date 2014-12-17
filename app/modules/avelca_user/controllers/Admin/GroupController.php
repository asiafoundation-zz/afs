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

class GroupController extends \BaseController {

	public $restful = true;
	
	/* Index */

	public function getIndex()
	{
		$permissions = Permission::all();
		$groups = Group::orderBy('id', 'desc')->get();

		$data = array(
			'groups' => $groups,
			'permissions' => $permissions
			);

		return View::make('avelca_user::group.index', $data);
		
	}

	/* Edit */
	
	public function postEdit()
	{
		
		$rules = array(
			'name' => 'required',
			'permissions' => 'required'

			);

		$validator = Validator::make(Input::all(), $rules);

		if ($validator->fails())
		{
			return Redirect::to('admin/group')->with('messages', $validator->messages());
		}
		else
		{

			// Empty
			$group_empty = Group::find(Input::get('id'));
			$group_empty->permissions = '';
			$group_empty->save();

			$input_permissions = Input::get('permissions');

			$old_permissions_all = Permission::all();
			foreach ($old_permissions_all as $permission) {
				$old_permissions[] = $permission->name;
			}

			foreach ($input_permissions as $permission) {
				$temps[] = Permission::find($permission)->name;
			}

			asort($temps, SORT_STRING);
			asort($old_permissions, SORT_STRING);

			$permissions_data = array();

			foreach ($temps as $permission) {
				$permissions_data["$permission"] = 1;
			}

			$group = Sentry::getGroupProvider()->findById(Input::get('id'));

    // Update the group details
			$group->name = Input::get('name');
			$group->permissions = $permissions_data;

    // Update the group
			if ($group->save())
			{
				Permission::truncate();

				foreach ($old_permissions as $permission) {
					$permission_item = new Permission;
					$permission_item->name = $permission;
					$permission_item->save();
				}

				return Redirect::to('admin/group')->with('status', 'Success.');
			}
			else
			{
				return Redirect::to('admin/group')->with('status', 'Failed.');
			}
		}
		
	}

	/* Create */

	public function postCreate()
	{
		
		$rules = array(
			'name' => 'required',
			'permissions' => 'required'
			);

		$validator = Validator::make(Input::all(), $rules);

		if ($validator->fails())
		{
			return Redirect::to('admin/group')->with('messages', $validator->messages());
		}
		else
		{
			try
			{
				$permissions = Input::get('permissions');

				$permissions_data = array();

				foreach ($permissions as $permission) {
					$permissions_name = Permission::find($permission)->name;
					$permissions_data["$permissions_name"] = 1;

				}

   				 // Create the group
				$group = Sentry::getGroupProvider()->create(array(
					'name'        => Input::get('name'),
					'permissions' => $permissions_data
					));
				$status = 'Success.';
			}

			catch (\Cartalyst\Sentry\Groups\NameRequiredException $e)
			{
				$status = 'Name field is required';
			}

			catch (\Cartalyst\Sentry\Groups\GroupExistsException $e)
			{
				$status = 'Group already exists';
			}

			return Redirect::to('admin/group')->with('status', $status);
		}
		
	}

	/* Delete */

	public function postDelete()
	{
		try
		{
			$group = Sentry::getGroupProvider()->findById(Input::get('id'));

			$id = $group->id;

			$group->delete();

			$status = 'Successfully deleted.';
		}

		catch (\Cartalyst\Sentry\Groups\GroupNotFoundException $e)
		{
			$status = 'Group was not found.';
		}

		return Redirect::to('admin/group')->with('status', $status);
		
	}

}