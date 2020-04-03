<?php

namespace App\Services\Interfaces;

use App\Franchise;
use App\SalesContact;

interface PostcodeServiceInterface 
{

    public function checkSalesContactPostcode(SalesContact $salesContact, Franchise $franchise);

    public function checkParentPostcodes(Franchise $franchise, Array $postcodes);
    
    
}