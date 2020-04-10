<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePasswordTables extends Migration
{
    /**
     * {@inheritdoc}
     */
    /*public function getConnection()
    {
        return config('database.default');
    }*/

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('laravel-security.database.password_history_table'), function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('user_id');
            $table->string('password', 60);
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('laravel-security.database.password_history_table'));
    }
}
