<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ProjectUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_student', function (Blueprint $table) {
            $table->increments('id_project_student');
            $table->unsignedInteger("id_project");
            $table->unsignedInteger("id_class_student");
            $table->unsignedInteger("id_user");
            $table->unsignedInteger('expired');
            $table->text('note');
            $table->integer('point')->nullable();
            $table->string('file');
            $table->integer('status');
            $table->unsignedInteger("created_at");
            $table->unsignedInteger("updated_at");
            
            $table->foreign('id_class_student')->references('id_kelas')->on('kelas');
            $table->foreign('id_project')->references('id_project')->on('project');
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
