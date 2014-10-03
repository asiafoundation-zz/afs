<?php
class GroupSeeder extends Seeder {

    public function run()
    {
        DB::table('groups')->truncate();

        /* Administrator */
        $permissions = Permission::all();
        $all_permission = array();

        foreach ($permissions as $permission) {
            $all_permission[$permission->name] = 1;
        }

        Sentry::getGroupProvider()->create(array(
            'name'        => 'Administrator',
            'permissions' => $all_permission
            ));

        
				/* Owner */
				$groups = "Owner";

				$permission["Owner"] = array(
					"dashboard"
					);

foreach ($permission["Owner"] as $perm) {
	$all_permission["Owner"][$perm] = 1;
}

Sentry::getGroupProvider()->create(array(
	"name"        => "Owner",
	"permissions" => $all_permission["Owner"]
	));


				/* Admin */
				$groups = "Admin";

				$permission["Admin"] = array(
					"dashboard"
					);

foreach ($permission["Admin"] as $perm) {
	$all_permission["Admin"][$perm] = 1;
}

Sentry::getGroupProvider()->create(array(
	"name"        => "Admin",
	"permissions" => $all_permission["Admin"]
	));



    }

}