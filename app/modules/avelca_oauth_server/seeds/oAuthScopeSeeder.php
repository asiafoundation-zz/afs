<?php namespace App\Modules\Avelca_Oauth_Server\Seeds;

use DB;
use Seeder;
use App\Modules\Avelca_Oauth_Server\Models\OauthScope;

class oAuthScopeSeeder extends Seeder {

	public function run()
	{
		$basic = OauthScope::where('scope','=','basic')->get();
		
		if($basic->count() == 0)
		{
			OauthScope::create(array('scope' => 'basic', 'name' => 'Basic', 'description' => 'Basic Scope'));
		}
	}
}