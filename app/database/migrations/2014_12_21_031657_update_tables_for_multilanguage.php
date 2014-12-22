<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTablesForMultilanguage extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('cycles', function(Blueprint $table)
    {
      $table->integer('survey_id');
    });
    Schema::table('regions', function(Blueprint $table)
    {
      $table->integer('survey_id');
    });
    Schema::table('questions', function(Blueprint $table)
    {
      $table->integer('survey_id');
    });
    Schema::table('filter_participants', function(Blueprint $table)
    {
      $table->integer('survey_id');
    });
    Schema::table('question_participants', function(Blueprint $table)
    {
      $table->integer('survey_id');
    });
    Schema::table('surveys', function(Blueprint $table)
    {
      $table->string('url');
      $table->string('url_name');
    });
  }

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('cycles', function(Blueprint $table)
    {
      $table->dropColumn('survey_id');
    });
    Schema::table('regions', function(Blueprint $table)
    {
      $table->dropColumn('survey_id');
    });
    Schema::table('questions', function(Blueprint $table)
    {
      $table->dropColumn('survey_id');
    });
    Schema::table('filter_participants', function(Blueprint $table)
    {
      $table->dropColumn('survey_id');
    });
    Schema::table('question_participants', function(Blueprint $table)
    {
      $table->dropColumn('survey_id');
    });
    Schema::table('surveys', function(Blueprint $table)
    {
      $table->dropColumn('url');
      $table->dropColumn('url_name');
    });
	}

}
