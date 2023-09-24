<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Team;

class TeamController extends Controller
{
    public function index() {
        $teams = [
            ['name' => 'Liverpool',
            'power_point' => 86,
            'supporter_power' => 54,
            'goal_keeper_power' => 68,
            'motivation' => 55,
            ],
            ['name' => 'Manchester City',
            'power_point' => 79,
            'supporter_power' => 76,
            'goal_keeper_power' => 87,
            'motivation' => 65,
            ],
            ['name' => 'Manchester United',
            'power_point' => 81,
            'supporter_power' => 86,
            'goal_keeper_power' => 56,
            'motivation' => 75,
            ],
            ['name' => 'Chelsea',
            'power_point' => 87,
            'supporter_power' => 76,
            'goal_keeper_power' => 78,
            'motivation' => 65,
            ],
        ];

        foreach($teams as $team) {
            // update or create team
            $team = Team::updateOrCreate(
                ['name' => $team['name']],
                [
                    'power_point' => $team['power_point'],
                    'supporter_power' => $team['supporter_power'],
                    'goal_keeper_power' => $team['goal_keeper_power'],
                    'motivation' => $team['motivation'],
                ],
            );
        }

        return response()->json([
            'success' => true,
            'data' => Team::all()
        ], 200);
    }
}
