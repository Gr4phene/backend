<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBidsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bids', function (Blueprint $table) {
            // Set the primary key for the table
            $table->increments('id')->unique;

            // Add the table's columns
            $table->dateTime('bid_at');
            $table->integer('emeralds');

            // Add any foreign keys
            $table->integer('bidder')->unsigned();
            $table->integer('bidder')->references('id')->on('users');

            $table->integer('auction_id')->unsigned();
            $table->foreign('auction_id')->references('id')->on('auctions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('bids');
    }

}
