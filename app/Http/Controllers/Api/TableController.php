<?php

namespace App\Http\Controllers\Api;

use App\Table;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TableController extends Controller
{
    public function __construct() {
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

}
