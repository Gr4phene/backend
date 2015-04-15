<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAuctionsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auctions', function (Blueprint $table) {
            // Set the primary key for the table
            $table->increments('id')->unique;

            // Add the table's columns
            $table->dateTime('created_at');
            $table->dateTime('ends_at');
            $table->string('item_name', 20);
            $table->longText('item_description');
            $table->string('status', 7)->default('open');
            $table->integer('starting_bid')->default(0);

            // Add any foreign keys
            $table->integer('creator')->unsigned();
            $table->integer('creator')->references('id')->on('users');

            $table->integer('highest_bid_id')->unsigned();
            $table->foreign('highest_bid_id')->references('id')->on('bids');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('auctions');
    }

}
