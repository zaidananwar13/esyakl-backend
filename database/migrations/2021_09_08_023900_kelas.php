<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Kelas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('class', function (Blueprint $table) {
            $table->increments('id_class');
            $table->unsignedInteger('id_class_level');
            $table->unsignedInteger('id_bahasa');
            $table->unsignedInteger('owner');
            $table->string('title');
            $table->string('thumb');
            $table->string('image');
            $table->text('desc');
            $table->string('type');
            $table->string('status');
            $table->unsignedInteger("created_at");
            $table->unsignedInteger("updated_at");
            
            $table->foreign('id_class_level')->references('id_class_level')->on('class_level');
            $table->foreign('id_bahasa')->references('id_bahasa')->on('bahasa');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP table class IF EXISTS");
    }
}
