<?php

namespace Tests\Unit\Infrastructure\Repository;

use Tests\TestCase;
use Game\Infrastructure\Repository\Eloquent\PlayerRepository;
use Game\Infrastructure\Persistance\Eloquent\User as EloquentUser;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PlayerRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function testFindById()
    {
        $eloquentUser = factory(EloquentUser::class)->create();

        $player = resolve(PlayerRepository::class)->findById($eloquentUser->id);

        $this->assertEquals($eloquentUser->id, $player->getId());
    }
}
