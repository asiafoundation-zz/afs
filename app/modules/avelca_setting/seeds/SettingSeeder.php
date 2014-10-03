<?php namespace App\Modules\Avelca_Setting\Seeds;

use DB;
use Seeder;
use App\Modules\Avelca_Setting\Models\Setting;

class SettingSeeder extends Seeder {

	public function run()
	{
		DB::table('settings')->truncate();

		/* General */
		Setting::create(array('category' => 'general', 'meta_data' => '{
			"name": {
				"type": "text",
				"value": "Application Name"
			},
			"tag_line": {
				"type": "text",
				"value": "Sample Tag Line"
			},
			"organization": {
				"type": "text",
				"value": "New Organization"
			},
			"administrator_email": {
				"type": "text",
				"value": "your@email.com"
			},
			"theme_color": {
				"type": "color",
				"value": "#553982"
			}
		}'));
		
		/* User Management */
		Setting::create(array('category' => 'user_management', 'meta_data' => '{
			"suspend_time": {
				"type": "number",
				"value": "15",
				"unit" : "minutes"
			},
			"max_attempt": {
				"type": "number",
				"value": "3",
				"unit" : "times"
			},
			"allow_sign_up": {
				"type": "radio",
				"options": {
					"Yes": "1",
					"No": "0"
				},
				"selected": "1"
			},
			"auto_activation": {
				"type": "radio",
				"options": {
					"Yes": "1",
					"No": "0"
				},
				"selected": "0"
			},
			"default_group": {
				"type": "text",
				"value": "Administrator"
			},
			"auto_sign_out": {
				"type": "number",
				"value": "10",
				"unit" : "minutes"
			},
			"minimum_auto_tracked": {
				"type": "number",
				"value": "5",
				"unit" : "minutes"
			},
			"password_expiry_duration": {
				"type": "number",
				"value": "60",
				"unit" : "days"
			},
			"password_expiry_reminder": {
				"type": "number",
				"value": "14",
				"unit" : "days"
			}


		}'));
	}
}