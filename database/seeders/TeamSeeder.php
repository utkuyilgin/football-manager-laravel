<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('teams')->insert([
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
        ]);
    }
}
