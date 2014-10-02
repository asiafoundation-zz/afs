<?php namespace App\Modules\Avelca_Setting\Controllers;

use App\Modules\Avelca_User\Models\Group;

use Sentry;
use App\Modules\Avelca_User\Models\User;
use View;
use Validator;
use Input;
use Redirect;
use URL;
use Mail;
use App\Modules\Avelca_Setting\Models\Setting;

use AvelcaSettingController;

class SettingController extends AvelcaSettingController {

	public function getIndex()
	{
		$data = array (
			'groups' => Group::all(),
			'settings' => Setting::all()
			);

		return View::make('avelca_setting::index', $data);
	}

	public function postUpdateGeneral()
	{
		$rules = array(
			'category' => 'required',
			'name' => 'required',
			'tag_line' => 'required',
			'organization' => 'required',
			'administrator_email' => 'required|email',
			'theme_color' => 'required'
			);

		return $this->updateSetting(Input::get('category'), $rules);
	}

	public function postUpdateUserManagement()
	{
		$rules = array(
			'category' => 'required',
			'suspend_time' => 'required|integer',
			'max_attempt' => 'required|integer',
			'default_group' => 'required',
			'auto_sign_out' => 'required|integer',
			'minimum_auto_tracked' => 'required|integer',
			'password_expiry_duration' => 'required|integer'
			);

		return $this->updateSetting(Input::get('category'), $rules);
		
	}

	public static function updateSettingValue($category, $key, $value)
	{
		$setting = Setting::where('category','=', $category)->first();
		$meta_data = array();
		$meta_data[$key]['type'] = 'text';
		$meta_data[$key]['value'] = $value;
		$setting->meta_data = json_encode($meta_data);
		$setting->save();
	}

	/* Private Functions */

	public static function updateSetting($category, $rules = array())
	{
		$keys = Input::all();

		$validator = Validator::make($keys, $rules);

		if ($validator->fails())
		{
			return Redirect::to('admin/setting')->with('messages', $validator->messages());
		}
		else
		{
			$meta_data = array();

			foreach ($keys as $key => $value)
			{
				if ($key != '_token' && $key != 'category')
				{
					if(substr($key, strlen($key) - 5) != '_type' && substr($key, strlen($key) - 8) != '_options' && substr($key, strlen($key) - 5) != '_unit')
					{
						if(is_numeric($value))
						{
							if(array_key_exists($key.'_type', $keys)) /* Radio atau Checkbox */
							{
								$meta_data[$key]['type'] = $keys[$key.'_type'];
								$meta_data[$key]['options'] = json_decode($keys[$key.'_options']);
								$meta_data[$key]['selected'] = $value;
							}
							else
							{	
								$meta_data[$key]['type'] = 'number';
								$meta_data[$key]['value'] = $value;
								$meta_data[$key]['unit'] = $keys[$key.'_unit'];							
							}
						}
						else
						{
							if(array_key_exists($key.'_options', $keys)) /* Select */
							{
								$meta_data[$key]['type'] = 'select';
								$meta_data[$key]['options'] = json_decode($keys[$key.'_options']);
								$meta_data[$key]['selected'] = $value;
							}
							elseif(strlen($value) == 7 && substr($value,0,1) == '#') /* Color */
							{
								$meta_data[$key]['type'] = 'color';
								$meta_data[$key]['value'] = $value;
							}
							else
							{
								$meta_data[$key]['type'] = 'text';
								$meta_data[$key]['value'] = $value;
							}
						}

					}
				}	
			}

			$setting = Setting::where('category','=', $category)->first();
			$setting->meta_data = json_encode($meta_data);
			$setting->save();

			return Redirect::to('admin/setting')->with('status', 'Your '.ucwords(str_replace('_',' ',$category)).' Setting have been updated.');
		}
	}

}