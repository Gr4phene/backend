<?php namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

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
            'id' => 'integer|required'
        ]);

        // Return info as a json dump
        return User::find($id)->select(['id', 'name', 'karma']);
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
            'id' => 'integer|required'
        ]);

        // Get current karma
        $user = User::find($id);

        // Ensure that this isn't self-karma
        if (UserController::getAuthenticatedUser() !== $user->id) {
            $user->decrement('karma');

            // Return info as a json dump
            return response()->json(['completed' => true]);
        } else {
            return response()->json(['completed' => false]);
        }
    }

    /**
     * Get a user from a request auth token
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public static function getAuthenticatedUser()
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }
        } catch (TokenExpiredException $e) {
            return false;
        } catch (TokenInvalidException $e) {
            return false;
        } catch (JWTException $e) {
            return false;
        }

        // the token is valid and we have found the user via the sub claim
        return response()->json(compact('user'));
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
            'id' => 'integer|required'
        ]);

        // Get current karma
        $user = User::find($id);

        // Ensure that this isn't self-karma
        if (UserController::getAuthenticatedUser() !== $user->id) {
            $user->increment('karma');

            // Return info as a json dump
            return response()->json(['completed' => true]);
        } else {
            return response()->json(['completed' => false]);
        }
    }

    /**
     * Create a user and return its id
     *
     * @param Request $request
     * @return string
     */
    public function doCreate(Request $request)
    {
        // Ensure that POST parameters are valid
        $this->validate($request, [
            'name' => 'alpha_num|between:1,16|required|unique:users'
        ]);

        // Create the user
        $user = new User;

        $user->name = $request->input('name');

        $user->save();

        return response()->json(['id' => $user->id]);
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
            'limit' => 'integer|required',
            'page' => 'integer|required',
            'karma' => 'integer|required'
        ]);

        // Fetch the user list
        return User::where('karma', '>', $karma)->skip(100 * $page)->take($limit)->get();
    }

    /**
     * "Login" a user by registering a token for their session
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function doAuthenticate(Request $request)
    {
        // grab credentials from the request
        $credentials = $request->only('username', 'password');

        try {
            // attempt to verify the credentials and create a token for the user
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        // all good so return the token
        return response()->json(compact('token'));
    }

}
