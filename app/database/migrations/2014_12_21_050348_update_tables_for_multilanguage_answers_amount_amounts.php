<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTablesForMultilanguageAnswersAmountAmounts extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('answers', function(Blueprint $table)
    {
      $table->integer('survey_id');
    });
    Schema::table('amounts', function(Blueprint $table)
    {
      $table->integer('survey_id');
    });
    Schema::table('amount_filters', function(Blueprint $table)
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
		Schema::table('answers', function(Blueprint $table)
    {
      $table->dropColumn('survey_id');
    });
    Schema::table('amounts', function(Blueprint $table)
    {
      $table->dropColumn('survey_id');
    });
    Schema::table('amount_filters', function(Blueprint $table)
    {
      $table->dropColumn('survey_id');
    });
	}

}
