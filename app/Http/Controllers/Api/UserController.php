<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    //

    public function __construct() {
        $this->middleware('auth');
    }

    public function getUser($id) {

    }

    public function getUsersConnected() {

    }

    public function refillUserStack($id) {

    }

    public function updateUser(Request $request, $id) {

    }

    public function deleteUser($id) {

    }




}
