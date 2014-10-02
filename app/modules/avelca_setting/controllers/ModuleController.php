<?php namespace App\Modules\Avelca_Setting\Controllers;

use Schema;

class ModuleController extends \BaseController {

	private static function artisan($parameter)
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

