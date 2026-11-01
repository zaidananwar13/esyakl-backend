<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class KelasUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('kelas_user', function (Blueprint $table) {
            $table->increments('id_kelas_user');
            $table->unsignedInteger('id_user');
            $table->unsignedInteger('id_kelas');
            $table->string('point_review');
            $table->string('komentar_review');
            $table->timestamps();
            // $table->unsignedInteger("created_at");
            // $table->unsignedInteger("updated_at");
            
            $table->foreign('id_user')->references('id_user')->on('user');
            $table->foreign('id_kelas')->references('id_kelas')->on('kelas');
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
