<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class BlogLike extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blog_like', function (Blueprint $table) {
            $table->increments("id_blog_like");
            $table->unsignedInteger("id_blog");
            $table->unsignedInteger("id_user");
            $table->unsignedInteger("created_at");
            $table->unsignedInteger("updated_at");
            
            $table->foreign('id_user')->references('id_user')->on('user');
            $table->foreign('id_blog')->references('id_blog')->on('blog');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
