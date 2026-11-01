<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Reviewer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('reviewer', function (Blueprint $table) {
            $table->increments('id_reviewer');
            $table->string('nama');
            $table->string('foto');
            $table->string('jabatan');
            $table->string('portofolio');
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
        //
    }
}
