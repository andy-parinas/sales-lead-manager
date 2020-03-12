<?php

namespace Tests\Unit;

use App\Branch;
use App\Lead;
use App\SalesStaff;
use App\TradeStaff;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


class BranchTest extends TestCase
{
    use RefreshDatabase;


    public function testBranchHasParentBranch()
    {
        $main = factory(Branch::class)->create();
        $sub = factory(Branch::class)->create(['parent_id' => $main->id]);


        $this->assertEquals($main->name, $sub->parent->name);

    }

    /**
     * @test
     */
    public function testBranchHasChildrenBranch()
    {
        $main = factory(Branch::class)->create();
        factory(Branch::class, 3)->create(['parent_id' => $main->id]);


        $this->assertContainsOnlyInstancesOf(Branch::class, $main->children);
        $this->assertCount(3, $main->children);
    }

    /**
     * @test
     */
    public function testBranchHasUsers()
    {
        $this->withoutExceptionHandling();

        $branch = factory(Branch::class)->create();
        factory(User::class, 3)->create(['branch_id' => $branch->id]);


        $this->assertContainsOnlyInstancesOf(User::class, $branch->users);
        $this->assertCount(3, $branch->users);

    }

    public function testBranchHasLeads()
    {
        $branch = factory(Branch::class)->create();
        factory(Lead::class, 3)->create(['branch_id' => $branch->id]);

        $this->assertContainsOnlyInstancesOf(Lead::class, $branch->leads);
        $this->assertCount(3, $branch->leads);
    }

    public function testBranchHasTradeStaffs()
    {
        $branch = factory(Branch::class)->create();
        factory(TradeStaff::class, 3)->create(['branch_id' => $branch->id]);

        $this->assertContainsOnlyInstancesOf(TradeStaff::class, $branch->tradeStaffs);
    }

    public function testBranchHasSalesStaffs()
    {
        $branch = factory(Branch::class)->create();
        factory(SalesStaff::class, 3)->create(['branch_id' => $branch->id]);

        $this->assertContainsOnlyInstancesOf(SalesStaff::class, $branch->salesStaffs);
    }

}
