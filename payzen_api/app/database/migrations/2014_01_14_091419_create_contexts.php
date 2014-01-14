<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContexts extends Migration
{

    /*
     * create_table "contexts", force: true do |t| t.integer "charge_id" t.string "status" t.string "trans_date" t.string "trans_id" t.datetime "created_at" t.datetime "updated_at" t.string "trans_time" t.string "cache_id" t.string "locale" end add_index "contexts", ["charge_id"], name: "index_contexts_on_charge_id"
     */

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contexts', function (Blueprint $table)
        {
            $table->unsignedInteger("charge_id");
            $table->enum("status", [
                "incomplete",
                "complete",
                "cancelled"
            ]);
            $table->string("trans_date", 14);
            $table->string("trans_id", 6);
            $table->timestamps(); // created_at & updated_at
            $table->timestamp("trans_time");
            $table->string("cache_id");
            $table->string("locale", 5);

            $table->foreign('charge_id', 'index_contexts_on_charge_id')
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
        Schema::table('contexts', function (Blueprint $table)
        {
            $table->dropIfExists();
        });
    }
}