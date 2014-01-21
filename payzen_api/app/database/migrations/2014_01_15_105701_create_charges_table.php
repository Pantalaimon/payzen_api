<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
class CreateChargesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create( 'charges', function (Blueprint $table) {
			$table->increments( 'id' );
			$table->float( 'amount' );
			$table->string( 'currency', 3 );
			$table->string( 'shop_id', 8 );
			$table->string( 'shop_key', 16 );
			//TODO Charge::STATUS_XXX
			$table->enum( 'status', [
					'incomplete',
					'complete',
					'cancelled'
			] );
			$table->timestamps();
		} );
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::drop( 'charges' );
	}
}
