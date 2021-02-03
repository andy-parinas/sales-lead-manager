<?php


namespace App\Repositories;


use App\SalesStaff;
use Illuminate\Support\Facades\DB;

class SalesStaffRepository implements Interfaces\SalesStafRepositoryInterface
{

    public function getAll(array $params)
    {

        $query = DB::table('sales_staff')
            ->select('sales_staff.id', 'first_name', 'last_name', 'email', 'contact_number', 'status', 'franchises.franchise_number')
            ->leftJoin('franchise_sales_staff', 'franchise_sales_staff.sales_staff_id', '=', 'sales_staff.id')
            ->leftJoin('franchises', 'franchises.id', '=','franchise_sales_staff.franchise_id' );

        if(key_exists('search', $params) && key_exists('on', $params))
        {

            $query = $query->where($params['on'], 'LIKE', '%' . $params['search'] . '%');

        }

        return $query ->orderBy($params['column'], $params['direction'])
            ->paginate($params['size']);

//        if(key_exists('search', $params) && key_exists('on', $params))
//        {
//            return SalesStaff::with('franchises')->where($params['on'], 'LIKE', '%' . $params['search'] . '%')
//                ->orderBy($params['column'], $params['direction'])
//                ->paginate($params['size']);
//        }
//
//        return SalesStaff::with('franchises')->orderBy($params['column'], $params['direction'])
//            ->paginate($params['size']);
    }


    public function searchAll($search)
    {

        return DB::table('sales_staff')
            ->select('id',
                'first_name',
                'last_name',
                'email',
                'status',
                'contact_number'
            )
            ->where('status', 'active')
            ->where(function ($query) use ($search){
                $query->where('first_name','LIKE', '%' . $search . '%' )
                    ->orWhere('last_name','LIKE', '%' . $search . '%' )
                    ->orWhere('email', 'LIKE', '%' . $search . '%');
            })
            ->get();


    }

    public function getAllByFranchise(array $franchiseIds, array $params)
    {


        $query = DB::table('sales_staff')
            ->select('sales_staff.id', 'first_name', 'last_name', 'email', 'contact_number', 'status', 'franchises.franchise_number')
            ->leftJoin('franchise_sales_staff', 'franchise_sales_staff.sales_staff_id', '=', 'sales_staff.id')
            ->leftJoin('franchises', 'franchises.id', '=','franchise_sales_staff.franchise_id' )
            ->whereIn('franchises.id', $franchiseIds);

        if(key_exists('search', $params) && key_exists('on', $params))
        {

            $query = $query->where($params['on'], 'LIKE', '%' . $params['search'] . '%');

        }

        return $query ->orderBy($params['column'], $params['direction'])
            ->paginate($params['size']);



//        if(key_exists('search', $params) && key_exists('on', $params))
//        {
//            return SalesStaff::with('franchises')->where($params['on'], 'LIKE', '%' . $params['search'] . '%')
//                ->where('status', SalesStaff::ACTIVE)
//                ->whereIn('franchises.id', $franchiseIds)
//                ->orderBy($params['column'], $params['direction'])
//                ->paginate($params['size']);
//        }
//
//        return SalesStaff::with('franchises')
//            ->where('status', SalesStaff::ACTIVE)
//            ->whereIn('franchises.id', $franchiseIds)
//            ->orderBy($params['column'], $params['direction'])
//            ->paginate($params['size']);
    }

    public function searchAllByFranchise(array $franchiseIds, $search)
    {
        return DB::table('sales_staff')
            ->select('sales_staff.id',
                'first_name',
                'last_name',
                'email',
                'status',
                'contact_number'
            )
            ->join("franchise_sales_staff", "sales_staff.id", '=', "franchise_sales_staff.sales_staff_id")
            ->join("franchises", "franchises.id", '=', "franchise_sales_staff.franchise_id")
            ->where('status', 'active')
            ->where(function ($query) use ($search){
                $query->where('first_name','LIKE', '%' . $search . '%' )
                    ->orWhere('last_name','LIKE', '%' . $search . '%' )
                    ->orWhere('email', 'LIKE', '%' . $search . '%');
            })
            ->whereIn('franchises.id', $franchiseIds)
            ->get();
    }
}
