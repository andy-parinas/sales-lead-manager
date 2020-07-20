<?php

namespace App\Repositories;

use App\Repositories\Interfaces\SalesContactRepositoryInterface;
use Illuminate\Support\Facades\DB;

class SalesContactRepository  implements SalesContactRepositoryInterface
{

    public function sortAndPaginate(Array $params)
    {
        $query =  DB::table('sales_contacts')
                    ->join('postcodes','postcodes.id', '=', 'sales_contacts.postcode_id')
                    ->select(
                        "sales_contacts.id",
                        'first_name',
                        'last_name',
                        'contact_number',
                        'customer_type',
                        'street1',
                        'street2',
                        'status',
                        'email',
                        'email2',
                        'postcodes.id as postcodeId',
                        'postcodes.locality as suburb',
                        'postcodes.state',
                        'postcodes.pcode as postcode'
                        );


        if(key_exists('search', $params) && key_exists('on', $params))
        {
            $query = $query
                    ->where($params['on'], 'LIKE', '%' . $params['search'] . '%');
//                    ->orderBy('sales_contacts.' . $params['column'], $params['direction'])
//                    ->paginate($params['size']);
        }

        if(key_exists('column', $params) && ($params['column'] == 'pcode' || $params['column'] == 'state' || $params['column'] == 'locality' ) ){

            $query = $query->orderBy('postcodes.' . $params['column'], $params['direction']);

        }else {
            $query = $query->orderBy('sales_contacts.' . $params['column'], $params['direction']);
        }

//        return $query->orderBy('sales_contacts.' . $params['column'], $params['direction'])
//                    ->paginate($params['size']);

        return $query->paginate($params['size']);

    }


    public function simpleSearch(Array $params)
    {
        $query =  DB::table('sales_contacts')
            ->join('postcodes','postcodes.id', '=', 'sales_contacts.postcode_id')
            ->select("sales_contacts.id",
                'first_name',
                'last_name',
                'contact_number',
                'customer_type',
                'street1',
                'street2',
                'status',
                'email',
                'email2',
                'postcodes.id as postcodeId',
                'postcodes.locality as suburb',
                'postcodes.state',
                'postcodes.pcode as postcode'
            );

        if(key_exists('search', $params))
        {
            $query =  $query
                ->where('first_name', 'LIKE',  $params['search'] . '%')
                ->orWhere('last_name', 'LIKE',  $params['search'] . '%')
                ->orWhere('email', 'LIKE',  '%'. $params['search'] . '%');


            if(key_exists('column', $params) && ($params['column'] == 'pcode' || $params['column'] == 'state' || $params['column'] == 'locality' ) ){

                $query = $query->orderBy('postcodes.' . $params['column'], $params['direction']);

            }else {
                $query = $query->orderBy('sales_contacts.' . $params['column'], $params['direction']);
            }

            return $query->paginate($params['size']);
        }

        return $query->paginate($params['size']);
    }
}
