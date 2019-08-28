<?php

namespace App\Http\Resources;

use Game\Domain\Entity\Game;
use Illuminate\Http\Resources\Json\JsonResource;

class GameResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        /** @var Game $game */
        $game = $this->resource;

        $competitor = $game->getCompetitor();
        $winner = $game->getWinner();
        $startedAt = $game->getStartedAt();
        $endedAt = $game->getEndedAt();

        return [
            'owner' => PlayerResource::make($game->getOwner()),
            'competitor' => $competitor ? PlayerResource::make($competitor) : null,
            'winner' => $winner ? PlayerResource::make($winner) : null,
            'startedAt' => $startedAt ? $startedAt->format('Y-m-d H:i:s') : null,
            'endedAt' => $endedAt ? $endedAt->format('Y-m-d H:i:s') : null,
            'stepsCount' => $game->getStepsCount()
        ];

    }
}
