<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GoalSeeder extends Seeder
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
            $homeTeam = DB::table('teams')->find($match->home_team_id);
            $awayTeam = DB::table('teams')->find($match->away_team_id);


            $homeTeamWinRate = ($homeTeam->power_point + $homeTeam->supporter_power + $homeTeam->goal_keeper_power + $homeTeam->motivation) / 4;
            $awayTeamWinRate = ($awayTeam->power_point + $awayTeam->supporter_power + $awayTeam->goal_keeper_power + $awayTeam->motivation) / 4;

            $rates = $homeTeamWinRate + $awayTeamWinRate;

            $coefficient = 100 / $rates;

            $homeTeamWinRate = $homeTeamWinRate * $coefficient;
            $awayTeamWinRate = $awayTeamWinRate * $coefficient;

            dd($homeTeamWinRate, $awayTeamWinRate);

            $homeTeamGoals = $this->calculateGoals($homeTeamWinRate);
            $awayTeamGoals = $this->calculateGoals($awayTeamWinRate);

            if ($homeTeamGoals > $awayTeamGoals) {
                $findWinner = $homeTeam->id;
            } elseif ($homeTeamGoals < $awayTeamGoals) {
                $findWinner = $awayTeam->id;
            } else {
                $findWinner = 0;
            }

            DB::table('teams')->update([
                'win' => $findWinner == $homeTeam->id ? 1 : 0,
                'draw' => $findWinner == 0 ? 1 : 0,
                'lose' => $findWinner == $awayTeam->id ? 1 : 0,
                'point' => $findWinner == $homeTeam->id ? 3 : ($findWinner == 0 ? 1 : 0),
            ]);

            DB::table('teams')->update([
                'win' => $findWinner == $awayTeam->id ? 1 : 0,
                'draw' => $findWinner == 0 ? 1 : 0,
                'lose' => $findWinner == $homeTeam->id ? 1 : 0,
                'point' => $findWinner == $awayTeam->id ? 3 : ($findWinner == 0 ? 1 : 0),
            ]);

            DB::table('matches')->update(
                ['id' => $match->id],
                ['home_team_goals' => $homeTeamGoals,
                    'away_team_goals' => $awayTeamGoals,
                    'winner_id' => $findWinner,
                    'is_played' => '1',
                ]
            );



        }
    }
    public function calculateGoals($winRate)
    {
        $goals = 0;

        if ($winRate > 50) {
            $goals = rand(2,5);
        } elseif ($winRate <= 50) {
            $goals = rand(0,5);
        }

        return $goals;
    }
}
