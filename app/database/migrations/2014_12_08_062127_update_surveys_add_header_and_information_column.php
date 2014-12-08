<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateSurveysAddHeaderAndInformationColumn extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('surveys', function($table)
		{
			$table->DropColumn('endline_file');
			$table->string("header_file");
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('surveys', function($table)
		{
			$table->DropColumn('header_file');
			$table->string("endline_file");
		});
	}

}
