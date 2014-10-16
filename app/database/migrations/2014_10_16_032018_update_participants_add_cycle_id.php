<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateParticipantsAddCycleId extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
    Schema::table('participants', function(Blueprint $table)
    {
      $table->integer('cycle_id');
      $table->dropColumn('cycle');
    });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
    Schema::table('participants', function(Blueprint $table)
    {
      $table->dropColumn('cycle_id');
      $table->string('cycle');
    });
	}
}
