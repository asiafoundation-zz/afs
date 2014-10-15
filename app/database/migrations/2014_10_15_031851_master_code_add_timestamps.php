<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MasterCodeAddTimestamps extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('master_codes', function($table){
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
		Schema::table('master_codes', function($table){
			$table->dropColumn('created_at');
			$table->dropColumn('updated_at');
			$table->dropColumn('deleted_at');
		});
	}

}
