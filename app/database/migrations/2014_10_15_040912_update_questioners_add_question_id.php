<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateQuestionersAddQuestionId extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
    Schema::table('questioners', function(Blueprint $table)
    {
      $table->integer('question_id');
    });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
    Schema::table('questioners', function(Blueprint $table)
    {
      $table->dropColumn('question_id');
    });
	}

}
