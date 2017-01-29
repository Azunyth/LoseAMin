<?php

namespace App\Http\Controllers\Api;

use Laravel\Passport\Client;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Validator;

class AuthController extends Controller
{
    public function __construct() {
        $this->middleware('guest');
    }

    public function createUser(Request $request) {
        $user = $request->input('user');

        if(is_null($user)) {
            return response()->json([
                'status' => 400,
                'error_code' => 'register_data_empty',
                'error' => htmlentities('Les données envoyées ne sont pas correctement formées.')
            ]);
        }

        $validator = Validator::make($request->input('user'), [
          'username' => 'required|unique:users,username|max:190',
          'firstname' => 'required|max:190',
          'lastname' => 'required|max:190',
          'email' => 'required|unique:users,email|max:190|email',
          'password' => 'required|max:255',
        ]);

        if($validator->fails()) {
          return response()->json([
              'status' => 400,
              'error_code' => 'register_data_fails',
              'error' => 'Certaines données sont invalides',
              'message' => $validator->errors()
          ]);
        }


        \DB::beginTransaction();
        try {
            $userSaved = \App\User::create([
              'username'     => $user["username"],
              'firstname'    => $user["firstname"],
              'lastname'     => $user["lastname"],
              'email'        => $user["email"],
              'password'     => bcrypt($user["password"]),
            ]);

            $oauth_client = Client::create([
                'id'                     => $userSaved->email,
                'user_id'                => $userSaved->id,
                'name'                   => $userSaved->email,
                'secret'                 => base64_encode(md5($user["password"])),
                'password_client'        => 1,
                'personal_access_client' => 0,
                'redirect'               => '',
                'revoked'                => 0
            ]);

            \DB::commit();
        }catch(\Exception $e) {
            \DB::rollback();
            return response()->json([
                'status' => 400,
                'error_code' => 'register_insert_fail',
                'message' => $e->getMessage()
            ]);
        }


        return response()->json([
            'status' => 200,
            'message' => htmlentities('Utilisateur créé avec succès !'),
            'user' => $oauth_client
        ]);
    }

    public function login(Request $request) {
        $validator = Validator::make($request->all(), [
          'email' => 'required|exists:users,email|exists:oauth_clients,id',
          'password' => 'required',
          'secret' => 'required'
        ]);

        if($validator->fails()) {
          return response()->json([
              'status' => 400,
              'error_code' => 'login_data_fails',
              'error' => 'Certaines données sont invalides',
              'message' => $validator->errors()
          ]);
        }

        $email = $request->input('email');
        $password = $request->input('password');
        $client = $request->input('client');
        $secret = $request->input('secret');

        $request->request->add([
            'grant_type'    => "password",
            'client_id'     => $email,
            'client_secret' => $secret,
            'username'      => $email,
            'password'      => $password,
            'scope'         => '*',
        ]);

        $proxy = Request::create(
           'oauth/token',
           'POST'
         );

         return \Route::dispatch($proxy);
    }

    public function logout(Request $request) {
        var_dump($request); die;
    }
}
