<?php namespace App\Modules\Avelca_Setting\Seeds;

use DB;
use Seeder;
use App\Modules\Avelca_User\Models\Permission;

class PermissionSeeder extends Seeder {

	public function run()
	{
		/* Settings */
		$permissions = array(
			'setting',
			'setting.update-general',
			'setting.update-user-management'
			);

		Permission::whereIn('name', $permissions)->delete();

		foreach ($permissions as $permission)
		{
			Permission::create(array('name' => $permission));
		}
	}
}