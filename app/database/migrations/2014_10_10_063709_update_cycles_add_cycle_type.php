<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCyclesAddCycleType extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
    Schema::table('cycles', function(Blueprint $table)
    {
      $table->integer('cycle_type');
    });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
    Schema::table('cycles', function(Blueprint $table)
    {
      $table->dropColumn('cycle_type');
    });
	}

}
