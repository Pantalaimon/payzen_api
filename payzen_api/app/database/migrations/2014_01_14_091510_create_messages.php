<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessages extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table)
        {
            $table->unsignedInteger("charge_id");
            $table->string("title");
            $table->string("description");
            $table->timestamps(); // created_at & updated_at

            $table->foreign("charge_id", "index_messages_on_charge_id")
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
        Schema::table('messages', function (Blueprint $table)
        {
            $table->dropIfExists();
        });
    }
}