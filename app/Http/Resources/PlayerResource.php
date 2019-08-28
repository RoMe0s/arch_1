<?php

namespace App\Http\Resources;

use Game\Domain\Entity\Player;
use Illuminate\Http\Resources\Json\JsonResource;

class PlayerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        /** @var Player $player */
        $player = $this->resource;
        return [
            'name' => $player->getName(),
            'steps' => StepResource::collection(collect($player->getSteps()))
        ];
    }
}
