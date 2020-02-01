<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRechargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recharges', function (Blueprint $table) {
            $table->bigIncrements('id');
//            $table->primary('transaction_id');
//            $table->enum('method', ['voucher','creditCard']);
            $table->timestamps();

            $table->morphs('rechargeable');


//            $table->foreign('transaction_id')->references('id')->on('transactions');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('recharges');
    }
}
