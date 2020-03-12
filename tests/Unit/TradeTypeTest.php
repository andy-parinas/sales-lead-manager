<?php

namespace Tests\Unit;

use App\TradeStaff;
use App\TradeType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TradeTypeTest extends TestCase
{

    use RefreshDatabase;

    public function testTradeTypeHasTradeStaffs()
    {
        $tradeType = factory(TradeType::class)->create();
        factory(TradeStaff::class, 3)->create(['trade_type_id' => $tradeType]);

        $this->assertContainsOnlyInstancesOf(TradeStaff::class, $tradeType->tradeStaffs);
        $this->assertCount(3, $tradeType->tradeStaffs);
    }
}
