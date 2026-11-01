<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LikeDislike extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('like_dislike', function (Blueprint $table) {
            $table->increments('id_like_dislike');
            $table->string('likeable');
            $table->unsignedInteger('id_likeable');
            $table->unsignedInteger('owner');
            $table->string('type')->nullable();            
            $table->integer('created_at');
            $table->integer('updated_at');

            $table->foreign('owner')->references('id_user')->on('user');
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
