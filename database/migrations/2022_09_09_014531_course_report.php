<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CourseReport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_report', function (Blueprint $table) {
            $table->increments('id_course_report');
            $table->unsignedInteger('id_class_student');
            $table->string('status');
            $table->text('description');
            $table->integer('created_at');
            $table->integer('updated_at');

            $table->foreign('id_class_student')->references('id_class_student')->on('class_student');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP table course_report");
    }
}
