<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQueueTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('delayed_jobs', function($table){
			$table->bigIncrements("id")->unsigned();
			$table->integer("survey_id");
			$table->string("type");
			$table->text("data");
			$table->text("information");
			$table->integer("queue");
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('delayed_jobs');
	}

}
