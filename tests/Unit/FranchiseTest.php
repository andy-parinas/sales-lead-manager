<?php

namespace Tests\Unit;


use App\Franchise;
use App\Lead;
use App\SalesStaff;
use App\TradeStaff;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


class FranchiseTest extends TestCase
{
    use RefreshDatabase;


    public function testFranchiseHasParentFranchise()
    {
        $main = factory(Franchise::class)->create();
        $sub = factory(Franchise::class)->create(['parent_id' => $main->id]);


        $this->assertEquals($main->name, $sub->parent->name);

    }


    public function testFranchiseHasChildrenFranchise()
    {
        $main = factory(Franchise::class)->create();
        factory(Franchise::class, 3)->create(['parent_id' => $main->id]);


        $this->assertContainsOnlyInstancesOf(Franchise::class, $main->children);
        $this->assertCount(3, $main->children);
    }


    public function testFranchiseBelongsToManyUser()
    {
        $this->withoutExceptionHandling();

        $franchise = factory(Franchise::class)->create();
        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();

        $franchise->users()->attach([$user1->id, $user2->id]);


        $this->assertContainsOnlyInstancesOf(User::class, $franchise->users);
        $this->assertCount(2, $franchise->users);

    }

    public function testFranchiseHasLeads()
    {
        $branch = factory(Franchise::class)->create();
        factory(Lead::class, 3)->create(['franchise_id' => $branch->id]);

        $this->assertContainsOnlyInstancesOf(Lead::class, $branch->leads);
        $this->assertCount(3, $branch->leads);
    }

    public function testFranchiseHasTradeStaffs()
    {
        $branch = factory(Franchise::class)->create();
        factory(TradeStaff::class, 3)->create(['franchise_id' => $branch->id]);

        $this->assertContainsOnlyInstancesOf(TradeStaff::class, $branch->tradeStaffs);
    }

    public function testFranchiseHasSalesStaffs()
    {
        $branch = factory(Franchise::class)->create();
        factory(SalesStaff::class, 3)->create(['franchise_id' => $branch->id]);

        $this->assertContainsOnlyInstancesOf(SalesStaff::class, $branch->salesStaffs);
    }

}
