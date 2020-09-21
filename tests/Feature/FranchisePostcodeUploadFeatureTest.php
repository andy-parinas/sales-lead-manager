<?php

namespace Tests\Feature;

use App\Franchise;
use App\Postcode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Tests\TestHelper;

class FranchisePostcodeUploadFeatureTest extends TestCase
{
    use RefreshDatabase, TestHelper;


    public function testCanCreateFranchisePostcodeFromFile()
    {

        $this->withoutExceptionHandling();

        $postcodeFile = storage_path() . '/app/files/postcodes/postcode-franchise-uploads.csv';

        $file = fopen($postcodeFile, 'r');
        $count = 1;
        while (($data = fgetcsv($file)) !== FALSE) {
            if($count >= 2){

                factory(Postcode::class)->create([
                    'pcode' => $data[0],
                    'locality' => $data[1],
                    'state' => $data[2]
                ]);

                $mainFranchise = Franchise::where('franchise_number', $data[4])->first();

                if($mainFranchise == null){
                    $mainFranchise = factory(Franchise::class)->create([
                        'franchise_number' => $data[4]
                    ]);
                }

                $subFranchise = Franchise::where('franchise_number', $data[3])
                                ->where('parent_id', $mainFranchise->id)
                                ->first();

                if($subFranchise == null){
                    print "SubFranchises Created\n";
                    factory(Franchise::class)->create([
                        'franchise_number' => $data[3],
                        'parent_id' => $mainFranchise->id
                    ]);
                }


            }
            $count++;
        }

        $upload = UploadedFile::createFromBase(
            (new \Symfony\Component\HttpFoundation\File\UploadedFile(
                $postcodeFile,
                'postcode-franchise-uploads.csv',
                'text/csv',
                null,
                true
            ))
        );

        $data = [
            'file' => $upload,
            'title' => 'postcode-franchise-uploads.csv',
            'type' => 'text/csv'
        ];
//
//        $data = [
//            'file' => 'required',
//            'title' => 'required',
//            'type' => 'required'
//        ];

        $this->authenticateHeadOfficeUser();

        $this->post('api/franchises/uploads', $data)
            ->assertStatus(Response::HTTP_CREATED);


        $subFranchises = Franchise::where('parent_id', '<>', null)->get();

//        dd($subFranchises);

        $subFranchises->each(function ($subFranchise) use ($count) {
           $this->assertCount($count - 2, $subFranchise->postcodes);
        });


    }

}
