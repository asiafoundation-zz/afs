<?php namespace App\Modules\Avelca_User\Controllers;

use Schema;

class ModuleController extends \BaseController {

	public static function artisan($parameter)
	{
		return '<br>'.nl2br(shell_exec('php '.base_path().'/artisan '.$parameter));		
	}

	public static function install()
	{
		//
	}

	public function uninstall()
	{
		//
	}
}

