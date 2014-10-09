<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCyclesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cycles', function($table)
		{
			$table->bigIncrements("id")->unsigned();
			$table->string("name");
			$table->timestamps();
			$table->softDeletes();
		});
		Schema::table('questions', function(Blueprint $table)
    {
    	$table->integer('cycle_id');
    });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('cycles');
		Schema::table('questions', function(Blueprint $table)
    {
      $table->dropColumn('cycle_id');
    });
	}

}
