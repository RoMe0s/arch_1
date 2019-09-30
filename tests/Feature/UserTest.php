<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function testGetUserGuest()
    {
        $response = $this->json('GET', '/api/user');
        $response->assertStatus(401);
    }

    public function testGetUserWrongRequestType()
    {
        $this->createUserAndAuthenticate();

        $response = $this->json('POST', '/api/user');
        $response->assertStatus(405);
    }

    public function testGetUser()
    {
        $user = $this->createUserAndAuthenticate();

        $response = $this->json('GET', '/api/user');
        $response->assertStatus(200)
            ->assertExactJson($user->toArray());
    }
}
