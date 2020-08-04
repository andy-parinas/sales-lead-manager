<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Tests\TestHelper;

class ReportFeatureTest extends TestCase
{

    use RefreshDatabase, TestHelper;

//    public function setUp(): void
//    {
//        parent::setUp();
//
//        Artisan::call('db:seed', ['--class' => 'FranchiseSeeder']);
//        Artisan::call('db:seed', ['--class' => 'ProductSeeder']);
//        Artisan::call('db:seed', ['--class' => 'LeadSourceSeeder']);
//        Artisan::call('db:seed', ['--class' => 'LeadTestSeeder']);
//
//    }

    public function testSalesSummaryReport()
    {

        $this->withoutExceptionHandling();

        $this->authenticateHeadOfficeUser();

        $response = $this->get('api/reports/sales-summary?start_date=2020-01-01&end_date=2020-07-19');

        dd(json_decode($response->content()));

        $response->assertStatus(Response::HTTP_OK);


    }



}
