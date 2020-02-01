<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class   CreateVouchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->bigIncrements('id');
//            $table->primary('recharge_id');
            $table->float('amount',8,2)->default(0);
            $table->string('voucher_code');
            $table->boolean('valid')->default(1);
            $table->timestamps();

//            $table->foreign('recharge_id')->references('transaction_id')->on('recharges');





        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vouchers');
    }
}
