<?php

namespace Tests\Unit\Domain\Service;

use Tests\TestCase;
use Game\Infrastructure\Repository\Eloquent\GameRepository;
use Game\Infrastructure\Persistance\Eloquent\Game as EloquentGame;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Game\Domain\Service\CheckIfSomeoneWinsService;

class CheckIfSomeoneWinsServiceTest extends TestCase
{
    use RefreshDatabase;

    public function testCheckWinner()
    {
        $eloquentGame = factory(EloquentGame::class)->create([
            'competitor_id' => factory(EloquentUser::class)->create()->id
        ]);
        $game = resolve(GameRepository::class)->findById($eloquentGame->id);

        //TODO: add steps and check winner
    }
}
