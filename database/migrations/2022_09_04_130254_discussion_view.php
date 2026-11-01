<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DiscussionView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discussion_view', function (Blueprint $table) {
            $table->increments('id_discussion_view');
            $table->unsignedInteger('id_user');
            $table->unsignedInteger('id_discussion');
            $table->timestamps();

            $table->foreign('id_user')->references('id_user')->on('user');
            $table->foreign('id_discussion')->references('id_class_discussion')->on('class_discussion');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // DB::unprepared("DROP table discussion_view");
    }
}
