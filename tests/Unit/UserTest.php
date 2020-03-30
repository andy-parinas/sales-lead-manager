<?php

namespace Tests\Unit;

use App\Franchise;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;


    // public function testUserBelongsToAFranchise()
    // {
    //     $branch = factory(Franchise::class)->create();
    //     $user = factory(User::class)->create(['franchise_id' => $branch->id]);

    //     $this->assertInstanceOf(Franchise::class, $user->branch);
    //     $this->assertEquals($branch->name, $user->branch->name);

    // }



}
