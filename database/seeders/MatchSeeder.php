<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MatchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Maçları ekleyin
        $teams = DB::table('teams')->pluck('id');

        for ($week = 1; $week <= 6; $week++) {
            // İlk 3 hafta
            if ($week <= 3) {
                DB::table('matches')->insert([
                    'home_team_id' => $teams[0],
                    'away_team_id' => $teams[1],
                    'week' => $week,
                ]);
                DB::table('matches')->insert([
                    'home_team_id' => $teams[2],
                    'away_team_id' => $teams[3],
                    'week' => $week,
                ]);
            } else {
                // Sonraki 3 hafta
                DB::table('matches')->insert([
                    'home_team_id' => $teams[1],
                    'away_team_id' => $teams[0],
                    'week' => $week,
                ]);
                DB::table('matches')->insert([
                    'home_team_id' => $teams[3],
                    'away_team_id' => $teams[2],
                    'week' => $week,
                ]);
            }
        }
    }
}
