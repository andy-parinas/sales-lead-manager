<?php

namespace Tests\Feature;

use App\TradeType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Tests\TestHelper;

class TradeTypeFeatureTest extends TestCase
{

    use RefreshDatabase, TestHelper;

    public function testCanCreateTradeTypeByHeadOffice()
    {
        $data = ['name' => 'Trade Type 1'];

        $this->authenticateHeadOfficeUser();

        $this->post('api/trade-types', $data)
            ->assertStatus(Response::HTTP_CREATED);

        $this->assertCount(1, TradeType::all());


    }

    public function testCanNoCreateTradeTypeByNonHeadOffice()
    {
        $data = ['name' => 'Trade Type 1'];

        $this->authenticateStaffUser();
        $this->post('api/trade-types', $data)
            ->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertCount(0, TradeType::all());

        $this->authenticateFranchiseAdmin();
        $this->post('api/trade-types', $data)
            ->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertCount(0, TradeType::all());
    }

    public function testCanListTradeTypeByAuthenticatedUsers()
    {
        factory(TradeType::class, 5)->create();

        $this->authenticateStaffUser();

        $this->get('api/trade-types')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(5, 'data');
    }

    public function testCanNotListTradeTypeByNonAuthenticatedUser()
    {
        factory(TradeType::class, 5)->create();

        $this->get('api/trade-types')
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testCanUpdateTradeTypeByHeadOffice()
    {
        $tradeType = factory(TradeType::class)->create();

        $this->authenticateHeadOfficeUser();

        $this->put('api/trade-types/' . $tradeType->id, ['name' => 'update'])
            ->assertStatus(Response::HTTP_OK);

        $tradeType->refresh();

        $this->assertEquals('update', $tradeType->name);
    }

    public function testCanNotUpdateTradeTypeByNonHeadOffice()
    {
        $tradeType = factory(TradeType::class)->create();

        $this->authenticateStaffUser();
        $this->put('api/trade-types/' . $tradeType->id, ['name' => 'update'])
            ->assertStatus(Response::HTTP_FORBIDDEN);

        $this->authenticateFranchiseAdmin();
        $this->put('api/trade-types/' . $tradeType->id, ['name' => 'update'])
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testCanDeleteTradeTypeByHeadOffice()
    {
        $tradeType = factory(TradeType::class)->create();

        $this->authenticateHeadOfficeUser();

        $this->delete('api/trade-types/' . $tradeType->id)
            ->assertStatus(Response::HTTP_OK);

        $this->assertCount(0, TradeType::all());
    }

    public function testCanNotDeleteTradeTypeByNonHeadOffice()
    {
        $tradeType = factory(TradeType::class)->create();

        $this->authenticateStaffUser();
        $this->delete('api/trade-types/' . $tradeType->id)
            ->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertCount(1, TradeType::all());

        $this->authenticateFranchiseAdmin();
        $this->delete('api/trade-types/' . $tradeType->id)
            ->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertCount(1, TradeType::all());
    }
}
