<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class KelasChecker extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kelas_checker', function (Blueprint $table) {
            $table->increments('id_kelas_checker');
            $table->unsignedInteger('id_user');
            $table->unsignedInteger('id_kelas');
            // $table->unsignedInteger("created_at");
            // $table->unsignedInteger("updated_at");
            $table->timestamps();
            
            $table->dropPrimary('id_kelas_checker');
            $table->unique('id_kelas_checker');
            $table->primary(['id_user', 'id_kelas']);
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
