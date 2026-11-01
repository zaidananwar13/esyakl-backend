<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LectureRating extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lecture_rating', function (Blueprint $table) {
            $table->increments('id_lecture_rating');
            $table->unsignedInteger('id_lecture');
            $table->unsignedInteger('id_user');
            $table->integer('rating');
            $table->timestamps();

            $table->foreign('id_user')->references('id_user')->on('user');
            $table->foreign('id_lecture')->references('id_class_lecture')->on('class_lecture');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP table lecture_rating");
    }
}
