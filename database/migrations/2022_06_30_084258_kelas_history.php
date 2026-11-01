<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class KelasHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kelas_history', function (Blueprint $table) {
            $table->increments('id_history');
            $table->unsignedInteger("id_user");
            $table->unsignedInteger("id_kelas");
            $table->unsignedInteger("id_kategori_silabus");
            $table->unsignedInteger("id_sub_kategori_silabus");
            $table->unsignedInteger("created_at");
            $table->unsignedInteger("updated_at");
            
            $table->foreign('id_user')->references('id_user')->on('user');
            $table->foreign('id_kelas')->references('id_kelas')->on('kelas');
            $table->foreign('id_kategori_silabus')->references('id_kategori_silabus')->on('kategori_silabus');
            $table->foreign('id_sub_kategori_silabus')->references('id_sub_kategori_silabus')->on('sub_kategori_silabus');
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
