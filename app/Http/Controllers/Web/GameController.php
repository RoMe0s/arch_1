<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Game\Infrastructure\Persistance\Eloquent\Game as EloquentGame;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function show(EloquentGame $eloquentGame, Request $request)
    {
        $this->authorize('view', $eloquentGame);
        $eloquentUser = $request->user();
        return view('game.show', compact('eloquentGame', 'eloquentUser'));
    }
}
