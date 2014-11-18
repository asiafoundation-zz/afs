<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableAmounts extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('amounts', function($table){
			$table->bigIncrements("id")->unsigned();
			$table->integer("answer_id");
			$table->integer("region_id");
			$table->integer("sample_type");
			$table->integer("amount");
			$table->timestamps();
			$table->softDeletes();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('amounts');
	}

}
