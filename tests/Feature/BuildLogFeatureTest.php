<?php

namespace Tests\Feature;

use App\BuildLog;
use App\Construction;
use App\TradeStaff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Tests\TestHelper;

class BuildLogFeatureTest extends TestCase
{

    use RefreshDatabase, TestHelper;


    public function testCanListBuildLogs()
    {

        $construction = factory(Construction::class)->create();

        factory(BuildLog::class, 5)->create([
            'construction_id' => $construction->id
        ]);

        $this->authenticateHeadOfficeUser();

        $response = $this->get('api/constructions/' .$construction->id . '/build-logs');

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(5, 'data');

    }


    public function testCanCreateBuildLog()
    {

        $this->withoutExceptionHandling();

        $construction = factory(Construction::class)->create();
        $tradeStaff = factory(TradeStaff::class)->create();

        $data = [
            'work_date' => '2020-08-29',
            'time_spent' => 7.5,
            'hourly_rate' => 25.50,
            'comments' => 'No Comment',
            'trade_staff_id' => $tradeStaff->id,
        ];


        $this->post('api/constructions/' .$construction->id . '/build-logs', $data)
            ->assertStatus(Response::HTTP_CREATED);

        $this->assertCount(1, BuildLog::all());

    }

}
