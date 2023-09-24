<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Models\Matches;
use App\Models\Team;

class FixtureController extends Controller
{
    public function generateFixture(Request $request)
    {
        $allTeams = DB::table('teams')->get();
        $teams = [];

        foreach($allTeams as $t){
            $teams[$t->name] = $t->id;
        }

        $teamsForFixture = DB::table('teams')->pluck('name')->toArray();

        $weeks = $this->createFixtures($teamsForFixture);

        DB::table('matches')->truncate();

        foreach ($weeks as $key => $week) {
            $weeknumb = $key + 1;
            foreach($week as $match){
                $home_id = $teams[$match['home']];
                $away_id = $teams[$match['away']];

                $array = [
                    'home_team_id' => $home_id,
                    'away_team_id' => $away_id,
                    'week' => $weeknumb,
                ];
                $fixture = DB::table('matches')->insert($array);
            }
        }

        $weeks = Matches::with('homeTeam', 'awayTeam')->get()->groupBy('week');

        $matchesByWeek = [];

        foreach ($weeks as $weekNumber => $week) {
            $matchesByWeek[$weekNumber] = $week->map(function ($match) {
                return [
                    'home_team' => $match->homeTeam->name,
                    'away_team' => $match->awayTeam->name,
                ];
            })->toArray();
        }
        return response()->json($matchesByWeek);
    }

    public function createFixtures($names) {

        $teams = sizeof($names);

        // Generate the fixtures using the cyclic algorithm.
        $totalRounds = $teams - 1;
        $matchesPerRound = $teams / 2;

        $result = [];
        for ($i = 0; $i < $totalRounds; $i++) {
            $result[$i] = array();
        }

        for ($round = 0; $round < $totalRounds; $round++) {
            for ($match = 0; $match < $matchesPerRound; $match++) {
                $home = ($round + $match) % ($teams - 1);
                $away = ($teams - 1 - $match + $round) % ($teams - 1);

                // Last team stays in the same place while the others
                // rotate around it.
                if ($match == 0) {
                    $away = $teams - 1;
                }

                $result[$round][$match] = [
                    'home' => $this->team_name($home + 1, $names),
                    'away' => $this->team_name($away + 1, $names),
                ];
            }
        }

        // Last team can't be away for every game so flip them
        // to home on odd rounds.

        $result_counter = sizeof($result);
        for ($i = sizeof($result) - 1; $i >= 0; $i--) {
            $result_counter += 1;
            foreach ($result[$i] as $r) {
                $result[$result_counter][] = $this->flip($r);
            }
        }
        return array_values($result);
    }

    public function flip($match) {
        $reverse = [
            'home' => $match['away'],
            'away' => $match['home'],
        ];

        return $reverse;
    }

    public function team_name($num, $names) {
        $i = $num - 1;
        if (sizeof($names) > $i && strlen(trim($names[$i])) > 0) {
            return trim($names[$i]);
        } else {
            return $num;
        }
    }

    public function getScoreBoard() {
        $scoreBoard = Team::orderBy('point', 'desc')->get();
        if ($scoreBoard->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'You have to generate teams first.'
            ], 404);
        }
        return response()->json($scoreBoard);
    }

    public function getCurrentWeek() {
        $currentWeek = Matches::where('is_played', 0)->first('week');
        if ($currentWeek == null) {
            $currentWeek = 6;
        }
        return response()->json($currentWeek);
    }

    public function championshipProdictions() {
        $teams = Team::get();

        $remainingWeeks = Matches::where('is_played', 0)->count();



        if ($remainingWeeks == 0) {

            $championPoint = $teams->max('point');
            $champion = $teams->where('point', $championPoint)->first();

            $teams = $teams->map(function($q) use ($champion) {
                if ($q->id == $champion->id) {
                    $q->percentage = 100;
                } else {
                    $q->percentage = 0;
                }
                return [
                    'name' => $q['name'],
                    'value' => $q['percentage']];
            });
            return response()->json($teams->toArray());
        }


        $remainingWeeks = $remainingWeeks / 2;

        // Toplam puanları hesapla
        $totalPoints = $teams->sum('point');

        // Her takımın şampiyon olma yüzdesini hesapla
        foreach ($teams as &$team) {
            if ($team['point'] == 0 && $totalPoints == 0) {
                $team['percentage'] = 25;
            } else {
                $team['percentage'] = ($team['point'] / $totalPoints) * 100;
            }
        }

        $percentages = array_map(function($q) {
            return [
                'name' => $q['name'],
                'value' => $q['percentage']];
        }, $teams->toArray());


        return response()->json($percentages);
    }

    public function getCurrentWeekMatches() {
        $currentWeekMatches = Matches::where('is_played', 0)->with('awayTeam', 'homeTeam')->orderBy('week')->get()->take(2);

        $matches = $currentWeekMatches->map(function($q) {
            return [
                'home_team' => $q->homeTeam->name,
                'away_team' => $q->awayTeam->name];
        })->toArray();

        return response()->json($matches);
    }
}
