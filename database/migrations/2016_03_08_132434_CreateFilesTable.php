<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            // individual file id
            $table->increments('id');
            $table->integer('song_id')->unsigned()->nullable();
            $table->foreign('song_id')->references('id')->on('songs');
            $table->integer('item_id')->unsigned()->nullable();
            $table->foreign('item_id')->references('id')->on('items');
            $table->integer('seq_no')->unsigned()->nullable();
            $table->string('token');
            $table->string('filename');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('files');
    }
}
