<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ResetController extends Controller
{
    public function resetData() {
        \DB::table('matches')->delete();
        \DB::table('teams')->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data has been reset',
        ]);
    }
}
