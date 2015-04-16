<?php namespace App\Http\Controllers;

use App\Models\Bid;
use Illuminate\Http\Request;

class BidController extends Controller
{

    /**
     * Expose information about a bid as a json response
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

        // Fetch the bid info
        return Bid::find($id);
    }

    /**
     * Post a new bid for an auction and return its id
     *
     * @param Request $request
     * @param $id
     * @return string
     */
    public function doAuctionBid(Request $request, $id)
    {
        // Make sure we have a user token
        if (UserController::getAuthenticatedUser()) {
            // Ensure that POST parameters are valid
            $this->validate($request, [
                'id' => 'integer',
                'bidder' => 'integer',
                'emeralds' => 'integer'
            ]);

            // Create the bid
            $bid = new Bid;

            $bid->bidder = $request->input('bidder');
            $bid->emeralds = $request->input('emeralds');
            $bid->auction_id = $id;

            $bid->save();

            return response()->json(['id' => $bid->id]);
        } else {
            return response()->json(['completed' => true]);
        }
    }

}
