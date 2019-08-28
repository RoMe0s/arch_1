<?php

namespace App\Http\Resources;

use Game\Domain\Entity\Step;
use Illuminate\Http\Resources\Json\JsonResource;

class StepResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        /** @var Step $step */
        $step = $this->resource;
        return [
            'x' => $step->getX()->getValue(),
            'y' => $step->getY()->getValue()
        ];
    }
}
