<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
class CreateCharges extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create ( 'charges', function (Blueprint $table) {
			$table->increments ( "charge_id" ); // int + pk + auto-increment
			$table->float ( "amount" );
			$table->string ( "currency", 3 );
			$table->timestamps (); // created_at & updated_at
			$table->string ( "shop_id", 8 );
			$table->string ( "shop_key", 16 );
			$table->enum ( "status", [
					"incomplete",
					"complete",
					"cancelled"
			] );
		} );
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table ( 'charges', function (Blueprint $table) {
			$table->dropIfExists ();
		} );
	}
}