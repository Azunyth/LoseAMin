<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('user/connected', 'Api\UserController@getUsersConnected');

Route::get('user/{email}', 'Api\UserController@getUser');

Route::get('user/{id}/refill', 'Api\UserController@refillUserStack');

Route::get('table/opened', 'Api\TableController@getAllTables');

Route::post('auth/logout', 'Api\AuthController@logout');

Route::post('auth/login', 'Api\AuthController@login');

Route::post('auth/register', 'Api\AuthController@createUser');

Route::put('user/{email}', 'Api\UserController@updateUser');

Route::delete('user/{email}', 'Api\UserController@deleteUser');
