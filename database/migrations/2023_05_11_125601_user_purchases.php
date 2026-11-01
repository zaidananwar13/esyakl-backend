<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UserPurchases extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_purchase', function (Blueprint $table) {
            $table->uuid('id_user_purchase')->primary();
            $table->integer('id_order')->nullable(); 
            $table->unsignedInteger('id_user');
            $table->unsignedInteger('id_class');
            $table->integer('id_promo')->nullable();
            $table->string('payment_method')->nullable();
            $table->double('value')->nullable();
            $table->integer('expired_at');
            $table->integer('created_at');
            $table->integer('updated_at');

            $table->foreign('id_user')->references('id_user')->on('user');
            $table->foreign('id_class')->references('id_class')->on('class');
            $table->foreign('id_promo')->references('id_promo')->on('promo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
