<?php

class UserSeeder extends Seeder
{

    public function run()
    {
        /* Clean */
        DB::table('users')->truncate();
        DB::table('throttle')->truncate();
        DB::table('users_groups')->truncate();

        
	/* Owner */
	$group_name = "Owner";
	$users = array(
			array(
				"email" => "owner@demo.com",
				"password" => "demo123",
				"first_name"  => "Owner",
				"last_name"   => "User",
				"created_at" => date("Y-m-d H:i:s"),
				"activated_at" => date("Y-m-d H:i:s"),
				"activated"   => 1
				)
);

foreach ($users as $user => $attributes) {
	Sentry::getUserProvider()->create($attributes);
	$user  = Sentry::getUserProvider()->findByLogin($attributes["email"]);
	$group = Sentry::getGroupProvider()->findByName($group_name);
	$user->addGroup($group);
}


	/* Admin */
	$group_name = "Admin";
	$users = array(
			array(
				"email" => "admin@demo.com",
				"password" => "demo321",
				"first_name"  => "Admin",
				"last_name"   => "User",
				"created_at" => date("Y-m-d H:i:s"),
				"activated_at" => date("Y-m-d H:i:s"),
				"activated"   => 1
				)
);

foreach ($users as $user => $attributes) {
	Sentry::getUserProvider()->create($attributes);
	$user  = Sentry::getUserProvider()->findByLogin($attributes["email"]);
	$group = Sentry::getGroupProvider()->findByName($group_name);
	$user->addGroup($group);
}



    }

}