<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

abstract class TestCase extends BaseTestCase
{
    //
    protected function getAuthToken($user = null)
    {
        $user = $user ?? User::factory()->create(); // Cria um usuário caso não seja passado

        return JWTAuth::fromUser($user); // Gera o token JWT
    }
}
