<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateQuestionChangeCodeToCodeId extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('questions', function($table){
			DB::statement("ALTER TABLE questions CHANGE code code_id INT");
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('questions', function($table){
			DB::statement("ALTER TABLE questions CHANGE code_id code INT");
		});
	}

}
