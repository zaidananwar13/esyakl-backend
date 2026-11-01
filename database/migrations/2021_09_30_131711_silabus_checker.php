<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SilabusChecker extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('silabus_checker', function (Blueprint $table) {
            $table->increments('id_silabus_checker');
            $table->unsignedInteger('id_user');
            $table->unsignedInteger('id_kategori_silabus');
            $table->unsignedInteger('id_sub_kategori_silabus');
            $table->unsignedInteger('id_bahasa');
            // $table->unsignedInteger("created_at");
            // $table->unsignedInteger("updated_at");
            $table->timestamps();
            
            $table->dropPrimary('id_silabus_checker');
            $table->unique('id_silabus_checker');
            $table->primary(['id_user', 'id_kategori_silabus', 'id_sub_kategori_silabus']);
            $table->foreign('id_user')->references('id_user')->on('user');
            $table->foreign('id_kategori_silabus')->references('id_kategori_silabus')->on('kategori_silabus');
            $table->foreign('id_sub_kategori_silabus')->references('id_sub_kategori_silabus')->on('sub_kategori_silabus');
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
