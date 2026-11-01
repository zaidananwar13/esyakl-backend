<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class StudentQuizLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quiz_student_log', function (Blueprint $table) {
            $table->increments('id_quiz_student_log');
            $table->unsignedInteger('id_quiz_student')->nullable();
            $table->string('question')->nullable();            
            $table->text('answere')->nullable();            
            $table->integer('created_at');
            $table->integer('updated_at');

            $table->foreign('id_quiz_student')->references('id_quiz_student')->on('quiz_student');
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
