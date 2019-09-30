<?php

namespace Tests;

use Game\Infrastructure\Persistance\Eloquent\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function createUserAndAuthenticate(string $driver = 'api'): User
    {
        $user = factory(User::class)->create();

        $this->actingAs($user, $driver);

        return $user;
    }
}
