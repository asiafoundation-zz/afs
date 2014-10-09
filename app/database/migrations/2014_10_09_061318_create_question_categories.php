<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuestionCategories extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('question_categories', function($table)
		{
			$table->bigIncrements("id")->unsigned();
			$table->string("name");
			$table->integer("survey_id")->unsigned();
			$table->timestamps();
			$table->softDeletes();
		});
		Schema::table('questions', function(Blueprint $table)
    {
    	$table->dropColumn('survey_id');
      $table->integer('question_category_id');
    });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('question_categories');
		Schema::table('categories', function(Blueprint $table)
    {
      $table->dropColumn('code_id');
      $table->integer('survey_id');
    });
	}

}
