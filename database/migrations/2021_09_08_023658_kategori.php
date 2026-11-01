<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Kategori extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('kategori', function (Blueprint $table) {
            $table->increments('id_kategori');
            $table->string('judul');
            $table->string('gambar');
            $table->string('deskripsi');
            $table->unsignedInteger("created_at")->nullable();
            $table->unsignedInteger("updated_at")->nullable();
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
