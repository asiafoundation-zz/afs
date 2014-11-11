<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeQuestionToText extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('questions', function(Blueprint $table)
    {
    	DB::statement("ALTER TABLE questions CHANGE question question TEXT");
    });
    Schema::table('question_categories', function(Blueprint $table)
    {
      DB::statement("ALTER TABLE question_categories CHANGE name name TEXT");
    });
    Schema::table('answers', function(Blueprint $table)
    {
     DB::statement("ALTER TABLE answers CHANGE answer answer TEXT");
    });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('questions', function(Blueprint $table)
    {
      DB::statement("ALTER TABLE questions CHANGE question question varchar(255)");
    });
    Schema::table('question_categories', function(Blueprint $table)
    {
      DB::statement("ALTER TABLE question_categories CHANGE name name varchar(255)");
    });
    Schema::table('answers', function(Blueprint $table)
    {
      DB::statement("ALTER TABLE answers CHANGE answer answer varchar(255)");
    });
	}

}
