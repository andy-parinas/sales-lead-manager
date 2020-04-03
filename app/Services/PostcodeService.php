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


    /**
     * This methods will return only the postcodes that is within the parent postcodes
     * Any out of bounds postcodes will just be ignored
     * @param Franchise
     * @param Array
     * @return Array
     */
    public function checkParentPostcodes(Franchise $franchise, Array $postcodes): Array
    {

        $postcodesData = [];

        if($franchise->parent !== null){

            $parentPostcodes = $franchise->parent->postcodes->pluck('id')->toArray();
            
            foreach ($postcodes as $postcode) {

                if(in_array($postcode, $parentPostcodes)){

                   array_push($postcodesData, $postcode);
                }
            }
         
        }else {
            $postcodesData = array_merge($postcodesData, $postcodes);
        }

        return $postcodesData;
    }
}