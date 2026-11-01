<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ClassStudent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('class_student', function (Blueprint $table) {
            $table->increments('id_class_student');
            $table->unsignedInteger("id_user");
            $table->unsignedInteger("id_class");
            $table->unsignedInteger("completed_at");
            $table->unsignedInteger("expired_at");
            $table->unsignedInteger("created_at");
            $table->unsignedInteger("updated_at");
            
            $table->foreign('id_class')->references('id_kelas')->on('kelas');
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
