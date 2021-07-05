<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDevAppTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dev_app', function (Blueprint $table) {
            $table->increments('app_id');
            $table->string('title', 250)->nullable();
            $table->char('type', 10)->nullable();
            $table->json('data')->nullable();
            $table->integer('create_time')->nullable();
            $table->integer('update_time')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dev_app');
    }
}
