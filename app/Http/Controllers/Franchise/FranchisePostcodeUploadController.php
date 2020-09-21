<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FranchisePostcodeUploadController extends Controller
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

                dump($data);


            }

            $count++;

        }

    }
}
