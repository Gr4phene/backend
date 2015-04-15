<?php namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{

    /**
     * Expose information about an user as a json response
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

        // Return info as a json dump
        return User::find($id);
    }

    /**
     * Remove karma from a user
     *
     * @param Request $request
     * @param $id
     * @return string
     */
    public function doHate(Request $request, $id)
    {
        // Ensure that the id is a integer string
        $this->validate($request, [
            'id' => 'integer'
        ]);

        // Get current karma
        $user = User::find($id);
        $user->decrement('karma');

        // Return info as a json dump
        return json_encode(['completed' => true]);
    }

    /**
     * Give a user karma
     *
     * @param Request $request
     * @param $id
     * @return string
     */
    public function doLove(Request $request, $id)
    {
        // Ensure that the id is a integer string
        $this->validate($request, [
            'id' => 'integer'
        ]);

        // Get current karma
        $user = User::find($id);
        $user->increment('karma');

        // Return info as a json dump
        return json_encode(['completed' => true]);
    }

    /**
     * Create a user and return its id
     *
     * @param Request $request
     * @param $id
     * @return string
     */
    public function doCreate(Request $request, $id)
    {
        // Ensure that POST parameters are valid
        $this->validate($request, [
            'name' => 'alpha_num|between:1,16'
        ]);

        // Create the user
        $user = new User;

        $user->name = $request->input('name');

        $user->save();

        return json_encode(['id' => $user->id]);
    }

    /**
     * Retrieve a list of all users based on their karma being greater than x
     *
     * @param Request $request
     * @return mixed
     */
    public function showAll(Request $request)
    {
        // Fetch the POST input for request pagination
        $limit = $request->input('limit');
        $page = $request->input('page');
        $karma = $request->input('karma');

        // Ensure that the id is a integer string
        $this->validate($request, [
            'limit' => 'integer',
            'page' => 'integer',
            'karma' => 'integer'
        ]);

        // Fetch the auction bid list
        return User::where('karma', '>', $karma)->skip(100 * $page)->take($limit)->get();
    }

}
