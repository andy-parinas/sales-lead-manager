<?php

namespace Tests\Feature;

use App\Franchise;
use App\Lead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Tests\TestHelper;

class FranchiseLeadFeatureTest extends TestCase
{

    use RefreshDatabase, TestHelper;


    public function testCanListLeadsUnderUsersFranchise()
    {
        $franchise = factory(Franchise::class)->create();
        factory(Lead::class, 10)->create(['franchise_id' => $franchise->id]);

        $user = $this->createStaffUser();
        $user->franchises()->attach($franchise->id);

        Sanctum::actingAs(
            $user,
            ['*']
        );

        $this->get('api/franchises/' . $franchise->id . '/leads')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(10, 'data');

    }

    public function testCanNotListLeadsOutsideUsersFranchise()
    {

        $franchise = factory(Franchise::class)->create();
        factory(Lead::class, 10)->create(['franchise_id' => $franchise->id]);

        $user = $this->createStaffUser();
        $user->franchises()->attach($franchise->id);

        Sanctum::actingAs(
            $this->createStaffUser(),
            ['*']
        );

        $this->get('api/franchises/' . $franchise->id . '/leads')
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }


    public function testCanListAllLeadByHeadOffice()
    {
        
        $franchise = factory(Franchise::class)->create();
        factory(Lead::class, 10)->create(['franchise_id' => $franchise->id]);

        $user = $this->createStaffUser();
        $user->franchises()->attach($franchise->id);

        Sanctum::actingAs(
            $this->createHeadOfficeUser(),
            ['*']
        );

        $this->get('api/franchises/' . $franchise->id . '/leads')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(10, 'data');

    }





}
