<?php

namespace Tests\Unit;

use App\Document;
use App\Lead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentTest extends TestCase
{
    use RefreshDatabase;

    public function testDocumentBelongsToLead()
    {
        $lead = factory(Lead::class)->create();
        $document = factory(Document::class)->create(['lead_id' => $lead->id ]);


        $this->assertInstanceOf(Lead::class, $document->lead);


    }

}
