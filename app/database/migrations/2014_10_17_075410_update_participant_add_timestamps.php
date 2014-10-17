<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateParticipantAddTimestamps extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
    Schema::table('participants', function(Blueprint $table)
    {
			$table->timestamps();
			$table->softDeletes();
    });
    Schema::table('filter_participants', function(Blueprint $table)
    {
      $table->integer('category_item_id');
      $table->string('participant_id');
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
      $table->dropColumn('category_item_id');
      $table->dropColumn('participant_id');
    });
	}

}
