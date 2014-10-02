<?php namespace App\Modules\Avelca_User\Seeds;

use Seeder;
use App\Modules\Avelca_User\Models\Permission;

class PermissionSeeder extends Seeder {

	public function run()
	{
		/* Dashboard */
		$permissions = array(
			'dashboard'
			);

		Permission::whereIn('name', $permissions)->delete();

		foreach ($permissions as $permission)
		{
			Permission::create(array('name' => $permission));
		}

		/* Users */
		$permissions = array(
			'user',
			'user.create',
			'user.view',
			'user.edit',
			'user.delete',
			'user.reset-password',
			'user.set-password',
			'user.ban',
			'user.unban',
			'user.suspend',
			'user.unsuspend'
			);

		Permission::whereIn('name', $permissions)->delete();

		foreach ($permissions as $permission)
		{
			Permission::create(array('name' => $permission));
		}

		/* Groups */
		$permissions = array(
			'group',
			'group.create',
			'group.edit',
			'group.delete'
			);

		Permission::whereIn('name', $permissions)->delete();

		foreach ($permissions as $permission)
		{
			Permission::create(array('name' => $permission));
		}
	}
}