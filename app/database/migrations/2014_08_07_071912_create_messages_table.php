<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
    public function up()
    {
        Schema::create('messages', function(Blueprint $table) {
            $table->increments('id');
            $table->string('from_id', 255)->nullable();
            $table->string('from_name', 255)->nullable();
            $table->string('thread_id', 255)->nullable();
            $table->string('time', 255)->nullable();
            $table->text('message')->nullable();

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
        Schema::drop('messages');
    }

}
