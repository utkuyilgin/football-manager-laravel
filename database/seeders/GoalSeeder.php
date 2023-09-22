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

            // Takımların gol atmaya olan ortalama eğilimlerini hesapladık, ev sahibine kendi sahası olduğu için
            // rastgele 5 puan verdik

            $homeTeamWinRate = 50 + 5 + $homeTeam->win_rate;
            $awayTeamWinRate = 50 + $awayTeam->win_rate;

            $homeTeamMotivation = $homeTeam->motivation;
            $awayTeamMotivation = $awayTeam->motivation;

            $homeTeamWinRate += $homeTeamMotivation;
            $awayTeamWinRate += $awayTeamMotivation;

            $homeTeamWinRate = $homeTeamWinRate / ($homeTeamWinRate + $awayTeamWinRate) * 100;
            $awayTeamWinRate = 100 - $homeTeamWinRate;

            $homeTeamGoals = $this->calculateGoals($homeTeamWinRate);
            $awayTeamGoals = $this->calculateGoals($awayTeamWinRate);

            DB::table('goals')->insert([
                'match_id' => $match->id,
                'team_id' => $match->home_team_id,
                'scored_goals' => $homeTeamGoals,
            ]);

            DB::table('goals')->insert([
                'match_id' => $match->id,
                'team_id' => $match->away_team_id,
                'scored_goals' => $awayTeamGoals,
            ]);
        }
    }
    public function calculateGoals($motivation)
    {
        $goals = 0;

        if ($motivation >= 90) {
            $goals = rand(3, 5);
        } elseif ($motivation >= 80) {
            $goals = rand(2, 4);
        } elseif ($motivation >= 70) {
            $goals = rand(1, 3);
        } elseif ($motivation >= 60) {
            $goals = rand(1, 2);
        } elseif ($motivation >= 50) {
            $goals = rand(0, 2);
        } elseif ($motivation >= 40) {
            $goals = rand(0, 1);
        } elseif ($motivation >= 30) {
            $goals = rand(0, 1);
        } elseif ($motivation >= 20) {
            $goals = rand(0, 1);
        } elseif ($motivation >= 10) {
            $goals = rand(0, 1);
        } else {
            $goals = rand(0, 1);
        }

        return $goals;
    }
}
