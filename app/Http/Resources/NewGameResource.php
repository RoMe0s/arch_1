<?php

namespace App\Http\Resources;

use Game\Domain\Entity\Game;
use Illuminate\Http\Resources\Json\JsonResource;

class NewGameResource extends JsonResource
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

        return [
            'id' => $game->getId()
        ];
    }
}
