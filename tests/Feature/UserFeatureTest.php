<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Tests\TestHelper;

class UserFeatureTest extends TestCase
{

    use RefreshDatabase, TestHelper, WithFaker;

    public function testCanCreateUserByHeadOffice()
    {

        $this->withoutExceptionHandling();

        $this->authenticateHeadOfficeUser();

        //For assertion
        $username = $this->faker->userName;

        $userData = [
            'username' => $username,
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => 'password',
            'password_confirmation' => 'password'
        ];

        
        $response = $this->post('api/users', $userData);
        $response->assertStatus(Response::HTTP_CREATED);

        $result = json_decode($response->content())->data;

        $this->assertCount(2, User::all());
        $this->assertEquals($username, User::find($result->id)->username);

    }

    public function testCanNotCreateUsersByNonHeadOffice()
    {

        // $this->withoutExceptionHandling();
        //For assertion
        $username = $this->faker->userName;

        $userData = [
            'username' => $username,
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => 'password',
            'password_confirmation' => 'password'
        ];

        $this->authenticateStaffUser();
        
        $this->post('api/users', $userData)
            ->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertCount(1, User::all()); //No extra user is created except for the one used in authentication

        $this->authenticateFranchiseAdmin();

        $this->post('api/users', $userData)
            ->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertCount(2, User::all()); //No extra user is created except for the one used in authentication


    }

    public function testCanNotCreateUserByUnAuthenticatedUser()
    {
        
        $userData = [
            'username' => $this->faker->userName,
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => 'password',
            'password_confirmation' => 'password'
        ];

        $this->post('api/users', $userData)
        ->assertStatus(Response::HTTP_UNAUTHORIZED);
        $this->assertCount(0, User::all());
    }

}
