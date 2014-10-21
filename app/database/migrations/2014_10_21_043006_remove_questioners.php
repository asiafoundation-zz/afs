<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveQuestioners extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::drop('questioners');
    Schema::table('questions', function($table)
    {
      $table->dropColumn('cycle_id');
    });
		Schema::table('question_participants', function($table){
			$table->Integer('region_id');
		});
		Schema::table('answers', function($table){
			$table->Integer('cycle_id');
		});
    Schema::table('participants', function($table)
    {
      $table->dropColumn('region_id');
      $table->dropColumn('cycle_id');
      $table->Integer('sample_type');
    });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::Create('questioners', function($table)
		{
			$table->Integer('region_id');
			$table->Integer('cycle_id');
		});
    Schema::table('questions', function($table)
    {
      $table->integer('cycle_id');
    });
		Schema::table('question_participants', function($table){
			$table->dropColumn('region_id');
		});
		Schema::table('answers', function($table){
			$table->dropColumn('cycle_id');
		});
    Schema::table('participants', function($table)
    {
      $table->Integer('region_id');
      $table->Integer('cycle_id');
      $table->dropColumn('sample_type');
    });
	}
}
