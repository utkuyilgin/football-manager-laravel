<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'power_point',
        'supporter_power',
        'goal_keeper_power',
        'motivation',
        'win',
        'draw',
        'lose',
        'point',
    ];
}
