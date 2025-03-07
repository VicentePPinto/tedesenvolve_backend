<?php

namespace Tests\Feature\API;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_users_get_endpoint(): void
    {
        $response = $this->getJson('/api/users');

        $response->assertStatus(200);
        $response->assertJsonCount(1);
    }
}
