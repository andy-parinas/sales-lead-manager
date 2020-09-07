<?php


namespace App\Repositories;


use App\TradeStaff;
use Illuminate\Support\Facades\DB;

class TradeStaffRepository implements Interfaces\TradeStaffRepositoryInterface
{

    public function getAll(array $params)
    {
        $query = DB::table('trade_staff')
            ->join('franchises', 'franchises.id', '=', 'trade_staff.franchise_id')
            ->join('trade_types', 'trade_types.id', '=', 'trade_staff.trade_type_id')
            ->select('trade_staff.id',
                'trade_staff.franchise_id',
                'trade_staff.trade_type_id',
                'trade_staff.first_name',
                'trade_staff.last_name',
                'trade_staff.email',
                'trade_staff.contact_number',
                'trade_staff.company',
                'trade_staff.abn',
                'trade_staff.builders_license',
                'trade_staff.status',
                'franchises.franchise_number',
                'trade_types.name as trade_type',
                'trade_staff.created_at as created_at'
            );



        if(key_exists('search', $params) && key_exists('on', $params))
        {

            if($params['on'] == 'trade_type'){
                $query = $query->where('trade_types.name', 'LIKE', '%' . $params['search'] . '%');
            }elseif ($params['on'] == 'franchise_number') {
                $query = $query->where('franchises.franchise_number', 'LIKE', '%' . $params['search'] . '%');

            }elseif($params['on'] == 'franchise'){
                $query = $query->where('franchises.franchise_number', 'LIKE', '%' . $params['search'] . '%');
            }else {
                $query = $query->where($params['on'], 'LIKE', '%' . $params['search'] . '%');
            }

        }


        $query = $this->sortColumn($query, $params);

        return $query->paginate($params['size']);
    }

    public function searchAll($search)
    {
        return DB::table('trade_staff')
            ->join('trade_types', 'trade_types.id', '=', 'trade_staff.trade_type_id')
            ->select('trade_staff.id',
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
        $query = DB::table('trade_staff')
            ->join('franchises', 'franchises.id', '=', 'trade_staff.franchise_id')
            ->join('trade_types', 'trade_types.id', '=', 'trade_staff.trade_type_id')
            ->select('trade_staff.id',
                'trade_staff.franchise_id',
                'trade_staff.trade_type_id',
                'trade_staff.first_name',
                'trade_staff.last_name',
                'trade_staff.email',
                'trade_staff.contact_number',
                'trade_staff.company',
                'trade_staff.abn',
                'trade_staff.builders_license',
                'trade_staff.status',
                'franchises.franchise_number',
                'trade_types.name as trade_type',
                'trade_staff.created_at as created_at'
            )->whereIn('franchise_id', $franchiseIds)
            ->where('status', TradeStaff::ACTIVE);



        if(key_exists('search', $params) && key_exists('on', $params))
        {

            if($params['on'] == 'trade_type'){
                $query->where('trade_types.name', 'LIKE', '%' . $params['search'] . '%');
            }else if ($params['on'] == 'franchise_number') {
                $query->where('franchises.franchise_number', 'LIKE', '%' . $params['search'] . '%');
            }else {
                $query->where($params['on'], 'LIKE', '%' . $params['search'] . '%');
            }

        }

        $query = $this->sortColumn($query, $params);


        return $query->paginate($params['size']);
    }

    public function searchAllByFranchise(array $franchiseIds, $search)
    {
        return DB::table('trade_staff')
            ->join('trade_types', 'trade_types.id', '=', 'trade_staff.trade_type_id')
            ->select('trade_staff.id',
                'first_name',
                'last_name',
                'email',
                'status',
                'contact_number'
            )
            ->where('status', TradeStaff::ACTIVE)
            ->whereIn('franchise_id', $franchiseIds)
            ->where(function ($query) use ($search){
                $query->where('first_name','LIKE', '%' . $search . '%' )
                    ->orWhere('last_name','LIKE', '%' . $search . '%' )
                    ->orWhere('email', 'LIKE', '%' . $search . '%');
            })
            ->get();
    }

    private function sortColumn($query, $params){

        if($params['column'] == 'trade_type'){
            $query = $query->orderBy('trade_types.name', $params['direction']);
        }elseif ($params['column'] == 'franchise_number') {
            $query = $query->orderBy('franchises.franchise_number', $params['direction']);
        }elseif($params['column'] == 'franchise'){
            $query = $query->orderBy('franchises.franchise_number', $params['direction']);
        }else {
            $query = $query->orderBy($params['column'], $params['direction']);
        }


        return $query;
    }
}
