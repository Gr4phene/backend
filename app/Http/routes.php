<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

$app->get('/', function() use ($app) {
	return $app->welcome();
});

// Auction endpoints
$app->get('auction/{id}', 'AuctionController@showInfo');
$app->get('auction/{id}/bids', 'AuctionController@showBids');
$app->post('auction/{id}/close', 'AuctionController@doClose');
$app->post('auction/{id}/create', 'AuctionController@doCreate');
$app->post('auction/{id}/delete', 'AuctionController@doDelete');

// Bid endpoints
$app->get('bid/{id}', 'BidController@showInfo');
$app->post('bid/on/{id}', 'BidController@doAuctionBid');

// User endpoints
$app->get('user/{id}', 'UserController@showInfo');
$app->post('user/{id}/hate', 'UserController@doHate');
$app->post('user/{id}/love', 'UserController@doLove');