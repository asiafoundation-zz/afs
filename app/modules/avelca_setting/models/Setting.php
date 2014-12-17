<?php namespace App\Modules\Avelca_Setting\Models;

use Eloquent;

class Setting extends Eloquent {

	/* Eloquent */
	protected $table = "settings";
	public $timestamps = false;

	/* Mass Assignment */
	protected $fillable = array(
		'category',
		'meta_data'
		);


	public static function meta_data($category, $name)
	{
		$setting = Setting::where('category','=',$category)->first()->meta_data;
		$setting = json_decode($setting);

		return $setting->$name;
	}

	public static function value($category, $name)
	{
		$setting = Setting::where('category','=',$category)->first()->meta_data;
		$setting = json_decode($setting);

		return $setting->$name;
	}
	
}