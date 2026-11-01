<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class QuizContainer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quiz_container', function (Blueprint $table) {
            $table->increments('id_quiz_container');
            $table->unsignedInteger('id_kategori_silabus');
            $table->string('question');
            $table->text('desc');
            $table->unsignedInteger("created_at");
            $table->unsignedInteger("updated_at");
            
            $table->foreign('id_kategori_silabus')->references('id_kategori_silabus')->on('kategori_silabus');
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
