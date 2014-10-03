<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateThrottleTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('throttle', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id')->unsigned();
			$table->string('ip_address')->nullable();
			$table->integer('attempts')->default(0);
			$table->tinyInteger('suspended')->default(0);
			$table->tinyInteger('banned')->default(0);
			$table->dateTime('last_attempt_at')->nullable();
			$table->dateTime('suspended_at')->nullable();
			$table->dateTime('banned_at')->nullable();
			$table->dateTime('created_at')->nullable();
			$table->dateTime('updated_at')->nullable();

			$table->index('user_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('throttle');
	}

}
