<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PointSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $matches = DB::table('matches')->get();

        foreach ($matches as $match) {
            $homeTeamGoals = DB::table('goals')
                ->where('match_id', $match->id)
                ->where('team_id', $match->home_team_id)
                ->sum('scored_goals');

            $awayTeamGoals = DB::table('goals')
                ->where('match_id', $match->id)
                ->where('team_id', $match->away_team_id)
                ->sum('scored_goals');

            $homeTeamPoints = 0;
            $awayTeamPoints = 0;

            if ($homeTeamGoals > $awayTeamGoals) {
                $homeTeamPoints = 3;
            } elseif ($homeTeamGoals < $awayTeamGoals) {
                $awayTeamPoints = 3;
            } else {
                $homeTeamPoints = 1;
                $awayTeamPoints = 1;
            }

            DB::table('points')->updateOrInsert(
                ['team_id' => $match->home_team_id],
                ['points' => DB::raw("points + $homeTeamPoints")]
            );

            DB::table('points')->updateOrInsert(
                ['team_id' => $match->away_team_id],
                ['points' => DB::raw("points + $awayTeamPoints")]
            );
        }
    }
}
