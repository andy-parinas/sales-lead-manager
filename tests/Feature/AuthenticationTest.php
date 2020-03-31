<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;


    public function testCanAccessProtectedRouteByAuthenticatedUser()
    {
        Sanctum::actingAs(
            factory(User::class)->create(),
            ['*']
        );

        $response = $this->get('api/auth-test', ['Content-Type' => 'application/json', 'Accept' => 'application/json']);

        $response->assertStatus(Response::HTTP_ACCEPTED);

    }

    public function testCanNotAccessProtectedRouteByNonAuthenticatedUser()
    {
        $response = $this->get('api/auth-test', ['Content-Type' => 'application/json', 'Accept' => 'application/json']);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }
}
