<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMessagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('messages', function(Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('charge_id');
			$table->string('title');
			$table->string('description');
			$table->timestamps();

			$table->foreign("charge_id", "index_messages_on_charge_id")
			->references("id")
			->on("charges");
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('messages');
	}

}
