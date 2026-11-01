<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Comment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comment', function (Blueprint $table) {
            $table->increments('id_comment');
            $table->string('commented')->nullable();
            $table->unsignedInteger('id_commented')->nullable();
            $table->unsignedInteger('owner');
            $table->unsignedInteger('id_parent')->nullable();
            $table->text("content");  
            $table->integer('like')->default(0);
            $table->integer('dislike')->default(0);
            $table->timestamp("time");

            $table->foreign('owner')->references('id_user')->on('user');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP table comment");
    }
}
