<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ClassLevel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('class_level', function (Blueprint $table) {
            $table->increments('id_class_level');
            $table->string('level');
            $table->unsignedInteger("created_at");
            $table->unsignedInteger("updated_at");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Illuminate\Support\Facades\DB::unprepared("DROP table class_level IF EXISTS");
    }
}
