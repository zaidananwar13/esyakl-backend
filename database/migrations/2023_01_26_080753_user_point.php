<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UserPoint extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('point_student', function (Blueprint $table) {
            $table->increments('id_point_student');
            $table->unsignedInteger('id_user');
            $table->unsignedInteger('id_class_student');
            $table->integer('points')->nullable();
            $table->integer('status')->default(0)->nullable();
            $table->integer('created_at');
            $table->integer('updated_at');

            $table->foreign('id_user')->references('id_user')->on('user');
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
        //
    }
}
