<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Table extends Model
{
    //
    protected $table = "tables";

    protected $fillable = ['min_bet'];

    public function users() {
        return $this->belongsToMany('App\Table', 'users_tables')
                    ->withTimestamps();
    }

    public function openTable($minBet) {
        return static::create(['min_bet' => $minBet]);
    }

    public function getTableOpenedByMinBet() {
        return $this->select('min_bet', DB::raw('count(*) as `nb_table_opened`'))
                  ->where('is_closed', 0)
                  ->groupBy('min_bet')
                  ->get();
    }

    public function getCloseTables($diffTimeInMinutes = 120) {
        return static::where('is_closed', 0)
                        ->whereRaw('max_seat = seats_available')
                        ->where(DB::raw('TIMESTAMPDIFF(MINUTE, `last_activity`, CURRENT_TIMESTAMP())'), '>=', $diffTimeInMinutes)
                        ->get();
    }

    public function getTableStats() {

        return $this->select('min_bet', DB::raw('SUM(max_seat) as sum_max_seat'),
                    DB::raw('SUM(seats_available) as sum_seats_available'),
                    DB::raw('round((SUM(seats_available)/SUM(max_seat))*100, 2) as ratio_available'),
                    DB::raw('round(((SUM(max_seat)-SUM(seats_available))/SUM(max_seat))*100, 2) as ratio_used'))
                    ->where('is_closed', 0)
                    ->groupBy('min_bet')
                    ->get();
    }

    public function getSeats() {
        return $this->selectRaw('SUM(seats_available) as sum_seats_available,
                                SUM(max_seat) as sum_max_seat')
                    ->where('is_closed', 0)
                    ->first();
    }

    public function getStatsMinBet() {
        return $this->selectRaw('SUM(seats_available) as sum_seats_available')
                    ->where('is_closed', 0)
                    ->groupBy('min_bet')
                    ->having('min_bet', 2)
                    ->first();
    }
}
