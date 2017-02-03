<?php

namespace App\Http\Controllers\Api;

use App\Table;
use App\User;
use App\Repositories\Table\TableInterface as TableInterface;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TableController extends Controller
{

    private $tableRepo;

    public function __construct(TableInterface $table) {
        $this->tableRepo = $table;
        $this->middleware('auth:api');
    }

    public function getAllTables() {
      try {
          $tables = Table::where('is_closed', 0)->get();

          return response()->json([
            'status' => 200,
            'message' => htmlentities('Liste des tables ouvertes'),
            'tables' => $tables
          ]);

      } catch (\Exception $e) {
          return response()->json([
              'status' => 400,
              'error_code' => 'tables_open_fail',
              'message' => $e->getMessage()
          ]);
      }
    }

    public function sitOnTable($email, $id) {
        try {
            $user = User::where('email', $email)->firstOrFail();

            $table = Table::findOrFail($id);

            if($table->is_closed) {
                return response()->json([
                    'status' => 400,
                    'error_code' => 'table_closed',
                    'message' => 'La table est fermée'
                ]);
            }

            if($table->seats_available >= 1) {
                $user->tables()->attach($id);

                $table->seats_available -= 1;
                $table->last_activity = Carbon::now()->toDateTimeString();
                $table->save();

                $this->tableRepo->rescaleTable();

                return response()->json([
                    'status' => 200,
                    'message' => htmlentities('Place à la table autorisée'),
                    'user' => $user,
                    'table' => $table
                ]);
            }else {
                return response()->json([
                    'status' => 400,
                    'error_code' => 'table_full',
                    'message' => 'La table est pleine'
                ]);
            }


        }catch(\PDOException $qe) {
            return response()->json([
                'status' => 400,
                'error_code' => 'user_already_on_table',
                'error' => htmlentities('L\'utilisateur est déjà assis à la table'),
                'message' => $qe->getMessage()
            ]);
        } catch(ModelNotFoundException $e) {
            $errorCode = ($e->getModel() == 'App\Table') ? 'table_no_result' : 'user_no_result';
            return response()->json([
                'status' => 401,
                'error_code' => $errorCode,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function leaveTable($email, $id) {
        try {
            $user = User::where('email', $email)->firstOrFail();

            $table = Table::findOrFail($id);

            if($user->tables()->detach($id)) {
                $table->seats_available += 1;
                $table->last_activity = Carbon::now()->toDateTimeString();
                $table->save();

                $this->tableRepo->rescaleTable();

                return response()->json([
                    'status' => 200,
                    'message' => htmlentities('Place à la table restaurée'),
                    'user' => $user,
                    'table' => $table
                ]);
            }else {
                return response()->json([
                    'status' => 400,
                    'error_code' => 'user_not_on_table',
                    'message' => htmlentities('L\'utilisateur n\'est pas assis à la table')
                ]);
            }
        } catch(ModelNotFoundException $e) {
            $errorCode = ($e->getModel() == 'App\Table') ? 'table_no_result' : 'user_no_result';
            return response()->json([
                'status' => 401,
                'error_code' => 'user_no_result',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function test() {
        $this->tableRepo->rescaleTable();
    }

}
