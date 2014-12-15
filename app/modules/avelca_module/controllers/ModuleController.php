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

class ModuleController extends \BaseController {

	public function getIndex()
	{
		$modules = File::directories(app_path().'/modules');

		$data = array (
			'modules' => $modules
			);

		return View::make('avelca_module::index', $data);
	}

	public function postIndex()
	{
		$rules = array(
			'name' => 'required',
			'enabled' => 'required'
			);

		$validator = Validator::make(Input::all(), $rules);

		if ($validator->fails())
		{
			return Redirect::to('admin/module')->with('messages', $validator->messages());
		}
		else
		{
			$module_name = Input::get('name');
			$json_file = app_path().'/modules/'.$module_name.'/module.json';

			$name = ucwords(str_replace('_',' ',str_replace('-',' ', $module_name)));
			$enabled = Input::get('enabled');

			$module = File::get($json_file);
			$module = json_decode($module);

			$class_name = str_replace(' ','_',ucwords(str_replace('_',' ',str_replace('-',' ', $module_name))));
			$class_name = '\\App\Modules\\'.$class_name.'\Controllers\ModuleController';

			if (class_exists($class_name))
			{
				$obj = new $class_name;
			}

			if($enabled == '1')
			{
				$action = 'enabled';
				$module->enabled = true;

				if (class_exists($class_name))
				{
					$status_info = $obj->install();
					Session::flash('status_info', $status_info);
				}
			}
			else
			{
				$action = 'disabled';
				$module->enabled = false;

				if (class_exists($class_name))
				{
					$status_info = $obj->uninstall();
					Session::flash('status_warning', $status_info);
				}
			}

			File::put($json_file, json_encode($module));

			ToolController::updateAdminPermissions();

			ToolController::artisan('optimize');

			return Redirect::to('admin/module')->with('status', "$name module successfully $action.");
		}
	}

	public static function install()
	{
		//
	}

}