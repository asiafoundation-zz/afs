<?php

class PermissionSeeder extends Seeder {

	public function run()
	{
		Eloquent::unguard();
		/* Surveys */
		$permissions = array(
			"survey",
			"survey.index",
			"survey.create",
			"survey.view",
			"survey.edit",
			"survey.delete",
			"survey.print-pdf",
			"survey.export",
			"survey.cycle",
			"survey.category",
			"survey.region",
			"survey.oversample",
			"survey.import",
			"survey.upload"
			);

Permission::whereIn("name", $permissions)->delete();

foreach ($permissions as $permission)
{
	Permission::create(array("name" => $permission));
}


		/* Questions */
		$permissions = array(
			"question",
			"question.index",
			"question.create",
			"question.view",
			"question.edit",
			"question.delete",
			"question.print-pdf",
			"question.export"
			);

Permission::whereIn("name", $permissions)->delete();

foreach ($permissions as $permission)
{
	Permission::create(array("name" => $permission));
}


		/* Answers */
		$permissions = array(
			"answer",
			"answer.index",
			"answer.create",
			"answer.view",
			"answer.edit",
			"answer.delete",
			"answer.print-pdf",
			"answer.export"
			);

Permission::whereIn("name", $permissions)->delete();

foreach ($permissions as $permission)
{
	Permission::create(array("name" => $permission));
}


		/* Colors */
		$permissions = array(
			"color",
			"color.index",
			"color.create",
			"color.view",
			"color.edit",
			"color.delete",
			"color.print-pdf",
			"color.export"
			);

Permission::whereIn("name", $permissions)->delete();

foreach ($permissions as $permission)
{
	Permission::create(array("name" => $permission));
}


		/* Regions */
		$permissions = array(
			"region",
			"region.index",
			"region.create",
			"region.view",
			"region.edit",
			"region.delete",
			"region.print-pdf",
			"region.export"
			);

Permission::whereIn("name", $permissions)->delete();

foreach ($permissions as $permission)
{
	Permission::create(array("name" => $permission));
}


		/* Categories */
		$permissions = array(
			"category",
			"category.index",
			"category.create",
			"category.view",
			"category.edit",
			"category.delete",
			"category.print-pdf",
			"category.export"
			);

Permission::whereIn("name", $permissions)->delete();

foreach ($permissions as $permission)
{
	Permission::create(array("name" => $permission));
}


		/* Questioners */
		$permissions = array(
			"questioner",
			"questioner.index",
			"questioner.create",
			"questioner.view",
			"questioner.edit",
			"questioner.delete",
			"questioner.print-pdf",
			"questioner.export"
			);

Permission::whereIn("name", $permissions)->delete();

foreach ($permissions as $permission)
{
	Permission::create(array("name" => $permission));
}


		
	/* Reports */
	$permissions = array(

		"report",
"report.report-a",
"report.report-b",
"report.report-c",
"report.report-d"
);

Permission::whereIn("name", $permissions)->delete();

foreach ($permissions as $permission)
{
	Permission::create(array("name" => $permission));
}


		
	}
}