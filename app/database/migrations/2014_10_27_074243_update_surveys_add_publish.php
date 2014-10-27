<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateSurveysAddPublish extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
    Schema::table('surveys', function(Blueprint $table)
    {
      $table->integer('publish');
    });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
    Schema::table('surveys', function(Blueprint $table)
    {
      $table->dropColumn('publish');
    });
	}

}
