<?php

namespace App\Http\Controllers\Api;

use Laravel\Passport\Token;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Controllers\Controller;

class UserController extends Controller
{

    public function __construct() {
        $this->middleware('auth:api');
    }

    public function getUser($email) {

      try {
          $user = \App\User::where('email', $email)->firstOrFail();

          return response()->json([
            'status' => 200,
            'user' => $user]);

      } catch (ModelNotFoundException $e) {
          return response()->json([
              'status' => 400,
              'error_code' => 'user_not_found',
              'message' => $e->getMessage()
          ]);
      }
    }

    public function getUsersConnected() {
        try {
            $users = \App\User::where('is_connected', 1)->get();

            return response()->json([
              'status' => 200,
              'users' => $users]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 400,
                'error_code' => 'user_connected_fail',
                'message' => $e->getMessage()
            ]);
        }

    }

    public function refillUserStack($id) {

    }

    public function updateUser(Request $request, $email) {
        try {
            $user = \App\User::where('email', $email)->firstOrFail();

            $properties = ['firstname', 'lastname', 'username'];

            foreach($properties as $property) {
                if($request->has($property)) {
                    $user->$property = $request->input($property);
                }
            }

            $user->save();

        } catch(ModelNotFoundException $e) {
            return response()->json([
                'status' => 400,
                'error_code' => 'update_user_fail',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function deleteUser($email) {
        try {
            $user = \App\User::where('email', $email)->findOrFail();

            $oauthAccessToken = Token::where('client_id', $email)
                                     ->update(['revoked' => 1]);

            $user->delete();

            return response()->json([
              'status' => 200,
              'message' => 'Utilisateur supprimé.']);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 400,
                'error_code' => 'user_not_found',
                'message' => $e->getMessage()
            ]);
        }
    }

}
