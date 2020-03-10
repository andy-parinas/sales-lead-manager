<?php

namespace Tests\Unit;

use App\Branch;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;


    public function testUserBelongsToABranch()
    {
        $branch = factory(Branch::class)->create();
        $user = factory(User::class)->create(['branch_id' => $branch->id]);

        $this->assertInstanceOf(Branch::class, $user->branch);
        $this->assertEquals($branch->name, $user->branch->name);

    }



}
