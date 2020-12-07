<?php

namespace Tests\Feature;

use App\Contract;
use App\Lead;
use App\SalesContact;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Tests\TestHelper;

class LetterFeatureTest extends TestCase
{
    use RefreshDatabase, TestHelper;


    public function testCanSendUnassingedIntroLetter()
    {

        $salesContact = factory(SalesContact::class)->create([
            'title' => 'Mr',
            'first_name' => 'Andy',
            'last_name' => 'Parinas',
            'email' => 'andyp@crystaltec.com.au'
        ]);

        $lead = factory(Lead::class)->create([
            'sales_contact_id' => $salesContact->id
        ]);

        $user = factory(User::class)->create([
            'user_type' => User::HEAD_OFFICE,
            'email' => 'ACT@spanline.com.au'
        ]);

        Sanctum::actingAs(
            $user,
            ['*']
        );

        $this->post('api/leads/'. $lead->id .'/letters/unassigned-intro/' . $salesContact->id)
            ->assertStatus(Response::HTTP_OK);

    }


    public function testCanSendAssingedIntroLetter()
    {

        $salesContact = factory(SalesContact::class)->create([
            'title' => 'Mr',
            'first_name' => 'Andy',
            'last_name' => 'Parinas',
            'email' => 'andyp@crystaltec.com.au'
        ]);

        $lead = factory(Lead::class)->create([
            'sales_contact_id' => $salesContact->id
        ]);

        $user = factory(User::class)->create([
            'user_type' => User::HEAD_OFFICE,
            'email' => 'ACT@spanline.com.au'
        ]);

        Sanctum::actingAs(
            $user,
            ['*']
        );

        $this->post('api/leads/'. $lead->id .'letters/assigned-intro/' . $salesContact->id)
            ->assertStatus(Response::HTTP_OK);

    }

    public function testCanSendWelcomeLetter()
    {

        $salesContact = factory(SalesContact::class)->create([
            'title' => 'Mr',
            'first_name' => 'Andy',
            'last_name' => 'Parinas',
            'email' => 'andyp@crystaltec.com.au'
        ]);

        $lead = factory(Lead::class)->create([
            'sales_contact_id' => $salesContact->id
        ]);

        factory(Contract::class)->create([
            'lead_id' => $lead->id
        ]);

        $user = factory(User::class)->create([
            'user_type' => User::HEAD_OFFICE,
            'email' => 'ACT@spanline.com.au'
        ]);

        Sanctum::actingAs(
            $user,
            ['*']
        );

        $this->post('api/contracts/'. $lead->id. '/letters/welcome/')
            ->assertStatus(Response::HTTP_OK);

    }

    public function testCanSendCouncilIntroLetter()
    {

        $salesContact = factory(SalesContact::class)->create([
            'title' => 'Mr',
            'first_name' => 'Andy',
            'last_name' => 'Parinas',
            'email' => 'andyp@crystaltec.com.au'
        ]);

        $user = factory(User::class)->create([
            'user_type' => User::HEAD_OFFICE,
            'email' => 'ACT@spanline.com.au'
        ]);

        Sanctum::actingAs(
            $user,
            ['*']
        );

        $this->post('api/letters/council-intro/' . $salesContact->id)
            ->assertStatus(Response::HTTP_OK);

    }


    public function testCanSendNoCouncilLetter()
    {

        $salesContact = factory(SalesContact::class)->create([
            'title' => 'Mr',
            'first_name' => 'Andy',
            'last_name' => 'Parinas',
            'email' => 'andyp@crystaltec.com.au'
        ]);

        $user = factory(User::class)->create([
            'user_type' => User::HEAD_OFFICE,
            'email' => 'ACT@spanline.com.au'
        ]);

        Sanctum::actingAs(
            $user,
            ['*']
        );

        $this->post('api/letters/no-council/' . $salesContact->id)
            ->assertStatus(Response::HTTP_OK);

    }

    public function testCanSendOutOfCouncilLetter()
    {

        $salesContact = factory(SalesContact::class)->create([
            'title' => 'Mr',
            'first_name' => 'Andy',
            'last_name' => 'Parinas',
            'email' => 'andyp@crystaltec.com.au'
        ]);

        $user = factory(User::class)->create([
            'user_type' => User::HEAD_OFFICE,
            'email' => 'ACT@spanline.com.au'
        ]);

        Sanctum::actingAs(
            $user,
            ['*']
        );

        $this->post('api/letters/out-of-council/' . $salesContact->id)
            ->assertStatus(Response::HTTP_OK);

    }
}
