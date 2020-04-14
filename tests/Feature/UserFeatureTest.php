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


    public function testCanListUserByHeadOffice()
    {

        $this->withoutExceptionHandling();

        factory(User::class, 30)->create();

        $this->authenticateHeadOfficeUser();

        $this->get('api/users')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(15, 'data');
    }


    public function testCanNotListUserByNonHeadOffice()
    {
        
        // $this->withoutExceptionHandling();

        factory(User::class, 30)->create();

        $this->authenticateFranchiseAdmin();

        $this->get('api/users')
            ->assertStatus(Response::HTTP_FORBIDDEN);


    }


    public function testCanShowUserByHeadOffice()
    {

        $this->authenticateHeadOfficeUser();


        $user = factory(User::class)->create();

        $response = $this->get('api/users/' . $user->id );
        $result = json_decode($response->content())->data;

        $response->assertStatus(Response::HTTP_OK);
        $this->assertEquals($user->username, $result->username);

    }

    public function testCanNotShowUserByNonHeadOffice()
    {
        $user = factory(User::class)->create();

        $this->authenticateStaffUser();

        $this->get('api/users/' . $user->id )
            ->assertStatus(Response::HTTP_FORBIDDEN);

    }


    public function testCanUpdateUserByHeadOffice()
    {
        $user = factory(User::class)->create();

        $this->authenticateHeadOfficeUser();

        $updates = [
            'username' => 'update',
            'name' => 'update',
            'email' => 'update@email.com' 
        ];


        $this->put('api/users/' . $user->id , $updates)
            ->assertStatus(Response::HTTP_OK);

        $user->refresh();

        $this->assertEquals('update', $user->username);
        $this->assertEquals('update', $user->name);
        $this->assertEquals('update@email.com', $user->email);

    }

}
