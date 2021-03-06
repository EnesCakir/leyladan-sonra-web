<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('chats', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('volunteer_id')->unsigned();
            $table->bigInteger('faculty_id')->unsigned()->nullable();
            $table->bigInteger('child_id')->unsigned()->nullable();
            $table->string('via');
            $table->string('status');

            $table->baseActions();

            $table->foreign('volunteer_id')->references('id')->on('volunteers')->onDelete('cascade');
            $table->foreign('child_id')->references('id')->on('children')->onDelete('cascade');
            $table->foreign('faculty_id')->references('id')->on('faculties')->onDelete('cascade');

        });

        Schema::create('messages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('chat_id')->unsigned();
            $table->longtext('text');
            $table->bigInteger('answered_by')->unsigned()->nullable();
            $table->datetime('answered_at')->nullable();
            $table->bigInteger('sent_by')->unsigned()->nullable();
            $table->datetime('sent_at')->nullable();
            $table->timestamps();
            $table->foreign('chat_id')->references('id')->on('chats')->onDelete('cascade');
            $table->foreign('answered_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('sent_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('messages');
        Schema::dropIfExists('chats');
    }
}
