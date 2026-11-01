<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Events extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event', function (Blueprint $table) {
            $table->increments('id_event');
            $table->unsignedInteger('hoster');
            $table->unsignedInteger('id_bahasa');
            $table->string('title');
            $table->string('image');
            $table->text('desc');
            $table->unsignedInteger('start');
            $table->unsignedInteger('end');
            $table->string('place');
            $table->string('location');
            $table->unsignedInteger('quota');
            $table->string('status')->default('Meet-up');
            $table->unsignedInteger("created_at");
            $table->unsignedInteger("updated_at");
            
            $table->foreign('hoster')->references('id_user')->on('user');
            $table->foreign('id_bahasa')->references('id_bahasa')->on('bahasa');
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
