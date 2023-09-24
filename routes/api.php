<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FixtureController;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\ResetController;
use App\Http\Controllers\TeamController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/teams', [TeamController::class, 'index']);
Route::get('/current-week', [FixtureController::class, 'getCurrentWeek']);
Route::get('/current-week-matches', [FixtureController::class, 'getCurrentWeekMatches']);
Route::get('/generate-fixtures', [FixtureController::class, 'generateFixture']);
Route::get('/scoreboard', [FixtureController::class, 'getScoreBoard']);
Route::post('/play', [MatchController::class, 'play']);
Route::get('championship-prodictions', [FixtureController::class, 'championshipProdictions']);
Route::get('/reset', [ResetController::class, 'resetData']);
