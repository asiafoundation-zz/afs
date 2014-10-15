<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateFilterFilterParticipantsRemoveCategoryIdAndName extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
    Schema::table('filter_participants', function(Blueprint $table)
    {
      $table->dropColumn('category_id');
      $table->dropColumn('name');
    });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
    Schema::table('filter_participants', function(Blueprint $table)
    {
      $table->integer('category_id');
      $table->string('name');
    });
	}

}
