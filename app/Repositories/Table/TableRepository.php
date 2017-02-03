<?php

namespace App\Repositories\Table;

use App\Repositories\Table\TableInterface as TableInterface;
use App\Table;
use App\User;
use Illuminate\Support\Facades\DB;


class TableRepository implements TableInterface {

    private $table;
    private $user;
    /*
    * Min config works as follow
    * Array of key / value pair where
    * key : min bet
    * value : number of tables that should be opened for this bet
    */
    private $minConfig = [2 => 3, 5 => 2, 10 => 1, 20 => 1];

    function __construct(Table $table, User $user) {
        $this->table = $table;
        $this->user = $user;
    }

    public function generateTable() {

        $tablesOpened = $this->table->getTableOpenedByMinBet();
        $diffOpened = $this->minConfig;
        foreach ($tablesOpened as $tableOpened) {
            $minBet = $tableOpened->min_bet;
            $diffOpened[$minBet] = $this->minConfig[$minBet] - $tableOpened->nb_table_opened;
        }

        $tablesCreated = array();
        foreach($diffOpened as $minBet => $restToOpen) {
            if($restToOpen > 0) {
                for($i = 0; $i < $restToOpen; $i++) {
                    $tablesCreated[] = $this->table->openTable($minBet);
                }
            }
        }

        return count($tablesCreated);
    }

    public function removeTable() {
        $tablesToClose = $this->table->getCloseTables();

        foreach ($tablesToClose as $tbc) {
            $tbc->users()->detach();
            $tbc->is_closed = true;
            $tbc->save();
        }

        $nbTableClosed = count($tablesToClose);
        $nbTableOpened = 0;
        if($nbTableClosed > 0) {
            $nbTableOpened = $this->generateTable();
        }

        return ['tables_closed' => $nbTableClosed,
                'table_opened' => $nbTableOpened];
    }

    public function rescaleTable() {
        $this->removeTable();

        $tableStats = $this->table->getTableStats();
        $totalSeatsAvailable = $this->table->getSeats();

        if($totalSeatsAvailable->sum_max_seat != 0) {
            foreach($tableStats as $stats) {
                if($stats->ratio_available < 30) {
                    $this->table->openTable($stats->min_bet);
                }
            }
        } else {
            $this->generateTable();
        }
    }
}
