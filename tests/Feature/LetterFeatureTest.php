<?php

namespace Tests\Feature;

use App\SalesContact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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

        $this->authenticateHeadOfficeUser();

        $this->post('api/letters/unassigned-intro/' . $salesContact->id)
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

        $this->authenticateHeadOfficeUser();

        $this->post('api/letters/assigned-intro/' . $salesContact->id)
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

        $this->authenticateHeadOfficeUser();

        $this->post('api/letters/welcome/' . $salesContact->id)
            ->assertStatus(Response::HTTP_OK);

    }
}
