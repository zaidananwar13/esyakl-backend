<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FEAuthorizer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('feauthorizer', function (Blueprint $table) {
            $table->increments("id_authorizer");
            $table->unsignedInteger("id_user");
            $table->unsignedInteger("id_kelas");
            $table->integer("unlocked");
            $table->integer("status_class")->default(1);
            // $table->unsignedInteger("created_at");
            // $table->unsignedInteger("updated_at");
            $table->timestamps();
            
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
        DB::unprepared("DROP table feauthorizer");
    }
}
