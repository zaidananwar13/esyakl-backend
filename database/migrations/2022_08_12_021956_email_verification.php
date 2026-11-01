<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

class EmailVerification extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_verification', function (Blueprint $table) {
            $table->increments("id_email_verification");
            $table->unsignedInteger("id_user");
            $table->string('email');
            $table->string('token')->unique();
            $table->integer('expired_at');
            $table->integer('created_at');
            $table->integer('updated_at');
            
            $table->foreign('id_user')->references('id_user')->on('user');
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
