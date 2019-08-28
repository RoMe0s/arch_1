<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\MakeAMoveRequest;
use App\Http\Requests\SetPlayerNameRequest;
use Game\Domain\Service\MovementMakerService;
use Game\Infrastructure\DTO\NewStepDTO;
use Game\Infrastructure\DTO\SetCompetitorDTO;
use Game\Infrastructure\DTO\SetPlayerNameDTO;
use Game\Infrastructure\Service\JoinGameService;
use Game\Infrastructure\Service\PlayerNameSetterService;
use Game\Infrastructure\Service\StepCreatorService;
use Game\Infrastructure\Mapper\{
    GameMapper,
    PlayerMapper
};
use App\Http\Resources\{
    GameResource,
    NewGameResource,
    PlayerResource
};
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Game\Infrastructure\Service\GameCreatorService;
use Game\Infrastructure\Persistance\Eloquent\Game as EloquentGame;

class GameController extends Controller
{
    public function startGame(Request $request, GameCreatorService $gameCreatorService)
    {
        $game = $gameCreatorService->createGameForPlayer($request->user()->id);
        return NewGameResource::make($game)->resolve($request);
    }

    public function show(EloquentGame $eloquentGame, Request $request, GameMapper $gameMapper, PlayerMapper $playerMapper)
    {
        $this->authorize('participate', $eloquentGame);
        $eloquentGame->load(['owner', 'competitor', 'steps']);

        $somePlayer = $playerMapper->make($request->user());
        $game = $gameMapper->make($eloquentGame);

        $player = $game->getPlayerOfGame($somePlayer);
        $competitor = $game->getAnotherPlayer($player);

        return response()->json([
            'game' => GameResource::make($game)->resolve($request),
            'player' => PlayerResource::make($player)->resolve($request),
            'competitor' => $competitor ? PlayerResource::make($competitor)->resolve($request) : null
        ]);
    }

    public function join(EloquentGame $eloquentGame, Request $request, JoinGameService $joinGameService)
    {
        $setCompetitorDTO = new SetCompetitorDTO($eloquentGame->id, $request->user()->id);
        $joinGameService->joinGame($setCompetitorDTO);
        return response()->json();
    }

    public function setName(EloquentGame $eloquentGame, SetPlayerNameRequest $request, PlayerNameSetterService $playerNameSetterService)
    {
        $this->authorize('show', $eloquentGame);
        $setPlayerNameDTO = new SetPlayerNameDTO(
            $eloquentGame->id,
            $request->user()->id,
            $request->get('name')
        );
        $playerNameSetterService->setPlayerName($setPlayerNameDTO);
        return response()->json();
    }

    public function makeAMove(EloquentGame $eloquentGame, MakeAMoveRequest $request, StepCreatorService $stepCreatorService)
    {
        $this->authorize('participate', $eloquentGame);

        $newStepDTO = new NewStepDTO(
            $eloquentGame->id,
            $request->user()->id,
            $request->get('x'),
            $request->get('y')
        );
        $stepCreatorService->createStepForGame($newStepDTO);
        return response()->json();
    }
}
