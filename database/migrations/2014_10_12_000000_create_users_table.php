<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
            
            // Columns to be altered:

            // $table->bigIncrements('id');
            // $table->string('username')->unique();
            // $table->string('email')->unique();
            // $table->timestamp('email_verified_at')->nullable();
            // $table->string('password');

            // $table->enum('type', ['Customer','Merchant']);

            // $table->float('wallet',8,2)->default(0);
            // $table->float('total_income',8,2)->default(0);
            // $table->string('image')->nullable();
            // $table->rememberToken();
            // $table->timestamps();
//            $table->morphs('userable');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
