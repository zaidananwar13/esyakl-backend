<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Blog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blog', function (Blueprint $table) {
            $table->increments('id_blog');
            $table->unsignedInteger("id_user");
            $table->unsignedInteger("id_bahasa");
            $table->string('image');
            $table->string('title');
            $table->text('content');
            $table->integer('claps');
            $table->string('category');
            $table->unsignedInteger("created_at");
            $table->unsignedInteger("updated_at");
            
            $table->foreign('id_user')->references('id_user')->on('user');
            $table->foreign('id_bahasa')->references('id_bahasa')->on('bahasa');
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
