<?php

namespace App\Http\Controllers\Franchise;

use App\Franchise;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Postcode;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FranchisePostcodeUploadController extends ApiController
{


    public function __invoke(Request $request)
    {
        $postcodeFile = storage_path() . '/app/files/postcodes/postcode-franchise-uploads.csv';

        $file = fopen($postcodeFile, 'r');
        $count = 1;
        while (($data = fgetcsv($file)) !== FALSE) {

            if ($count == 1){
                if ($data[0] != '﻿postcode'|| $data[1] != 'locality' ||
                    $data[2] != 'state' || $data[3] != 'subfranchise' || $data[4] != 'mainfranchise' ){

                    abort(Response::HTTP_BAD_REQUEST, "Invalid File Format");

                }
            }

            if ($count >= 2){

                $post = Postcode::where('pcode', $data[0])
                    ->where('locality', $data[1])
                    ->first();

                $mainFranchise = Franchise::where('franchise_number', $data[4])
                    ->where('parent_id', null)
                    ->first();

                if($mainFranchise != null && $post != null){

                    $subFranchise = Franchise::where('franchise_number', $data[3])
                        ->where('parent_id', $mainFranchise->id)
                        ->first();

                    if ($subFranchise != null){
                        $subFranchise->postcodes()->attach($post->id);
                        $mainFranchise->postcodes()->attach($post->id);
                    }

                }
            }

            $count++;

        }

        return $this->showOne([
            'number_of_franchise_linked' => $count - 2
        ], Response::HTTP_CREATED);


    }
}
