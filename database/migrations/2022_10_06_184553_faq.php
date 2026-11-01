<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Faq extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('faq', function (Blueprint $table) {
            $table->increments('id_faq');
            $table->unsignedInteger('id_bahasa')->nullable();
            $table->string('question')->nullable();            
            $table->text('answere')->nullable();            
            $table->integer('created_at');
            $table->integer('updated_at');

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
        // DB::unprepared("DROP table like_dislike");
    }
}
