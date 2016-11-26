<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user1_id')->unsigned();
            $table->integer('user2_id')->unsigned()->nullable();
            $table->integer('turn')->default(1);
            $table->text('table_data');
            $table->timestamps();

            $table->foreign('user1_id')->references('id')->on('users');
            $table->foreign('user2_id')->references('id')->on('users');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rooms');
    }
}
