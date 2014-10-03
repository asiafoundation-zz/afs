<?php namespace App\Modules\Avelca_Oauth_Server\Seeds;

use Seeder;
use App\Modules\Avelca_User\Models\Permission;

class PermissionSeeder extends Seeder {

	public function run()
	{
		/* API oAuth Client */
		Permission::create(array('name' => 'api.client'));
		Permission::create(array('name' => 'api.client.create'));
		Permission::create(array('name' => 'api.client.edit'));
		Permission::create(array('name' => 'api.client.delete'));

		/* API oAuth Scope */
		Permission::create(array('name' => 'api.scope'));
		Permission::create(array('name' => 'api.scope.create'));
		Permission::create(array('name' => 'api.scope.edit'));
		Permission::create(array('name' => 'api.scope.delete'));
	}
}