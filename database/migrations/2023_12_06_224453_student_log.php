<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class StudentLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_log_activity', function (Blueprint $table) {
            $table->increments('id_student_log_activity');
            $table->unsignedInteger('id_student')->nullable();
            $table->unsignedInteger('last_class_access')->nullable();            
            $table->string('access_type')->nullable();            
            $table->string('information_detail')->nullable();            
            $table->integer('created_at');
            $table->integer('updated_at');

            $table->foreign('id_student')->references('id_user')->on('user');
            $table->foreign('last_class_access')->references('id_class')->on('class');
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
