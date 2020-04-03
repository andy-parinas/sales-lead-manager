<?php


namespace App\Services;

use App\Franchise;
use App\Lead;
use App\SalesContact;
use App\Services\Interfaces\PostcodeServiceInterface;

class PostcodeService implements PostcodeServiceInterface
{

    public function checkSalesContactPostcode(SalesContact $salesContact, Franchise $franchise)
    {
        if($franchise->postcodes->contains('pcode', $salesContact->postcode))
        {
            return Lead::INSIDE_OF_FRANCHISE;
        }
        else {

            return Lead::OUTSIDE_OF_FRANCHISE;
        }
    }
}