<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class User extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('user', function (Blueprint $table) {
            $table->increments('id_user');
            $table->string('type')->default('student');
            $table->string('sub_type')->nullable();
            $table->string('name');
            $table->string('google_id')->nullable();
            $table->string('email')->unique();
            $table->integer('email_verified_at')->nullable();
            $table->string('social_media')->nullable();
            $table->string('password')->nullable();
            $table->string('avatar')->nullable();
            $table->string('avatar_original')->nullable();
            $table->rememberToken();
            $table->string('api_token')
                ->unique();
            $table->integer("created_at");
            $table->integer("updated_at");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Illuminate\Support\Facades\DB::unprepared("DROP table user IF EXISTS");
    }
}
