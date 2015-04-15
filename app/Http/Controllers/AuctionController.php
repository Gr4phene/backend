<?php namespace App\Http\Controllers;

use App\Models\Auction;
use App\Models\Bid;
use Illuminate\Http\Request;

class AuctionController extends Controller
{

    /**
     * Retrieve a list of all open auctions using their status as a filter
     *
     * @param Request $request
     * @return mixed
     */
    public function showAll(Request $request)
    {
        // Fetch the POST input for request pagination
        $limit = $request->input('limit');
        $page = $request->input('page');
        $status = $request->input('status');

        // Ensure that the id is a integer string
        $this->validate($request, [
            'limit' => 'integer',
            'page' => 'integer',
            'status' => 'in:closed,deleted,open'
        ]);

        // Fetch the auction bid list
        return Auction::where('status', $status)->skip(100 * $page)->take($limit)->get();
    }

    /**
     * Expose information about an auction as a json response
     *
     * @param Request $request
     * @param $id
     * @return string
     */
    public function showInfo(Request $request, $id)
    {
        // Ensure that the id is a integer string
        $this->validate($request, [
            'id' => 'integer'
        ]);

        // Fetch the auction info
        return Auction::find($id);
    }

    /**
     * Expose an auction's bid list as a json response
     *
     * @param Request $request
     * @param $id
     * @return string
     */
    public function showBids(Request $request, $id)
    {
        // Fetch the POST input for request pagination
        $limit = $request->input('limit');
        $page = $request->input('page');

        // Ensure that the id is a integer string
        $this->validate($request, [
            'id' => 'integer',
            'limit' => 'integer',
            'page' => 'integer'
        ]);

        // Fetch the auction bid list
        return Bid::where('auction_id', $id)->skip(100 * $page)->take($limit)->get();
    }

    /**
     * Mark an auction as closed and fetch the highest bid id
     *
     * @param Request $request
     * @param $id
     * @return string
     */
    public function doClose(Request $request, $id)
    {
        // Ensure that the id is a integer string
        $this->validate($request, [
            'id' => 'integer'
        ]);

        // Fetch the highest bid
        $highest_bid_id = Bid::where('auction_id', $id)->orderBy('emeralds', 'desc')->take(1)->pluck('id');

        // Set the auction as closed
        $auction = Auction::find($id);
        $auction->highest_bid_id = $highest_bid_id;
        $auction->status = 'closed';

        // Return info as a json dump
        return json_encode(['completed' => true]);
    }

    /**
     * Create auction and return its id
     *
     * @param Request $request
     * @return string
     */
    public function doCreate(Request $request)
    {
        // Ensure that POST parameters are valid
        $this->validate($request, [
            'creator' => 'alpha_num',
            'item_name' => 'alpha',
            'item_desc' => 'alpha_num',
            'ends_at' => 'date',
            'starting_bid' => 'integer'
        ]);

        // Create the auction
        $auction = new Auction;

        $auction->creator = $request->input('creator');
        $auction->item_name = $request->input('item_name');
        $auction->item_desc = $request->input('item_desc');
        $auction->ends_at = $request->input('ends_at');
        $auction->starting_bid = $request->input('starting_bid');

        $auction->save();

        return json_encode(['id' => $auction->id]);
    }

    /**
     * Mark an auction as deleted
     *
     * @param Request $request
     * @param $id
     * @return string
     */
    public function doDelete(Request $request, $id)
    {
        // Ensure that the id is a integer string
        $this->validate($request, [
            'id' => 'integer'
        ]);

        // Set the auction as closed
        $auction = Auction::find($id);
        $auction->status = 'deleted';

        // Return info as a json dump
        return json_encode(['completed' => true]);
    }

}
