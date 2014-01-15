<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
class CreateContextsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create( 'contexts', function (Blueprint $table) {
			$table->increments( 'id' );
			$table->unsignedInteger( 'charge_id' );
			$table->enum( 'status', [
					Context::STATUS_CREATED,
					Context::STATUS_SUCCESS,
					Context::STATUS_FAILURE,
					Context::STATUS_LOCKED,
					Context::STATUS_CANCELLED
			] );
			$table->string( 'trans_date', 14 );
			$table->string( 'trans_id', 6 );
			$table->timestamp( 'trans_time' );
			$table->string( 'cache_id' );
			$table->string( 'locale', 5 );
			$table->timestamps();

			$table->foreign( 'charge_id', 'index_contexts_on_charge_id' )->references( 'id' )->on( 'charges' );
		} );
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::drop( 'contexts' );
	}
}
