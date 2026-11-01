<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ClassLecture extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('class_lecture', function (Blueprint $table) {
            $table->increments('id_class_lecture');
            $table->unsignedInteger('id_class');
            $table->unsignedInteger('id_user');
            $table->string('type');
            $table->string('status');
            $table->timestamps();

            $table->foreign('id_user')->references('id_user')->on('user');
            $table->foreign('id_class')->references('id_kelas')->on('kelas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP table class_lecture");
    }
}
