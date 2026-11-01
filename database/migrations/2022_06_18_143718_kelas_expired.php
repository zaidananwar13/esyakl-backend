<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class KelasExpired extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kelas_expired', function (Blueprint $table) {
            $table->increments('id_kelas_expired');
            $table->unsignedInteger("id_user");
            $table->unsignedInteger("id_kelas");
            $table->unsignedInteger("expired_date");
            $table->unsignedInteger("created_at");
            $table->unsignedInteger("updated_at");
            
            $table->foreign('id_user')->references('id_user')->on('user');
            $table->foreign('id_kelas_expired')->references('id_kelas_expired')->on('kelas_expired');
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
