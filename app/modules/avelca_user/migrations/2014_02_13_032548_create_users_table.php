<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('email');
			$table->string('password');
			$table->longText('permissions')->nullable();
			$table->tinyInteger('activated')->default(0);
			$table->dateTime('activated_at')->nullable();
			$table->dateTime('last_activity')->nullable();
			$table->dateTime('last_login')->nullable();
			$table->string('remember_token')->nullable();
			$table->dateTime('update_password_at')->nullable();
			$table->dateTime('submit_reset_password_at')->nullable();
			$table->string('activation_code')->nullable();
			$table->string('persist_code')->nullable();
			$table->string('reset_password_code')->nullable();
			$table->string('first_name');
			$table->string('last_name');
			$table->timestamps();
			$table->softDeletes();

			$table->unique('email');
			$table->index('activation_code');
			$table->index('reset_password_code');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('users');
	}

}
