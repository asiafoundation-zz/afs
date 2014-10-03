<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuestionersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('questioners', function($table)
		{
			$table->bigIncrements("id")->unsigned();
			$table->integer("amount")->unsigned();
$table->string("is_default");
$table->integer("answer_id")->unsigned();
$table->integer("category_item_id")->unsigned();
$table->integer("region_id")->unsigned();
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
		Schema::drop('questioners');
	}

}
