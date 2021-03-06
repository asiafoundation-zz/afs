<?php namespace App\Modules\Avelca_Module\Seeds;

use Seeder;
use Eloquent;

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();

		$this->call('App\Modules\Avelca_Module\Seeds\PermissionSeeder');
	}

}