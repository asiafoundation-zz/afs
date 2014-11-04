<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSurveyIdInMasterCodeCategory extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('master_codes', function(Blueprint $table)
    {
      $table->integer('survey_id');
    });
    Schema::table('categories', function(Blueprint $table)
    {
      $table->integer('survey_id');
    });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('master_codes', function(Blueprint $table)
    {
      $table->dropColumn('survey_id');
    });
    Schema::table('categories', function(Blueprint $table)
    {
      $table->dropColumn('survey_id');
    });
	}

}
