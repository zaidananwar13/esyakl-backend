<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ClassReview extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('class_review', function (Blueprint $table) {
            $table->increments('id_class_review');
            $table->unsignedInteger('id_user');
            $table->unsignedInteger('id_kelas');
            $table->integer("star");
            $table->text("content");
            $table->timestamps();

            $table->foreign('id_kelas')->references('id_kelas')->on('kelas');
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
        DB::unprepared("DROP table class_review");
    }
}
