<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Matches;
use App\Models\Team;

class MatchController extends Controller
{
    public function play(Request $request) {

        // Get week from request.
        $week = $request->week;
        if ($request->has('week')) {
            if (!is_numeric($week)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Week must be numeric.'
                ], 400);
            }
        }


        $findWeek = Matches::query()->where('is_played', 0);

        // If week is given, play matches of that week else play all matches.
        if ($week) {
            $findWeek = $findWeek->where('week', $week);
        } else {
            $findWeek = $findWeek;
        }

        $week = $findWeek->get();

        // If there is no match to play, return error.
        if ($week->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'There are either no matches left this week or the league is over'
            ], 404);
        }

        // Play matches by week.
        foreach ($week as $match) {
            $homeTeam = Team::find($match->home_team_id);
            $awayTeam = Team::find($match->away_team_id);

            $homeTeamPoint = 10;

            // Arithmetics
            $homeTeamWinRate = ($homeTeam->power_point + $homeTeam->supporter_power + $homeTeam->goal_keeper_power + $homeTeam->motivation + $homeTeamPoint) / 4;
            $awayTeamWinRate = ($awayTeam->power_point + $awayTeam->supporter_power + $awayTeam->goal_keeper_power + $awayTeam->motivation) / 4;

            // Sum Rates
            $rates = $homeTeamWinRate + $awayTeamWinRate;

            // Calculate Coefficient
            $coefficient = 100 / $rates;

            // Calculate Win Rate
            $homeTeamWinRate = $homeTeamWinRate * $coefficient;
            $awayTeamWinRate = $awayTeamWinRate * $coefficient;

            // Calculate Goals
            $homeTeamGoals = $this->calculateGoals($homeTeamWinRate);
            $awayTeamGoals = $this->calculateGoals($awayTeamWinRate);

            // Find Winner
            if ($homeTeamGoals > $awayTeamGoals) {
                $findWinner = $homeTeam->id;
            } elseif ($homeTeamGoals < $awayTeamGoals) {
                $findWinner = $awayTeam->id;
            } else {
                $findWinner = 0;
            }

            // Increase Motivation Of Winner
            if ($findWinner != 0) {
                $increaseMotivationOfWinner = Team::find($findWinner);
                $increaseMotivationOfWinner->motivation = $increaseMotivationOfWinner->motivation + 5;
                $increaseMotivationOfWinner->save();

                // Decrease Motivation Of Loser
                $decreaseMotivationOfLoser = Team::find($findWinner == $homeTeam->id ? $awayTeam->id : $homeTeam->id);
                $decreaseMotivationOfLoser->motivation = $decreaseMotivationOfLoser->motivation - 5;
                $decreaseMotivationOfLoser->save();
            }


            // Home Team Match Results
            $homeWin = $homeTeam->win + ($findWinner == $homeTeam->id ? 1 : 0);
            $homeDraw = $homeTeam->draw + ($findWinner == 0 ? 1 : 0);
            $homeLose = $homeTeam->lose + ($findWinner == $awayTeam->id ? 1 : 0);
            $homePoint = $homeTeam->point + ($findWinner == $homeTeam->id ? 3 : ($findWinner == 0 ? 1 : 0));

            $homeTeam->update([
                'win' => $homeWin,
                'draw' => $homeDraw,
                'lose' => $homeLose,
                'point' => $homePoint,
            ]);

            // Away Team Match Results
            $awayWin = $awayTeam->win + ($findWinner == $awayTeam->id ? 1 : 0);
            $awayDraw = $awayTeam->draw + ($findWinner == 0 ? 1 : 0);
            $awayLose = $awayTeam->lose + ($findWinner == $homeTeam->id ? 1 : 0);
            $awayPoint = $awayTeam->point + ($findWinner == $awayTeam->id ? 3 : ($findWinner == 0 ? 1 : 0));

            $awayTeam->update([
                'win' => $awayWin,
                'draw' => $awayDraw,
                'lose' => $awayLose,
                'point' => $awayPoint,
            ]);

            // Update Match
            $match = Matches::find($match->id);
            $match->home_team_goals = $homeTeamGoals;
            $match->away_team_goals = $awayTeamGoals;
            if ($findWinner != 0) {
                $match->winner_id = $findWinner;
            }
            $match->is_played = 1;
            $match->save();
        }



        return response()->json([
            'success' => true,
            'message' => 'Matches are played.'
        ], 200);
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
