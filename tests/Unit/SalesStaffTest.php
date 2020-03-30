<?php

namespace Tests\Unit;

use App\Franchise;
use App\SalesStaff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SalesStaffTest extends TestCase
{

    use RefreshDatabase;

    public function testSalesStaffBelongsToFranchise()
    {
        $branch = factory(Franchise::class)->create();
        $sales = factory(SalesStaff::class)->create(['franchise_id' => $branch->id]);

        $this->assertInstanceOf(Franchise::class, $sales->franchise);
    }
}
