<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsedMethods extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('used_methods', function (Blueprint $table)
        {
            $table->unsignedInteger("charge_id");
            $table->string("method");
            $table->timestamps();

            $table->foreign("charge_id", "index_used_methods_on_charge_id")
                ->references("charge_id")
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
        Schema::table('used_methods', function (Blueprint $table)
        {
            //
        });
    }
}