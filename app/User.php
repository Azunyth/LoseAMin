<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'firstname', 'lastname', 'email', 'password', 'is_connected',
        'stack', 'last_refill'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function tables() {
        return $this->belongsToMany('App\Table', 'users_tables')
                    ->withTimestamps();
    }

    public function getUsersConnectedCount() {
        return $this->where('is_connected', 1)->count();
    }
}
