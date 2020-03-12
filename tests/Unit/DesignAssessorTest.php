<?php

namespace Tests\Unit;

use App\DesignAssessor;
use App\JobType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


class DesignAssessorTest extends TestCase
{
    use RefreshDatabase;

    public function testDesignAssessorHasJobTypes()
    {
        $assessor = factory(DesignAssessor::class)->create();
        factory(JobType::class, 3)->create(['design_assessor_id' => $assessor->id]);

        $this->assertContainsOnlyInstancesOf(JobType::class, $assessor->jobTypes);
        $this->assertCount(3, $assessor->jobTypes);

    }

    public function testDesignAssessorHasFullNameAttribute()
    {
        $firstName = 'Sheldon';
        $lastName = 'Cooper';
        $fullName = $firstName . ' ' . $lastName;

        $assessor = factory(DesignAssessor::class)->create([
            'first_name' => $firstName,
            'last_name' => $lastName
        ]);

        $this->assertEquals($fullName, $assessor->full_name);

    }
}
