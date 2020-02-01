<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscriptions', function (Blueprint $table) {
         
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('service_id');   
            $table->enum('status', ['active','expired']);
            $table->timestamps();

            $table->primary(['customer_id','service_id']);
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
        });


    }
    public function primary($columns, $name = null)
    {
        return $this->indexCommand('primary', $columns, $name);
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subscriptions');
    }
}
