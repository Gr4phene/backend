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
            'limit' => 'integer|required',
            'page' => 'integer|required',
            'status' => 'in:closed,deleted,open|required'
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
            'id' => 'integer|required'
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
            'id' => 'integer|required',
            'limit' => 'integer|required',
            'page' => 'integer|required'
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
            'id' => 'integer|required'
        ]);

        // Check that an auction should be closed
        $auction = Auction::find($id);

        if (strtotime($auction->ends_at) >= strtotime($auction->created_at) && UserController::getAuthenticatedUser() && $auction->status == "open") {
            // Fetch the highest bid
            $highest_bid_id = Bid::where('auction_id', $id)->orderBy('emeralds', 'desc')->take(1)->pluck('id');

            // Set the auction as closed
            $auction->highest_bid_id = $highest_bid_id;
            $auction->status = 'closed';

            // Return info as a json dump
            return response()->json(['completed' => true]);
        } else {
            return response()->json(['completed' => false]);
        }
    }

    /**
     * Create auction and return its id
     *
     * @param Request $request
     * @return string
     */
    public function doCreate(Request $request)
    {
        // Make sure we have a user token
        if (UserController::getAuthenticatedUser()) {
            // Ensure that POST parameters are valid
            $this->validate($request, [
                'creator' => 'alpha_num|required',
                'item_name' => 'alpha|required',
                'item_desc' => 'alpha_num|required',
                'ends_at' => 'date|required',
                'starting_bid' => 'integer|required'
            ]);

            // Create the auction
            $auction = new Auction;

            $auction->creator = $request->input('creator');
            $auction->item_name = $request->input('item_name');
            $auction->item_desc = $request->input('item_desc');
            $auction->ends_at = $request->input('ends_at');
            $auction->starting_bid = $request->input('starting_bid');

            $auction->save();

            return response()->json(['id' => $auction->id]);
        } else {
            return response()->json(['completed' => true]);
        }
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
            'id' => 'integer|required'
        ]);

        // Set the auction as closed
        $auction = Auction::find($id);

        // Ensure that the user is the same as the one who created it
        if(UserController::getAuthenticatedUser() == $auction->creator && $auction->status == "open") {
            $auction->status = 'deleted';

            // Return info as a json dump
            return response()->json(['completed' => true]);
        } else {
            return response()->json(['completed' => false]);
        }
    }

}
