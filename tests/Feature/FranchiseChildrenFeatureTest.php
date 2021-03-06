<?php

namespace Tests\Feature;

use App\Franchise;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class FranchiseChildrenFeatureTest extends TestCase
{

    use RefreshDatabase;

    public function testCanListChildrenFranchiseByFranchiseAdminFranchise()
    {

        $parent = factory(Franchise::class)->create();
        factory(Franchise::class, 5)->create(['parent_id' => $parent->id]);

        $user = factory(User::class)->create(['user_type' => User::FRANCHISE_ADMIN]);
        $user->franchises()->attach($parent->id);

        Sanctum::actingAs(
            $user,
            ['*']
        );


        $this->get('api/franchises/' . $parent->id . '/children')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(5, 'data');

    }

    public function testCanNotListChildrenOutsideFranchiseAdminFranchise()
    {
        $parent = factory(Franchise::class)->create();
        factory(Franchise::class, 5)->create(['parent_id' => $parent->id]);

        $user = factory(User::class)->create(['user_type' => User::FRANCHISE_ADMIN]);
        $user->franchises()->attach($parent->id);

        Sanctum::actingAs(
            factory(User::class)->create(['user_type' => User::FRANCHISE_ADMIN]),
            ['*']
        );


        $this->get('api/franchises/' . $parent->id . '/children')
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testCanListChildrenFranchiseByHeadOffice()
    {
        $parent = factory(Franchise::class)->create();
        factory(Franchise::class, 5)->create(['parent_id' => $parent->id]);

        $user = factory(User::class)->create(['user_type' => User::FRANCHISE_ADMIN]);
        $user->franchises()->attach($parent->id);

        Sanctum::actingAs(
            factory(User::class)->create(['user_type' => User::HEAD_OFFICE]),
            ['*']
        );


        $this->get('api/franchises/' . $parent->id . '/children')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(5, 'data');
    }

    public function testCanStoreChildrenFranchiseInParentByHeadOffice()
    {

        $this->withoutExceptionHandling();

        $parent = factory(Franchise::class)->create();

        $childData = factory(Franchise::class)->raw();

        Sanctum::actingAs(
            factory(User::class)->create(['user_type' => User::HEAD_OFFICE]),
            ['*']
        );

        $this->post('api/franchises/' . $parent->id . '/children', $childData)
            ->assertStatus(Response::HTTP_CREATED);

        $this->assertCount(1, Franchise::find($parent->id)->children);


    }

    public function testCanNotStoreChildrenFranchiseInParentByNonHeadOffice()
    {

        $parent = factory(Franchise::class)->create();

        $childData = factory(Franchise::class)->raw();

        Sanctum::actingAs(
            factory(User::class)->create(['user_type' => User::FRANCHISE_ADMIN]),
            ['*']
        );

        $this->post('api/franchises/' . $parent->id . '/children', $childData)
            ->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertCount(0, Franchise::find($parent->id)->children);
    }

}
