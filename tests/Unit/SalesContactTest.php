<?php

namespace Tests\Unit;

use App\Lead;
use App\SalesContact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SalesContactTest extends TestCase
{
    use RefreshDatabase;

    public function testSalesContactHasFullNameAttribute()
    {
        $firstName = 'Sheldon';
        $lastName = 'Cooper';
        $fullName = $firstName . ' ' . $lastName;

        $salesContact = factory(SalesContact::class)->create([
            'first_name' => $firstName,
            'last_name' => $lastName
        ]);


        $this->assertEquals($fullName, $salesContact->full_name);
    }

    public function testSalesContactHasTitledFullNameAttribute(){

        $title = 'Dr.';
        $firstName = 'Sheldon';
        $lastName = 'Cooper';
        $titledFullName = $title . ' ' . $firstName . ' ' . $lastName;

          $salesContact = factory(SalesContact::class)->create([
              'title' => $title,
              'first_name' => $firstName,
              'last_name' => $lastName
          ]);

        $this->assertEquals($titledFullName, $salesContact->titled_full_name);
    }

    public function testSalesContactHasLeads()
    {
        $salesContact = factory(SalesContact::class)->create();
        factory(Lead::class, 3)->create(['sales_contact_id' => $salesContact->id]);

        $this->assertContainsOnlyInstancesOf(Lead::class, $salesContact->leads);
        $this->assertCount(3, $salesContact->leads);
    }

}
