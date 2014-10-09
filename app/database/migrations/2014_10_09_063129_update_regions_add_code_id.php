<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateRegionsAddCodeId extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('regions', function($table){
			DB::statement("ALTER TABLE regions CHANGE regionscol name varchar(255)");
		});
    Schema::table('regions', function(Blueprint $table)
    {
      $table->integer('code_id');
    });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
    Schema::table('regions', function(Blueprint $table)
    {
      $table->dropColumn('code_id');
    });
  }
}
