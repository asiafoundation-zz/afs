<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateQuestionersRemoveIsDefaultAndCategoryItemId extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
    Schema::table('questioners', function(Blueprint $table)
    {
      $table->dropColumn('is_default');
      $table->dropColumn('category_item_id');
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
      $table->integer('is_default');
      $table->integer('category_item_id');
    });
	}

}
