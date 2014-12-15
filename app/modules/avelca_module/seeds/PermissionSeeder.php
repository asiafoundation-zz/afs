<?php namespace App\Modules\Avelca_Module\Seeds;

use DB;
use Seeder;
use App\Modules\Avelca_User\Models\Permission;

class PermissionSeeder extends Seeder {

	public function run()
	{
		/* Module */
		$permissions = array(
			'module',
			'module.edit'
			);

		Permission::whereIn('name', $permissions)->delete();

		foreach ($permissions as $permission)
		{
			Permission::create(array('name' => $permission));
		}
	}
}