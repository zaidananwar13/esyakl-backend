<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Discussion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('class_discussion', function (Blueprint $table) {
            $table->increments('id_class_discussion');
            $table->unsignedInteger("id_class");
            $table->unsignedInteger("owner")->nullable();
            $table->string("title");
            $table->text("content");
            $table->unsignedInteger("created_at");
            $table->unsignedInteger("updated_at");
            
            $table->foreign('owner')->references('id_user')->on('user');
            $table->foreign('id_class')->references('id_class')->on('class');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP table class_discussion");
    }
}
