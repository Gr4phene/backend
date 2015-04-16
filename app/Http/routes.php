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

$app->get('/', function () use ($app) {
    return $app->welcome();
});

// Auction endpoints
$app->get('auctions', 'AuctionController@showAll');
$app->post('auction/create', 'AuctionController@doCreate');
$app->get('auction/{id}', 'AuctionController@showInfo');
$app->get('auction/{id}/bids', 'AuctionController@showBids');
$app->post('auction/{id}/close', 'AuctionController@doClose');
$app->post('auction/{id}/delete', 'AuctionController@doDelete');

// Bid endpoints
$app->get('bid/{id}', 'BidController@showInfo');
$app->post('bid/on/{id}', 'BidController@doAuctionBid');

// Item endpoints
$app->get('items', 'ItemController@showAll');
$app->get('item/{id}', 'ItemController@showInfo');

// User endpoints
$app->get('users', 'UserController@showAll');
$app->post('user/create', 'UserController@doCreate');
$app->post('user/create', 'UserController@doCreate');
$app->get('user/{id}', 'UserController@showInfo');
$app->post('user/{id}/hate', 'UserController@doHate');
$app->post('user/{id}/love', 'UserController@doLove');
