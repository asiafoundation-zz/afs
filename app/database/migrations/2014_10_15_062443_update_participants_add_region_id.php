<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateParticipantsAddRegionId extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
    Schema::table('participants', function(Blueprint $table)
    {
      $table->integer('region_id');
      $table->dropColumn('participant_id');
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
      $table->dropColumn('region_id');
      $table->integer('participant_id');
    });
	}

}
