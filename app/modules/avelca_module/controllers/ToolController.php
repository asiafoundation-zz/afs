<?php namespace App\Modules\Avelca_Module\Controllers;

use File;

use Sentry;
use User;
use View;
use Validator;
use Input;
use Redirect;
use URL;
use Mail;
use Setting;
use Session;
use DB;
use Schema;

use App\Modules\Avelca_User\Models\Group;
use App\Modules\Avelca_User\Models\Permission;

class ToolController extends \BaseController {

	public static function deleteMigrations($migrations)
	{
		DB::table('migrations')->whereIn('migration',$migrations)->delete();
	}

	public static function deleteTables($tables)
	{
		DB::statement('SET foreign_key_checks = 0');

		foreach ($tables as $table)
		{
			Schema::dropIfExists($table);
		}

		DB::statement('SET foreign_key_checks = 1');
	}

	public static function updateAdminPermissions()
	{
		$group = Group::where('name','=','Administrator')->first();
		$permissions = Permission::all();

		foreach ($permissions as $permission) {
			$new_permissions[$permission->name] = 1;
		}

		$group->permissions = json_encode($new_permissions);
		$group->save();
	}

	public static function artisan($parameter)
	{
		return '<br>'.nl2br(shell_exec('php '.base_path().'/artisan '.$parameter));		
	}

	public static function composer($parameter)
	{
		return '<br>'.nl2br(shell_exec('cd .. && composer '.$parameter));		
	}

	public static function deletePermissions($permissions)
	{
		foreach ($permissions as $permission) {
			$permissions = Permission::where('name','=', $permission)->delete();
		}
	}
}
