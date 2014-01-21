<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTransactionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('transactions', function(Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('charge_id');
			$table->string('trans_date', 14);
			$table->string('trans_id', 6);
			$table->timestamps();

			// All extra information is always queried from payzen WS

			$table->foreign( 'charge_id', 'index_transactions_on_charge_id' )->references( 'id' )->on( 'charges' );
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('transactions');
	}

}
