<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('name', 'username')->unique();
            $table->enum('type', ['Customer','Merchant']);
            $table->float('wallet',8,2)->default(0);
            $table->float('total_income',8,2)->default(0);
            $table->string('image')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->dropColumn('wallet');
            $table->dropColumn('total_income');
            $table->dropColumn('image');
            $table->dropColumn('username');
            $table->string('name');
            //Ununique $table->string('username')->unique();
        });
    }
}
