<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\TestHelper;

class FranchisePostcodeUploadFeatureTest extends TestCase
{
    use RefreshDatabase, TestHelper;


    public function testCanCreateFranchisePostcodeFromFile()
    {
        $this->post('api/franchises/uploads');
    }

}
