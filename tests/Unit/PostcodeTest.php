<?php

namespace Tests\Unit;

use App\Franchise;
use App\Postcode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostcodeTest extends TestCase
{
    use RefreshDatabase;


    public function testPostcodeBelongsToManyFranchise()
    {
        
        $postcode = factory(Postcode::class)->create();
        $franchise1 = factory(Franchise::class)->create();
        $franchise2 = factory(Franchise::class)->create();


        $postcode->franchises()->attach([$franchise1->id, $franchise2->id]);

        $this->assertContainsOnlyInstancesOf(Franchise::class, $postcode->franchises);
        $this->assertCount(2, $postcode->franchises);

    }

}
