<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
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
            'message' => htmlentities('Détail utilisateur'),
            'user' => $user
          ]);

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
              'message' => htmlentities('Liste des utilisateurs connectés'),
              'users' => $users
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 400,
                'error_code' => 'user_connected_fail',
                'message' => $e->getMessage()
            ]);
        }

    }

    public function refillUserStack($email) {
      try {
          $user = \App\User::where('email', $email)->firstOrFail();

          $current = Carbon::now();
          $lastRefill = new Carbon($user->last_refill);

          $diff = $current->diffInMinutes($lastRefill);
          if($diff < 60){
              return response()->json([
                  'status' => 400,
                  'error_code' => 'refill_too_soon',
                  'message' => 'Vous devez attendre '.$diff.' minutes',
                  'error' => [ 'minutes' => $diff ]
              ]);
          }

          $user->stack += 100;
          $user->last_refill = $current->toDateTimeString();

          $user->save();

          return response()->json([
            'status' => 200,
            'message' => htmlentities('Jetons mis à jour'),
            'user' => $user
          ]);

      } catch(ModelNotFoundException $e) {
          return response()->json([
              'status' => 400,
              'error_code' => 'user_not_found',
              'message' => $e->getMessage()
          ]);
      } catch (\Exception $e) {
          return response()->json([
              'status' => 400,
              'error_code' => 'refill_fail',
              'message' => $e->getMessage()
          ]);
      }
    }

    public function updateUserStack($email, $stack) {
        try {
            $user = \App\User::where('email', $email)->firstOrFail();

            $amount = ctype_digit($stack) ? intval($stack) : null;
            if ($amount === null)
            {
                return response()->json([
                    'status' => 400,
                    'error_code' => 'stack_not_number',
                    'message' => 'La variable montant n\'est pas un nombre'
                ]);
            }

            $user->stack += $amount;

            $user->save();

            return response()->json([
              'status' => 200,
              'message' => htmlentities('Stack mis à jour'),
              'user' => $user
            ]);

        } catch(ModelNotFoundException $e) {
            return response()->json([
                'status' => 400,
                'error_code' => 'user_not_found',
                'message' => $e->getMessage()
            ]);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 400,
                'error_code' => 'user_update_fail',
                'message' => $e->getMessage()
            ]);
        }
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

            return response()->json([
              'status' => 200,
              'message' => htmlentities('Utilisateur mis à jour'),
              'user' => $user
            ]);

        } catch(ModelNotFoundException $e) {
            return response()->json([
                'status' => 400,
                'error_code' => 'user_not_found',
                'message' => $e->getMessage()
            ]);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 400,
                'error_code' => 'user_update_fail',
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
              'message' => htmlentities('Utilisateur supprimé')
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 400,
                'error_code' => 'user_not_found',
                'message' => $e->getMessage()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 400,
                'error_code' => 'delete_fail',
                'message' => $e->getMessage()
            ]);
        }
    }

}
