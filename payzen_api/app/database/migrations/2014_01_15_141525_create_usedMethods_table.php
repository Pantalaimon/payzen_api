<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
class CreateUsedMethodsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create( 'usedMethods', function (Blueprint $table) {
			$table->increments( 'id' );
			$table->unsignedInteger( 'charge_id' );
			$table->string( 'method' );
			$table->timestamps();

			$table->foreign( "charge_id", "index_used_methods_on_charge_id" )->references( "id" )->on( "charges" );
		} );
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::drop( 'usedMethods' );
	}
}
