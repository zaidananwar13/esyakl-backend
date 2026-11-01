<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SubKategoriSilabus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('sub_kategori_silabus', function (Blueprint $table) {
            $table->increments('id_sub_kategori_silabus');
            $table->unsignedInteger('id_kategori_silabus');
            $table->unsignedInteger('id_bahasa');
            $table->string('judul');
            $table->string('deskripsi');
            $table->text('konten');
            $table->unsignedInteger("created_at");
            $table->unsignedInteger("updated_at");
            
            $table->foreign('id_kategori_silabus')->references('id_kategori_silabus')->on('kategori_silabus');
            // $table->foreign('id_bahasa')->references('id_bahasa')->on('bahasa');
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
