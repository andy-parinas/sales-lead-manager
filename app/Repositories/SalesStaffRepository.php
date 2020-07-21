<?php


namespace App\Repositories;


use App\SalesStaff;
use Illuminate\Support\Facades\DB;

class SalesStaffRepository implements Interfaces\SalesStafRepositoryInterface
{

    public function getAll(array $params)
    {
        if(key_exists('search', $params) && key_exists('on', $params))
        {
            return SalesStaff::with('franchise')->where($params['on'], 'LIKE', '%' . $params['search'] . '%')
                ->orderBy($params['column'], $params['direction'])
                ->paginate($params['size']);
        }

        return SalesStaff::with('franchise')->orderBy($params['column'], $params['direction'])
            ->paginate($params['size']);
    }


    public function searchAll($search)
    {

        return DB::table('sales_staff')
            ->select('id',
                'first_name',
                'last_name',
                'email',
                'status',
                'contact_number',
                'franchise_id'
            )
            ->where('status', 'active')
            ->where(function ($query) use ($search){
                $query->where('first_name','LIKE', '%' . $search . '%' )
                    ->orWhere('last_name','LIKE', '%' . $search . '%' )
                    ->orWhere('email', 'LIKE', '%' . $search . '%');
            })
            ->get();


    }
}
