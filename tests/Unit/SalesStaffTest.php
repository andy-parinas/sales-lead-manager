<?php

namespace Tests\Unit;

use App\Branch;
use App\SalesStaff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SalesStaffTest extends TestCase
{

    use RefreshDatabase;

    public function testSalesStaffBelongsToBranch()
    {
        $branch = factory(Branch::class)->create();
        $sales = factory(SalesStaff::class)->create(['branch_id' => $branch->id]);

        $this->assertInstanceOf(Branch::class, $sales->branch);
    }
}
