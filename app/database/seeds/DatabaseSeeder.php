<?php

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();

		$this->call('PermissionSeeder');
		
		$this->call("GroupSeeder");
		$this->call("UserSeeder");

		$this->call("AnswerSeeder");
		$this->call("CategoryItemSeeder");
		$this->call("CategorySeeder");
		$this->call("CodeSeeder");
		$this->call("ColorSeeder");
		$this->call("CycleSeeder");
		$this->call("MasterCodeSeeder");
		$this->call("QuestionCategorySeeder");
		$this->call("QuestionSeeder");
		$this->call("QuestionerSeeder");
		$this->call("RegionSeeder");
		$this->call("SurveySeeder");
		$this->call("ParticipantSeeder");
		$this->call("FilterParticipantSeeder");
		$this->call("QuestionParticipantSeeder");
	}

}