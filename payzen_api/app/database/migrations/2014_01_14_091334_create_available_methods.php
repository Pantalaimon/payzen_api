<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAvailableMethods extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('available_methods', function (Blueprint $table)
        {
            $table->unsignedInteger("charge_id");
            $table->string("method"); // 255 by default
            $table->timestamps(); // created_at & updated_at

            $table->foreign('charge_id', 'index_available_methods_on_charge_id')
                ->references('charge_id')
                ->on('charges');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('available_methods', function (Blueprint $table)
        {
            $table->dropIfExists();
        });
    }
}