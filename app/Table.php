<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    //
    protected $table = "tables";

    public function users() {
        return $this->belongsToMany('App\Table', 'users_tables')
                    ->withTimestamps();
    }
}
